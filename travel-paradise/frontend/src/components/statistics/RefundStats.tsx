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

interface RefundStatsProps {
  totalAmount: number;
  byReason: Array<{
    reason: string;
    count: number;
  }>;
  approvalRate: number;
  processingTime: number;
}

const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8'];

const RefundStats: React.FC<RefundStatsProps> = ({
  totalAmount,
  byReason,
  approvalRate,
  processingTime
}) => {
  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(value);
  };

  return (
    <Paper sx={{ p: 3, height: '100%' }}>
      <Typography variant="h6" gutterBottom>
        Refund Statistics
      </Typography>

      <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
        {/* Total Refunded Amount */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Total Refunded Amount
          </Typography>
          <Typography variant="h4" color="error">
            {formatCurrency(totalAmount)}
          </Typography>
        </Box>

        {/* Refund Reasons */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Refund Reasons
          </Typography>
          <Box sx={{ height: 200 }}>
            <ResponsiveContainer>
              <PieChart>
                <Pie
                  data={byReason}
                  dataKey="count"
                  nameKey="reason"
                  cx="50%"
                  cy="50%"
                  outerRadius={80}
                  label
                >
                  {byReason.map((entry, index) => (
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

        {/* Approval Rate */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Approval Rate
          </Typography>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Typography variant="h4">
                {(approvalRate * 100).toFixed(1)}%
              </Typography>
              <Typography variant="body2" color="text.secondary">
                of refunds approved
              </Typography>
            </Box>
            <LinearProgress
              variant="determinate"
              value={approvalRate * 100}
              sx={{ height: 8, borderRadius: 4 }}
            />
          </Box>
        </Box>

        {/* Average Processing Time */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Average Processing Time
          </Typography>
          <Typography variant="h4">
            {processingTime.toFixed(1)} days
          </Typography>
        </Box>

        {/* Reason Details */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Reason Details
          </Typography>
          <TableContainer>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Reason</TableCell>
                  <TableCell align="right">Count</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {byReason.map((reason) => (
                  <TableRow key={reason.reason}>
                    <TableCell>{reason.reason}</TableCell>
                    <TableCell align="right">{reason.count}</TableCell>
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

export default RefundStats; 