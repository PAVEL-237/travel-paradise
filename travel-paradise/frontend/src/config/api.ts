import axios from 'axios'

// Configuration de base d'Axios
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
})

// Intercepteur pour ajouter le token d'authentification
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Intercepteur pour gérer les erreurs
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Routes API
export const API_ROUTES = {
  // Auth
  LOGIN: '/auth/login',
  LOGOUT: '/auth/logout',
  REFRESH_TOKEN: '/auth/refresh-token',

  // Guides
  GUIDES: '/guides',
  GUIDE_DETAILS: (id: number) => `/guides/${id}`,
  GUIDE_VISITS: (id: number) => `/guides/${id}/visits`,

  // Visits
  VISITS: '/visits',
  VISIT_DETAILS: (id: number) => `/visits/${id}`,
  VISIT_STATUS: (id: number) => `/visits/${id}/status`,
  VISIT_COMMENT: (id: number) => `/visits/${id}/comment`,
  VISIT_VISITORS: (id: number) => `/visits/${id}/visitors`,
  VISITOR_PRESENCE: (visitId: number, visitorId: number) =>
    `/visits/${visitId}/visitors/${visitorId}/presence`,
  VISITOR_COMMENTS: (visitId: number, visitorId: number) =>
    `/visits/${visitId}/visitors/${visitorId}/comments`,

  // Users
  USERS: '/users',
  USER_DETAILS: (id: number) => `/users/${id}`,

  // Statistics
  STATISTICS: '/statistics',
  VISITS_PER_MONTH: '/statistics/visits-per-month',
  VISITS_PER_GUIDE: '/statistics/visits-per-guide',
  ATTENDANCE_RATE: '/statistics/attendance-rate',

  // Guide specific
  MY_VISITS: '/guides/my-visits',
}

export default api 