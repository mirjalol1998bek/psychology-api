<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis;

use App\Component\Organization\Hemis\Dto\HemisGroup;
use App\Component\Organization\Hemis\Dto\HemisStudent;
use App\Component\Organization\FacultyManager;
use App\Component\Organization\OrganizationFactory;
use App\Component\Organization\StudentFactory;
use App\Component\Organization\StudyGroupManager;
use App\Component\User\UserManager;
use App\Entity\Faculty;
use App\Entity\StudyGroup;
use App\Repository\FacultyRepository;
use App\Repository\StudyGroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * HEMIS talaba REST API'dan fakultet / guruh / talabalarni bizning bazaga
 * ko'chiradi. `externalId` (fakultet/guruh) va `hemisId` (talaba) bo'yicha
 * moslashtiradi — mavjudini yangilaydi, yo'g'ini yaratadi. Rol/holatni
 * o'zgartirmaydi (admin bergan huquqlar saqlanadi).
 *
 * HEMIS `group-list` barcha (eski bitirgan) guruhlarni ham qaytaradi, shuning
 * uchun guruhlar to'plab import qilinmaydi — admin `listGroups` dan kerakligini
 * tanlab `importGroup` qiladi. `syncGroupStudents` esa faqat hozir o'qiyotgan
 * talabalarni oladi (`_group` filtri).
 */
final class HemisOrganizationSync
{
    public function __construct(
        private readonly HemisApiClient $api,
        private readonly FacultyRepository $facultyRepository,
        private readonly StudyGroupRepository $studyGroupRepository,
        private readonly UserRepository $userRepository,
        private readonly OrganizationFactory $organizationFactory,
        private readonly StudentFactory $studentFactory,
        private readonly FacultyManager $facultyManager,
        private readonly StudyGroupManager $studyGroupManager,
        private readonly UserManager $userManager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function syncFaculties(): SyncCounts
    {
        $counts = new SyncCounts();

        foreach ($this->api->fetchFaculties() as $item) {
            if ($item->active === true) {
                $this->upsertFaculty($item->externalId, $item->name, $counts);
            }
        }

        $this->entityManager->flush();

        return $counts;
    }

    /**
     * HEMIS'dagi guruhlar (saqlanmaydi — tanlash uchun). Nom bo'yicha tartib.
     *
     * @return list<HemisGroup>
     */
    public function listGroups(Faculty $faculty): array
    {
        $groups = array_values(array_filter(
            $this->api->fetchGroups((string) $faculty->getExternalId()),
            static fn (HemisGroup $group): bool => $group->active,
        ));

        usort($groups, static fn (HemisGroup $a, HemisGroup $b): int => strnatcasecmp($b->name, $a->name));

        return $groups;
    }

    /**
     * Fakultetning barcha faol guruhlarini import qiladi (CLI — `ask:hemis:sync`).
     * UI buni ishlatmaydi (juda ko'p eski guruh bo'lishi mumkin).
     */
    public function syncAllGroups(Faculty $faculty): SyncCounts
    {
        $counts = new SyncCounts();

        foreach ($this->api->fetchGroups((string) $faculty->getExternalId()) as $item) {
            if ($item->active === false || $this->isGraduatedGroup($item->name) === true) {
                continue;
            }

            $existing = $this->studyGroupRepository->findOneBy(['externalId' => $item->externalId]) !== null;
            $this->upsertGroup($faculty, $item);
            $existing ? $counts->updated++ : $counts->created++;
        }

        $this->entityManager->flush();

        return $counts;
    }

    public function importGroup(Faculty $faculty, string $groupExternalId): StudyGroup
    {
        foreach ($this->api->fetchGroups((string) $faculty->getExternalId()) as $item) {
            if ($item->externalId === $groupExternalId) {
                $group = $this->upsertGroup($faculty, $item);
                $this->entityManager->flush();

                return $group;
            }
        }

        throw new NotFoundHttpException('Bu guruh HEMIS fakultetida topilmadi: ' . $groupExternalId);
    }

    public function syncGroupStudents(StudyGroup $group): SyncCounts
    {
        $counts = new SyncCounts();

        foreach ($this->api->fetchStudents((string) $group->getExternalId()) as $item) {
            if ($item->studying === true && $item->studentIdNumber !== '') {
                $this->upsertStudent($group, $item, $counts);
            }
        }

        $this->entityManager->flush();

        return $counts;
    }

    /** HEMIS bitirgan guruhlar nomini " Y" bilan tugatadi. */
    private function isGraduatedGroup(string $name): bool
    {
        return str_ends_with(rtrim($name), ' Y');
    }

    private function upsertFaculty(string $externalId, string $name, SyncCounts $counts): void
    {
        $faculty = $this->facultyRepository->findOneBy(['externalId' => $externalId]);

        if ($faculty === null) {
            $faculty = $this->organizationFactory->createFaculty($name, $externalId);
            $counts->created++;
        } else {
            $faculty->setName($name);
            $counts->updated++;
        }

        $this->facultyManager->save($faculty);
    }

    private function upsertGroup(Faculty $faculty, HemisGroup $item): StudyGroup
    {
        $group = $this->studyGroupRepository->findOneBy(['externalId' => $item->externalId]);

        if ($group === null) {
            $group = $this->organizationFactory->createStudyGroup($faculty, $item->name, $item->studyLanguage);
            $group->setExternalId($item->externalId);
        } else {
            $group->setName($item->name);
            $group->setStudyLanguage($item->studyLanguage);
            $group->setFaculty($faculty);
        }

        $this->studyGroupManager->save($group);

        return $group;
    }

    private function upsertStudent(StudyGroup $group, HemisStudent $item, SyncCounts $counts): void
    {
        $student = $this->userRepository->findOneBy(['hemisId' => $item->studentIdNumber]);

        if ($student === null) {
            $student = $this->studentFactory->create($item->fullName, $item->studentIdNumber, $group, $item->studyLanguage);
            $this->userManager->hashPassword($student, bin2hex(random_bytes(8)));
            $counts->created++;
        } else {
            $student->setFullName($item->fullName);
            $student->setStudyGroup($group);
            $counts->updated++;
        }

        if ($item->image !== null) {
            $student->setImage($item->image);
        }

        $this->userManager->save($student);
    }
}
