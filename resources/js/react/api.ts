import axios from 'axios';
import { Order, OrderStatus, UserProfile } from './types';

// Axios instance wired to the real Laravel backend (same origin, session-based auth).
export const api = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// Attach the CSRF token Laravel renders into <meta name="csrf-token">.
const csrfMeta = document.querySelector('meta[name="csrf-token"]');
if (csrfMeta) {
  api.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content') || '';
}

// ---- Types coming back from Laravel ----

export interface ApiPickupDelivery {
  address: string;
  status: string;
  pickupDate: string | null;
  pickupTime: string | null;
}

export interface ApiOrder {
  id: string; // e.g. "ORD-0007"
  rawId: number;
  serviceType: 'Cuci Lipat' | 'Cuci Setrika' | 'Setrika Saja';
  weight: number;
  totalPrice: number;
  status: 'Menunggu' | 'Diproses' | 'Dicuci' | 'Dikeringkan' | 'Disetrika' | 'Selesai' | 'Diantar';
  isExpress: boolean;
  pickupMethod: 'Pickup' | 'Datang Langsung';
  notes: string | null;
  orderDate: string;
  createdAt: string;
  estimatedDelivery: string | null;
  pickup: ApiPickupDelivery | null;
}

export interface ApiUser {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  address: string | null;
  role: string;
  totalOrders?: number;
  totalSpending?: number;
}

// ---- Backend calls ----

export async function fetchOrders(): Promise<ApiOrder[]> {
  const { data } = await api.get('/api/orders');
  return data.orders;
}

export interface CreateOrderPayload {
  serviceType: 'Cuci Lipat' | 'Cuci Setrika' | 'Setrika Saja';
  weight: number;
  isPickupDelivery: boolean;
  isExpress: boolean;
  pickupDate?: string;
  pickupTime?: string;
  pickupAddress?: string;
  instructions?: string;
}

export async function createOrder(payload: CreateOrderPayload) {
  const { data } = await api.post('/api/orders', payload);
  return data as { message: string; order_id: number };
}

export async function fetchProfile(): Promise<ApiUser> {
  const { data } = await api.get('/api/profile');
  return data.user;
}

export async function updateProfile(payload: { name: string; email: string; phone: string; address: string }) {
  const { data } = await api.put('/api/profile', payload);
  return data.user as ApiUser;
}

export async function updatePassword(payload: {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}) {
  const { data } = await api.put('/api/profile/password', payload);
  return data;
}

export async function logout() {
  await api.post('/logout');
  window.location.href = '/login';
}

// ---- Mapping helpers: backend shape -> the UI's Order / UserProfile shape ----

// Neutral placeholder thumbnail (a simple shirt icon) since real order items
// in the DB don't have photos of individual clothing pieces.
const PLACEHOLDER_ITEM_IMAGE =
  'data:image/svg+xml;utf8,' +
  encodeURIComponent(
    `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48">
      <rect width="48" height="48" fill="#dae2ff"/>
      <path d="M18 8l6 4 6-4 6 6-4 4v22H16V18l-4-4z" fill="#003d9b"/>
    </svg>`,
  );

const PRICE_PER_KG: Record<ApiOrder['serviceType'], number> = {
  'Cuci Setrika': 10000,
  'Setrika Saja': 6000,
  'Cuci Lipat': 7000,
};

// The prototype UI has 10 fine-grained progress steps, the real backend has a
// simpler 7-step status column. We map real status onto the closest UI step
// so the existing progress bar / timeline UI keeps working with real data.
const STATUS_MAP: Record<ApiOrder['status'], OrderStatus> = {
  Menunggu: 'Konfirmasi',
  Diproses: 'Pencucian',
  Dicuci: 'Pengeringan',
  Dikeringkan: 'Penyetrikaan',
  Disetrika: 'Siap',
  Diantar: 'Pengiriman',
  Selesai: 'Selesai',
};

export function mapApiOrderToOrder(o: ApiOrder): Order {
  const pricePerKg = PRICE_PER_KG[o.serviceType];
  const servicePrice = Math.round(o.weight * pricePerKg);
  const isPickup = o.pickupMethod === 'Pickup';
  const pickupFee = isPickup ? 5000 : 0;
  const deliveryFee = isPickup ? 5000 : 0;
  const handlingFee = o.isExpress ? 15000 : 0;

  const address = o.pickup?.address || '-';
  const createdDate = new Date(o.createdAt);

  return {
    id: `#${o.id}`,
    serviceName: `${o.serviceType}${o.isExpress ? ' (Ekspres)' : ' (Reguler)'}`,
    weight: o.weight,
    orderDate: o.estimatedDelivery || o.orderDate,
    orderTime: isNaN(createdDate.getTime())
      ? ''
      : createdDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
    status: STATUS_MAP[o.status] ?? 'Konfirmasi',
    statusLabelIndo: o.status,
    totalPrice: o.totalPrice,
    // Payment isn't tracked in the DB schema yet, default to a sensible value.
    paymentMethod: 'QRIS',
    paymentStatus: o.status === 'Selesai' ? 'LUNAS' : 'BELUM BAYAR',
    pickupAddress: address,
    deliveryAddress: address,
    pickupTimeLabel: o.pickup?.pickupDate
      ? `${o.pickup.pickupDate} • ${o.pickup.pickupTime ?? ''}`
      : 'Datang Langsung',
    readyTimeLabel: o.isExpress ? 'Ekspres • 24 Jam Selesai' : 'Reguler • Estimasi Selesai',
    notes: o.notes ?? undefined,
    platformFee: 0,
    discount: 0,
    items: [
      {
        id: `${o.rawId}-item-1`,
        name: `Pakaian Laundry (${o.serviceType})`,
        type: 'Campur',
        quantity: `${o.weight} KG`,
        treatment: o.isExpress ? 'CUCI KILAT EXPRESS' : 'CUCI STANDAR',
        priceUnit: `Rp ${pricePerKg.toLocaleString('id-ID')}/kg`,
        subtotal: servicePrice,
        imageUrl: PLACEHOLDER_ITEM_IMAGE,
      },
    ],
  };
}

export function mapApiUserToProfile(u: ApiUser): UserProfile {
  return {
    name: u.name,
    memberId: `#CL-${String(u.id).padStart(5, '0')}`,
    email: u.email,
    phone: u.phone || '-',
    savedAddressLabel: 'Alamat Utama',
    savedAddressDetails: u.address || '-',
    memberLevel: 'MEMBER',
    totalOrders: u.totalOrders ?? 0,
    totalSpending: u.totalSpending ?? 0,
    progressSpending: u.totalSpending ?? 0,
    targetSpending: 1000000,
  };
}
