import React, { useEffect, useState } from 'react';
import { Box, Grid, Paper, Typography, CircularProgress } from '@mui/material';
import { useTheme } from '@mui/material/styles';
import OverviewCard from './OverviewCard';
import RevenueChart from './RevenueChart';
import VisitStats from './VisitStats';
import PlaceStats from './PlaceStats';
import UserStats from './UserStats';
import RefundStats from './RefundStats';
import { fetchDashboardStats } from '../../services/statisticsService';

interface DashboardStats {
  overview: {
    total_revenue: number;
    monthly_revenue: number;
    today_visits: number;
    monthly_visits: number;
    active_users: number;
    total_places: number;
  };
  revenue: {
    monthly_trend: Array<{
      month: string;
      revenue: number;
      visits: number;
    }>;
    average_ticket: number;
    refund_rate: number;
  };
  visits: {
    by_status: Array<{
      status: string;
      count: number;
    }>;
    by_place: Array<{
      place: string;
      count: number;
    }>;
    by_guide: Array<{
      guide: string;
      count: number;
    }>;
    cancellation_rate: number;
  };
  places: {
    by_category: Array<{
      category: string;
      count: number;
    }>;
    most_visited: Array<{
      name: string;
      visits: number;
    }>;
    best_rated: Array<{
      name: string;
      rating: number;
    }>;
    average_rating: number;
  };
  users: {
    by_role: Array<{
      role: string;
      count: number;
    }>;
    new_users: number;
    active_users: number;
    user_satisfaction: number;
  };
  refunds: {
    total_amount: number;
    by_reason: Array<{
      reason: string;
      count: number;
    }>;
    approval_rate: number;
    average_processing_time: number;
  };
}

const Dashboard: React.FC = () => {
  const theme = useTheme();
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const loadStats = async () => {
      try {
        const data = await fetchDashboardStats();
        setStats(data);
        setError(null);
      } catch (err) {
        setError('Failed to load dashboard statistics');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    loadStats();
  }, []);

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="80vh">
        <CircularProgress />
      </Box>
    );
  }

  if (error) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="80vh">
        <Typography color="error">{error}</Typography>
      </Box>
    );
  }

  if (!stats) {
    return null;
  }

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" gutterBottom>
        Dashboard
      </Typography>

      {/* Overview Cards */}
      <Grid container spacing={3} sx={{ mb: 4 }}>
        <Grid item xs={12} sm={6} md={4}>
          <OverviewCard
            title="Total Revenue"
            value={stats.overview.total_revenue}
            prefix="$"
            trend={stats.overview.monthly_revenue}
            trendLabel="This Month"
          />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <OverviewCard
            title="Total Visits"
            value={stats.overview.monthly_visits}
            trend={stats.overview.today_visits}
            trendLabel="Today"
          />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <OverviewCard
            title="Active Users"
            value={stats.overview.active_users}
            trend={stats.users.new_users}
            trendLabel="New This Month"
          />
        </Grid>
      </Grid>

      {/* Revenue Chart */}
      <Paper sx={{ p: 3, mb: 4 }}>
        <Typography variant="h6" gutterBottom>
          Revenue Overview
        </Typography>
        <RevenueChart data={stats.revenue.monthly_trend} />
      </Paper>

      {/* Visit Statistics */}
      <Grid container spacing={3} sx={{ mb: 4 }}>
        <Grid item xs={12} md={6}>
          <VisitStats
            byStatus={stats.visits.by_status}
            byPlace={stats.visits.by_place}
            byGuide={stats.visits.by_guide}
            cancellationRate={stats.visits.cancellation_rate}
          />
        </Grid>
        <Grid item xs={12} md={6}>
          <PlaceStats
            byCategory={stats.places.by_category}
            mostVisited={stats.places.most_visited}
            bestRated={stats.places.best_rated}
            averageRating={stats.places.average_rating}
          />
        </Grid>
      </Grid>

      {/* User and Refund Statistics */}
      <Grid container spacing={3}>
        <Grid item xs={12} md={6}>
          <UserStats
            byRole={stats.users.by_role}
            newUsers={stats.users.new_users}
            activeUsers={stats.users.active_users}
            satisfaction={stats.users.user_satisfaction}
          />
        </Grid>
        <Grid item xs={12} md={6}>
          <RefundStats
            totalAmount={stats.refunds.total_amount}
            byReason={stats.refunds.by_reason}
            approvalRate={stats.refunds.approval_rate}
            processingTime={stats.refunds.average_processing_time}
          />
        </Grid>
      </Grid>
    </Box>
  );
};

export default Dashboard; 