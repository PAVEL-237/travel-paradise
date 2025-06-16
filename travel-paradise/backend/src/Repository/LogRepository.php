<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function getLogsByAction(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.action, COUNT(l.id) as count')
            ->groupBy('l.action')
            ->getQuery()
            ->getResult();
    }

    public function getLogsByEntity(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.entity, COUNT(l.id) as count')
            ->groupBy('l.entity')
            ->getQuery()
            ->getResult();
    }

    public function getLogsByUser(): array
    {
        return $this->createQueryBuilder('l')
            ->select('u.email, COUNT(l.id) as count')
            ->leftJoin('l.user', 'u')
            ->groupBy('u.email')
            ->getQuery()
            ->getResult();
    }

    public function getLogsByDate(): array
    {
        return $this->createQueryBuilder('l')
            ->select('DATE(l.createdAt) as date, COUNT(l.id) as count')
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }

    public function deleteLogsOlderThan(\DateTime $date): int
    {
        return $this->createQueryBuilder('l')
            ->delete()
            ->where('l.createdAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }
} 