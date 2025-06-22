import { useDispatch, useSelector } from 'react-redux'
import { useNavigate } from 'react-router-dom'
import { RootState } from '../stores/store'
import { loginStart, loginSuccess, loginFailure, logout } from '../stores/slices/authSlice'
import api, { API_ROUTES } from '../config/api'
import { toast } from 'react-toastify'
import axios from 'axios'

export const useAuth = () => {
  const dispatch = useDispatch()
  const navigate = useNavigate()
  const { user, isAuthenticated, loading, error } = useSelector(
    (state: RootState) => state.auth
  )

  const login = async (email: string, password: string) => {
    try {
      console.log('Début de la tentative de connexion')
      dispatch(loginStart())

      const response = await api.post(API_ROUTES.LOGIN, { email, password })
      console.log('Réponse du serveur:', response.data)

      if (!response.data.token) {
        throw new Error('Token non reçu du serveur')
      }

      dispatch(loginSuccess(response.data))
      navigate('/')
      toast.success('Connexion réussie')
    } catch (error) {
      console.error('Erreur détaillée:', error)
      let errorMessage = 'Erreur de connexion'

      if (axios.isAxiosError(error)) {
        console.log('Erreur Axios:', {
          status: error.response?.status,
          data: error.response?.data,
          message: error.message
        })

        if (error.response?.status === 401) {
          errorMessage = 'Email ou mot de passe incorrect'
        } else if (error.response?.data?.message) {
          errorMessage = error.response.data.message
        } else if (error.response?.data?.error) {
          errorMessage = error.response.data.error
        }
      }

      dispatch(loginFailure(errorMessage))
      toast.error(errorMessage)
      throw new Error(errorMessage)
    }
  }

  const logoutUser = async () => {
    try {
      await api.post(API_ROUTES.LOGOUT)
      localStorage.removeItem('token')
      dispatch(logout())
      navigate('/login')
      toast.success('Déconnexion réussie')
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error)
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