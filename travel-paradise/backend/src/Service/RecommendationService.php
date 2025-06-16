<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Visit;
use App\Repository\VisitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VisitRepository $visitRepository,
        private UserRepository $userRepository
    ) {}

    public function getRecommendationsForUser(User $user, int $limit = 5): array
    {
        // 1. Get user's favorite activities
        $favoriteActivities = $this->getUserFavoriteActivities($user);

        // 2. Get similar users based on preferences
        $similarUsers = $this->findSimilarUsers($user);

        // 3. Get popular activities
        $popularActivities = $this->getPopularActivities();

        // 4. Combine and rank recommendations
        $recommendations = $this->combineAndRankRecommendations(
            $favoriteActivities,
            $similarUsers,
            $popularActivities,
            $limit
        );

        return $recommendations;
    }

    private function getUserFavoriteActivities(User $user): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('v.location, COUNT(v.id) as visitCount, AVG(t.rating) as avgRating')
            ->from(Visit::class, 'v')
            ->leftJoin('v.tourists', 't')
            ->where('t.user = :user')
            ->andWhere('t.rating >= 4')
            ->setParameter('user', $user)
            ->groupBy('v.location')
            ->orderBy('avgRating', 'DESC')
            ->getQuery()
            ->getResult();
    }

    private function findSimilarUsers(User $user): array
    {
        // Get user's activity preferences
        $userPreferences = $this->getUserPreferences($user);

        // Find users with similar preferences
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('u.id, COUNT(v.id) as commonActivities')
            ->from(User::class, 'u')
            ->leftJoin('u.tourists', 't')
            ->leftJoin('t.visit', 'v')
            ->where('u.id != :userId')
            ->andWhere('v.location IN (:locations)')
            ->setParameter('userId', $user->getId())
            ->setParameter('locations', array_keys($userPreferences))
            ->groupBy('u.id')
            ->orderBy('commonActivities', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    private function getUserPreferences(User $user): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $preferences = $qb->select('v.location, AVG(t.rating) as avgRating')
            ->from(Visit::class, 'v')
            ->leftJoin('v.tourists', 't')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->groupBy('v.location')
            ->getQuery()
            ->getResult();

        $preferencesMap = [];
        foreach ($preferences as $pref) {
            $preferencesMap[$pref['location']] = $pref['avgRating'];
        }

        return $preferencesMap;
    }

    private function getPopularActivities(): array
    {
        return $this->visitRepository->getPopularActivities();
    }

    private function combineAndRankRecommendations(
        array $favoriteActivities,
        array $similarUsers,
        array $popularActivities,
        int $limit
    ): array {
        $recommendations = [];
        $weights = [
            'favorite' => 0.4,
            'similar' => 0.4,
            'popular' => 0.2
        ];

        // Add favorite activities
        foreach ($favoriteActivities as $activity) {
            $recommendations[$activity['location']] = [
                'score' => $activity['avgRating'] * $weights['favorite'],
                'type' => 'favorite',
                'rating' => $activity['avgRating']
            ];
        }

        // Add activities from similar users
        foreach ($similarUsers as $similarUser) {
            $userActivities = $this->getUserFavoriteActivities(
                $this->userRepository->find($similarUser['id'])
            );
            foreach ($userActivities as $activity) {
                if (!isset($recommendations[$activity['location']])) {
                    $recommendations[$activity['location']] = [
                        'score' => $activity['avgRating'] * $weights['similar'],
                        'type' => 'similar',
                        'rating' => $activity['avgRating']
                    ];
                }
            }
        }

        // Add popular activities
        foreach ($popularActivities as $activity) {
            if (!isset($recommendations[$activity['location']])) {
                $recommendations[$activity['location']] = [
                    'score' => $activity['visitCount'] * $weights['popular'],
                    'type' => 'popular',
                    'visits' => $activity['visitCount']
                ];
            }
        }

        // Sort by score and limit results
        uasort($recommendations, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($recommendations, 0, $limit, true);
    }
} 