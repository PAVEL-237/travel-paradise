import api, { API_ROUTES } from '../config/api'
import axios from 'axios'

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
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  }

  private clearAuthHeader() {
    delete api.defaults.headers.common['Authorization'];
  }

  async login(email: string, password: string): Promise<LoginResponse> {
    try {
      console.log('Tentative de connexion avec:', { email })
      
      const response = await api.post(API_ROUTES.LOGIN, {
        email,
        password
      }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })

      console.log('Réponse du serveur:', response.data)

      if (response.data.token) {
        localStorage.setItem('token', response.data.token)
        this.token = response.data.token
        this.setAuthHeader(response.data.token)
        return response.data
      } else {
        throw new Error('Token non reçu du serveur')
      }
    } catch (error: any) {
      console.error('Erreur de connexion:', error)
      
      if (error.response) {
        // Le serveur a répondu avec un code d'état d'erreur
        if (error.response.status === 401) {
          throw new Error('Email ou mot de passe incorrect')
        } else if (error.response.status === 403) {
          throw new Error('Accès refusé')
        } else {
          throw new Error(error.response.data.message || 'Erreur de connexion')
        }
      } else if (error.request) {
        // La requête a été faite mais aucune réponse n'a été reçue
        console.error('Pas de réponse du serveur:', error.request)
        throw new Error('Le serveur ne répond pas. Veuillez réessayer plus tard.')
      } else {
        // Une erreur s'est produite lors de la configuration de la requête
        console.error('Erreur de configuration:', error.message)
        throw new Error('Erreur de configuration de la requête')
      }
    }
  }

  async register(data: RegisterData): Promise<User> {
    try {
      const response = await api.post(API_ROUTES.REGISTER, data, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        withCredentials: true
      });
      return response.data.user;
    } catch (error) {
      if (api.isAxiosError(error)) {
        if (error.response?.data?.message) {
          throw new Error(error.response.data.message);
        } else if (error.response?.data?.error) {
          throw new Error(error.response.data.error);
        }
      }
      throw new Error('Erreur lors de l\'inscription');
    }
  }

  async refreshToken(): Promise<string> {
    try {
      const response = await api.post(API_ROUTES.REFRESH_TOKEN, {}, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        withCredentials: true
      });
      const { token } = response.data;
      this.token = token;
      localStorage.setItem('token', token);
      this.setAuthHeader(token);
      return token;
    } catch (error) {
      this.logout();
      throw new Error('Session expirée, veuillez vous reconnecter');
    }
  }

  async changePassword(data: ChangePasswordData): Promise<void> {
    try {
      await api.post(API_ROUTES.CHANGE_PASSWORD, data, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        withCredentials: true
      });
    } catch (error) {
      if (api.isAxiosError(error)) {
        if (error.response?.data?.message) {
          throw new Error(error.response.data.message);
        } else if (error.response?.data?.error) {
          throw new Error(error.response.data.error);
        }
      }
      throw new Error('Erreur lors du changement de mot de passe');
    }
  }

  async resetPassword(email: string): Promise<void> {
    try {
      await api.post(API_ROUTES.RESET_PASSWORD, { email }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        withCredentials: true
      });
    } catch (error) {
      if (api.isAxiosError(error)) {
        if (error.response?.data?.message) {
          throw new Error(error.response.data.message);
        } else if (error.response?.data?.error) {
          throw new Error(error.response.data.error);
        }
      }
      throw new Error('Erreur lors de la réinitialisation du mot de passe');
    }
  }

  async activateAccount(email: string): Promise<void> {
    try {
      await api.post(API_ROUTES.ACTIVATE_ACCOUNT, { email }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        withCredentials: true
      });
    } catch (error) {
      if (api.isAxiosError(error)) {
        if (error.response?.data?.message) {
          throw new Error(error.response.data.message);
        } else if (error.response?.data?.error) {
          throw new Error(error.response.data.error);
        }
      }
      throw new Error('Erreur lors de l\'activation du compte');
    }
  }

  async deactivateAccount(email: string): Promise<void> {
    try {
      await api.post(API_ROUTES.DEACTIVATE_ACCOUNT, { email }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        withCredentials: true
      });
    } catch (error) {
      if (api.isAxiosError(error)) {
        if (error.response?.data?.message) {
          throw new Error(error.response.data.message);
        } else if (error.response?.data?.error) {
          throw new Error(error.response.data.error);
        }
      }
      throw new Error('Erreur lors de la désactivation du compte');
    }
  }

  async logout(): Promise<void> {
    try {
      localStorage.removeItem('token')
      this.token = null
      this.clearAuthHeader()
      return api.post(API_ROUTES.LOGOUT)
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error)
      throw error
    }
  }

  isAuthenticated(): boolean {
    return !!localStorage.getItem('token')
  }

  getToken(): string | null {
    return this.token;
  }

  getCurrentUser() {
    const token = localStorage.getItem('token')
    if (!token) return null

    try {
      // Décodage du token JWT
      const base64Url = token.split('.')[1]
      const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/')
      const jsonPayload = decodeURIComponent(
        atob(base64)
          .split('')
          .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
          .join('')
      )

      return JSON.parse(jsonPayload)
    } catch (error) {
      console.error('Erreur lors du décodage du token:', error)
      return null
    }
  }
}

export default new AuthService(); 