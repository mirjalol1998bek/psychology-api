<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StudyGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StudyGroup>
 */
class StudyGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudyGroup::class);
    }

    /**
     * HEMIS'dan import qilingan guruhlar (tunlik sinxron shularni yangilaydi).
     *
     * @return list<StudyGroup>
     */
    public function findLinkedToHemis(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.externalId IS NOT NULL')
            ->orderBy('g.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
