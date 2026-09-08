<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AssessmentInterpretation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssessmentInterpretation>
 */
class AssessmentInterpretationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssessmentInterpretation::class);
    }
}
