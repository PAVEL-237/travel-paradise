import React from 'react';
import { Link } from 'react-router-dom';
import { Button, Container, Typography, Box, Grid } from '@mui/material';
import { useAuthStore } from '../stores/authStore';

const Home: React.FC = () => {
  const { isAuthenticated } = useAuthStore();

  return (
    <Box
      sx={{
        minHeight: 'calc(100vh - 64px)',
        background: 'linear-gradient(45deg, #2196F3 30%, #21CBF3 90%)',
        color: 'white',
      }}
    >
      <Container maxWidth="lg">
        <Grid container spacing={4} sx={{ py: 8 }}>
          <Grid item xs={12} md={6}>
            <Box sx={{ mt: 8 }}>
              <Typography variant="h2" component="h1" gutterBottom>
                Bienvenue sur Travel Paradise
              </Typography>
              <Typography variant="h5" component="h2" gutterBottom sx={{ mb: 4 }}>
                Découvrez des destinations extraordinaires et vivez des expériences inoubliables
              </Typography>
              {!isAuthenticated && (
                <Button
                  component={Link}
                  to="/register"
                  variant="contained"
                  size="large"
                  sx={{
                    backgroundColor: 'white',
                    color: '#2196F3',
                    '&:hover': {
                      backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    },
                  }}
                >
                  Commencer l'aventure
                </Button>
              )}
            </Box>
          </Grid>
          <Grid item xs={12} md={6}>
            <Box
              component="img"
              src="/images/travel-hero.jpg"
              alt="Travel Paradise"
              sx={{
                width: '100%',
                height: 'auto',
                borderRadius: 2,
                boxShadow: 3,
              }}
            />
          </Grid>
        </Grid>

        {/* Section des fonctionnalités */}
        <Grid container spacing={4} sx={{ py: 8 }}>
          <Grid item xs={12} md={4}>
            <Box sx={{ textAlign: 'center', p: 2 }}>
              <Typography variant="h4" gutterBottom>
                Destinations
              </Typography>
              <Typography>
                Explorez nos destinations soigneusement sélectionnées à travers le monde
              </Typography>
            </Box>
          </Grid>
          <Grid item xs={12} md={4}>
            <Box sx={{ textAlign: 'center', p: 2 }}>
              <Typography variant="h4" gutterBottom>
                Guides Experts
              </Typography>
              <Typography>
                Profitez de l'expertise de nos guides professionnels
              </Typography>
            </Box>
          </Grid>
          <Grid item xs={12} md={4}>
            <Box sx={{ textAlign: 'center', p: 2 }}>
              <Typography variant="h4" gutterBottom>
                Expériences Uniques
              </Typography>
              <Typography>
                Créez des souvenirs inoubliables avec nos visites personnalisées
              </Typography>
            </Box>
          </Grid>
        </Grid>
      </Container>
    </Box>
  );
};

export default Home; 