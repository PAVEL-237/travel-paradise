import { create } from 'zustand';
import { Guide } from '../types/Guide';
import { api } from '../services/api';

interface GuideStore {
  guides: Guide[];
  loading: boolean;
  error: string | null;
  fetchGuides: () => Promise<Guide[]>;
  createGuide: (guide: Partial<Guide>) => Promise<Guide>;
  updateGuide: (id: number, guide: Partial<Guide>) => Promise<Guide>;
  deleteGuide: (id: number) => Promise<void>;
}

export const useGuideStore = create<GuideStore>((set, get) => ({
  guides: [],
  loading: false,
  error: null,

  fetchGuides: async () => {
    set({ loading: true, error: null });
    try {
      const response = await api.get('/guides');
      set({ guides: response.data, loading: false });
      return response.data;
    } catch (error) {
      set({ error: 'Failed to fetch guides', loading: false });
      throw error;
    }
  },

  createGuide: async (guide) => {
    set({ loading: true, error: null });
    try {
      const response = await api.post('/guides', guide);
      set((state) => ({
        guides: [...state.guides, response.data],
        loading: false,
      }));
      return response.data;
    } catch (error) {
      set({ error: 'Failed to create guide', loading: false });
      throw error;
    }
  },

  updateGuide: async (id, guide) => {
    set({ loading: true, error: null });
    try {
      const response = await api.put(`/guides/${id}`, guide);
      set((state) => ({
        guides: state.guides.map((g) => (g.id === id ? response.data : g)),
        loading: false,
      }));
      return response.data;
    } catch (error) {
      set({ error: 'Failed to update guide', loading: false });
      throw error;
    }
  },

  deleteGuide: async (id) => {
    set({ loading: true, error: null });
    try {
      await api.delete(`/guides/${id}`);
      set((state) => ({
        guides: state.guides.filter((g) => g.id !== id),
        loading: false,
      }));
    } catch (error) {
      set({ error: 'Failed to delete guide', loading: false });
      throw error;
    }
  },
})); 