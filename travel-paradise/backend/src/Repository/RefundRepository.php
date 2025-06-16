<?php

namespace App\Repository;

use App\Entity\Refund;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RefundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Refund::class);
    }

    public function findPendingRefunds(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('r.requestedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findRefundsByUser(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.requestedBy = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.requestedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRefundsByVisit(int $visitId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.visit = :visitId')
            ->setParameter('visitId', $visitId)
            ->orderBy('r.requestedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalRefundedAmount(): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as total')
            ->andWhere('r.status = :status')
            ->setParameter('status', 'approved')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
} 