<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\Visit;
use App\Repository\RatingRepository;
use App\Repository\VisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/ratings')]
class RatingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RatingRepository $ratingRepository,
        private VisitRepository $visitRepository,
        private Security $security,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'rating_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['visitId']) || !isset($data['rating']) || !isset($data['comment'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $visit = $this->visitRepository->find($data['visitId']);
        if (!$visit) {
            return new JsonResponse(['error' => 'Visit not found'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si l'utilisateur a déjà noté cette visite
        $existingRating = $this->ratingRepository->findOneBy([
            'visit' => $visit,
            'user' => $user
        ]);

        if ($existingRating) {
            return new JsonResponse(['error' => 'You have already rated this visit'], Response::HTTP_BAD_REQUEST);
        }

        $rating = new Rating();
        $rating->setVisit($visit);
        $rating->setUser($user);
        $rating->setRating($data['rating']);
        $rating->setComment($data['comment']);
        $rating->setCreatedAt(new \DateTime());

        $errors = $this->validator->validate($rating);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Rating created successfully',
            'rating' => [
                'id' => $rating->getId(),
                'rating' => $rating->getRating(),
                'comment' => $rating->getComment(),
                'createdAt' => $rating->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'rating_update', methods: ['PUT'])]
    public function update(Rating $rating, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user || $rating->getUser() !== $user) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['rating'])) {
            $rating->setRating($data['rating']);
        }
        if (isset($data['comment'])) {
            $rating->setComment($data['comment']);
        }
        $rating->setUpdatedAt(new \DateTime());

        $errors = $this->validator->validate($rating);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Rating updated successfully',
            'rating' => [
                'id' => $rating->getId(),
                'rating' => $rating->getRating(),
                'comment' => $rating->getComment(),
                'updatedAt' => $rating->getUpdatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    #[Route('/{id}', name: 'rating_delete', methods: ['DELETE'])]
    public function delete(Rating $rating): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user || $rating->getUser() !== $user) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($rating);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/visit/{visitId}', name: 'rating_list_by_visit', methods: ['GET'])]
    public function listByVisit(int $visitId, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $ratings = $this->ratingRepository->findByVisit($visitId, $page, $limit);
        $total = $this->ratingRepository->count(['visit' => $visitId]);

        return new JsonResponse([
            'ratings' => $ratings,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    #[Route('/moderate/{id}', name: 'rating_moderate', methods: ['POST'])]
    public function moderate(Rating $rating, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['action'])) {
            return new JsonResponse(['error' => 'Action is required'], Response::HTTP_BAD_REQUEST);
        }

        switch ($data['action']) {
            case 'approve':
                $rating->setStatus('approved');
                break;
            case 'reject':
                $rating->setStatus('rejected');
                break;
            case 'flag':
                $rating->setStatus('flagged');
                break;
            default:
                return new JsonResponse(['error' => 'Invalid action'], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Rating moderated successfully',
            'rating' => [
                'id' => $rating->getId(),
                'status' => $rating->getStatus()
            ]
        ]);
    }
} 