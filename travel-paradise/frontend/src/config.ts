export const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export const ROUTES = {
  HOME: '/',
  LOGIN: '/login',
  REGISTER: '/register',
  DASHBOARD: '/dashboard',
  GUIDES: '/guides',
  VISITS: '/visits',
  STATISTICS: '/statistics',
  PROFILE: '/profile',
};

export const USER_ROLES = {
  ADMIN: 'ROLE_ADMIN',
  USER: 'ROLE_USER',
  GUIDE: 'ROLE_GUIDE',
};

export const API_ENDPOINTS = {
    AUTH: {
        LOGIN: '/auth/login',
        REGISTER: '/auth/register',
        REFRESH_TOKEN: '/auth/refresh-token',
        CHANGE_PASSWORD: '/auth/change-password',
        RESET_PASSWORD: '/auth/reset-password',
        ACTIVATE_ACCOUNT: '/auth/activate-account',
        DEACTIVATE_ACCOUNT: '/auth/deactivate-account'
    },
    PLACES: {
        LIST: '/places',
        DETAIL: (id: number) => `/places/${id}`,
        CREATE: '/places',
        UPDATE: (id: number) => `/places/${id}`,
        DELETE: (id: number) => `/places/${id}`
    },
    VISITS: {
        LIST: '/visits',
        DETAIL: (id: number) => `/visits/${id}`,
        CREATE: '/visits',
        UPDATE: (id: number) => `/visits/${id}`,
        DELETE: (id: number) => `/visits/${id}`,
        TOURISTS: (id: number) => `/visits/${id}/tourists`,
        RATINGS: (id: number) => `/visits/${id}/ratings`
    },
    GUIDES: {
        LIST: '/guides',
        DETAIL: (id: number) => `/guides/${id}`,
        CREATE: '/guides',
        UPDATE: (id: number) => `/guides/${id}`,
        DELETE: (id: number) => `/guides/${id}`,
        VISITS: (id: number) => `/guides/${id}/visits`
    },
    STATISTICS: {
        VISITS_BY_MONTH: '/statistics/visits-by-month',
        VISITS_BY_GUIDE: '/statistics/visits-by-guide',
        ATTENDANCE_RATE: '/statistics/attendance-rate'
    }
}; 