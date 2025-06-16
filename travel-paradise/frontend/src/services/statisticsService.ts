import axios from 'axios';
import { API_BASE_URL } from '../config';

export const fetchDashboardStats = async () => {
  const response = await axios.get(`${API_BASE_URL}/statistics/dashboard`);
  return response.data;
};

export const generateReport = async (type: string, parameters: any = {}) => {
  const response = await axios.get(`${API_BASE_URL}/statistics/report/${type}`, {
    params: parameters
  });
  return response.data;
};

export const exportReport = async (type: string, parameters: any = {}) => {
  const response = await axios.get(`${API_BASE_URL}/statistics/export/${type}`, {
    params: parameters,
    responseType: 'blob'
  });
  
  // Create a download link
  const url = window.URL.createObjectURL(new Blob([response.data]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', `report_${type}_${new Date().toISOString().split('T')[0]}.json`);
  document.body.appendChild(link);
  link.click();
  link.remove();
  window.URL.revokeObjectURL(url);
}; 