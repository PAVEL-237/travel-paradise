import { create } from 'zustand';
import axios from 'axios';
import { API_URL, API_ENDPOINTS } from '../config';

interface User {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
  roles: string[];
}

interface RegisterData {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  phone?: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  isLoading: boolean;
  error: string | null;
  isAuthenticated: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (userData: RegisterData) => Promise<void>;
  logout: () => void;
  clearError: () => void;
  checkAuth: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: localStorage.getItem('token'),
  isLoading: false,
  error: null,
  isAuthenticated: !!localStorage.getItem('token'),

  login: async (email: string, password: string) => {
    set({ isLoading: true, error: null });
    try {
      const response = await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.LOGIN}`, {
        email,
        password,
      });
      const { token, user } = response.data;
      localStorage.setItem('token', token);
      set({ user, token, isLoading: false, isAuthenticated: true });
    } catch (error: any) {
      set({
        error: error.response?.data?.message || 'An error occurred during login',
        isLoading: false,
        isAuthenticated: false,
      });
    }
  },

  register: async (userData: RegisterData) => {
    set({ isLoading: true, error: null });
    try {
      const response = await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.REGISTER}`, userData);
      const { token, user } = response.data;
      localStorage.setItem('token', token);
      set({ user, token, isLoading: false, isAuthenticated: true });
    } catch (error: any) {
      set({
        error: error.response?.data?.message || 'An error occurred during registration',
        isLoading: false,
        isAuthenticated: false,
      });
    }
  },

  logout: () => {
    localStorage.removeItem('token');
    set({ user: null, token: null, isAuthenticated: false });
  },

  clearError: () => set({ error: null }),

  checkAuth: () => {
    const token = localStorage.getItem('token');
    if (!token) {
      set({ user: null, token: null, isAuthenticated: false });
    }
  },
})); 