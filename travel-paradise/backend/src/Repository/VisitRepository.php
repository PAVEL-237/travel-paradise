<?php

namespace App\Repository;

use App\Entity\Guide;
use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function save(Visit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Visit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByMonth(int $month, int $year): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('MONTH(v.startDate) = :month')
            ->andWhere('YEAR(v.startDate) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->orderBy('v.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByGuideAndMonth(int $guideId, int $month, int $year): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.guide = :guideId')
            ->andWhere('MONTH(v.startDate) = :month')
            ->andWhere('YEAR(v.startDate) = :year')
            ->setParameter('guideId', $guideId)
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->orderBy('v.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('v')
            ->leftJoin('v.guide', 'g')
            ->leftJoin('v.tourists', 't');

        if (!empty($filters['status'])) {
            $qb->andWhere('v.status = :status')
               ->setParameter('status', $filters['status']);
        }

        if (!empty($filters['guide'])) {
            $qb->andWhere('g.id = :guideId')
               ->setParameter('guideId', $filters['guide']);
        }

        if (!empty($filters['date'])) {
            $qb->andWhere('v.date = :date')
               ->setParameter('date', new \DateTime($filters['date']));
        }

        return $qb->getQuery()->getResult();
    }

    public function getVisitsByMonth(?Guide $guide = null): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('MONTH(v.date) as month, COUNT(v.id) as count')
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($guide) {
            $qb->where('v.guide = :guide')
               ->setParameter('guide', $guide);
        }

        return $qb->getQuery()->getResult();
    }

    public function getPresenceRate(?Guide $guide = null): float
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(DISTINCT t.id) as totalTourists, SUM(CASE WHEN t.presence = true THEN 1 ELSE 0 END) as presentTourists')
            ->leftJoin('v.tourists', 't');

        if ($guide) {
            $qb->where('v.guide = :guide')
               ->setParameter('guide', $guide);
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        if (!$result || $result['totalTourists'] === 0) {
            return 0;
        }

        return ($result['presentTourists'] / $result['totalTourists']) * 100;
    }

    public function getOverallPresenceRate(): float
    {
        return $this->getPresenceRate();
    }

    public function getVisitsByPeriod(?string $startDate, ?string $endDate): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id) as total')
            ->addSelect('v.date')
            ->groupBy('v.date');

        if ($startDate) {
            $qb->andWhere('v.date >= :startDate')
               ->setParameter('startDate', new \DateTime($startDate));
        }

        if ($endDate) {
            $qb->andWhere('v.date <= :endDate')
               ->setParameter('endDate', new \DateTime($endDate));
        }

        return $qb->getQuery()->getResult();
    }

    public function getPresenceRateByGuide(?string $startDate = null, ?string $endDate = null): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('g.id as guideId')
            ->addSelect('g.firstName')
            ->addSelect('g.lastName')
            ->addSelect('COUNT(t.id) as totalTourists')
            ->addSelect('SUM(CASE WHEN t.presence = true THEN 1 ELSE 0 END) as presentTourists')
            ->leftJoin('v.guide', 'g')
            ->leftJoin('v.tourists', 't')
            ->groupBy('g.id', 'g.firstName', 'g.lastName');

        if ($startDate) {
            $qb->andWhere('v.date >= :startDate')
               ->setParameter('startDate', new \DateTime($startDate));
        }

        if ($endDate) {
            $qb->andWhere('v.date <= :endDate')
               ->setParameter('endDate', new \DateTime($endDate));
        }

        return $qb->getQuery()->getResult();
    }

    public function getPopularActivities(?string $startDate = null, ?string $endDate = null, string $period = 'month'): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('v.location')
            ->addSelect('COUNT(v.id) as visitCount')
            ->groupBy('v.location')
            ->orderBy('visitCount', 'DESC');

        if ($startDate) {
            $qb->andWhere('v.date >= :startDate')
               ->setParameter('startDate', new \DateTime($startDate));
        }

        if ($endDate) {
            $qb->andWhere('v.date <= :endDate')
               ->setParameter('endDate', new \DateTime($endDate));
        }

        return $qb->getQuery()->getResult();
    }

    public function getSatisfactionStats(?string $startDate = null, ?string $endDate = null): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('AVG(t.rating) as averageRating')
            ->addSelect('COUNT(t.id) as totalRatings')
            ->leftJoin('v.tourists', 't')
            ->where('t.rating IS NOT NULL');

        if ($startDate) {
            $qb->andWhere('v.date >= :startDate')
               ->setParameter('startDate', new \DateTime($startDate));
        }

        if ($endDate) {
            $qb->andWhere('v.date <= :endDate')
               ->setParameter('endDate', new \DateTime($endDate));
        }

        return $qb->getQuery()->getResult();
    }

    public function getActivityTrends(string $period = 'month'): array
    {
        return $this->createQueryBuilder('v')
            ->select('v.location')
            ->addSelect('v.date')
            ->addSelect('COUNT(v.id) as visitCount')
            ->groupBy('v.location', 'v.date')
            ->orderBy('v.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getSatisfactionByActivity(string $period = 'month'): array
    {
        return $this->createQueryBuilder('v')
            ->select('v.location')
            ->addSelect('AVG(t.rating) as averageRating')
            ->addSelect('COUNT(t.id) as totalRatings')
            ->leftJoin('v.tourists', 't')
            ->where('t.rating IS NOT NULL')
            ->groupBy('v.location')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingVisits(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.date <= :date')
            ->andWhere('v.date > :now')
            ->andWhere('v.isFinished = :isFinished')
            ->andWhere('v.isCancelled = :isCancelled')
            ->setParameter('date', $date)
            ->setParameter('now', new \DateTime())
            ->setParameter('isFinished', false)
            ->setParameter('isCancelled', false)
            ->orderBy('v.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('v.date', 'ASC')
            ->addOrderBy('v.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByGuide(int $guideId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.guide = :guideId')
            ->setParameter('guideId', $guideId)
            ->orderBy('v.date', 'ASC')
            ->addOrderBy('v.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastVisits(): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.date < :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('v.date', 'DESC')
            ->addOrderBy('v.startTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findVisitsByStatus(string $status): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.status = :status')
            ->setParameter('status', $status)
            ->orderBy('v.date', 'ASC')
            ->addOrderBy('v.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyVisitStats(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id) as totalVisits')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleResult();
    }

    public function getMonthlyGuideStats(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        return $this->createQueryBuilder('v')
            ->select('g.id as guideId', 'g.firstName', 'g.lastName', 'COUNT(v.id) as visitCount')
            ->join('v.guide', 'g')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('g.id', 'g.firstName', 'g.lastName')
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyPresenceStats(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id) as totalVisits', 'SUM(CASE WHEN vis.isPresent = true THEN 1 ELSE 0 END) as presentCount')
            ->leftJoin('v.visitors', 'vis')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByGuideAndDateRange(int $guideId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.guide = :guideId')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('guideId', $guideId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('v.date', 'ASC')
            ->addOrderBy('v.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findVisitsByMonth(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        return $this->createQueryBuilder('v')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('v.date', 'ASC')
            ->addOrderBy('v.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyStats(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id) as total_visits')
            ->addSelect('COUNT(DISTINCT v.guide) as total_guides')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleResult();
    }
}
