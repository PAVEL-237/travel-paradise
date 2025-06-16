import { useDispatch, useSelector } from 'react-redux'
import { useNavigate } from 'react-router-dom'
import { RootState } from '../stores/store'
import { loginStart, loginSuccess, loginFailure, logout } from '../stores/slices/authSlice'
import api, { API_ROUTES } from '../config/api'
import { toast } from 'react-toastify'

export const useAuth = () => {
  const dispatch = useDispatch()
  const navigate = useNavigate()
  const { user, isAuthenticated, loading, error } = useSelector(
    (state: RootState) => state.auth
  )

  const login = async (email: string, password: string) => {
    try {
      dispatch(loginStart())
      const response = await api.post(API_ROUTES.LOGIN, { email, password })
      dispatch(loginSuccess(response.data))
      navigate('/')
      toast.success('Connexion réussie')
    } catch (error) {
      dispatch(loginFailure('Identifiants invalides'))
      toast.error('Erreur de connexion')
    }
  }

  const logoutUser = async () => {
    try {
      await api.post(API_ROUTES.LOGOUT)
      dispatch(logout())
      navigate('/login')
      toast.success('Déconnexion réussie')
    } catch (error) {
      toast.error('Erreur lors de la déconnexion')
    }
  }

  const checkAuth = () => {
    const token = localStorage.getItem('token')
    if (!token && isAuthenticated) {
      dispatch(logout())
      navigate('/login')
    }
  }

  return {
    user,
    isAuthenticated,
    loading,
    error,
    login,
    logout: logoutUser,
    checkAuth,
  }
} 