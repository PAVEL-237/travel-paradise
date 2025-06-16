<?php

namespace App\Controller;

use App\Entity\Visitor;
use App\Repository\VisitorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/visitors')]
#[IsGranted('ROLE_USER')]
class VisitorController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VisitorRepository $visitorRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/visit/{visitId}', name: 'app_visitor_list', methods: ['GET'])]
    public function list(int $visitId): JsonResponse
    {
        $visitors = $this->visitorRepository->findByVisit($visitId);
        return $this->json($visitors, 200, [], ['groups' => 'visitor:read']);
    }

    #[Route('/{id}', name: 'app_visitor_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $visitor = $this->visitorRepository->find($id);
        if (!$visitor) {
            return $this->json(['message' => 'Visitor not found'], 404);
        }
        return $this->json($visitor, 200, [], ['groups' => 'visitor:read']);
    }

    #[Route('', name: 'app_visitor_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $visitor = new Visitor();
        
        $visitor->setFirstName($data['firstName']);
        $visitor->setLastName($data['lastName']);
        $visitor->setIsPresent($data['isPresent'] ?? false);
        $visitor->setComments($data['comments'] ?? null);

        if (isset($data['visitId'])) {
            $visit = $this->entityManager->getReference('App\Entity\Visit', $data['visitId']);
            $visitor->setVisit($visit);
        }

        $errors = $this->validator->validate($visitor);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->persist($visitor);
        $this->entityManager->flush();

        return $this->json($visitor, 201, [], ['groups' => 'visitor:read']);
    }

    #[Route('/{id}', name: 'app_visitor_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): JsonResponse
    {
        $visitor = $this->visitorRepository->find($id);
        if (!$visitor) {
            return $this->json(['message' => 'Visitor not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['firstName'])) {
            $visitor->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $visitor->setLastName($data['lastName']);
        }
        if (isset($data['isPresent'])) {
            $visitor->setIsPresent($data['isPresent']);
        }
        if (isset($data['comments'])) {
            $visitor->setComments($data['comments']);
        }
        if (isset($data['visitId'])) {
            $visit = $this->entityManager->getReference('App\Entity\Visit', $data['visitId']);
            $visitor->setVisit($visit);
        }

        $errors = $this->validator->validate($visitor);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->flush();

        return $this->json($visitor, 200, [], ['groups' => 'visitor:read']);
    }

    #[Route('/{id}', name: 'app_visitor_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $visitor = $this->visitorRepository->find($id);
        if (!$visitor) {
            return $this->json(['message' => 'Visitor not found'], 404);
        }

        $this->entityManager->remove($visitor);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route('/visit/{visitId}/stats', name: 'app_visitor_stats', methods: ['GET'])]
    public function getVisitStats(int $visitId): JsonResponse
    {
        $stats = $this->visitorRepository->getPresenceStats($visitId);
        return $this->json($stats);
    }
} 