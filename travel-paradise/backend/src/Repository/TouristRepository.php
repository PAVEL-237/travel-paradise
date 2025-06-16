<?php

namespace App\Repository;

use App\Entity\Tourist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tourist>
 *
 * @method Tourist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tourist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tourist[]    findAll()
 * @method Tourist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TouristRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tourist::class);
    }

    public function save(Tourist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tourist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return float Returns the attendance rate for a specific visit
     */
    public function getAttendanceRate(int $visitId): float
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id) as total, SUM(CASE WHEN t.isPresent = true THEN 1 ELSE 0 END) as present')
            ->where('t.visit = :visitId')
            ->setParameter('visitId', $visitId);

        $result = $qb->getQuery()->getOneOrNullResult();

        if (!$result || $result['total'] === 0) {
            return 0;
        }

        return ($result['present'] / $result['total']) * 100;
    }
} 