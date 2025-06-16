<?php

namespace App\Service;

use App\Entity\Place;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlaceRepository $placeRepository
    ) {}

    public function search(Request $request): array
    {
        $query = $request->query->all();
        $qb = $this->placeRepository->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.media', 'm')
            ->where('p.isPublished = :isPublished')
            ->setParameter('isPublished', true);

        // Recherche par texte
        if (!empty($query['q'])) {
            $qb->andWhere('p.name LIKE :query OR p.description LIKE :query')
               ->setParameter('query', '%' . $query['q'] . '%');
        }

        // Filtre par catégorie
        if (!empty($query['category'])) {
            $qb->andWhere('c.slug = :category')
               ->setParameter('category', $query['category']);
        }

        // Filtre par prix
        if (!empty($query['minPrice'])) {
            $qb->andWhere('p.price >= :minPrice')
               ->setParameter('minPrice', $query['minPrice']);
        }
        if (!empty($query['maxPrice'])) {
            $qb->andWhere('p.price <= :maxPrice')
               ->setParameter('maxPrice', $query['maxPrice']);
        }

        // Filtre par durée
        if (!empty($query['minDuration'])) {
            $qb->andWhere('p.duration >= :minDuration')
               ->setParameter('minDuration', $query['minDuration']);
        }
        if (!empty($query['maxDuration'])) {
            $qb->andWhere('p.duration <= :maxDuration')
               ->setParameter('maxDuration', $query['maxDuration']);
        }

        // Filtre par disponibilité
        if (!empty($query['date'])) {
            $date = new \DateTime($query['date']);
            $qb->andWhere('p.availableDates LIKE :date')
               ->setParameter('date', '%' . $date->format('Y-m-d') . '%');
        }

        // Filtre par note moyenne
        if (!empty($query['minRating'])) {
            $qb->andWhere('p.averageRating >= :minRating')
               ->setParameter('minRating', $query['minRating']);
        }

        // Tri
        $sort = $query['sort'] ?? 'name';
        $direction = $query['direction'] ?? 'asc';
        
        switch ($sort) {
            case 'price':
                $qb->orderBy('p.price', $direction);
                break;
            case 'rating':
                $qb->orderBy('p.averageRating', $direction);
                break;
            case 'duration':
                $qb->orderBy('p.duration', $direction);
                break;
            default:
                $qb->orderBy('p.name', $direction);
        }

        // Pagination
        $page = max(1, $query['page'] ?? 1);
        $limit = min(50, max(1, $query['limit'] ?? 10));
        $offset = ($page - 1) * $limit;

        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        // Exécution de la requête
        $places = $qb->getQuery()->getResult();
        $total = $this->getTotalResults($qb);

        return [
            'places' => $places,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ];
    }

    private function getTotalResults($qb): int
    {
        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT p.id)');
        return (int) $countQb->getQuery()->getSingleScalarResult();
    }

    public function getSearchSuggestions(string $query): array
    {
        return $this->placeRepository->createQueryBuilder('p')
            ->select('DISTINCT p.name')
            ->where('p.name LIKE :query')
            ->andWhere('p.isPublished = :isPublished')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('isPublished', true)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getPopularSearches(): array
    {
        // À implémenter avec un système de logs des recherches
        return [];
    }
} 