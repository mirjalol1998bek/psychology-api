<?php

declare(strict_types=1);

namespace App\Component\Organization\Hemis\MessageHandler;

use App\Component\Organization\Hemis\HemisOrganizationSync;
use App\Component\Organization\Hemis\Message\SyncFacultyStudentsMessage;
use App\Repository\FacultyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Bitta fakultetning barcha hozirgi guruh + talabalarini sinxronlaydi
 * (`syncFacultyStudents` → `student-list?_department=`). Fakultet qulfi —
 * tugma tez-tez bosilsa yoki xabar takrorlansa, ikki marta yugurmaydi.
 */
#[AsMessageHandler]
final readonly class SyncFacultyStudentsHandler
{
    public function __construct(
        private FacultyRepository $facultyRepository,
        private HemisOrganizationSync $sync,
        private LockFactory $lockFactory,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SyncFacultyStudentsMessage $message): void
    {
        $faculty = $this->facultyRepository->find($message->facultyId);

        if ($faculty === null || $faculty->getExternalId() === null) {
            return;
        }

        $lock = $this->lockFactory->createLock('hemis-faculty-' . $message->facultyId, 1800);

        if ($lock->acquire() === false) {
            $this->logger->info(sprintf('HEMIS faculty %d sync already running — skipped.', $message->facultyId));

            return;
        }

        try {
            $counts = $this->sync->syncFacultyStudents($faculty);
            $this->logger->info(sprintf(
                'HEMIS faculty %d: %d new, %d updated students.',
                $message->facultyId,
                $counts->created,
                $counts->updated,
            ));
        } finally {
            $lock->release();
        }
    }
}
