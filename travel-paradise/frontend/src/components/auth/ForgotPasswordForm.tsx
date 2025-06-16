import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../../stores/authStore';
import { Button, TextField, Box, Typography, Alert } from '@mui/material';

export const ForgotPasswordForm: React.FC = () => {
  const navigate = useNavigate();
  const { resetPassword, isLoading, error, clearError } = useAuthStore();
  const [email, setEmail] = useState('');
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    clearError();
    await resetPassword(email);
    if (!error) {
      setSuccess(true);
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
          Password reset instructions have been sent to your email.
        </Alert>
      )}
      <Typography variant="body1" sx={{ mb: 2 }}>
        Enter your email address and we'll send you instructions to reset your password.
      </Typography>
      <TextField
        margin="normal"
        required
        fullWidth
        id="email"
        label="Email Address"
        name="email"
        autoComplete="email"
        autoFocus
        value={email}
        onChange={(e) => setEmail(e.target.value)}
      />
      <Button
        type="submit"
        fullWidth
        variant="contained"
        sx={{ mt: 3, mb: 2 }}
        disabled={isLoading}
      >
        {isLoading ? 'Sending...' : 'Send Reset Instructions'}
      </Button>
      <Box sx={{ textAlign: 'center' }}>
        <Button
          color="primary"
          onClick={() => navigate('/login')}
          sx={{ textTransform: 'none' }}
        >
          Back to Sign In
        </Button>
      </Box>
    </Box>
  );
}; 