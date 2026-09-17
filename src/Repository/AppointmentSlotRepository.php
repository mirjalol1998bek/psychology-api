<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AppointmentSlot;
use App\Entity\User;
use App\Enum\AppointmentStatus;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppointmentSlot>
 */
class AppointmentSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppointmentSlot::class);
    }

    /** Shu psixologning shu kundagi band vaqti berilgan oraliq bilan kesishadimi. */
    public function hasBookedOverlap(User $psychologist, DateTime $date, string $startTime, string $endTime): bool
    {
        $count = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.psychologist = :psychologist')
            ->andWhere('s.date = :date')
            ->andWhere('s.status = :status')
            ->andWhere('s.startTime < :endTime')
            ->andWhere('(s.endTime IS NULL OR s.endTime > :startTime)')
            ->setParameter('psychologist', $psychologist)
            ->setParameter('date', $date, 'date')
            ->setParameter('status', AppointmentStatus::Booked->value)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    /** Berilgan 2 soatlik blokni to'liq qamrab oluvchi bo'sh (`free`) vaqt oralig'ini topadi. */
    public function findCoveringFreeWindow(User $psychologist, DateTime $date, string $startTime, string $endTime): ?AppointmentSlot
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.psychologist = :psychologist')
            ->andWhere('s.date = :date')
            ->andWhere('s.status = :status')
            ->andWhere('s.startTime <= :startTime')
            ->andWhere('s.endTime >= :endTime')
            ->setParameter('psychologist', $psychologist)
            ->setParameter('date', $date, 'date')
            ->setParameter('status', AppointmentStatus::Free->value)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
