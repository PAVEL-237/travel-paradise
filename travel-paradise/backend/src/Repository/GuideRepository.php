<?php

namespace App\Repository;

use App\Entity\Guide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Guide>
 *
 * @method Guide|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guide|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guide[]    findAll()
 * @method Guide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guide::class);
    }

    public function save(Guide $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Guide $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getGuideAvailability(Guide $guide, ?string $date = null): array
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id', 'g.firstName', 'g.lastName')
            ->addSelect('CASE WHEN v.id IS NOT NULL THEN false ELSE true END as isAvailable')
            ->leftJoin('g.visits', 'v')
            ->where('g.id = :guideId')
            ->setParameter('guideId', $guide->getId());

        if ($date) {
            $qb->andWhere('v.date = :date')
               ->setParameter('date', new \DateTime($date));
        }

        return $qb->getQuery()->getResult();
    }

    public function getAllGuidesAvailability(?string $date = null): array
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id', 'g.firstName', 'g.lastName')
            ->addSelect('CASE WHEN v.id IS NOT NULL THEN false ELSE true END as isAvailable')
            ->leftJoin('g.visits', 'v');

        if ($date) {
            $qb->andWhere('v.date = :date')
               ->setParameter('date', new \DateTime($date));
        }

        return $qb->getQuery()->getResult();
    }

    public function getGuideSchedule(Guide $guide, ?string $startDate = null, ?string $endDate = null): array
    {
        $qb = $this->createQueryBuilder('g')
            ->select('v.id', 'v.date', 'v.startTime', 'v.duration', 'v.location')
            ->leftJoin('g.visits', 'v')
            ->where('g.id = :guideId')
            ->setParameter('guideId', $guide->getId())
            ->orderBy('v.date', 'ASC')
            ->addOrderBy('v.startTime', 'ASC');

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

    public function updateGuideAvailability(Guide $guide, string $date, bool $availability): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        
        if ($availability) {
            // Supprimer les indisponibilités pour cette date
            $qb->delete('App\Entity\GuideUnavailability', 'gu')
               ->where('gu.guide = :guide')
               ->andWhere('gu.date = :date')
               ->setParameter('guide', $guide)
               ->setParameter('date', new \DateTime($date));
        } else {
            // Ajouter une indisponibilité pour cette date
            $unavailability = new GuideUnavailability();
            $unavailability->setGuide($guide);
            $unavailability->setDate(new \DateTime($date));
            $this->getEntityManager()->persist($unavailability);
        }

        $this->getEntityManager()->flush();
    }

    public function getGuidePerformance(Guide $guide, ?string $startDate = null, ?string $endDate = null): array
    {
        $qb = $this->createQueryBuilder('g')
            ->select('COUNT(v.id) as totalVisits')
            ->addSelect('AVG(t.rating) as averageRating')
            ->addSelect('COUNT(DISTINCT t.id) as totalTourists')
            ->addSelect('SUM(CASE WHEN t.presence = true THEN 1 ELSE 0 END) as presentTourists')
            ->leftJoin('g.visits', 'v')
            ->leftJoin('v.tourists', 't')
            ->where('g.id = :guideId')
            ->setParameter('guideId', $guide->getId());

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

    public function findAvailableReplacements(string $date, string $time, ?int $currentGuideId = null): array
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id', 'g.firstName', 'g.lastName')
            ->leftJoin('g.visits', 'v')
            ->where('v.id IS NULL OR (v.date != :date OR v.startTime != :time)')
            ->setParameter('date', new \DateTime($date))
            ->setParameter('time', new \DateTime($time));

        if ($currentGuideId) {
            $qb->andWhere('g.id != :currentGuideId')
               ->setParameter('currentGuideId', $currentGuideId);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.status = :status')
            ->setParameter('status', $status)
            ->orderBy('g.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCountry(string $country): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.country = :country')
            ->andWhere('g.status = :status')
            ->setParameter('country', $country)
            ->setParameter('status', 'active')
            ->orderBy('g.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveGuides(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.status = :status')
            ->setParameter('status', 'active')
            ->orderBy('g.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findGuidesWithUpcomingVisits(): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.visits', 'v')
            ->andWhere('v.date >= :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('v.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 