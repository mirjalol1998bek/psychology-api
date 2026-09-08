<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AssessmentResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssessmentResult>
 */
class AssessmentResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssessmentResult::class);
    }

    /**
     * @return list<AssessmentResult>
     */
    public function findByGroupAndCategory(int $studyGroupId, int $categoryId): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.attempt', 'a')
            ->join('a.student', 's')
            ->join('a.quiz', 'q')
            ->andWhere('s.studyGroup = :group')
            ->andWhere('q.category = :category')
            ->setParameter('group', $studyGroupId)
            ->setParameter('category', $categoryId)
            ->orderBy('s.fullName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
