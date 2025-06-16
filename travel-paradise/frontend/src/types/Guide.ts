export interface Guide {
  id: number;
  firstName: string;
  lastName: string;
  photo?: string;
  status: 'active' | 'inactive' | 'on_leave';
  country: string;
  user: number;
  visits?: Visit[];
} 