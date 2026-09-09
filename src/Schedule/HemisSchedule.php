<?php

declare(strict_types=1);

namespace App\Schedule;

use App\Component\Organization\Hemis\Message\NightlyHemisSyncMessage;
use DateTimeZone;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

/**
 * HEMIS sinxroni har kuni **00:30 (Asia/Tashkent)** — hech kim ishlamayotgan
 * vaqtda. `messenger:consume hemis` worker'i ishga tushiradi.
 */
#[AsSchedule('hemis')]
final class HemisSchedule implements ScheduleProviderInterface
{
    public function __construct(private readonly LockFactory $lockFactory)
    {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::cron(
                    '30 0 * * *',
                    new NightlyHemisSyncMessage(),
                    new DateTimeZone('Asia/Tashkent'),
                ),
            )
            ->lock($this->lockFactory->createLock('hemis-schedule'));
    }
}
