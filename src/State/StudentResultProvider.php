<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Component\User\CurrentUser;
use App\Entity\AssessmentResult;
use App\Repository\AssessmentResultRepository;

/**
 * @implements ProviderInterface<AssessmentResult>
 */
final class StudentResultProvider implements ProviderInterface
{
    public function __construct(
        private readonly AssessmentResultRepository $resultRepository,
        private readonly CurrentUser $currentUser,
    ) {
    }

    /**
     * @return list<AssessmentResult>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->currentUser->getUser();

        if ($user->getPrimaryRole()->isStaff() === true) {
            return $this->resultRepository->findBy([], ['id' => 'DESC']);
        }

        return $this->filterByStudent($user->getId());
    }

    /**
     * @return list<AssessmentResult>
     */
    private function filterByStudent(?int $studentId): array
    {
        $own = [];

        foreach ($this->resultRepository->findBy([], ['id' => 'DESC']) as $result) {
            if ($result->getAttempt()?->getStudent()?->getId() === $studentId) {
                $own[] = $result;
            }
        }

        return $own;
    }
}
