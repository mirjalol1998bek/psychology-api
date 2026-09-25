<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AssessmentResult;
use App\Enum\InstrumentType;
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
     * @return list<array{studentId: int, algo: string, resultKey: string}>
     */
    public function facultyBreakdown(int $facultyId): array
    {
        return $this->createQueryBuilder('r')
            ->select('s.id AS studentId', 'c.instrumentType AS algo', 'r.resultKey AS resultKey')
            ->join('r.attempt', 'a')
            ->join('a.student', 's')
            ->join('s.studyGroup', 'g')
            ->join('a.quiz', 'q')
            ->join('q.category', 'c')
            ->andWhere('g.faculty = :faculty')
            ->setParameter('faculty', $facultyId)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param list<InstrumentType> $types
     * @return list<AssessmentResult>
     */
    public function findByInstrumentTypes(array $types): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.attempt', 'a')
            ->join('a.quiz', 'q')
            ->join('q.category', 'c')
            ->andWhere('c.instrumentType IN (:types)')
            ->setParameter('types', $types)
            ->getQuery()
            ->getResult();
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
