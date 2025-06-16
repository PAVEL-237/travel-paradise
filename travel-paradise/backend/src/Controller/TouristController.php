<?php

namespace App\Controller;

use App\Entity\Tourist;
use App\Entity\Visit;
use App\Repository\TouristRepository;
use App\Repository\VisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tourists')]
class TouristController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TouristRepository $touristRepository,
        private VisitRepository $visitRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'tourist_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $visitId = $request->query->get('visitId');
        $tourists = $visitId 
            ? $this->touristRepository->findBy(['visit' => $visitId])
            : $this->touristRepository->findAll();

        $data = $this->serializer->serialize($tourists, 'json', ['groups' => 'tourist:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'tourist_show', methods: ['GET'])]
    public function show(Tourist $tourist): JsonResponse
    {
        $data = $this->serializer->serialize($tourist, 'json', ['groups' => 'tourist:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'tourist_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Vérifier si la visite existe
        $visit = $this->visitRepository->find($data['visitId']);
        if (!$visit) {
            return new JsonResponse(['error' => 'Visit not found'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier le nombre maximum de touristes
        if (count($visit->getTourists()) >= 15) {
            return new JsonResponse(['error' => 'Maximum number of tourists reached for this visit'], Response::HTTP_BAD_REQUEST);
        }

        $tourist = new Tourist();
        $tourist->setFirstName($data['firstName']);
        $tourist->setLastName($data['lastName']);
        $tourist->setPresence($data['presence'] ?? false);
        if (isset($data['comment'])) {
            $tourist->setComment($data['comment']);
        }
        $tourist->setVisit($visit);

        $errors = $this->validator->validate($tourist);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($tourist);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($tourist, 'json', ['groups' => 'tourist:read']);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'tourist_update', methods: ['PUT'])]
    public function update(Tourist $tourist, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['firstName'])) $tourist->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $tourist->setLastName($data['lastName']);
        if (isset($data['presence'])) $tourist->setPresence($data['presence']);
        if (isset($data['comment'])) $tourist->setComment($data['comment']);

        $errors = $this->validator->validate($tourist);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($tourist, 'json', ['groups' => 'tourist:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'tourist_delete', methods: ['DELETE'])]
    public function delete(Tourist $tourist): JsonResponse
    {
        $this->entityManager->remove($tourist);
        $this->entityManager->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/presence', name: 'tourist_update_presence', methods: ['PATCH'])]
    public function updatePresence(Tourist $tourist, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['presence'])) {
            return new JsonResponse(['error' => 'Presence status is required'], Response::HTTP_BAD_REQUEST);
        }

        $tourist->setPresence($data['presence']);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($tourist, 'json', ['groups' => 'tourist:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/comment', name: 'tourist_update_comment', methods: ['PATCH'])]
    public function updateComment(Tourist $tourist, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['comment'])) {
            return new JsonResponse(['error' => 'Comment is required'], Response::HTTP_BAD_REQUEST);
        }

        $tourist->setComment($data['comment']);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($tourist, 'json', ['groups' => 'tourist:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
} 