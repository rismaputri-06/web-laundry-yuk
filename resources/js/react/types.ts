export type OrderStatus =
  | 'Konfirmasi'
  | 'Menunggu Jemput'
  | 'Dalam Perjalanan'
  | 'Dijemput'
  | 'Pencucian'
  | 'Pengeringan'
  | 'Penyetrikaan'
  | 'Siap'
  | 'Pengiriman'
  | 'Selesai';

export interface OrderItem {
  id: string;
  name: string;
  type: string;
  quantity: string;
  treatment: string;
  priceUnit: string;
  subtotal: number;
  imageUrl: string;
}

export interface Order {
  id: string;
  serviceName: string;
  weight: number; // kg
  orderDate: string;
  orderTime: string;
  status: OrderStatus;
  statusLabelIndo: string;
  totalPrice: number;
  paymentMethod: 'QRIS' | 'Tunai';
  paymentStatus: 'BELUM BAYAR' | 'LUNAS';
  pickupAddress: string;
  deliveryAddress: string;
  pickupTimeLabel: string;
  readyTimeLabel: string;
  notes?: string;
  items: OrderItem[];
  platformFee: number;
  discount: number;
  kurirName?: string;
  kurirEtaMinutes?: number;
  kurirPhone?: string;
}

export interface UserProfile {
  name: string;
  memberId: string;
  email: string;
  phone: string;
  savedAddressLabel: string;
  savedAddressDetails: string;
  memberLevel: string;
  totalOrders: number;
  totalSpending: number;
  progressSpending: number;
  targetSpending: number;
}
