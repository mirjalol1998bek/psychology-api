<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\MessageHandler;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Component\Organization\Hemis\Message\NightlyHemisSyncMessage;
use App\Component\Organization\Hemis\Message\SyncGroupStudentsMessage;
use App\Repository\StudyGroupRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Fakultetlarni yangilaydi va import qilingan har guruh uchun alohida
 * SyncGroupStudentsMessage tashlaydi. Xabarlar navbatda birma-bir bajariladi
 * (bir vaqtda 50 000 so'rov emas). Lock — oldingi tunning ishi tugamagan
 * bo'lsa, ustidan yugurmaydi.
 */
#[AsMessageHandler]
final readonly class NightlyHemisSyncHandler
{
    public function __construct(
        private HemisOrganizationSync $sync,
        private StudyGroupRepository $studyGroupRepository,
        private MessageBusInterface $bus,
        private LockFactory $lockFactory,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(NightlyHemisSyncMessage $message): void
    {
        $lock = $this->lockFactory->createLock('hemis-nightly-sync', 7200);

        if ($lock->acquire() === false) {
            $this->logger->info('HEMIS nightly sync already running — skipped.');

            return;
        }

        try {
            $this->sync->syncFaculties();
            $groups = $this->studyGroupRepository->findLinkedToHemis();

            foreach ($groups as $group) {
                $this->bus->dispatch(new SyncGroupStudentsMessage((int) $group->getId()));
            }

            $this->logger->info(sprintf('HEMIS nightly sync queued %d groups.', count($groups)));
        } finally {
            $lock->release();
        }
    }
}
