<?php

namespace App\Service;

use App\Entity\Visit;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VisitFilterService
{
    public function __construct(
        private ValidatorInterface $validator
    ) {}

    public function applyFilters(QueryBuilder $qb, array $filters): QueryBuilder
    {
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

        if (!empty($filters['location'])) {
            $qb->andWhere('v.location LIKE :location')
               ->setParameter('location', '%' . $filters['location'] . '%');
        }

        if (!empty($filters['country'])) {
            $qb->andWhere('v.country = :country')
               ->setParameter('country', $filters['country']);
        }

        return $qb;
    }

    public function validateVisit(Visit $visit): array
    {
        $errors = $this->validator->validate($visit);
        $errorMessages = [];

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorMessages[] = [
                    'property' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }
        }

        return $errorMessages;
    }

    public function extractFiltersFromRequest(Request $request): array
    {
        return [
            'status' => $request->query->get('status'),
            'guide' => $request->query->get('guide'),
            'date' => $request->query->get('date'),
            'location' => $request->query->get('location'),
            'country' => $request->query->get('country'),
            'page' => $request->query->getInt('page', 1),
            'limit' => $request->query->getInt('limit', 10)
        ];
    }
} 