import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../../stores/authStore';
import { Button, TextField, Box, Typography, Alert } from '@mui/material';

export const ChangePasswordForm: React.FC = () => {
  const navigate = useNavigate();
  const { changePassword, isLoading, error, clearError } = useAuthStore();
  const [formData, setFormData] = useState({
    currentPassword: '',
    newPassword: '',
    confirmNewPassword: ''
  });
  const [success, setSuccess] = useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    clearError();

    if (formData.newPassword !== formData.confirmNewPassword) {
      clearError();
      return;
    }

    const { confirmNewPassword, ...changePasswordData } = formData;
    await changePassword(changePasswordData);
    if (!error) {
      setSuccess(true);
      setTimeout(() => {
        navigate('/dashboard');
      }, 2000);
    }
  };

  return (
    <Box component="form" onSubmit={handleSubmit} sx={{ mt: 1 }}>
      {error && (
        <Alert severity="error" sx={{ mb: 2 }}>
          {error}
        </Alert>
      )}
      {success && (
        <Alert severity="success" sx={{ mb: 2 }}>
          Password changed successfully. Redirecting to dashboard...
        </Alert>
      )}
      <TextField
        margin="normal"
        required
        fullWidth
        name="currentPassword"
        label="Current Password"
        type="password"
        id="currentPassword"
        value={formData.currentPassword}
        onChange={handleChange}
      />
      <TextField
        margin="normal"
        required
        fullWidth
        name="newPassword"
        label="New Password"
        type="password"
        id="newPassword"
        value={formData.newPassword}
        onChange={handleChange}
      />
      <TextField
        margin="normal"
        required
        fullWidth
        name="confirmNewPassword"
        label="Confirm New Password"
        type="password"
        id="confirmNewPassword"
        value={formData.confirmNewPassword}
        onChange={handleChange}
      />
      <Button
        type="submit"
        fullWidth
        variant="contained"
        sx={{ mt: 3, mb: 2 }}
        disabled={isLoading}
      >
        {isLoading ? 'Changing Password...' : 'Change Password'}
      </Button>
      <Box sx={{ textAlign: 'center' }}>
        <Button
          color="primary"
          onClick={() => navigate('/dashboard')}
          sx={{ textTransform: 'none' }}
        >
          Cancel
        </Button>
      </Box>
    </Box>
  );
}; 