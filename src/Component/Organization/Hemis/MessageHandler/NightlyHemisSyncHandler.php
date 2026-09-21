<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\MessageHandler;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Component\Organization\Hemis\Message\NightlyHemisSyncMessage;
use App\Component\Organization\Hemis\Message\SyncFacultyStudentsMessage;
use App\Repository\FacultyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Fakultetlarni va tyutor-guruh biriktiruvlarini yangilaydi, so'ng HEMIS'ga
 * bog'langan har fakultet uchun bitta SyncFacultyStudentsMessage tashlaydi
 * (14 ta xabar, mingtalab emas). Lock — oldingi tunning ishi tugamagan
 * bo'lsa, ustidan yugurmaydi.
 */
#[AsMessageHandler]
final readonly class NightlyHemisSyncHandler
{
    public function __construct(
        private HemisOrganizationSync $sync,
        private FacultyRepository $facultyRepository,
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
            $this->sync->syncTutors();
            $faculties = $this->facultyRepository->findLinkedToHemis();

            foreach ($faculties as $faculty) {
                $this->bus->dispatch(new SyncFacultyStudentsMessage((int) $faculty->getId()));
            }

            $this->logger->info(sprintf('HEMIS nightly sync queued %d faculties.', count($faculties)));
        } finally {
            $lock->release();
        }
    }
}
