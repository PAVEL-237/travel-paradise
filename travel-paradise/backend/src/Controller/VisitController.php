<?php

namespace App\Controller;

use App\Entity\Visit;
use App\Entity\Tourist;
use App\Repository\VisitRepository;
use App\Repository\GuideRepository;
use App\Service\VisitFilterService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/visits')]
#[IsGranted('ROLE_USER')]
class VisitController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VisitRepository $visitRepository,
        private GuideRepository $guideRepository,
        private SerializerInterface $serializer,
        private VisitFilterService $filterService,
        private SecurityService $securityService,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'app_visit_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $visits = $this->visitRepository->findAll();
        return $this->json($visits, 200, [], ['groups' => 'visit:read']);
    }

    #[Route('/{id}', name: 'app_visit_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $visit = $this->visitRepository->find($id);
        if (!$visit) {
            return $this->json(['message' => 'Visit not found'], 404);
        }
        return $this->json($visit, 200, [], ['groups' => 'visit:read']);
    }

    #[Route('', name: 'app_visit_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $visit = new Visit();
        
        $visit->setPhoto($data['photo'] ?? null);
        $visit->setCountry($data['country']);
        $visit->setLocation($data['location']);
        $visit->setDate(new \DateTime($data['date']));
        $visit->setStartTime(new \DateTime($data['startTime']));
        $visit->setDuration($data['duration']);
        $visit->setStatus($data['status'] ?? 'scheduled');
        $visit->setGeneralComment($data['generalComment'] ?? null);

        if (isset($data['guideId'])) {
            $guide = $this->entityManager->getReference('App\Entity\Guide', $data['guideId']);
            $visit->setGuide($guide);
        }

        $errors = $this->validator->validate($visit);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        return $this->json($visit, 201, [], ['groups' => 'visit:read']);
    }

    #[Route('/{id}', name: 'app_visit_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): JsonResponse
    {
        $visit = $this->visitRepository->find($id);
        if (!$visit) {
            return $this->json(['message' => 'Visit not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['photo'])) {
            $visit->setPhoto($data['photo']);
        }
        if (isset($data['country'])) {
            $visit->setCountry($data['country']);
        }
        if (isset($data['location'])) {
            $visit->setLocation($data['location']);
        }
        if (isset($data['date'])) {
            $visit->setDate(new \DateTime($data['date']));
        }
        if (isset($data['startTime'])) {
            $visit->setStartTime(new \DateTime($data['startTime']));
        }
        if (isset($data['duration'])) {
            $visit->setDuration($data['duration']);
        }
        if (isset($data['status'])) {
            $visit->setStatus($data['status']);
        }
        if (isset($data['generalComment'])) {
            $visit->setGeneralComment($data['generalComment']);
        }
        if (isset($data['guideId'])) {
            $guide = $this->entityManager->getReference('App\Entity\Guide', $data['guideId']);
            $visit->setGuide($guide);
        }

        $errors = $this->validator->validate($visit);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->entityManager->flush();

        return $this->json($visit, 200, [], ['groups' => 'visit:read']);
    }

    #[Route('/{id}', name: 'app_visit_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $visit = $this->visitRepository->find($id);
        if (!$visit) {
            return $this->json(['message' => 'Visit not found'], 404);
        }

        $this->entityManager->remove($visit);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route('/date-range', name: 'visit_date_range', methods: ['GET'])]
    public function findByDateRange(Request $request): JsonResponse
    {
        $startDate = new \DateTime($request->query->get('startDate'));
        $endDate = new \DateTime($request->query->get('endDate'));

        $visits = $this->visitRepository->findByDateRange($startDate, $endDate);
        $data = $this->serializer->serialize($visits, 'json', ['groups' => 'visit:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/guide/{guideId}', name: 'visit_by_guide', methods: ['GET'])]
    public function findByGuide(int $guideId): JsonResponse
    {
        $visits = $this->visitRepository->findByGuide($guideId);
        $data = $this->serializer->serialize($visits, 'json', ['groups' => 'visit:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/upcoming', name: 'app_visit_upcoming', methods: ['GET'])]
    public function upcoming(): JsonResponse
    {
        $visits = $this->visitRepository->findUpcomingVisits();
        return $this->json($visits, 200, [], ['groups' => 'visit:read']);
    }

    #[Route('/past', name: 'visit_past', methods: ['GET'])]
    public function findPast(): JsonResponse
    {
        $visits = $this->visitRepository->findPastVisits();
        $data = $this->serializer->serialize($visits, 'json', ['groups' => 'visit:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/status/{status}', name: 'visit_by_status', methods: ['GET'])]
    public function findByStatus(string $status): JsonResponse
    {
        $visits = $this->visitRepository->findVisitsByStatus($status);
        $data = $this->serializer->serialize($visits, 'json', ['groups' => 'visit:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/stats/monthly', name: 'visit_stats_monthly', methods: ['GET'])]
    public function getMonthlyStats(Request $request): JsonResponse
    {
        $year = (int) $request->query->get('year', date('Y'));
        $month = (int) $request->query->get('month', date('m'));

        $stats = $this->visitRepository->getMonthlyVisitStats($year, $month);
        return new JsonResponse($stats, Response::HTTP_OK);
    }

    #[Route('/stats/guide', name: 'visit_stats_guide', methods: ['GET'])]
    public function getGuideStats(Request $request): JsonResponse
    {
        $year = (int) $request->query->get('year', date('Y'));
        $month = (int) $request->query->get('month', date('m'));

        $stats = $this->visitRepository->getMonthlyGuideStats($year, $month);
        return new JsonResponse($stats, Response::HTTP_OK);
    }

    #[Route('/stats/presence', name: 'visit_stats_presence', methods: ['GET'])]
    public function getPresenceStats(Request $request): JsonResponse
    {
        $year = (int) $request->query->get('year', date('Y'));
        $month = (int) $request->query->get('month', date('m'));

        $stats = $this->visitRepository->getMonthlyPresenceStats($year, $month);
        return new JsonResponse($stats, Response::HTTP_OK);
    }

    #[Route('/{id}/tourists', name: 'visit_add_tourist', methods: ['POST'])]
    public function addTourist(Visit $visit, Request $request): JsonResponse
    {
        $tourist = $this->serializer->deserialize($request->getContent(), Tourist::class, 'json');
        $tourist->setVisit($visit);
        
        $errors = $this->validator->validate($tourist);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($tourist);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Tourist added successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}/close', name: 'visit_close', methods: ['POST'])]
    public function closeVisit(Visit $visit, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $visit->setStatus('completed');
        $visit->setGeneralComment($data['comment'] ?? null);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Visit closed successfully'], Response::HTTP_OK);
    }

    #[Route('/month/{year}/{month}', name: 'app_visit_by_month', methods: ['GET'])]
    public function findByMonth(int $year, int $month): JsonResponse
    {
        $visits = $this->visitRepository->findVisitsByMonth($year, $month);
        return $this->json($visits, 200, [], ['groups' => 'visit:read']);
    }
} 