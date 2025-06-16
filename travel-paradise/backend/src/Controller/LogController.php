<?php

namespace App\Controller;

use App\Service\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/logs')]
#[IsGranted('ROLE_ADMIN')]
class LogController extends AbstractController
{
    public function __construct(
        private LogService $logService
    ) {}

    #[Route('', name: 'app_logs_list', methods: ['GET'])]
    public function getLogs(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 50);
        $filters = $request->query->all();
        
        // Remove pagination parameters from filters
        unset($filters['page'], $filters['limit']);

        $result = $this->logService->getLogs($filters, $page, $limit);
        return $this->json($result);
    }

    #[Route('/stats', name: 'app_logs_stats', methods: ['GET'])]
    public function getLogStats(): JsonResponse
    {
        return $this->json($this->logService->getLogStats());
    }

    #[Route('/export', name: 'app_logs_export', methods: ['GET'])]
    public function exportLogs(Request $request): JsonResponse
    {
        $filters = $request->query->all();
        $exportData = $this->logService->exportLogs($filters);
        
        return $this->json([
            'data' => $exportData,
            'filename' => 'logs_export_' . date('Y-m-d') . '.json'
        ]);
    }

    #[Route('/cleanup', name: 'app_logs_cleanup', methods: ['POST'])]
    public function cleanupLogs(Request $request): JsonResponse
    {
        $days = $request->request->getInt('days', 90);
        $deletedCount = $this->logService->clearOldLogs($days);
        
        return $this->json([
            'message' => "Successfully deleted $deletedCount old logs",
            'deleted_count' => $deletedCount
        ]);
    }
} 