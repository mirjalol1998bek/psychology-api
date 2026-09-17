<?php

declare(strict_types=1);

namespace App\Component\Appeal;

use App\Component\Appointment\AppointmentSlotFactory;
use App\Component\Appointment\AppointmentSlotManager;
use App\Component\Appointment\AvailabilityWindowConsumer;
use App\Component\Notification\NotificationDispatcher;
use App\Entity\Appeal;
use App\Entity\AppointmentSlot;
use App\Entity\User;
use App\Enum\AppealStatus;
use App\Repository\AppointmentSlotRepository;
use DateTime;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Murojaatdagi "qabulga yozilish" so'rovini haqiqiy AppointmentSlot'ga
 * aylantiradi. Davomiylik doim aniq 2 soat (uzunroq bo'lmaydi). Bron qilingan
 * blok psixolog belgilagan `free` vaqt oralig'i ichida bo'lishi shart.
 */
final class AppealAppointmentBooker
{
    private const DURATION_HOURS = 2;

    public function __construct(
        private readonly AppointmentSlotFactory $appointmentSlotFactory,
        private readonly AppointmentSlotManager $appointmentSlotManager,
        private readonly AppointmentSlotRepository $appointmentSlotRepository,
        private readonly AvailabilityWindowConsumer $availabilityWindowConsumer,
        private readonly AppealManager $appealManager,
        private readonly NotificationDispatcher $notificationDispatcher,
    ) {
    }

    public function book(Appeal $appeal, User $psychologist, string $date, string $startTime): Appeal
    {
        $bookedDate = $this->parseDate($date);
        $this->assertValidTime($startTime);
        $endTime = $this->addDuration($startTime);

        $window = $this->appointmentSlotRepository->findCoveringFreeWindow($psychologist, $bookedDate, $startTime, $endTime);

        if ($window === null) {
            throw new UnprocessableEntityHttpException('Bu vaqtda psixologning qabul soatlari belgilanmagan.');
        }

        if ($this->appointmentSlotRepository->hasBookedOverlap($psychologist, $bookedDate, $startTime, $endTime) === true) {
            throw new ConflictHttpException('Bu vaqt allaqachon band.');
        }

        $slot = $this->appointmentSlotFactory->createBooked(
            $psychologist,
            $this->requireStudent($appeal),
            $bookedDate,
            $startTime,
            $endTime,
        );
        $this->appointmentSlotManager->save($slot, true);
        $this->availabilityWindowConsumer->consume($window, $startTime, $endTime);

        $this->linkAndAnswer($appeal, $slot, $psychologist);
        $this->notificationDispatcher->notifyStudentOnAppealAppointmentBooked($appeal);

        return $appeal;
    }

    private function linkAndAnswer(Appeal $appeal, AppointmentSlot $slot, User $psychologist): void
    {
        $appeal->setAppointmentSlot($slot);

        if ($appeal->getReply() === null) {
            $appeal->setReply(sprintf(
                'Qabulingiz %s kuni soat %s dan %s gacha belgilandi.',
                $slot->getDate()->format('Y-m-d'),
                $slot->getStartTime(),
                $slot->getEndTime(),
            ));
        }

        $appeal->setRepliedBy($psychologist);
        $appeal->setRepliedAt(new DateTime());
        $appeal->setStatus(AppealStatus::Answered);
        $this->appealManager->save($appeal, true);
    }

    private function requireStudent(Appeal $appeal): User
    {
        $student = $appeal->getStudent();

        if ($student === null) {
            throw new BadRequestHttpException('Murojaat talabaga bog\'lanmagan.');
        }

        return $student;
    }

    private function parseDate(string $date): DateTime
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) !== 1) {
            throw new BadRequestHttpException('Sana noto\'g\'ri (YYYY-MM-DD).');
        }

        $parsed = DateTime::createFromFormat('Y-m-d', $date);

        if ($parsed === false) {
            throw new BadRequestHttpException('Sana noto\'g\'ri.');
        }

        return $parsed;
    }

    private function assertValidTime(string $time): void
    {
        if (preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) !== 1) {
            throw new BadRequestHttpException('Vaqt noto\'g\'ri (HH:MM).');
        }
    }

    private function addDuration(string $startTime): string
    {
        $start = DateTime::createFromFormat('H:i', $startTime);
        $end = DateTime::createFromFormat('H:i', $startTime);
        $end->modify(sprintf('+%d hours', self::DURATION_HOURS));

        if ($end->format('Y-m-d') !== $start->format('Y-m-d')) {
            return '23:59';
        }

        return $end->format('H:i');
    }
}
