<?php

namespace App\Controller;

use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/statistics')]
#[IsGranted('ROLE_ADMIN')]
class StatisticsController extends AbstractController
{
    public function __construct(
        private StatisticsService $statisticsService
    ) {}

    #[Route('/dashboard', name: 'app_statistics_dashboard', methods: ['GET'])]
    public function getDashboardStats(): JsonResponse
    {
        return $this->json($this->statisticsService->getDashboardStats());
    }

    #[Route('/report/{type}', name: 'app_statistics_report', methods: ['GET'])]
    public function generateReport(string $type, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        
        try {
            $report = $this->statisticsService->generateReport($type, $parameters);
            return $this->json($report);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/export/{type}', name: 'app_statistics_export', methods: ['GET'])]
    public function exportReport(string $type, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        
        try {
            $report = $this->statisticsService->generateReport($type, $parameters);
            
            // Format the data for export
            $exportData = $this->formatReportForExport($type, $report);
            
            return $this->json([
                'data' => $exportData,
                'filename' => "report_{$type}_" . date('Y-m-d') . '.json'
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    private function formatReportForExport(string $type, array $report): array
    {
        $formattedData = [
            'type' => $type,
            'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'data' => $report
        ];

        switch ($type) {
            case 'revenue':
                $formattedData['summary'] = [
                    'total_revenue' => $report['total_revenue'],
                    'period' => $report['period']
                ];
                break;
            case 'visits':
                $formattedData['summary'] = [
                    'total_visits' => $report['total_visits'],
                    'period' => $report['period']
                ];
                break;
            case 'places':
                $formattedData['summary'] = [
                    'total_places' => $report['total_places']
                ];
                break;
            case 'users':
                $formattedData['summary'] = [
                    'total_users' => $report['total_users'],
                    'period' => $report['period']
                ];
                break;
            case 'refunds':
                $formattedData['summary'] = [
                    'total_refunds' => $report['total_refunds'],
                    'period' => $report['period']
                ];
                break;
        }

        return $formattedData;
    }

    #[Route('/monthly/{year}/{month}', name: 'app_statistics_monthly', methods: ['GET'])]
    public function getMonthlyStatistics(int $year, int $month): JsonResponse
    {
        $stats = $this->statisticsService->getMonthlyStatistics($year, $month);
        return $this->json($stats);
    }

    #[Route('/guide/{guideId}/{year}/{month}', name: 'app_statistics_guide', methods: ['GET'])]
    public function getGuideStatistics(int $guideId, int $year, int $month): JsonResponse
    {
        $stats = $this->statisticsService->getGuideStatistics($guideId, $year, $month);
        return $this->json($stats);
    }
} 