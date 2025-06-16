import { create } from 'zustand';
import { Visit } from '../types/Visit';
import { api } from '../services/api';

interface VisitStore {
  visits: Visit[];
  loading: boolean;
  error: string | null;
  fetchVisits: () => Promise<Visit[]>;
  createVisit: (visit: Partial<Visit>) => Promise<Visit>;
  updateVisit: (id: number, visit: Partial<Visit>) => Promise<Visit>;
  deleteVisit: (id: number) => Promise<void>;
  getMonthlyStats: () => Promise<any>;
  getGuideStats: () => Promise<any>;
  getPresenceStats: () => Promise<any>;
}

export const useVisitStore = create<VisitStore>((set, get) => ({
  visits: [],
  loading: false,
  error: null,

  fetchVisits: async () => {
    set({ loading: true, error: null });
    try {
      const response = await api.get('/visits');
      set({ visits: response.data, loading: false });
      return response.data;
    } catch (error) {
      set({ error: 'Failed to fetch visits', loading: false });
      throw error;
    }
  },

  createVisit: async (visit) => {
    set({ loading: true, error: null });
    try {
      const response = await api.post('/visits', visit);
      set((state) => ({
        visits: [...state.visits, response.data],
        loading: false,
      }));
      return response.data;
    } catch (error) {
      set({ error: 'Failed to create visit', loading: false });
      throw error;
    }
  },

  updateVisit: async (id, visit) => {
    set({ loading: true, error: null });
    try {
      const response = await api.put(`/visits/${id}`, visit);
      set((state) => ({
        visits: state.visits.map((v) => (v.id === id ? response.data : v)),
        loading: false,
      }));
      return response.data;
    } catch (error) {
      set({ error: 'Failed to update visit', loading: false });
      throw error;
    }
  },

  deleteVisit: async (id) => {
    set({ loading: true, error: null });
    try {
      await api.delete(`/visits/${id}`);
      set((state) => ({
        visits: state.visits.filter((v) => v.id !== id),
        loading: false,
      }));
    } catch (error) {
      set({ error: 'Failed to delete visit', loading: false });
      throw error;
    }
  },

  getMonthlyStats: async () => {
    set({ loading: true, error: null });
    try {
      const response = await api.get('/visits/stats/monthly');
      set({ loading: false });
      return response.data;
    } catch (error) {
      set({ error: 'Failed to fetch monthly stats', loading: false });
      throw error;
    }
  },

  getGuideStats: async () => {
    set({ loading: true, error: null });
    try {
      const response = await api.get('/visits/stats/guide');
      set({ loading: false });
      return response.data;
    } catch (error) {
      set({ error: 'Failed to fetch guide stats', loading: false });
      throw error;
    }
  },

  getPresenceStats: async () => {
    set({ loading: true, error: null });
    try {
      const response = await api.get('/visits/stats/presence');
      set({ loading: false });
      return response.data;
    } catch (error) {
      set({ error: 'Failed to fetch presence stats', loading: false });
      throw error;
    }
  },
})); 