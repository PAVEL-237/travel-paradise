import React from 'react';
import {
  Paper,
  Typography,
  Box,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  LinearProgress
} from '@mui/material';
import {
  PieChart,
  Pie,
  Cell,
  ResponsiveContainer,
  Tooltip
} from 'recharts';

interface UserStatsProps {
  byRole: Array<{
    role: string;
    count: number;
  }>;
  newUsers: number;
  activeUsers: number;
  satisfaction: number;
}

const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8'];

const UserStats: React.FC<UserStatsProps> = ({
  byRole,
  newUsers,
  activeUsers,
  satisfaction
}) => {
  return (
    <Paper sx={{ p: 3, height: '100%' }}>
      <Typography variant="h6" gutterBottom>
        User Statistics
      </Typography>

      <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
        {/* Role Distribution */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Role Distribution
          </Typography>
          <Box sx={{ height: 200 }}>
            <ResponsiveContainer>
              <PieChart>
                <Pie
                  data={byRole}
                  dataKey="count"
                  nameKey="role"
                  cx="50%"
                  cy="50%"
                  outerRadius={80}
                  label
                >
                  {byRole.map((entry, index) => (
                    <Cell
                      key={`cell-${index}`}
                      fill={COLORS[index % COLORS.length]}
                    />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </Box>
        </Box>

        {/* User Growth */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            User Growth
          </Typography>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Box>
              <Typography variant="body2" color="text.secondary">
                New Users This Month
              </Typography>
              <Typography variant="h4">{newUsers}</Typography>
            </Box>
            <Box>
              <Typography variant="body2" color="text.secondary">
                Active Users
              </Typography>
              <Typography variant="h4">{activeUsers}</Typography>
            </Box>
          </Box>
        </Box>

        {/* User Satisfaction */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            User Satisfaction
          </Typography>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Typography variant="h4">
                {(satisfaction * 100).toFixed(1)}%
              </Typography>
              <Typography variant="body2" color="text.secondary">
                satisfied users
              </Typography>
            </Box>
            <LinearProgress
              variant="determinate"
              value={satisfaction * 100}
              sx={{ height: 8, borderRadius: 4 }}
            />
          </Box>
        </Box>

        {/* Role Details */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Role Details
          </Typography>
          <TableContainer>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Role</TableCell>
                  <TableCell align="right">Count</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {byRole.map((role) => (
                  <TableRow key={role.role}>
                    <TableCell>{role.role}</TableCell>
                    <TableCell align="right">{role.count}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        </Box>
      </Box>
    </Paper>
  );
};

export default UserStats; 