<?php

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Place>
 *
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    public function save(Place $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Place $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPlacesByCategory(): array
    {
        return $this->createQueryBuilder('p')
            ->select('c.name as category, COUNT(p.id) as count')
            ->join('p.category', 'c')
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }

    public function getMostVisitedPlaces(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.name, COUNT(v.id) as visitCount')
            ->leftJoin('p.visits', 'v')
            ->groupBy('p.id')
            ->orderBy('visitCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getBestRatedPlaces(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.name, AVG(v.rating) as averageRating')
            ->leftJoin('p.visits', 'v')
            ->groupBy('p.id')
            ->orderBy('averageRating', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getAverageRating(): float
    {
        $result = $this->createQueryBuilder('p')
            ->select('AVG(v.rating) as avgRating')
            ->leftJoin('p.visits', 'v')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
} 