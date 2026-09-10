<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Assignment;
use App\Entity\Faculty;
use App\Entity\StudyGroup;
use App\Entity\User;
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

    /**
     * Fakultetning talabasi ham, biriktirilgan testi ham yo'q HEMIS guruhlarini
     * o'chiradi (eski/bitirgan kurslar sinxrondan keyin tozalanadi).
     */
    public function pruneEmptyHemisGroups(Faculty $faculty): int
    {
        return (int) $this->createQueryBuilder('g')
            ->delete()
            ->where('g.faculty = :faculty')
            ->andWhere('g.externalId IS NOT NULL')
            ->andWhere('g.id NOT IN (SELECT IDENTITY(u.studyGroup) FROM ' . User::class . ' u WHERE u.studyGroup IS NOT NULL)')
            ->andWhere('g.id NOT IN (SELECT IDENTITY(a.studyGroup) FROM ' . Assignment::class . ' a)')
            ->setParameter('faculty', $faculty)
            ->getQuery()
            ->execute();
    }
}
