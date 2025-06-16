import React, { useEffect, useState } from 'react';
import {
  Box,
  Card,
  CardContent,
  Grid,
  Typography,
  Chip,
  IconButton,
  Tooltip,
  Button,
} from '@mui/material';
import {
  Edit as EditIcon,
  Delete as DeleteIcon,
  People as PeopleIcon,
  CheckCircle as CheckCircleIcon,
} from '@mui/icons-material';
import { Visit } from '../../types/Visit';
import { useVisitStore } from '../../stores/visitStore';
import { format } from 'date-fns';

interface VisitListProps {
  onEdit: (visit: Visit) => void;
  onDelete: (visitId: number) => void;
  onManageVisitors: (visitId: number) => void;
  onCloseVisit: (visitId: number) => void;
}

export const VisitList: React.FC<VisitListProps> = ({
  onEdit,
  onDelete,
  onManageVisitors,
  onCloseVisit,
}) => {
  const [visits, setVisits] = useState<Visit[]>([]);
  const { fetchVisits } = useVisitStore();

  useEffect(() => {
    const loadVisits = async () => {
      const data = await fetchVisits();
      setVisits(data);
    };
    loadVisits();
  }, [fetchVisits]);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'scheduled':
        return 'primary';
      case 'in_progress':
        return 'warning';
      case 'completed':
        return 'success';
      case 'cancelled':
        return 'error';
      default:
        return 'default';
    }
  };

  return (
    <Box sx={{ flexGrow: 1, p: 3 }}>
      <Grid container spacing={3}>
        {visits.map((visit) => (
          <Grid item xs={12} sm={6} md={4} key={visit.id}>
            <Card>
              <CardContent>
                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                  <Typography variant="h6" component="div">
                    {visit.location}
                  </Typography>
                  <Box>
                    <Tooltip title="Edit">
                      <IconButton onClick={() => onEdit(visit)} size="small">
                        <EditIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="Delete">
                      <IconButton onClick={() => onDelete(visit.id)} size="small" color="error">
                        <DeleteIcon />
                      </IconButton>
                    </Tooltip>
                  </Box>
                </Box>

                <Typography variant="body2" color="text.secondary" gutterBottom>
                  Date: {format(new Date(visit.date), 'PPP')}
                </Typography>
                <Typography variant="body2" color="text.secondary" gutterBottom>
                  Time: {format(new Date(visit.startTime), 'p')} - {format(new Date(visit.endTime), 'p')}
                </Typography>
                <Typography variant="body2" color="text.secondary" gutterBottom>
                  Guide: {visit.guide.firstName} {visit.guide.lastName}
                </Typography>
                <Typography variant="body2" color="text.secondary" gutterBottom>
                  Country: {visit.country}
                </Typography>

                <Box sx={{ mt: 2, display: 'flex', gap: 1, flexWrap: 'wrap' }}>
                  <Chip
                    label={visit.status}
                    color={getStatusColor(visit.status)}
                    size="small"
                  />
                  <Button
                    startIcon={<PeopleIcon />}
                    size="small"
                    onClick={() => onManageVisitors(visit.id)}
                  >
                    Manage Visitors
                  </Button>
                  {visit.status === 'in_progress' && (
                    <Button
                      startIcon={<CheckCircleIcon />}
                      size="small"
                      color="success"
                      onClick={() => onCloseVisit(visit.id)}
                    >
                      Close Visit
                    </Button>
                  )}
                </Box>
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>
    </Box>
  );
}; 