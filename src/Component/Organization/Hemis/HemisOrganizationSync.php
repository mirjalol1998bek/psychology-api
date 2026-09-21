<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis;

use App\Component\Organization\Hemis\Dto\HemisEmployee;
use App\Component\Organization\Hemis\Dto\HemisGroup;
use App\Component\Organization\Hemis\Dto\HemisStudent;
use App\Component\Organization\Hemis\Dto\HemisTutorGroup;
use App\Component\Organization\FacultyManager;
use App\Component\Organization\OrganizationFactory;
use App\Component\Organization\StudentFactory;
use App\Component\Organization\StudyGroupManager;
use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Entity\Faculty;
use App\Entity\StudyGroup;
use App\Entity\User;
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
 * Asosiy yo'l — `syncFacultyStudents`: `student-list?_department=` ni oladi,
 * guruhlarni talabalardan yig'adi (faqat talabasi bor guruhlar yaratiladi).
 * `listGroups`/`importGroup` — admin bitta guruhni qo'lda tanlab import qilishi
 * uchun (`group-list?_department=`, eski guruhlarni ham qaytaradi).
 */
final class HemisOrganizationSync
{
    private const FLUSH_EVERY = 500;

    public function __construct(
        private readonly HemisApiClient $api,
        private readonly FacultyRepository $facultyRepository,
        private readonly StudyGroupRepository $studyGroupRepository,
        private readonly UserRepository $userRepository,
        private readonly OrganizationFactory $organizationFactory,
        private readonly StudentFactory $studentFactory,
        private readonly UserFactory $userFactory,
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

    /**
     * Fakultetning barcha hozirgi talabalari — `student-list?_department=`.
     * Guruhlar talabalardan yig'iladi (faqat talabasi bor guruhlar yaratiladi).
     * Har FLUSH_EVERY talabadan keyin flush + clear — minglab talabada xotira
     * to'lib ketmasin.
     */
    public function syncFacultyStudents(Faculty $faculty): SyncCounts
    {
        $counts = new SyncCounts();
        $facultyId = (int) $faculty->getId();
        $items = $this->api->fetchFacultyStudents((string) $faculty->getExternalId());
        $groups = [];
        $done = 0;

        foreach ($items as $item) {
            if ($this->isImportableStudent($item) === false) {
                continue;
            }

            $group = $groups[$item->groupExternalId] ??= $this->resolveGroup($faculty, $item);
            $this->upsertStudent($group, $item, $counts);
            $done++;

            if ($done % self::FLUSH_EVERY === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                $faculty = $this->requireFaculty($facultyId);
                $groups = [];
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
        $this->studyGroupRepository->pruneEmptyHemisGroups($this->requireFaculty($facultyId));

        return $counts;
    }

    /**
     * Tyutorlar — "employee-list"dan proaktiv sinxron: guruhga tyutor
     * biriktirilishi uchun xodim hali tizimga kirmagan bo'lsa ham `pending`
     * hisob yaratiladi (admin keyin `ROLE_TUTOR` beradi), mavjud bo'lsa
     * guruh biriktiruvi yangilanadi.
     */
    public function syncTutors(): SyncCounts
    {
        $counts = new SyncCounts();

        foreach ($this->api->fetchTutors() as $item) {
            $this->upsertTutor($item, $counts);
        }

        $this->entityManager->flush();

        return $counts;
    }

    private function upsertTutor(HemisEmployee $item, SyncCounts $counts): void
    {
        $tutor = $this->userRepository->findOneBy(['hemisId' => $item->hemisId]);

        if ($tutor === null) {
            $tutor = $this->userFactory->createFromHemisEmployee($item);
            $counts->created++;
        } else {
            $tutor->setFullName($item->fullName);
            $counts->updated++;
        }

        if ($item->image !== null) {
            $tutor->setImage($item->image);
        }

        $this->userManager->save($tutor);
        $this->linkTutorGroups($tutor, $item->tutorGroups);
    }

    /**
     * @param list<HemisTutorGroup> $tutorGroups
     */
    private function linkTutorGroups(User $tutor, array $tutorGroups): void
    {
        foreach ($tutorGroups as $group) {
            $studyGroup = $this->studyGroupRepository->findOneBy(['externalId' => $group->externalId]);

            if ($studyGroup !== null) {
                $studyGroup->setTutor($tutor);
                $this->studyGroupManager->save($studyGroup);
            }
        }
    }

    private function requireFaculty(int $id): Faculty
    {
        $faculty = $this->facultyRepository->find($id);

        if ($faculty === null) {
            throw new NotFoundHttpException('Fakultet topilmadi: ' . $id);
        }

        return $faculty;
    }

    private function isImportableStudent(HemisStudent $item): bool
    {
        return $item->studying === true
            && $item->studentIdNumber !== ''
            && $item->groupExternalId !== '';
    }

    private function resolveGroup(Faculty $faculty, HemisStudent $item): StudyGroup
    {
        return $this->upsertGroup($faculty, new HemisGroup(
            $item->groupExternalId,
            $item->groupName,
            (string) $faculty->getExternalId(),
            (string) $faculty->getName(),
            $item->studyLanguage,
            true,
        ));
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
