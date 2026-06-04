export interface Product {
  id?: number;
  brand: string;
  model: string;
  year: number;
  price: string;
  stock: number;
  description?: string;
  imageUrl?: string;
  createdAt?: string;
  updatedAt?: string;
}

export interface CartItem {
  product: Product;
  quantity: number;
}

export interface OrderItem {
  id?: number;
  product: Product;
  quantity: number;
  price: string;
}

export interface Order {
  id?: number;
  user?: any;
  items: OrderItem[];
  total: string;
  status: 'pending' | 'confirmed' | 'shipped' | 'completed' | 'cancelled';
  createdAt?: string;
  updatedAt?: string;
}

export interface CreateOrderDTO {
  items: {
    product: string; // IRI del producto
    quantity: number;
  }[];
}

export interface Faq {
  id?: number;
  question: string;
  answer: string;
  position: number;
  active: boolean;
  createdAt?: string;
  updatedAt?: string;
}

export interface Appointment {
  id?: number;
  customerName: string;
  customerEmail: string;
  customerPhone: string;
  appointmentDate: string;
  serviceAddress: string;
  vehicleMake: string;
  vehicleModel: string;
  vehicleYear?: string;
  serviceType: string;
  status: string;
  price?: string;
  notes?: string;
  user?: any;
  createdAt?: string;
  updatedAt?: string;
}
