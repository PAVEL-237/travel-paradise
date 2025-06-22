import axios from 'axios'

// Configuration de base d'Axios
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true
})

// Intercepteur pour ajouter le token d'authentification
api.interceptors.request.use(
  (config) => {
    console.log('Request config:', {
      url: config.url,
      method: config.method,
      headers: config.headers,
      data: config.data
    })

    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    console.error('Request error:', error)
    return Promise.reject(error)
  }
)

// Intercepteur pour gérer les erreurs
api.interceptors.response.use(
  (response) => {
    console.log('Response:', {
      status: response.status,
      data: response.data,
      headers: response.headers
    })
    return response
  },
  (error) => {
    console.error('Response error:', {
      status: error.response?.status,
      data: error.response?.data,
      headers: error.response?.headers,
      message: error.message
    })

    if (error.response) {
      // Le serveur a répondu avec un code d'état d'erreur
      switch (error.response.status) {
        case 401:
          // Ne pas rediriger automatiquement pour la page de login
          if (!window.location.pathname.includes('/login')) {
            localStorage.removeItem('token')
            window.location.href = '/login'
          }
          break
        case 403:
          console.error('Accès refusé')
          break
        case 404:
          console.error('Ressource non trouvée')
          break
        case 500:
          console.error('Erreur serveur')
          break
      }
    } else if (error.request) {
      // La requête a été faite mais aucune réponse n'a été reçue
      console.error('Pas de réponse du serveur')
    } else {
      // Une erreur s'est produite lors de la configuration de la requête
      console.error('Erreur de configuration de la requête')
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