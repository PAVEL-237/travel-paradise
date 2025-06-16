import React from 'react';
import {
  Box,
  Button,
  Dialog,
  DialogActions,
  DialogContent,
  DialogTitle,
  TextField,
} from '@mui/material';
import { useForm, Controller } from 'react-hook-form';
import { Visitor } from '../../types/Visitor';

interface VisitorFormProps {
  open: boolean;
  onClose: () => void;
  onSubmit: (data: Partial<Visitor>) => void;
  visitId: number;
}

export const VisitorForm: React.FC<VisitorFormProps> = ({
  open,
  onClose,
  onSubmit,
  visitId,
}) => {
  const { control, handleSubmit, reset } = useForm<Partial<Visitor>>({
    defaultValues: {
      firstName: '',
      lastName: '',
      isPresent: false,
      comments: '',
    },
  });

  const handleFormSubmit = (data: Partial<Visitor>) => {
    onSubmit({ ...data, visit: visitId });
    reset();
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle>Add New Visitor</DialogTitle>
      <form onSubmit={handleSubmit(handleFormSubmit)}>
        <DialogContent>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Controller
              name="firstName"
              control={control}
              rules={{ required: 'First name is required' }}
              render={({ field, fieldState: { error } }) => (
                <TextField
                  {...field}
                  label="First Name"
                  error={!!error}
                  helperText={error?.message}
                  fullWidth
                />
              )}
            />

            <Controller
              name="lastName"
              control={control}
              rules={{ required: 'Last name is required' }}
              render={({ field, fieldState: { error } }) => (
                <TextField
                  {...field}
                  label="Last Name"
                  error={!!error}
                  helperText={error?.message}
                  fullWidth
                />
              )}
            />

            <Controller
              name="comments"
              control={control}
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Comments"
                  multiline
                  rows={3}
                  fullWidth
                />
              )}
            />
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>Cancel</Button>
          <Button type="submit" variant="contained" color="primary">
            Add Visitor
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  );
}; 