import React from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'
import { useSelector } from 'react-redux'
import { RootState } from './stores/store'
import Layout from './components/layout/Layout'
import Login from './pages/Login'
import Guides from './pages/Guides'
import Visits from './pages/Visits'
import Statistics from './pages/Statistics'
import Users from './pages/Users'
import MyVisits from './pages/MyVisits'
import { RegisterPage } from './pages/auth/RegisterPage'
import Home from './pages/Home'
import About from './pages/About'
import Destinations from './pages/Destinations'

// Composant pour les routes protégées
const ProtectedRoute: React.FC<{ children: React.ReactNode; requiredRole?: string }> = ({
  children,
  requiredRole,
}) => {
  const { isAuthenticated, user } = useSelector((state: RootState) => state.auth)

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }

  if (requiredRole && user?.role !== requiredRole) {
    return <Navigate to="/" replace />
  }

  return <>{children}</>
}

const App: React.FC = () => {
  const { user } = useSelector((state: RootState) => state.auth)

  return (
    <Routes>
      {/* Routes publiques */}
      <Route path="/" element={<Layout />}>
        <Route index element={<Home />} />
        <Route path="about" element={<About />} />
        <Route path="destinations" element={<Destinations />} />
        <Route path="login" element={<Login />} />
        <Route path="register" element={<RegisterPage />} />

        {/* Routes protégées */}
        <Route
          path="guides"
          element={
            <ProtectedRoute requiredRole="ADMIN">
              <Guides />
            </ProtectedRoute>
          }
        />
        <Route
          path="visits"
          element={
            <ProtectedRoute requiredRole="ADMIN">
              <Visits />
            </ProtectedRoute>
          }
        />
        <Route
          path="statistics"
          element={
            <ProtectedRoute requiredRole="ADMIN">
              <Statistics />
            </ProtectedRoute>
          }
        />
        <Route
          path="users"
          element={
            <ProtectedRoute requiredRole="ADMIN">
              <Users />
            </ProtectedRoute>
          }
        />
        <Route
          path="my-visits"
          element={
            <ProtectedRoute requiredRole="GUIDE">
              <MyVisits />
            </ProtectedRoute>
          }
        />
      </Route>

      {/* Route 404 */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}

export default App 