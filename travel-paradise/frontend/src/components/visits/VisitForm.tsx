import React, { useEffect, useState } from 'react';
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
import { Visit } from '../../types/Visit';
import { Guide } from '../../types/Guide';
import { useGuideStore } from '../../stores/guideStore';
import { DateTimePicker } from '@mui/x-date-pickers/DateTimePicker';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';

interface VisitFormProps {
  open: boolean;
  onClose: () => void;
  onSubmit: (data: Partial<Visit>) => void;
  visit?: Visit;
}

const statusOptions = [
  { value: 'scheduled', label: 'Scheduled' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' },
];

export const VisitForm: React.FC<VisitFormProps> = ({
  open,
  onClose,
  onSubmit,
  visit,
}) => {
  const [guides, setGuides] = useState<Guide[]>([]);
  const { fetchGuides } = useGuideStore();
  const { control, handleSubmit, reset } = useForm<Partial<Visit>>({
    defaultValues: {
      location: '',
      country: '',
      date: new Date(),
      startTime: new Date(),
      duration: 60,
      status: 'scheduled',
      guide: undefined,
    },
  });

  useEffect(() => {
    const loadGuides = async () => {
      const data = await fetchGuides();
      setGuides(data);
    };
    loadGuides();
  }, [fetchGuides]);

  useEffect(() => {
    if (visit) {
      reset({
        ...visit,
        date: new Date(visit.date),
        startTime: new Date(visit.startTime),
      });
    } else {
      reset({
        location: '',
        country: '',
        date: new Date(),
        startTime: new Date(),
        duration: 60,
        status: 'scheduled',
        guide: undefined,
      });
    }
  }, [visit, reset]);

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle>{visit ? 'Edit Visit' : 'Add New Visit'}</DialogTitle>
      <form onSubmit={handleSubmit(onSubmit)}>
        <DialogContent>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Controller
              name="location"
              control={control}
              rules={{ required: 'Location is required' }}
              render={({ field, fieldState: { error } }) => (
                <TextField
                  {...field}
                  label="Location"
                  error={!!error}
                  helperText={error?.message}
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

            <LocalizationProvider dateAdapter={AdapterDateFns}>
              <Controller
                name="date"
                control={control}
                rules={{ required: 'Date is required' }}
                render={({ field, fieldState: { error } }) => (
                  <DateTimePicker
                    label="Date"
                    value={field.value}
                    onChange={field.onChange}
                    slotProps={{
                      textField: {
                        fullWidth: true,
                        error: !!error,
                        helperText: error?.message,
                      },
                    }}
                  />
                )}
              />

              <Controller
                name="startTime"
                control={control}
                rules={{ required: 'Start time is required' }}
                render={({ field, fieldState: { error } }) => (
                  <DateTimePicker
                    label="Start Time"
                    value={field.value}
                    onChange={field.onChange}
                    slotProps={{
                      textField: {
                        fullWidth: true,
                        error: !!error,
                        helperText: error?.message,
                      },
                    }}
                  />
                )}
              />
            </LocalizationProvider>

            <Controller
              name="duration"
              control={control}
              rules={{ required: 'Duration is required' }}
              render={({ field, fieldState: { error } }) => (
                <TextField
                  {...field}
                  type="number"
                  label="Duration (minutes)"
                  error={!!error}
                  helperText={error?.message}
                  fullWidth
                />
              )}
            />

            <FormControl fullWidth>
              <InputLabel>Guide</InputLabel>
              <Controller
                name="guide"
                control={control}
                rules={{ required: 'Guide is required' }}
                render={({ field, fieldState: { error } }) => (
                  <Select
                    {...field}
                    label="Guide"
                    error={!!error}
                  >
                    {guides.map((guide) => (
                      <MenuItem key={guide.id} value={guide.id}>
                        {guide.firstName} {guide.lastName}
                      </MenuItem>
                    ))}
                  </Select>
                )}
              />
            </FormControl>

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
            {visit ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  );
}; 