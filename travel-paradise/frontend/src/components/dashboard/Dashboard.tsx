import React, { useEffect, useState } from 'react';
import {
  Box,
  Grid,
  Card,
  CardContent,
  Typography,
  CircularProgress,
} from '@mui/material';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
} from 'recharts';
import { useVisitStore } from '../../stores/visitStore';

export const Dashboard: React.FC = () => {
  const [monthlyStats, setMonthlyStats] = useState<any>(null);
  const [guideStats, setGuideStats] = useState<any[]>([]);
  const [presenceStats, setPresenceStats] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const { getMonthlyStats, getGuideStats, getPresenceStats } = useVisitStore();

  useEffect(() => {
    const loadStats = async () => {
      try {
        const [monthly, guides, presence] = await Promise.all([
          getMonthlyStats(),
          getGuideStats(),
          getPresenceStats(),
        ]);
        setMonthlyStats(monthly);
        setGuideStats(guides);
        setPresenceStats(presence);
      } catch (error) {
        console.error('Error loading stats:', error);
      } finally {
        setLoading(false);
      }
    };
    loadStats();
  }, [getMonthlyStats, getGuideStats, getPresenceStats]);

  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Box sx={{ flexGrow: 1, p: 3 }}>
      <Grid container spacing={3}>
        {/* Monthly Visits Card */}
        <Grid item xs={12} md={4}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Monthly Visits
              </Typography>
              <Typography variant="h4">
                {monthlyStats?.totalVisits || 0}
              </Typography>
            </CardContent>
          </Card>
        </Grid>

        {/* Presence Rate Card */}
        <Grid item xs={12} md={4}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Presence Rate
              </Typography>
              <Typography variant="h4">
                {presenceStats
                  ? `${Math.round((presenceStats.presentCount / presenceStats.totalVisits) * 100)}%`
                  : '0%'}
              </Typography>
            </CardContent>
          </Card>
        </Grid>

        {/* Active Guides Card */}
        <Grid item xs={12} md={4}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Active Guides
              </Typography>
              <Typography variant="h4">
                {guideStats.length}
              </Typography>
            </CardContent>
          </Card>
        </Grid>

        {/* Guide Performance Chart */}
        <Grid item xs={12}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Guide Performance
              </Typography>
              <Box sx={{ height: 400 }}>
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={guideStats}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="name" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Bar dataKey="visits" fill="#8884d8" name="Number of Visits" />
                  </BarChart>
                </ResponsiveContainer>
              </Box>
            </CardContent>
          </Card>
        </Grid>

        {/* Monthly Trends Chart */}
        <Grid item xs={12}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Monthly Trends
              </Typography>
              <Box sx={{ height: 400 }}>
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={monthlyStats?.monthlyData || []}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="month" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Bar dataKey="visits" fill="#82ca9d" name="Number of Visits" />
                    <Bar dataKey="presence" fill="#8884d8" name="Presence Rate" />
                  </BarChart>
                </ResponsiveContainer>
              </Box>
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </Box>
  );
}; 