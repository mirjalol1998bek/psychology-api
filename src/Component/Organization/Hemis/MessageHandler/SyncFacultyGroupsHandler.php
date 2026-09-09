<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\MessageHandler;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Component\Organization\Hemis\Message\SyncFacultyGroupsMessage;
use App\Repository\FacultyRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SyncFacultyGroupsHandler
{
    public function __construct(
        private FacultyRepository $facultyRepository,
        private HemisOrganizationSync $sync,
    ) {
    }

    public function __invoke(SyncFacultyGroupsMessage $message): void
    {
        $faculty = $this->facultyRepository->find($message->facultyId);

        if ($faculty === null || $faculty->getExternalId() === null) {
            return;
        }

        $this->sync->syncAllGroups($faculty);
    }
}
