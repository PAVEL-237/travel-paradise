<?php

namespace App\Repository;

use App\Entity\Visitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VisitorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visitor::class);
    }

    public function findByVisit(int $visitId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.visit = :visitId')
            ->setParameter('visitId', $visitId)
            ->orderBy('v.lastName', 'ASC')
            ->addOrderBy('v.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPresentVisitors(int $visitId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.visit = :visitId')
            ->andWhere('v.isPresent = :isPresent')
            ->setParameter('visitId', $visitId)
            ->setParameter('isPresent', true)
            ->orderBy('v.lastName', 'ASC')
            ->addOrderBy('v.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAbsentVisitors(int $visitId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.visit = :visitId')
            ->andWhere('v.isPresent = :isPresent')
            ->setParameter('visitId', $visitId)
            ->setParameter('isPresent', false)
            ->orderBy('v.lastName', 'ASC')
            ->addOrderBy('v.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getPresenceStats(int $visitId): array
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id) as total_visitors')
            ->addSelect('SUM(CASE WHEN v.isPresent = true THEN 1 ELSE 0 END) as present_visitors')
            ->andWhere('v.visit = :visitId')
            ->setParameter('visitId', $visitId)
            ->getQuery()
            ->getSingleResult();
    }
} 