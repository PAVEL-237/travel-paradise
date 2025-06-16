import { API_ENDPOINTS } from '../config';
import { apiService } from './apiService';

export interface Place {
    id: number;
    name: string;
    country: string;
    photo?: string;
    description?: string;
    category: {
        id: number;
        name: string;
    };
    visits: Array<{
        id: number;
        date: string;
        startTime: string;
        duration: number;
    }>;
}

export interface CreatePlaceData {
    name: string;
    country: string;
    photo?: string;
    description?: string;
    category: number;
}

export interface UpdatePlaceData extends Partial<CreatePlaceData> {}

class PlaceService {
    async getAll(): Promise<Place[]> {
        return apiService.get<Place[]>(API_ENDPOINTS.PLACES.LIST);
    }

    async getById(id: number): Promise<Place> {
        return apiService.get<Place>(API_ENDPOINTS.PLACES.DETAIL(id));
    }

    async create(data: CreatePlaceData): Promise<Place> {
        return apiService.post<Place>(API_ENDPOINTS.PLACES.CREATE, data);
    }

    async update(id: number, data: UpdatePlaceData): Promise<Place> {
        return apiService.put<Place>(API_ENDPOINTS.PLACES.UPDATE(id), data);
    }

    async delete(id: number): Promise<void> {
        await apiService.delete(API_ENDPOINTS.PLACES.DELETE(id));
    }

    async getMostVisited(): Promise<Place[]> {
        return apiService.get<Place[]>(`${API_ENDPOINTS.PLACES.LIST}/most-visited`);
    }

    async getBestRated(): Promise<Place[]> {
        return apiService.get<Place[]>(`${API_ENDPOINTS.PLACES.LIST}/best-rated`);
    }

    async getByCategory(categoryId: number): Promise<Place[]> {
        return apiService.get<Place[]>(`${API_ENDPOINTS.PLACES.LIST}/category/${categoryId}`);
    }
}

export const placeService = new PlaceService(); 