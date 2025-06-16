<?php

namespace App\Controller;

use App\Service\RecommendationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/recommendations')]
class RecommendationController extends AbstractController
{
    public function __construct(
        private RecommendationService $recommendationService,
        private Security $security
    ) {}

    #[Route('', name: 'recommendations_list', methods: ['GET'])]
    public function getRecommendations(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $limit = $request->query->getInt('limit', 5);
        $recommendations = $this->recommendationService->getRecommendationsForUser($user, $limit);

        return new JsonResponse([
            'recommendations' => $recommendations,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]
        ]);
    }

    #[Route('/explain', name: 'recommendations_explain', methods: ['GET'])]
    public function explainRecommendations(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $location = $request->query->get('location');
        if (!$location) {
            return new JsonResponse(['error' => 'Location parameter is required'], Response::HTTP_BAD_REQUEST);
        }

        $recommendations = $this->recommendationService->getRecommendationsForUser($user);
        
        if (!isset($recommendations[$location])) {
            return new JsonResponse(['error' => 'Location not found in recommendations'], Response::HTTP_NOT_FOUND);
        }

        $explanation = $this->getRecommendationExplanation($recommendations[$location]);

        return new JsonResponse([
            'location' => $location,
            'explanation' => $explanation,
            'details' => $recommendations[$location]
        ]);
    }

    private function getRecommendationExplanation(array $recommendation): string
    {
        switch ($recommendation['type']) {
            case 'favorite':
                return sprintf(
                    'This activity is recommended because you previously rated it %.1f/5',
                    $recommendation['rating']
                );
            case 'similar':
                return sprintf(
                    'This activity is recommended because users with similar preferences rated it %.1f/5',
                    $recommendation['rating']
                );
            case 'popular':
                return sprintf(
                    'This activity is recommended because it is popular with %d visits',
                    $recommendation['visits']
                );
            default:
                return 'This activity is recommended based on various factors';
        }
    }
} 