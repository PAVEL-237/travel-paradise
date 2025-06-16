<?php

namespace App\Service;

use App\Repository\VisitRepository;
use App\Repository\VisitorRepository;

class StatisticsService
{
    public function __construct(
        private VisitRepository $visitRepository,
        private VisitorRepository $visitorRepository
    ) {}

    public function getMonthlyStatistics(int $year, int $month): array
    {
        $visits = $this->visitRepository->findVisitsByMonth($year, $month);
        $stats = $this->visitRepository->getMonthlyStats($year, $month);

        $totalVisitors = 0;
        $presentVisitors = 0;

        foreach ($visits as $visit) {
            $visitStats = $this->visitorRepository->getPresenceStats($visit->getId());
            $totalVisitors += $visitStats['total_visitors'];
            $presentVisitors += $visitStats['present_visitors'];
        }

        return [
            'total_visits' => $stats['total_visits'],
            'total_guides' => $stats['total_guides'],
            'total_visitors' => $totalVisitors,
            'present_visitors' => $presentVisitors,
            'presence_rate' => $totalVisitors > 0 ? ($presentVisitors / $totalVisitors) * 100 : 0,
        ];
    }

    public function getGuideStatistics(int $guideId, int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month');
        
        $visits = $this->visitRepository->findByGuideAndDateRange($guideId, $startDate, $endDate);
        
        $totalVisitors = 0;
        $presentVisitors = 0;

        foreach ($visits as $visit) {
            $visitStats = $this->visitorRepository->getPresenceStats($visit->getId());
            $totalVisitors += $visitStats['total_visitors'];
            $presentVisitors += $visitStats['present_visitors'];
        }

        return [
            'total_visits' => count($visits),
            'total_visitors' => $totalVisitors,
            'present_visitors' => $presentVisitors,
            'presence_rate' => $totalVisitors > 0 ? ($presentVisitors / $totalVisitors) * 100 : 0,
        ];
    }
} 