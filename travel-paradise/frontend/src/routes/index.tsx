import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useSelector } from 'react-redux';
import { RootState } from '../stores/store';
import Layout from '../components/Layout/Layout';
import Login from '../pages/Login';
import Guides from '../pages/Guides';
import Visits from '../pages/Visits';
import Statistics from '../pages/Statistics';
import Users from '../pages/Users';
import MyVisits from '../pages/MyVisits';
import { ProtectedRoute } from '../components/auth/ProtectedRoute';
import { LoginPage } from '../pages/auth/LoginPage';
import { RegisterPage } from '../pages/auth/RegisterPage';
import { ForgotPasswordPage } from '../pages/auth/ForgotPasswordPage';
import { ChangePasswordPage } from '../pages/auth/ChangePasswordPage';
import { HomePage } from '../pages/HomePage';
import { DestinationsPage } from '../pages/DestinationsPage';
import { HotelsPage } from '../pages/HotelsPage';
import { FlightsPage } from '../pages/FlightsPage';
import { AboutPage } from '../pages/AboutPage';
import { ContactPage } from '../pages/ContactPage';
import { ProfilePage } from '../pages/ProfilePage';
import { DashboardPage } from '../pages/DashboardPage';
import { SettingsPage } from '../pages/SettingsPage';
import Dashboard from '../components/statistics/Dashboard';
import LogsPage from '../components/logs/LogsPage';
import PlacesPage from '../components/places/PlacesPage';
import PlaceDetails from '../components/places/PlaceDetails';
import VisitsPage from '../components/visits/VisitsPage';
import VisitDetails from '../components/visits/VisitDetails';
import GuidesPage from '../components/guides/GuidesPage';
import GuideDetails from '../components/guides/GuideDetails';
import RefundsPage from '../components/refunds/RefundsPage';
import RefundDetails from '../components/refunds/RefundDetails';
import UserDetails from '../components/users/UserDetails';
import MySchedulePage from '../components/my-schedule/MySchedulePage';

const PrivateRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { isAuthenticated } = useSelector((state: RootState) => state.auth);
  return isAuthenticated ? <>{children}</> : <Navigate to="/login" />;
};

const AppRoutes: React.FC = () => {
  const { user } = useSelector((state: RootState) => state.auth);

  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      
      <Route
        path="/"
        element={
          <PrivateRoute>
            <Layout />
          </PrivateRoute>
        }
      >
        {user?.role === 'ADMIN' ? (
          <>
            <Route path="guides" element={<Guides />} />
            <Route path="visits" element={<Visits />} />
            <Route path="statistics" element={<Statistics />} />
            <Route path="users" element={<Users />} />
          </>
        ) : (
          <Route path="my-visits" element={<MyVisits />} />
        )}
        
        <Route path="/" element={<Navigate to={user?.role === 'ADMIN' ? '/guides' : '/my-visits'} />} />
      </Route>
    </Routes>
  );
};

export default AppRoutes; 