export interface Visitor {
  id: number;
  firstName: string;
  lastName: string;
  isPresent: boolean;
  comments?: string;
  visit: number;
} 