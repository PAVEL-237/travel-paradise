import React, { useEffect, useState } from 'react';
import {
  Box,
  Card,
  CardContent,
  Grid,
  Typography,
  Checkbox,
  IconButton,
  Tooltip,
  TextField,
  Button,
} from '@mui/material';
import {
  Edit as EditIcon,
  Delete as DeleteIcon,
  Save as SaveIcon,
} from '@mui/icons-material';
import { Visitor } from '../../types/Visitor';
import { useVisitorStore } from '../../stores/visitorStore';

interface VisitorListProps {
  visitId: number;
  onClose: () => void;
}

export const VisitorList: React.FC<VisitorListProps> = ({ visitId, onClose }) => {
  const [visitors, setVisitors] = useState<Visitor[]>([]);
  const [editedVisitors, setEditedVisitors] = useState<Visitor[]>([]);
  const { fetchVisitors, updateVisitor, deleteVisitor } = useVisitorStore();

  useEffect(() => {
    const loadVisitors = async () => {
      const data = await fetchVisitors(visitId);
      setVisitors(data);
      setEditedVisitors(data);
    };
    loadVisitors();
  }, [visitId, fetchVisitors]);

  const handlePresenceChange = (visitorId: number, isPresent: boolean) => {
    setEditedVisitors(prev =>
      prev.map(visitor =>
        visitor.id === visitorId ? { ...visitor, isPresent } : visitor
      )
    );
  };

  const handleCommentChange = (visitorId: number, comment: string) => {
    setEditedVisitors(prev =>
      prev.map(visitor =>
        visitor.id === visitorId ? { ...visitor, comments: comment } : visitor
      )
    );
  };

  const handleSave = async (visitorId: number) => {
    const visitor = editedVisitors.find(v => v.id === visitorId);
    if (visitor) {
      await updateVisitor(visitorId, visitor);
    }
  };

  const handleDelete = async (visitorId: number) => {
    await deleteVisitor(visitorId);
    setVisitors(prev => prev.filter(v => v.id !== visitorId));
    setEditedVisitors(prev => prev.filter(v => v.id !== visitorId));
  };

  return (
    <Box sx={{ flexGrow: 1, p: 3 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
        <Typography variant="h5">Visitors List</Typography>
        <Button variant="contained" onClick={onClose}>
          Close
        </Button>
      </Box>
      <Grid container spacing={3}>
        {visitors.map((visitor) => (
          <Grid item xs={12} sm={6} md={4} key={visitor.id}>
            <Card>
              <CardContent>
                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                  <Typography variant="h6">
                    {visitor.firstName} {visitor.lastName}
                  </Typography>
                  <Box>
                    <Tooltip title="Save">
                      <IconButton
                        onClick={() => handleSave(visitor.id)}
                        size="small"
                        color="primary"
                      >
                        <SaveIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="Delete">
                      <IconButton
                        onClick={() => handleDelete(visitor.id)}
                        size="small"
                        color="error"
                      >
                        <DeleteIcon />
                      </IconButton>
                    </Tooltip>
                  </Box>
                </Box>

                <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                  <Checkbox
                    checked={editedVisitors.find(v => v.id === visitor.id)?.isPresent || false}
                    onChange={(e) => handlePresenceChange(visitor.id, e.target.checked)}
                  />
                  <Typography>Present</Typography>
                </Box>

                <TextField
                  fullWidth
                  multiline
                  rows={2}
                  label="Comments"
                  value={editedVisitors.find(v => v.id === visitor.id)?.comments || ''}
                  onChange={(e) => handleCommentChange(visitor.id, e.target.value)}
                />
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>
    </Box>
  );
}; 