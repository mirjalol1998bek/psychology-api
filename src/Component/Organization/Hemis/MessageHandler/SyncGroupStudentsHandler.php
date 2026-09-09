<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\MessageHandler;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Component\Organization\Hemis\Message\SyncGroupStudentsMessage;
use App\Repository\StudyGroupRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SyncGroupStudentsHandler
{
    public function __construct(
        private StudyGroupRepository $studyGroupRepository,
        private HemisOrganizationSync $sync,
    ) {
    }

    public function __invoke(SyncGroupStudentsMessage $message): void
    {
        $group = $this->studyGroupRepository->find($message->studyGroupId);

        if ($group === null || $group->getExternalId() === null) {
            return;
        }

        $this->sync->syncGroupStudents($group);
    }
}
