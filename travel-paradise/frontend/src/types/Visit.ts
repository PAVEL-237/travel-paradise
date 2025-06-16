export interface Visit {
  id: number;
  photo?: string;
  country: string;
  location: string;
  date: string;
  startTime: string;
  duration: number;
  endTime: string;
  status: 'scheduled' | 'in_progress' | 'completed' | 'cancelled';
  generalComment?: string;
  guide: Guide;
  visitors?: Visitor[];
} 