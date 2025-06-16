<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function findByVisit(int $visitId, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.visit = :visitId')
            ->andWhere('r.status = :status')
            ->setParameter('visitId', $visitId)
            ->setParameter('status', 'approved')
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findPendingModeration(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFlagged(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', 'flagged')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAverageRatingForVisit(int $visitId): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as average')
            ->andWhere('r.visit = :visitId')
            ->andWhere('r.status = :status')
            ->setParameter('visitId', $visitId)
            ->setParameter('status', 'approved')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
} 