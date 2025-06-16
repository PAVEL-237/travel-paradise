import React, { useEffect } from 'react';
import {
  Box,
  Button,
  Dialog,
  DialogActions,
  DialogContent,
  DialogTitle,
  TextField,
  MenuItem,
  FormControl,
  InputLabel,
  Select,
} from '@mui/material';
import { useForm, Controller } from 'react-hook-form';
import { Guide } from '../../types/Guide';

interface GuideFormProps {
  open: boolean;
  onClose: () => void;
  onSubmit: (data: Partial<Guide>) => void;
  guide?: Guide;
}

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'on_leave', label: 'On Leave' },
];

export const GuideForm: React.FC<GuideFormProps> = ({
  open,
  onClose,
  onSubmit,
  guide,
}) => {
  const { control, handleSubmit, reset } = useForm<Partial<Guide>>({
    defaultValues: {
      firstName: '',
      lastName: '',
      photo: '',
      status: 'active',
      country: '',
    },
  });

  useEffect(() => {
    if (guide) {
      reset(guide);
    } else {
      reset({
        firstName: '',
        lastName: '',
        photo: '',
        status: 'active',
        country: '',
      });
    }
  }, [guide, reset]);

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle>{guide ? 'Edit Guide' : 'Add New Guide'}</DialogTitle>
      <form onSubmit={handleSubmit(onSubmit)}>
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
              name="photo"
              control={control}
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Photo URL"
                  fullWidth
                />
              )}
            />

            <Controller
              name="country"
              control={control}
              rules={{ required: 'Country is required' }}
              render={({ field, fieldState: { error } }) => (
                <TextField
                  {...field}
                  label="Country"
                  error={!!error}
                  helperText={error?.message}
                  fullWidth
                />
              )}
            />

            <FormControl fullWidth>
              <InputLabel>Status</InputLabel>
              <Controller
                name="status"
                control={control}
                rules={{ required: 'Status is required' }}
                render={({ field, fieldState: { error } }) => (
                  <Select
                    {...field}
                    label="Status"
                    error={!!error}
                  >
                    {statusOptions.map((option) => (
                      <MenuItem key={option.value} value={option.value}>
                        {option.label}
                      </MenuItem>
                    ))}
                  </Select>
                )}
              />
            </FormControl>
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>Cancel</Button>
          <Button type="submit" variant="contained" color="primary">
            {guide ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  );
}; 