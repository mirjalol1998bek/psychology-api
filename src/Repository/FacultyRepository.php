<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Faculty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faculty>
 */
class FacultyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faculty::class);
    }

    /**
     * HEMIS bilan bog'langan (externalId bor) fakultetlar.
     *
     * @return list<Faculty>
     */
    public function findLinkedToHemis(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.externalId IS NOT NULL')
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
