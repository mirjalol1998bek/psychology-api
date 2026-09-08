<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\Assignment;
use App\Entity\User;
use App\Repository\AssignmentRepository;
use DateTimeImmutable;

/**
 * @implements ProviderInterface<Assignment>
 */
final class StudentAssignmentProvider implements ProviderInterface
{
    public function __construct(
        private readonly AssignmentRepository $assignmentRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<Assignment>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->currentUser->getUser();

        if ($user->getPrimaryRole()->isStaff() === true) {
            return $this->assignmentRepository->findBy([], ['id' => 'DESC']);
        }

        return $this->openAssignmentsFor($user);
    }

    /**
     * @return list<Assignment>
     */
    private function openAssignmentsFor(User $user): array
    {
        $group = $user->getStudyGroup();

        if ($group === null) {
            return [];
        }

        $moment = new DateTimeImmutable();
        $open = [];

        foreach ($this->assignmentRepository->findBy(['studyGroup' => $group]) as $assignment) {
            if ($assignment->isOpenAt($moment) === true) {
                $open[] = $assignment;
            }
        }

        return $open;
    }
}
