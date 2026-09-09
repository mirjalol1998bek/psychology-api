<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\MessageHandler;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Component\Organization\Hemis\Message\SyncFacultyGroupsMessage;
use App\Component\Organization\Hemis\Message\SyncGroupStudentsMessage;
use App\Repository\FacultyRepository;
use App\Repository\StudyGroupRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Fakultetning barcha HEMIS guruhlarini import qiladi, so'ng har guruh
 * talabalarini alohida xabar bilan navbatga qo'yadi (birma-bir, rate limiter).
 */
#[AsMessageHandler]
final readonly class SyncFacultyGroupsHandler
{
    public function __construct(
        private FacultyRepository $facultyRepository,
        private StudyGroupRepository $studyGroupRepository,
        private HemisOrganizationSync $sync,
        private MessageBusInterface $bus,
    ) {
    }

    public function __invoke(SyncFacultyGroupsMessage $message): void
    {
        $faculty = $this->facultyRepository->find($message->facultyId);

        if ($faculty === null || $faculty->getExternalId() === null) {
            return;
        }

        $this->sync->syncAllGroups($faculty);

        foreach ($this->studyGroupRepository->findBy(['faculty' => $faculty]) as $group) {
            $this->bus->dispatch(new SyncGroupStudentsMessage((int) $group->getId()));
        }
    }
}
