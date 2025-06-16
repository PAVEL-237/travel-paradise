<?php

namespace App\Controller;

use App\Entity\Guide;
use App\Repository\GuideRepository;
use App\Repository\VisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/guides')]
#[IsGranted('ROLE_USER')]
class GuideController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GuideRepository $guideRepository,
        private VisitRepository $visitRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'app_guide_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $guides = $this->guideRepository->findAll();
        return $this->json($guides, 200, [], ['groups' => 'guide:read']);
    }

    #[Route('/{id}', name: 'app_guide_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $guide = $this->guideRepository->find($id);
        if (!$guide) {
            return $this->json(['message' => 'Guide not found'], 404);
        }
        return $this->json($guide, 200, [], ['groups' => 'guide:read']);
    }

    #[Route('', name: 'app_guide_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $guide = new Guide();
        
        $guide->setFirstName($data['firstName']);
        $guide->setLastName($data['lastName']);
        $guide->setPhoto($data['photo'] ?? null);
        $guide->setStatus($data['status'] ?? 'active');
        $guide->setCountry($data['country']);

        $errors = $this->validator->validate($guide);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->persist($guide);
        $this->entityManager->flush();

        return $this->json($guide, 201, [], ['groups' => 'guide:read']);
    }

    #[Route('/{id}', name: 'app_guide_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): JsonResponse
    {
        $guide = $this->guideRepository->find($id);
        if (!$guide) {
            return $this->json(['message' => 'Guide not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['firstName'])) {
            $guide->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $guide->setLastName($data['lastName']);
        }
        if (isset($data['photo'])) {
            $guide->setPhoto($data['photo']);
        }
        if (isset($data['status'])) {
            $guide->setStatus($data['status']);
        }
        if (isset($data['country'])) {
            $guide->setCountry($data['country']);
        }

        $errors = $this->validator->validate($guide);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->flush();

        return $this->json($guide, 200, [], ['groups' => 'guide:read']);
    }

    #[Route('/{id}', name: 'app_guide_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $guide = $this->guideRepository->find($id);
        if (!$guide) {
            return $this->json(['message' => 'Guide not found'], 404);
        }

        $this->entityManager->remove($guide);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route('/country/{country}', name: 'app_guide_by_country', methods: ['GET'])]
    public function findByCountry(string $country): JsonResponse
    {
        $guides = $this->guideRepository->findByCountry($country);
        return $this->json($guides, 200, [], ['groups' => 'guide:read']);
    }

    #[Route('/status/{status}', name: 'guide_by_status', methods: ['GET'])]
    public function findByStatus(string $status): JsonResponse
    {
        $guides = $this->guideRepository->findByStatus($status);
        $data = $this->serializer->serialize($guides, 'json', ['groups' => 'guide:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/active', name: 'guide_active', methods: ['GET'])]
    public function findActive(): JsonResponse
    {
        $guides = $this->guideRepository->findActiveGuides();
        $data = $this->serializer->serialize($guides, 'json', ['groups' => 'guide:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/upcoming-visits', name: 'guide_upcoming_visits', methods: ['GET'])]
    public function findWithUpcomingVisits(): JsonResponse
    {
        $guides = $this->guideRepository->findGuidesWithUpcomingVisits();
        $data = $this->serializer->serialize($guides, 'json', ['groups' => 'guide:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/stats', name: 'guide_stats', methods: ['GET'])]
    public function stats(Guide $guide): JsonResponse
    {
        $stats = [
            'totalVisits' => $this->visitRepository->count(['guide' => $guide]),
            'visitsByMonth' => $this->visitRepository->getVisitsByMonth($guide),
            'presenceRate' => $this->visitRepository->getPresenceRate($guide)
        ];

        return new JsonResponse($stats);
    }

    #[Route('/availability', name: 'guide_availability', methods: ['GET'])]
    public function getAvailability(Request $request): JsonResponse
    {
        $date = $request->query->get('date');
        $guideId = $request->query->get('guideId');

        if ($guideId) {
            $guide = $this->guideRepository->find($guideId);
            if (!$guide) {
                return new JsonResponse(['error' => 'Guide not found'], Response::HTTP_NOT_FOUND);
            }
            $availability = $this->guideRepository->getGuideAvailability($guide, $date);
        } else {
            $availability = $this->guideRepository->getAllGuidesAvailability($date);
        }

        return new JsonResponse($availability);
    }

    #[Route('/schedule', name: 'guide_schedule', methods: ['GET'])]
    public function getSchedule(Request $request): JsonResponse
    {
        $guideId = $request->query->get('guideId');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        if (!$guideId) {
            return new JsonResponse(['error' => 'Guide ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $guide = $this->guideRepository->find($guideId);
        if (!$guide) {
            return new JsonResponse(['error' => 'Guide not found'], Response::HTTP_NOT_FOUND);
        }

        $schedule = $this->guideRepository->getGuideSchedule($guide, $startDate, $endDate);
        return new JsonResponse($schedule);
    }

    #[Route('/schedule', name: 'guide_schedule_update', methods: ['POST'])]
    public function updateSchedule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['guideId']) || !isset($data['date']) || !isset($data['availability'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $guide = $this->guideRepository->find($data['guideId']);
        if (!$guide) {
            return new JsonResponse(['error' => 'Guide not found'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le guide a des visites programmées
        $hasVisits = $this->visitRepository->hasScheduledVisits($guide, $data['date']);
        if ($hasVisits && !$data['availability']) {
            return new JsonResponse(
                ['error' => 'Cannot mark as unavailable: guide has scheduled visits'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->guideRepository->updateGuideAvailability($guide, $data['date'], $data['availability']);
        
        return new JsonResponse(['message' => 'Schedule updated successfully']);
    }

    #[Route('/performance', name: 'guide_performance', methods: ['GET'])]
    public function getPerformance(Request $request): JsonResponse
    {
        $guideId = $request->query->get('guideId');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        if (!$guideId) {
            return new JsonResponse(['error' => 'Guide ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $guide = $this->guideRepository->find($guideId);
        if (!$guide) {
            return new JsonResponse(['error' => 'Guide not found'], Response::HTTP_NOT_FOUND);
        }

        $performance = $this->guideRepository->getGuidePerformance($guide, $startDate, $endDate);
        return new JsonResponse($performance);
    }

    #[Route('/replacements', name: 'guide_replacements', methods: ['GET'])]
    public function getAvailableReplacements(Request $request): JsonResponse
    {
        $date = $request->query->get('date');
        $time = $request->query->get('time');
        $currentGuideId = $request->query->get('currentGuideId');

        if (!$date || !$time) {
            return new JsonResponse(['error' => 'Date and time are required'], Response::HTTP_BAD_REQUEST);
        }

        $replacements = $this->guideRepository->findAvailableReplacements($date, $time, $currentGuideId);
        return new JsonResponse($replacements);
    }
} 