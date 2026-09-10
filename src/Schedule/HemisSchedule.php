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
use Symfony\Contracts\Cache\CacheInterface;

/**
 * HEMIS sinxroni har kuni **00:30 (Asia/Tashkent)** — hech kim ishlamayotgan
 * vaqtda. `messenger:consume hemis` worker'i ishga tushiradi. `stateful` —
 * kompyuter 00:30 da o'chiq bo'lsa, worker keyingi safar ishga tushganda
 * o'tkazib yuborilgan ishni bajaradi.
 */
#[AsSchedule('hemis')]
final class HemisSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly CacheInterface $cache,
    ) {
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
            ->stateful($this->cache)
            ->processOnlyLastMissedRun(true)
            ->lock($this->lockFactory->createLock('hemis-schedule'));
    }
}
