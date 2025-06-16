import React, { useEffect, useState } from 'react';
import {
  Box,
  Card,
  CardContent,
  CardMedia,
  Grid,
  Typography,
  Chip,
  IconButton,
  Tooltip,
} from '@mui/material';
import { Edit as EditIcon, Delete as DeleteIcon } from '@mui/icons-material';
import { Guide } from '../../types/Guide';
import { useGuideStore } from '../../stores/guideStore';

interface GuideListProps {
  onEdit: (guide: Guide) => void;
  onDelete: (guideId: number) => void;
}

export const GuideList: React.FC<GuideListProps> = ({ onEdit, onDelete }) => {
  const [guides, setGuides] = useState<Guide[]>([]);
  const { fetchGuides } = useGuideStore();

  useEffect(() => {
    const loadGuides = async () => {
      const data = await fetchGuides();
      setGuides(data);
    };
    loadGuides();
  }, [fetchGuides]);

  return (
    <Box sx={{ flexGrow: 1, p: 3 }}>
      <Grid container spacing={3}>
        {guides.map((guide) => (
          <Grid item xs={12} sm={6} md={4} key={guide.id}>
            <Card>
              <CardMedia
                component="img"
                height="200"
                image={guide.photo || '/default-guide.jpg'}
                alt={`${guide.firstName} ${guide.lastName}`}
              />
              <CardContent>
                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                  <Typography variant="h6" component="div">
                    {guide.firstName} {guide.lastName}
                  </Typography>
                  <Box>
                    <Tooltip title="Edit">
                      <IconButton onClick={() => onEdit(guide)} size="small">
                        <EditIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="Delete">
                      <IconButton onClick={() => onDelete(guide.id)} size="small" color="error">
                        <DeleteIcon />
                      </IconButton>
                    </Tooltip>
                  </Box>
                </Box>
                <Typography variant="body2" color="text.secondary" gutterBottom>
                  Country: {guide.country}
                </Typography>
                <Chip
                  label={guide.status}
                  color={guide.status === 'active' ? 'success' : 'default'}
                  size="small"
                />
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>
    </Box>
  );
}; 