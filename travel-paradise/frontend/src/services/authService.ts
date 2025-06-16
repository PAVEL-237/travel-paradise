import axios from 'axios';
import { API_URL, API_ENDPOINTS } from '../config';

export interface User {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
  phone?: string;
  roles: string[];
  isActive: boolean;
  createdAt: string;
  lastLoginAt?: string;
}

export interface LoginResponse {
  token: string;
  user: User;
}

export interface RegisterData {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  phone?: string;
}

export interface ChangePasswordData {
  currentPassword: string;
  newPassword: string;
}

class AuthService {
  private token: string | null = null;

  constructor() {
    this.token = localStorage.getItem('token');
    if (this.token) {
      this.setAuthHeader(this.token);
    }
  }

  private setAuthHeader(token: string) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  }

  private clearAuthHeader() {
    delete axios.defaults.headers.common['Authorization'];
  }

  async login(email: string, password: string): Promise<LoginResponse> {
    const response = await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.LOGIN}`, {
      email,
      password
    });
    const { token, user } = response.data;
    this.token = token;
    localStorage.setItem('token', token);
    this.setAuthHeader(token);
    return { token, user };
  }

  async register(data: RegisterData): Promise<User> {
    const response = await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.REGISTER}`, data);
    return response.data.user;
  }

  async refreshToken(): Promise<string> {
    const response = await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.REFRESH_TOKEN}`);
    const { token } = response.data;
    this.token = token;
    localStorage.setItem('token', token);
    this.setAuthHeader(token);
    return token;
  }

  async changePassword(data: ChangePasswordData): Promise<void> {
    await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.CHANGE_PASSWORD}`, data);
  }

  async resetPassword(email: string): Promise<void> {
    await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.RESET_PASSWORD}`, { email });
  }

  async activateAccount(email: string): Promise<void> {
    await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.ACTIVATE_ACCOUNT}`, { email });
  }

  async deactivateAccount(email: string): Promise<void> {
    await axios.post(`${API_URL}${API_ENDPOINTS.AUTH.DEACTIVATE_ACCOUNT}`, { email });
  }

  logout(): void {
    this.token = null;
    localStorage.removeItem('token');
    this.clearAuthHeader();
  }

  isAuthenticated(): boolean {
    return !!this.token;
  }

  getToken(): string | null {
    return this.token;
  }
}

export const authService = new AuthService(); 