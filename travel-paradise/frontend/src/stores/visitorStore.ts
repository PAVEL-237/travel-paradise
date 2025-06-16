import { create } from 'zustand';
import { Visitor } from '../types/Visitor';
import { api } from '../services/api';

interface VisitorStore {
  visitors: Visitor[];
  loading: boolean;
  error: string | null;
  fetchVisitors: (visitId: number) => Promise<Visitor[]>;
  createVisitor: (visitor: Partial<Visitor>) => Promise<Visitor>;
  updateVisitor: (id: number, visitor: Partial<Visitor>) => Promise<Visitor>;
  deleteVisitor: (id: number) => Promise<void>;
}

export const useVisitorStore = create<VisitorStore>((set, get) => ({
  visitors: [],
  loading: false,
  error: null,

  fetchVisitors: async (visitId) => {
    set({ loading: true, error: null });
    try {
      const response = await api.get(`/visitors/visit/${visitId}`);
      set({ visitors: response.data, loading: false });
      return response.data;
    } catch (error) {
      set({ error: 'Failed to fetch visitors', loading: false });
      throw error;
    }
  },

  createVisitor: async (visitor) => {
    set({ loading: true, error: null });
    try {
      const response = await api.post('/visitors', visitor);
      set((state) => ({
        visitors: [...state.visitors, response.data],
        loading: false,
      }));
      return response.data;
    } catch (error) {
      set({ error: 'Failed to create visitor', loading: false });
      throw error;
    }
  },

  updateVisitor: async (id, visitor) => {
    set({ loading: true, error: null });
    try {
      const response = await api.put(`/visitors/${id}`, visitor);
      set((state) => ({
        visitors: state.visitors.map((v) => (v.id === id ? response.data : v)),
        loading: false,
      }));
      return response.data;
    } catch (error) {
      set({ error: 'Failed to update visitor', loading: false });
      throw error;
    }
  },

  deleteVisitor: async (id) => {
    set({ loading: true, error: null });
    try {
      await api.delete(`/visitors/${id}`);
      set((state) => ({
        visitors: state.visitors.filter((v) => v.id !== id),
        loading: false,
      }));
    } catch (error) {
      set({ error: 'Failed to delete visitor', loading: false });
      throw error;
    }
  },
})); 