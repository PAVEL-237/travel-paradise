import React, { useEffect, useState } from 'react';
import {
  Box,
  Paper,
  Typography,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TablePagination,
  TextField,
  Button,
  Grid,
  MenuItem,
  CircularProgress,
  Alert
} from '@mui/material';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { format } from 'date-fns';
import { fetchLogs, fetchLogStats, exportLogs, cleanupLogs } from '../../services/logService';

interface Log {
  id: number;
  action: string;
  entity: string;
  entityId: number | null;
  data: any;
  user: string | null;
  ipAddress: string | null;
  userAgent: string | null;
  createdAt: string;
}

interface LogStats {
  by_action: Array<{
    action: string;
    count: number;
  }>;
  by_entity: Array<{
    entity: string;
    count: number;
  }>;
  by_user: Array<{
    email: string;
    count: number;
  }>;
  by_date: Array<{
    date: string;
    count: number;
  }>;
}

const LogsPage: React.FC = () => {
  const [logs, setLogs] = useState<Log[]>([]);
  const [stats, setStats] = useState<LogStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [totalCount, setTotalCount] = useState(0);
  const [filters, setFilters] = useState({
    action: '',
    entity: '',
    entityId: '',
    user: '',
    startDate: null as Date | null,
    endDate: null as Date | null
  });

  useEffect(() => {
    loadLogs();
    loadStats();
  }, [page, rowsPerPage, filters]);

  const loadLogs = async () => {
    try {
      setLoading(true);
      const response = await fetchLogs(page + 1, rowsPerPage, filters);
      setLogs(response.logs);
      setTotalCount(response.pagination.total);
      setError(null);
    } catch (err) {
      setError('Failed to load logs');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadStats = async () => {
    try {
      const data = await fetchLogStats();
      setStats(data);
    } catch (err) {
      console.error('Failed to load log statistics:', err);
    }
  };

  const handleChangePage = (event: unknown, newPage: number) => {
    setPage(newPage);
  };

  const handleChangeRowsPerPage = (event: React.ChangeEvent<HTMLInputElement>) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setPage(0);
  };

  const handleFilterChange = (field: string, value: any) => {
    setFilters(prev => ({
      ...prev,
      [field]: value
    }));
    setPage(0);
  };

  const handleExport = async () => {
    try {
      await exportLogs(filters);
    } catch (err) {
      setError('Failed to export logs');
      console.error(err);
    }
  };

  const handleCleanup = async () => {
    try {
      const result = await cleanupLogs();
      setError(null);
      loadLogs();
      loadStats();
    } catch (err) {
      setError('Failed to cleanup logs');
      console.error(err);
    }
  };

  const formatDate = (dateString: string) => {
    return format(new Date(dateString), 'yyyy-MM-dd HH:mm:ss');
  };

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" gutterBottom>
        System Logs
      </Typography>

      {error && (
        <Alert severity="error" sx={{ mb: 2 }}>
          {error}
        </Alert>
      )}

      {/* Filters */}
      <Paper sx={{ p: 2, mb: 3 }}>
        <Grid container spacing={2}>
          <Grid item xs={12} sm={6} md={2}>
            <TextField
              select
              fullWidth
              label="Action"
              value={filters.action}
              onChange={(e) => handleFilterChange('action', e.target.value)}
            >
              <MenuItem value="">All</MenuItem>
              {stats?.by_action.map((action) => (
                <MenuItem key={action.action} value={action.action}>
                  {action.action}
                </MenuItem>
              ))}
            </TextField>
          </Grid>
          <Grid item xs={12} sm={6} md={2}>
            <TextField
              select
              fullWidth
              label="Entity"
              value={filters.entity}
              onChange={(e) => handleFilterChange('entity', e.target.value)}
            >
              <MenuItem value="">All</MenuItem>
              {stats?.by_entity.map((entity) => (
                <MenuItem key={entity.entity} value={entity.entity}>
                  {entity.entity}
                </MenuItem>
              ))}
            </TextField>
          </Grid>
          <Grid item xs={12} sm={6} md={2}>
            <TextField
              fullWidth
              label="Entity ID"
              value={filters.entityId}
              onChange={(e) => handleFilterChange('entityId', e.target.value)}
            />
          </Grid>
          <Grid item xs={12} sm={6} md={2}>
            <TextField
              select
              fullWidth
              label="User"
              value={filters.user}
              onChange={(e) => handleFilterChange('user', e.target.value)}
            >
              <MenuItem value="">All</MenuItem>
              {stats?.by_user.map((user) => (
                <MenuItem key={user.email} value={user.email}>
                  {user.email}
                </MenuItem>
              ))}
            </TextField>
          </Grid>
          <Grid item xs={12} sm={6} md={2}>
            <LocalizationProvider dateAdapter={AdapterDateFns}>
              <DatePicker
                label="Start Date"
                value={filters.startDate}
                onChange={(date) => handleFilterChange('startDate', date)}
                slotProps={{ textField: { fullWidth: true } }}
              />
            </LocalizationProvider>
          </Grid>
          <Grid item xs={12} sm={6} md={2}>
            <LocalizationProvider dateAdapter={AdapterDateFns}>
              <DatePicker
                label="End Date"
                value={filters.endDate}
                onChange={(date) => handleFilterChange('endDate', date)}
                slotProps={{ textField: { fullWidth: true } }}
              />
            </LocalizationProvider>
          </Grid>
        </Grid>
      </Paper>

      {/* Actions */}
      <Box sx={{ mb: 2, display: 'flex', gap: 2 }}>
        <Button
          variant="contained"
          color="primary"
          onClick={handleExport}
          disabled={loading}
        >
          Export Logs
        </Button>
        <Button
          variant="outlined"
          color="error"
          onClick={handleCleanup}
          disabled={loading}
        >
          Cleanup Old Logs
        </Button>
      </Box>

      {/* Logs Table */}
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Action</TableCell>
              <TableCell>Entity</TableCell>
              <TableCell>Entity ID</TableCell>
              <TableCell>User</TableCell>
              <TableCell>IP Address</TableCell>
              <TableCell>Created At</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {loading ? (
              <TableRow>
                <TableCell colSpan={7} align="center">
                  <CircularProgress />
                </TableCell>
              </TableRow>
            ) : logs.length === 0 ? (
              <TableRow>
                <TableCell colSpan={7} align="center">
                  No logs found
                </TableCell>
              </TableRow>
            ) : (
              logs.map((log) => (
                <TableRow key={log.id}>
                  <TableCell>{log.id}</TableCell>
                  <TableCell>{log.action}</TableCell>
                  <TableCell>{log.entity}</TableCell>
                  <TableCell>{log.entityId}</TableCell>
                  <TableCell>{log.user}</TableCell>
                  <TableCell>{log.ipAddress}</TableCell>
                  <TableCell>{formatDate(log.createdAt)}</TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
        <TablePagination
          rowsPerPageOptions={[10, 25, 50]}
          component="div"
          count={totalCount}
          rowsPerPage={rowsPerPage}
          page={page}
          onPageChange={handleChangePage}
          onRowsPerPageChange={handleChangeRowsPerPage}
        />
      </TableContainer>
    </Box>
  );
};

export default LogsPage; 