<?php

declare(strict_types=1);

namespace App\Component\Assessment\Seed;

use App\Component\Assignment\AssignmentFactory;
use App\Component\Assignment\AssignmentManager;
use App\Component\Organization\OrganizationFactory;
use App\Component\Organization\StudentFactory;
use App\Component\Organization\StudyGroupManager;
use App\Component\Organization\FacultyManager;
use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Entity\Category;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Enum\StudyLanguage;
use App\Repository\AssignmentRepository;
use App\Repository\CategoryRepository;
use App\Repository\FacultyRepository;
use App\Repository\StudyGroupRepository;
use App\Repository\UserRepository;

final class DemoSeeder
{
    private const PASSWORD = 'demo1234';
    private const FACULTY = 'Demo fakultet';
    private const GROUP = 'DEMO-01';

    public function __construct(
        private readonly CatalogSeeder $catalogSeeder,
        private readonly OrganizationFactory $organizationFactory,
        private readonly FacultyManager $facultyManager,
        private readonly StudyGroupManager $studyGroupManager,
        private readonly UserFactory $userFactory,
        private readonly StudentFactory $studentFactory,
        private readonly UserManager $userManager,
        private readonly AssignmentFactory $assignmentFactory,
        private readonly AssignmentManager $assignmentManager,
        private readonly FacultyRepository $facultyRepository,
        private readonly StudyGroupRepository $studyGroupRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly AssignmentRepository $assignmentRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @return list<string>
     */
    public function seed(): array
    {
        $this->catalogSeeder->seed();
        $group = $this->ensureGroup();
        $accounts = [];
        $accounts[] = $this->ensureStaff('admin@demo.uz', RoleEnum::Admin, 'Demo Admin');
        $accounts[] = $this->ensureStaff('psixolog@demo.uz', RoleEnum::Psychologist, 'Demo Psixolog');
        $accounts[] = $this->ensureStudent('talaba@demo.uz', $group);
        $this->ensureAssignments($group);

        return $accounts;
    }

    private function ensureGroup(): StudyGroup
    {
        $group = $this->studyGroupRepository->findOneBy(['name' => self::GROUP]);

        if ($group !== null) {
            return $group;
        }

        $faculty = $this->facultyRepository->findOneBy(['name' => self::FACULTY])
            ?? $this->organizationFactory->createFaculty(self::FACULTY);
        $this->facultyManager->save($faculty, true);

        $group = $this->organizationFactory->createStudyGroup($faculty, self::GROUP, StudyLanguage::Uzbek);
        $this->studyGroupManager->save($group, true);

        return $group;
    }

    private function ensureStaff(string $email, RoleEnum $role, string $fullName): string
    {
        $user = $this->userRepository->findOneByEmail($email);

        if ($user === null) {
            $user = $this->userFactory->create($email, self::PASSWORD);
        }

        $user->setFullName($fullName);
        $user->setRoles([$role->value]);
        $user->setIsActive(true);
        $this->userManager->save($user, true);

        return $email . ' / ' . self::PASSWORD;
    }

    private function ensureStudent(string $email, StudyGroup $group): string
    {
        $user = $this->userRepository->findOneByEmail($email);

        if ($user === null) {
            $user = $this->studentFactory->create('Demo Talaba', 'demo-0001', $group, StudyLanguage::Uzbek);
            $user->setEmail($email);
        }

        $user->setStudyGroup($group);
        $user->setIsActive(true);
        $this->userManager->hashPassword($user, self::PASSWORD);
        $this->userManager->save($user, true);

        return $email . ' / ' . self::PASSWORD;
    }

    private function ensureAssignments(StudyGroup $group): void
    {
        foreach ($this->renderableCategories() as $category) {
            $existing = $this->assignmentRepository->findOneBy(['category' => $category, 'studyGroup' => $group]);

            if ($existing === null) {
                $this->assignmentManager->save($this->assignmentFactory->create($category, $group), true);
            }
        }
    }

    /**
     * @return list<Category>
     */
    private function renderableCategories(): array
    {
        $wanted = ['TEMPERAMENT_STATEMENTS', 'TEMPERAMENT_CHOICE', 'FIGURE_CHOICE', 'SCORE_SCALE'];
        $categories = [];

        foreach ($this->categoryRepository->findAll() as $category) {
            if (in_array($category->getInstrumentType()->value, $wanted, true)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }
}
