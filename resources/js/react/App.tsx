import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  LayoutDashboard,
  ShoppingCart,
  History,
  User,
  LogOut,
  Search,
  Bell,
  Settings,
  Plus,
  TrendingUp,
  CheckCircle2,
  QrCode,
  MapPin,
  Truck,
  ChevronRight,
  Mail,
  Phone,
  Lock,
  Map as MapIcon,
  Download,
  MessageSquare,
  Tag,
  Sparkles,
  Shirt,
  Footprints,
  Layers,
  Check,
  X,
  Calendar,
  Clock,
  ArrowRight,
  AlertCircle,
  Award,
  PlusCircle,
  Menu,
} from 'lucide-react';

import { Order, OrderStatus, OrderItem, UserProfile } from './types';
import { SERVICES } from './data';
import {
  fetchOrders,
  fetchProfile,
  createOrder,
  updateProfile,
  updatePassword,
  logout as apiLogout,
  mapApiOrderToOrder,
  mapApiUserToProfile,
} from './api';

const EMPTY_PROFILE: UserProfile = {
  name: '',
  memberId: '',
  email: '',
  phone: '',
  savedAddressLabel: 'Alamat Utama',
  savedAddressDetails: '',
  memberLevel: 'MEMBER',
  totalOrders: 0,
  totalSpending: 0,
  progressSpending: 0,
  targetSpending: 1000000,
};

export default function App() {
  // Navigation & UI States
  const [activeTab, setActiveTab] = useState<'dashboard' | 'new-order' | 'my-orders' | 'profile'>('dashboard');
  const [selectedOrderId, setSelectedOrderId] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  
  // App Data States — loaded from the real Laravel backend (see the effect below)
  const [orders, setOrders] = useState<Order[]>([]);
  const [profile, setProfile] = useState<UserProfile>(EMPTY_PROFILE);
  const [isLoadingData, setIsLoadingData] = useState(true);

  const reloadOrders = async () => {
    const apiOrders = await fetchOrders();
    setOrders(apiOrders.map(mapApiOrderToOrder));
  };

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const [apiUser, apiOrders] = await Promise.all([fetchProfile(), fetchOrders()]);
        if (!mounted) return;
        setProfile(mapApiUserToProfile(apiUser));
        setPickupAddress(apiUser.address || '');
        setOrders(apiOrders.map(mapApiOrderToOrder));
      } catch (err) {
        console.error('Gagal memuat data akun:', err);
        showToast('Gagal memuat data. Coba muat ulang halaman.');
      } finally {
        if (mounted) setIsLoadingData(false);
      }
    })();
    return () => {
      mounted = false;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  
  // New Order Form States
  const [selectedServiceId, setSelectedServiceId] = useState<string>('regular');
  const [orderWeight, setOrderWeight] = useState<string>('5.0');
  const [isAntarJemput, setIsAntarJemput] = useState(true);
  const [isExpress, setIsExpress] = useState(false);
  const [pickupDate, setPickupDate] = useState('2026-07-10');
  const [pickupTime, setPickupTime] = useState('10:00');
  const [pickupAddress, setPickupAddress] = useState('');
  const [orderNotes, setOrderNotes] = useState('');
  const [paymentMethod, setPaymentMethod] = useState<'QRIS' | 'Tunai'>('QRIS');
  const [promoCodeInput, setPromoCodeInput] = useState('');
  const [isPromoApplied, setIsPromoApplied] = useState(false);
  
  // Filter for My Orders Tab
  const [ordersFilter, setOrdersFilter] = useState<'Semua' | 'Aktif' | 'Selesai'>('Semua');

  // Interactive Live Map States (Courier coordinates relative animation)
  const [courierPosition, setCourierPosition] = useState({ x: 30, y: 55 });
  const [courierDirection, setCourierDirection] = useState<'up' | 'down' | 'left' | 'right'>('right');

  // Modal / Toast States
  const [isSuccessModalOpen, setIsSuccessModalOpen] = useState(false);
  const [justCreatedOrderId, setJustCreatedOrderId] = useState('');
  const [isEditProfileModalOpen, setIsEditProfileModalOpen] = useState(false);
  const [isChangePasswordModalOpen, setIsChangePasswordModalOpen] = useState(false);
  
  // Profile Edit Inputs
  const [editName, setEditName] = useState(profile.name);
  const [editEmail, setEditEmail] = useState(profile.email);
  const [editPhone, setEditPhone] = useState(profile.phone);
  const [editAddress, setEditAddress] = useState(profile.savedAddressDetails);
  
  // Change Password Inputs
  const [oldPassword, setOldPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  // Toast notification
  const [toastMessage, setToastMessage] = useState<string | null>(null);

  // Mobile menu open
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  // Trigger Toast
  const showToast = (message: string) => {
    setToastMessage(message);
    setTimeout(() => {
      setToastMessage(null);
    }, 4000);
  };

  // Courier map animation logic (simulates a moving delivery courier on the map)
  useEffect(() => {
    const interval = setInterval(() => {
      setCourierPosition((prev) => {
        // Move in a simple path around the screen
        let nextX = prev.x;
        let nextY = prev.y;

        if (prev.x < 65 && prev.y === 55) {
          nextX += 2;
          setCourierDirection('right');
        } else if (prev.x >= 65 && prev.y > 25) {
          nextY -= 2;
          setCourierDirection('up');
        } else if (prev.y <= 25 && prev.x > 30) {
          nextX -= 2;
          setCourierDirection('left');
        } else {
          nextY += 2;
          setCourierDirection('down');
        }

        // Keep inside bounds just in case
        if (nextX > 80) nextX = 30;
        if (nextY < 15) nextY = 55;

        return { x: nextX, y: nextY };
      });
    }, 2500);

    return () => clearInterval(interval);
  }, []);

  // Update profile edit form fields when profile updates
  useEffect(() => {
    setEditName(profile.name);
    setEditEmail(profile.email);
    setEditPhone(profile.phone);
    setEditAddress(profile.savedAddressDetails);
  }, [profile]);

  // Handle Edit Profile Save — persists to the real backend
  const handleSaveProfile = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!editName || !editEmail || !editPhone || !editAddress) {
      showToast('Harap isi semua kolom profil!');
      return;
    }
    try {
      const updated = await updateProfile({
        name: editName,
        email: editEmail,
        phone: editPhone,
        address: editAddress,
      });
      setProfile((prev) => ({ ...prev, ...mapApiUserToProfile(updated) }));
      setIsEditProfileModalOpen(false);
      showToast('Profil Anda berhasil diperbarui!');
    } catch (err: any) {
      const message = err?.response?.data?.message || 'Gagal memperbarui profil.';
      showToast(message);
    }
  };

  // Handle Change Password Save — persists to the real backend
  const handleChangePasswordSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!oldPassword || !newPassword || !confirmPassword) {
      showToast('Harap isi semua kolom kata sandi!');
      return;
    }
    if (newPassword !== confirmPassword) {
      showToast('Konfirmasi kata sandi tidak cocok!');
      return;
    }
    try {
      await updatePassword({
        current_password: oldPassword,
        new_password: newPassword,
        new_password_confirmation: confirmPassword,
      });
      setOldPassword('');
      setNewPassword('');
      setConfirmPassword('');
      setIsChangePasswordModalOpen(false);
      showToast('Kata sandi Anda berhasil diperbarui!');
    } catch (err: any) {
      const message = err?.response?.data?.message || 'Gagal memperbarui kata sandi. Periksa kata sandi lama Anda.';
      showToast(message);
    }
  };

  // Apply promo code logic
  const handleApplyPromo = () => {
    if (promoCodeInput.trim().toUpperCase() === 'BERSIHNOW') {
      setIsPromoApplied(true);
      showToast('Promo BERSIHNOW berhasil diterapkan! Diskon 20% aktif.');
    } else {
      showToast('Kode promo tidak valid!');
    }
  };

  // Calculate pricing dynamically for the New Order form
  // NOTE: mirrors the exact formula used server-side in OrderController@store,
  // so the estimate shown here matches what actually gets charged.
  const getSelectedService = () => {
    if (selectedServiceId === 'regular') return { name: 'Cuci Lipat', price: 7000 };
    if (selectedServiceId === 'express') return { name: 'Cuci Setrika', price: 10000 };
    return { name: 'Setrika Saja', price: 6000 };
  };

  const currentService = getSelectedService();
  const parsedWeight = parseFloat(orderWeight) || 0;
  const rawServiceCost = parsedWeight * currentService.price;

  // Express handling fee: flat Rp 15.000 (matches backend, not per-kg)
  const expressSurcharge = isExpress ? 15000 : 0;
  const serviceCostAfterExpress = rawServiceCost + expressSurcharge;

  // Pickup/Delivery fees (Rp 5.000 each)
  const pickupFee = isAntarJemput ? 5000 : 0;
  const deliveryFee = isAntarJemput ? 5000 : 0;

  // Promo codes aren't persisted by the backend yet, so they're shown as a
  // preview only and don't change the amount actually charged.
  const calculatedDiscount = isPromoApplied ? Math.round(rawServiceCost * 0.2) : 0;

  const calculatedTotal = rawServiceCost + expressSurcharge + pickupFee + deliveryFee;


  // Handle Order Placement — sends the order to the real Laravel backend
  const [isPlacingOrder, setIsPlacingOrder] = useState(false);

  const handlePlaceOrder = async () => {
    if (parsedWeight <= 0) {
      showToast('Harap masukkan estimasi berat pakaian yang valid!');
      return;
    }
    if (isAntarJemput && !pickupAddress.trim()) {
      showToast('Harap isi alamat penjemputan!');
      return;
    }

    setIsPlacingOrder(true);
    try {
      const { order_id } = await createOrder({
        serviceType: currentService.name as 'Cuci Lipat' | 'Cuci Setrika' | 'Setrika Saja',
        weight: parsedWeight,
        isPickupDelivery: isAntarJemput,
        isExpress: isExpress,
        pickupDate: isAntarJemput ? pickupDate : undefined,
        pickupTime: isAntarJemput ? pickupTime : undefined,
        pickupAddress: isAntarJemput ? pickupAddress : undefined,
        instructions: orderNotes || undefined,
      });

      // Refresh both the order list and profile totals from the backend
      await reloadOrders();
      try {
        const apiUser = await fetchProfile();
        setProfile(mapApiUserToProfile(apiUser));
      } catch {
        /* profile totals are non-critical here */
      }

      const newOrderId = `#ORD-${String(order_id).padStart(4, '0')}`;
      setJustCreatedOrderId(newOrderId);
      setIsSuccessModalOpen(true);

      // Clear form for next use
      setOrderWeight('5.0');
      setOrderNotes('');
      setIsPromoApplied(false);
      setPromoCodeInput('');
    } catch (err: any) {
      const message = err?.response?.data?.message || 'Gagal membuat pesanan. Silakan coba lagi.';
      showToast(message);
    } finally {
      setIsPlacingOrder(false);
    }
  };

  // Close success modal & navigate to detail view of new order
  const handleViewNewOrderDetails = () => {
    setIsSuccessModalOpen(false);
    setSelectedOrderId(justCreatedOrderId);
    setActiveTab('my-orders');
  };

  // Filter orders lists based on global search & custom filter states
  const getFilteredOrders = () => {
    return orders.filter((order) => {
      const matchesSearch =
        order.id.toLowerCase().includes(searchQuery.toLowerCase()) ||
        order.serviceName.toLowerCase().includes(searchQuery.toLowerCase()) ||
        order.statusLabelIndo.toLowerCase().includes(searchQuery.toLowerCase());

      if (activeTab === 'my-orders') {
        if (ordersFilter === 'Aktif') return matchesSearch && order.status !== 'Selesai';
        if (ordersFilter === 'Selesai') return matchesSearch && order.status === 'Selesai';
      }
      return matchesSearch;
    });
  };

  const filteredOrdersList = getFilteredOrders();
  const selectedOrder = orders.find((o) => o.id === selectedOrderId) || orders[0];

  // Map progress bar helper based on order tracking state
  const getProgressPercent = (status: OrderStatus) => {
    const steps: OrderStatus[] = [
      'Konfirmasi',
      'Menunggu Jemput',
      'Dalam Perjalanan',
      'Dijemput',
      'Pencucian',
      'Pengeringan',
      'Penyetrikaan',
      'Siap',
      'Pengiriman',
      'Selesai',
    ];
    const index = steps.indexOf(status);
    return Math.round(((index + 1) / steps.length) * 100);
  };

  if (isLoadingData) {
    return (
      <div className="min-h-screen bg-[#f9f9ff] flex items-center justify-center">
        <div className="flex items-center gap-3 text-[#003d9b] font-bold text-sm">
          <Sparkles className="w-5 h-5 animate-pulse" />
          <span>Memuat data akun Anda...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#f9f9ff] text-[#091c35] font-sans antialiased flex flex-col md:flex-row">
      {/* Toast Notification */}
      <AnimatePresence>
        {toastMessage && (
          <motion.div
            initial={{ opacity: 0, y: -50, scale: 0.9 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: -50, scale: 0.9 }}
            className="fixed top-5 left-1/2 -translate-x-1/2 z-[999] bg-[#003d9b] text-white px-6 py-3.5 rounded-xl shadow-xl flex items-center gap-3 font-semibold text-sm border border-white/20"
          >
            <Sparkles className="w-5 h-5 text-[#6ff7ee] animate-pulse" />
            <span>{toastMessage}</span>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Sidebar Navigation - Fixed & Responsive */}
      <aside className="w-full md:w-[260px] bg-white border-b md:border-b-0 md:border-r border-[#c3c6d6] flex flex-col p-6 shrink-0 z-40 sticky top-0 md:h-screen">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="font-display text-2xl font-extrabold text-[#003d9b] tracking-tight leading-none">
              Laundry Yuk!
            </h1>
            <p className="text-xs font-semibold text-[#434654] mt-1 tracking-wider uppercase opacity-85">
              Laundry Management
            </p>
          </div>
          {/* Mobile Hamburguer Toggle */}
          <button
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            className="p-2 text-[#003d9b] hover:bg-[#f0f3ff] rounded-lg md:hidden"
          >
            <Menu className="w-6 h-6" />
          </button>
        </div>

        {/* Sidebar Nav Items (Responsive Collapse) */}
        <nav className={`mt-8 space-y-2 flex-grow ${isMobileMenuOpen ? 'block' : 'hidden'} md:block`}>
          <button
            onClick={() => {
              setActiveTab('dashboard');
              setIsMobileMenuOpen(false);
            }}
            className={`w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 ${
              activeTab === 'dashboard'
                ? 'bg-[#f0f3ff] text-[#003d9b] border-r-4 border-[#003d9b]'
                : 'text-[#434654] hover:bg-[#f9f9ff] hover:text-[#003d9b]'
            }`}
          >
            <LayoutDashboard className="w-5 h-5" />
            <span>Dashboard</span>
          </button>

          <button
            onClick={() => {
              setActiveTab('new-order');
              setIsMobileMenuOpen(false);
            }}
            className={`w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 ${
              activeTab === 'new-order'
                ? 'bg-[#f0f3ff] text-[#003d9b] border-r-4 border-[#003d9b]'
                : 'text-[#434654] hover:bg-[#f9f9ff] hover:text-[#003d9b]'
            }`}
          >
            <ShoppingCart className="w-5 h-5" />
            <span>New Order</span>
          </button>

          <button
            onClick={() => {
              setActiveTab('my-orders');
              setIsMobileMenuOpen(false);
            }}
            className={`w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 ${
              activeTab === 'my-orders'
                ? 'bg-[#f0f3ff] text-[#003d9b] border-r-4 border-[#003d9b]'
                : 'text-[#434654] hover:bg-[#f9f9ff] hover:text-[#003d9b]'
            }`}
          >
            <History className="w-5 h-5" />
            <span>My Orders</span>
          </button>

          <button
            onClick={() => {
              setActiveTab('profile');
              setIsMobileMenuOpen(false);
            }}
            className={`w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 ${
              activeTab === 'profile'
                ? 'bg-[#f0f3ff] text-[#003d9b] border-r-4 border-[#003d9b]'
                : 'text-[#434654] hover:bg-[#f9f9ff] hover:text-[#003d9b]'
            }`}
          >
            <User className="w-5 h-5" />
            <span>Profile</span>
          </button>
        </nav>

        {/* Sidebar Footer Logout Button */}
        <div className={`mt-auto pt-6 border-t border-[#c3c6d6]/40 ${isMobileMenuOpen ? 'block' : 'hidden'} md:block`}>
          <button
            onClick={async () => {
              setIsMobileMenuOpen(false);
              try {
                await apiLogout();
              } catch (err) {
                showToast('Gagal keluar. Silakan coba lagi.');
              }
            }}
            className="w-full flex items-center gap-3 px-4 py-3 text-[#ba1a1a] hover:bg-[#ffdad6]/20 transition-colors rounded-xl font-semibold text-sm"
          >
            <LogOut className="w-5 h-5" />
            <span>Keluar</span>
          </button>
        </div>
      </aside>

      {/* Main Content Viewport */}
      <div className="flex-grow flex flex-col min-w-0 md:h-screen md:overflow-y-auto">
        {/* Top App Bar Header */}
        <header className="h-16 border-b border-[#c3c6d6]/60 bg-white px-6 md:px-8 flex justify-between items-center shrink-0 sticky top-0 z-30 shadow-sm shadow-[#091c35]/5">
          {/* Live Search Input */}
          <div className="flex items-center bg-[#f0f3ff] px-4 py-2 rounded-xl border border-[#c3c6d6]/30 max-w-sm w-full focus-within:ring-2 focus-within:ring-[#003d9b]/20 transition-all">
            <Search className="w-4.5 h-4.5 text-[#434654] mr-2 shrink-0" />
            <input
              type="text"
              placeholder="Cari pesanan, tagihan, atau status..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="bg-transparent border-none focus:outline-none text-sm w-full text-[#091c35] placeholder-[#434654]/75"
            />
            {searchQuery && (
              <button onClick={() => setSearchQuery('')} className="p-0.5 text-[#434654] hover:text-black">
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>

          {/* User Quick Controls */}
          <div className="flex items-center gap-4">
            {/* Notifications Alert icon */}
            <button
              onClick={() => showToast('Pemberitahuan: Cucian Anda #ORD-8924 sedang diproses di tahap Pencucian!')}
              className="w-10 h-10 flex items-center justify-center rounded-full text-[#434654] hover:text-[#003d9b] hover:bg-[#f0f3ff] transition-all relative"
            >
              <Bell className="w-5 h-5" />
              <span className="absolute top-2.5 right-2.5 w-2 h-2 bg-[#ba1a1a] rounded-full border border-white"></span>
            </button>

            {/* Quick Settings Icon */}
            <button
              onClick={() => showToast('Pengaturan cepat: Hubungi Customer Support untuk penyesuaian akun.')}
              className="w-10 h-10 flex items-center justify-center rounded-full text-[#434654] hover:text-[#003d9b] hover:bg-[#f0f3ff] transition-all"
            >
              <Settings className="w-5 h-5" />
            </button>

            <div className="h-8 w-px bg-[#c3c6d6]/60 hidden sm:block"></div>

            {/* Profile Avatar Trigger */}
            <div
              onClick={() => setActiveTab('profile')}
              className="flex items-center gap-3 cursor-pointer group hover:opacity-85 hidden sm:flex"
            >
              <div className="text-right">
                <p className="font-semibold text-xs text-[#091c35] leading-tight group-hover:text-[#003d9b] transition-colors">
                  {profile.name}
                </p>
                <p className="font-semibold text-[10px] text-[#003d9b] uppercase tracking-wider mt-0.5">
                  {profile.memberLevel}
                </p>
              </div>
              <div className="w-10 h-10 rounded-xl border-2 border-[#0052cc] overflow-hidden bg-[#d6e3ff] flex items-center justify-center text-[#003d9b] font-bold">
                {profile.name.charAt(0)}
              </div>
            </div>
          </div>
        </header>

        {/* Content Body Container */}
        <main className="flex-grow p-6 md:p-8">
          <AnimatePresence mode="wait">
            {/* VIEW 1: DASHBOARD */}
            {activeTab === 'dashboard' && (
              <motion.div
                key="dashboard"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                className="space-y-8"
              >
                {/* Welcome Greeting Banner */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                  <div>
                    <h2 className="font-display text-3xl font-extrabold text-[#091c35] tracking-tight">
                      Halo, {profile.name}!
                    </h2>
                    <p className="text-[#434654] font-medium text-sm sm:text-base mt-1">
                      Selamat datang kembali. Berikut adalah ringkasan cucian Anda hari ini.
                    </p>
                  </div>
                  <button
                    onClick={() => setActiveTab('new-order')}
                    className="bg-[#003d9b] hover:bg-[#0052cc] text-white px-6 py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-[#003d9b]/15 hover:shadow-xl transition-all hover:-translate-y-0.5 active:translate-y-0"
                  >
                    <Plus className="w-5 h-5 text-[#6ff7ee]" />
                    <span>Buat Pesanan Baru</span>
                  </button>
                </div>

                {/* Bento Statistics Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                  {/* Card 1: Active Orders */}
                  <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 hover:border-[#003d9b] hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                    <div>
                      <div className="w-10 h-10 rounded-lg bg-[#dae2ff] text-[#003d9b] flex items-center justify-center mb-4 font-bold">
                        <History className="w-5 h-5" />
                      </div>
                      <p className="text-[#434654] text-xs font-bold uppercase tracking-wider">Pesanan Aktif</p>
                    </div>
                    <div className="flex items-end justify-between mt-4">
                      <span className="font-display text-4xl font-extrabold text-[#091c35]">
                        {orders.filter((o) => o.status !== 'Selesai').length}
                      </span>
                      <span className="text-[#006a65] bg-[#6ff7ee]/20 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                        On Process <TrendingUp className="w-3.5 h-3.5" />
                      </span>
                    </div>
                  </div>

                  {/* Card 2: Completed Orders */}
                  <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 hover:border-[#003d9b] hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                    <div>
                      <div className="w-10 h-10 rounded-lg bg-[#6ff7ee]/15 text-[#006a65] flex items-center justify-center mb-4 font-bold">
                        <CheckCircle2 className="w-5 h-5" />
                      </div>
                      <p className="text-[#434654] text-xs font-bold uppercase tracking-wider">Pesanan Selesai</p>
                    </div>
                    <div className="flex items-end justify-between mt-4">
                      <span className="font-display text-4xl font-extrabold text-[#091c35]">
                        {orders.filter((o) => o.status === 'Selesai').length}
                      </span>
                      <span className="text-[#434654] text-xs font-semibold bg-[#f0f3ff] px-3 py-1 rounded-full">
                        Bulan ini
                      </span>
                    </div>
                  </div>

                  {/* Card 3: Spending Balance - Deep Corporate Blue Accent */}
                  <div className="bg-[#003d9b] text-white rounded-2xl p-6 flex flex-col justify-between shadow-xl shadow-[#003d9b]/15 relative overflow-hidden group">
                    <div className="absolute right-[-20px] bottom-[-20px] opacity-10 group-hover:scale-110 transition-transform duration-500">
                      <QrCode className="w-40 h-40" />
                    </div>
                    <div>
                      <div className="w-10 h-10 rounded-lg bg-white/10 text-white flex items-center justify-center mb-4 font-bold">
                        <Award className="w-5 h-5 text-[#6ff7ee]" />
                      </div>
                      <p className="text-[#c4d2ff] text-xs font-bold uppercase tracking-wider">Total Pengeluaran</p>
                    </div>
                    <div className="flex items-end justify-between mt-4">
                      <span className="font-display text-2xl sm:text-3xl font-extrabold text-white">
                        Rp {profile.totalSpending.toLocaleString('id-ID')}
                      </span>
                      <span className="bg-[#6ff7ee] text-[#001848] px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wide">
                        Verified Account
                      </span>
                    </div>
                  </div>
                </div>

                {/* Promotional banner container with visual context */}
                <div className="bg-gradient-to-r from-[#003d9b] to-[#0052cc] rounded-2xl overflow-hidden shadow-lg p-6 sm:p-10 relative flex flex-col md:flex-row items-center justify-between gap-6">
                  <div className="relative z-10 max-w-xl text-white space-y-3">
                    <span className="bg-[#006a65] text-[#6ff7ee] text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                      Special Offer
                    </span>
                    <h3 className="text-2xl sm:text-3xl font-display font-extrabold tracking-tight leading-tight">
                      Diskon 20% Untuk Layanan Express!
                    </h3>
                    <p className="text-white/80 text-sm sm:text-base">
                      Gunakan kode promo <span className="font-mono font-bold text-white bg-white/10 px-2 py-0.5 rounded">BERSIHNOW</span> untuk pesanan berikutnya.
                    </p>
                  </div>
                  <div className="relative shrink-0 w-full md:w-[240px] h-[120px] rounded-xl overflow-hidden border border-white/20 shadow-md">
                    <img
                      src="https://lh3.googleusercontent.com/aida-public/AB6AXuCx9A7zYG8OALjhvJWoC8v8kND-7kvowwxpGRKOPxOZTdVwwPCJoTc438qrd7JZE3ET8m0JQof46mfH5HH1EjLXuhqMs_EE3eOMYeV6rXD81beoWORUcuqU9gtc1vajs-Vm9K9UXbdVbpx-EGCAsqP6ZrQoHSIDOmw3RnlsBAE4Y7K-YISe2lJe6tx0WBJ73NSYjx_yBiaQ9ohJX7r8MGMxhmZkTfyFd-ahHWqlASJVnzf48QEV-se8Zo5MFAuu4PHE39i8VeCKQapS"
                      alt="Clean folded towels"
                      className="w-full h-full object-cover"
                    />
                    <div className="absolute inset-0 bg-[#003d9b]/25"></div>
                  </div>
                </div>

                {/* Split Layout: Recent Orders & Available Services */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                  {/* Left Column: Recent Orders List */}
                  <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 lg:col-span-8 shadow-sm">
                    <div className="flex justify-between items-center mb-6">
                      <h3 className="font-display text-lg font-extrabold text-[#091c35]">Pesanan Terbaru</h3>
                      <button
                        onClick={() => {
                          setOrdersFilter('Semua');
                          setActiveTab('my-orders');
                        }}
                        className="text-[#003d9b] hover:text-[#0052cc] text-xs font-bold hover:underline flex items-center gap-1"
                      >
                        Lihat Semua <ArrowRight className="w-3.5 h-3.5" />
                      </button>
                    </div>

                    <div className="overflow-x-auto">
                      <table className="w-full text-left border-collapse">
                        <thead>
                          <tr className="border-b border-[#c3c6d6]/40 text-xs text-[#434654] uppercase tracking-wider font-bold">
                            <th className="pb-3.5">Order ID</th>
                            <th className="pb-3.5">Layanan</th>
                            <th className="pb-3.5">Tanggal</th>
                            <th className="pb-3.5">Status</th>
                            <th className="pb-3.5 text-right">Total</th>
                          </tr>
                        </thead>
                        <tbody className="divide-y divide-[#c3c6d6]/20">
                          {orders.slice(0, 4).map((order) => (
                            <tr
                              key={order.id}
                              onClick={() => {
                                setSelectedOrderId(order.id);
                                setActiveTab('my-orders');
                              }}
                              className="group hover:bg-[#f0f3ff]/40 cursor-pointer transition-colors"
                            >
                              <td className="py-4 font-mono font-bold text-[#003d9b] text-sm group-hover:underline">
                                {order.id}
                              </td>
                              <td className="py-4 font-semibold text-sm text-[#091c35]">
                                {order.serviceName}
                              </td>
                              <td className="py-4 text-[#434654] text-xs">
                                {order.orderDate}
                              </td>
                              <td className="py-4">
                                <span
                                  className={`px-3 py-1 rounded-full text-[10px] font-bold ${
                                    order.status === 'Selesai'
                                      ? 'bg-[#6ff7ee]/15 text-[#006a65]'
                                      : 'bg-[#dae2ff] text-[#003d9b]'
                                  }`}
                                >
                                  {order.statusLabelIndo}
                                </span>
                              </td>
                              <td className="py-4 text-right font-bold text-sm text-[#091c35]">
                                Rp {order.totalPrice.toLocaleString('id-ID')}
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>

                  {/* Right Column: Available Services Panel */}
                  <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 lg:col-span-4 shadow-sm">
                    <h3 className="font-display text-lg font-extrabold text-[#091c35] mb-6">Layanan Tersedia</h3>
                    <div className="space-y-4">
                      {SERVICES.map((svc) => (
                        <div
                          key={svc.id}
                          onClick={() => {
                            setSelectedServiceId(svc.id === 'regular' ? 'regular' : svc.id === 'express' ? 'express' : 'iron');
                            setActiveTab('new-order');
                          }}
                          className="flex items-center p-4 border border-[#c3c6d6]/40 rounded-xl hover:border-[#003d9b] hover:bg-[#f0f3ff]/20 cursor-pointer transition-all duration-200 group"
                        >
                          <div className="w-12 h-12 rounded-xl bg-[#dae2ff]/50 text-[#003d9b] flex items-center justify-center mr-4 shrink-0 group-hover:scale-105 transition-transform">
                            <Sparkles className="w-5 h-5 text-[#003d9b]" />
                          </div>
                          <div className="flex-grow min-w-0">
                            <p className="font-bold text-sm text-[#091c35] group-hover:text-[#003d9b] transition-colors truncate">
                              {svc.name}
                            </p>
                            <p className="text-[11px] text-[#434654] font-medium mt-0.5 truncate">
                              {svc.estimate}
                            </p>
                          </div>
                          <span className="text-[#003d9b] font-extrabold text-sm whitespace-nowrap shrink-0 ml-2">
                            Rp {svc.price.toLocaleString('id-ID')}/{svc.unit}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>

                {/* Active Order Lifecycle Tracking at the bottom (shows the latest order status) */}
                {orders.length > 0 && (
                  <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h3 className="font-display text-lg font-extrabold text-[#091c35] mb-6">
                      Status Pesanan Aktif ({orders[0].id})
                    </h3>
                    
                    {/* Stepper track */}
                    <div className="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-4 mt-8">
                      {/* Gray connecting track line */}
                      <div className="absolute top-[22px] left-5 right-5 h-[3px] bg-[#c3c6d6]/30 -z-0 hidden md:block"></div>
                      
                      {/* Active green connecting track progress */}
                      <div
                        className="absolute top-[22px] left-5 h-[3px] bg-[#006a65] -z-0 hidden md:block transition-all duration-1000"
                        style={{ width: `${Math.min(95, getProgressPercent(orders[0].status))}%` }}
                      ></div>

                      {/* Render tracking milestones */}
                      {[
                        { label: 'Diterima', icon: CheckCircle2, complete: true },
                        { label: 'Dicuci', icon: Sparkles, complete: ['Pencucian', 'Pengeringan', 'Penyetrikaan', 'Siap', 'Pengiriman', 'Selesai'].includes(orders[0].status) },
                        { label: 'Pengeringan', icon: CheckCircle2, complete: ['Pengeringan', 'Penyetrikaan', 'Siap', 'Pengiriman', 'Selesai'].includes(orders[0].status) },
                        { label: 'Setrika/Lipat', icon: Shirt, complete: ['Penyetrikaan', 'Siap', 'Pengiriman', 'Selesai'].includes(orders[0].status) },
                        { label: 'Siap Ambil', icon: Truck, complete: ['Siap', 'Pengiriman', 'Selesai'].includes(orders[0].status) },
                      ].map((step, idx) => (
                        <div key={idx} className="relative z-10 flex md:flex-col items-center gap-4 md:gap-2">
                          <div
                            className={`w-11 h-11 rounded-full flex items-center justify-center transition-all ${
                              step.complete
                                ? 'bg-[#003d9b] text-white ring-4 ring-[#dae2ff]'
                                : 'bg-white border-2 border-[#c3c6d6] text-[#434654]'
                            }`}
                          >
                            <step.icon className="w-5 h-5" />
                          </div>
                          <div className="text-left md:text-center">
                            <p className={`text-xs font-bold ${step.complete ? 'text-[#003d9b]' : 'text-[#434654]'}`}>
                              {step.label}
                            </p>
                            <span className="text-[9px] text-[#434654] opacity-75">
                              {step.complete ? 'Selesai' : 'Antrean'}
                            </span>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </motion.div>
            )}

            {/* VIEW 2: NEW ORDER (BUAT PESANAN BARU) */}
            {activeTab === 'new-order' && (
              <motion.div
                key="new-order"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                className="space-y-8"
              >
                {/* Section Header */}
                <div className="flex flex-col sm:flex-row justify-between sm:items-end gap-4 border-b border-[#c3c6d6]/30 pb-4">
                  <div>
                    <h2 className="font-display text-3xl font-extrabold text-[#091c35] tracking-tight">
                      Buat Pesanan Baru
                    </h2>
                    <p className="text-[#434654] font-medium text-sm mt-1">
                      Isi detail pesanan untuk memulai layanan laundry premium Anda.
                    </p>
                  </div>
                  <div className="text-right">
                    <span className="text-xs font-semibold text-[#434654]">Order ID:</span>
                    <span className="font-mono font-extrabold text-[#003d9b] ml-2 text-sm tracking-wide">
                      #ORD-{(orders.length + 1000) * 2}
                    </span>
                  </div>
                </div>

                {/* Form and Summary Container */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                  {/* Left Column: Input Form (8 columns) */}
                  <div className="lg:col-span-8 space-y-6">
                    {/* Choose Service */}
                    <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 shadow-sm">
                      <h3 className="font-display text-base font-extrabold text-[#003d9b] mb-4 flex items-center gap-2">
                        <Sparkles className="w-5 h-5 text-[#003d9b]" />
                        <span>Pilih Layanan Utama</span>
                      </h3>
                      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {[
                          { id: 'regular', name: 'Cuci Lipat', price: 'Rp 7.000 / kg' },
                          { id: 'express', name: 'Cuci Setrika', price: 'Rp 10.000 / kg' },
                          { id: 'iron', name: 'Setrika Saja', price: 'Rp 6.000 / kg' },
                        ].map((svc) => (
                          <div
                            key={svc.id}
                            onClick={() => setSelectedServiceId(svc.id)}
                            className={`p-4 border rounded-xl cursor-pointer transition-all ${
                              selectedServiceId === svc.id
                                ? 'border-[#003d9b] bg-[#dae2ff]/30 ring-2 ring-[#003d9b]/15'
                                : 'border-[#c3c6d6]/60 hover:border-[#003d9b] hover:bg-[#f0f3ff]/20'
                            }`}
                          >
                            <p className="font-bold text-sm text-[#091c35]">{svc.name}</p>
                            <p className="text-xs font-semibold text-[#003d9b] mt-1">{svc.price}</p>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* Weight Inputs & Toggles */}
                    <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 shadow-sm space-y-6">
                      <h3 className="font-display text-base font-extrabold text-[#091c35] mb-4">
                        Detail Cucian &amp; Pengiriman
                      </h3>
                      
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {/* Weight Indicator */}
                        <div className="space-y-2">
                          <label className="text-xs font-bold text-[#091c35] uppercase tracking-wide block">
                            Estimasi Berat (kg)
                          </label>
                          <div className="relative">
                            <input
                              type="number"
                              step="0.1"
                              min="0.5"
                              value={orderWeight}
                              onChange={(e) => setOrderWeight(e.target.value)}
                              placeholder="0.0"
                              className="w-full px-4 py-3 bg-[#f9f9ff] border border-[#c3c6d6] rounded-xl focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] text-sm text-[#091c35] font-semibold"
                            />
                            <span className="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-mono font-bold text-[#434654]">
                              kg
                            </span>
                          </div>
                          <span className="text-[11px] text-[#434654] block">
                            *Berat riil akan ditimbang ulang oleh kurir saat penjemputan.
                          </span>
                        </div>

                        {/* Interactive Surcharges & Extras toggles */}
                        <div className="bg-[#f0f3ff]/50 border border-[#c3c6d6]/40 rounded-xl p-4 flex items-center justify-around gap-4">
                          <div className="flex flex-col items-center gap-1.5 text-center">
                            <span className="text-xs font-bold text-[#091c35]">Antar-Jemput</span>
                            <button
                              onClick={() => setIsAntarJemput(!isAntarJemput)}
                              className={`w-11 h-6 rounded-full transition-colors relative ${
                                isAntarJemput ? 'bg-[#003d9b]' : 'bg-[#c3c6d6]'
                              }`}
                            >
                              <div
                                className={`w-4 h-4 bg-white rounded-full absolute top-1 transition-all ${
                                  isAntarJemput ? 'left-6' : 'left-1'
                                }`}
                              ></div>
                            </button>
                            <span className="text-[9px] text-[#434654] font-semibold">Rp 10.000 (PP)</span>
                          </div>

                          <div className="h-10 w-px bg-[#c3c6d6]/50"></div>

                          <div className="flex flex-col items-center gap-1.5 text-center">
                            <span className="text-xs font-bold text-[#091c35]">Ekspres (24h)</span>
                            <button
                              onClick={() => setIsExpress(!isExpress)}
                              className={`w-11 h-6 rounded-full transition-colors relative ${
                                isExpress ? 'bg-[#003d9b]' : 'bg-[#c3c6d6]'
                              }`}
                            >
                              <div
                                className={`w-4 h-4 bg-white rounded-full absolute top-1 transition-all ${
                                  isExpress ? 'left-6' : 'left-1'
                                }`}
                              ></div>
                            </button>
                            <span className="text-[9px] text-[#434654] font-semibold">+ Rp 5.000/kg</span>
                          </div>
                        </div>
                      </div>

                      {/* Logistics date & time picking */}
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                        <div className="space-y-2">
                          <label className="text-xs font-bold text-[#091c35] uppercase tracking-wide block">
                            Tanggal Penjemputan
                          </label>
                          <div className="relative">
                            <input
                              type="date"
                              value={pickupDate}
                              onChange={(e) => setPickupDate(e.target.value)}
                              className="w-full px-4 py-3 bg-[#f9f9ff] border border-[#c3c6d6] rounded-xl focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] text-sm text-[#091c35] font-semibold"
                            />
                          </div>
                        </div>
                        <div className="space-y-2">
                          <label className="text-xs font-bold text-[#091c35] uppercase tracking-wide block">
                            Waktu Penjemputan
                          </label>
                          <input
                            type="time"
                            value={pickupTime}
                            onChange={(e) => setPickupTime(e.target.value)}
                            className="w-full px-4 py-3 bg-[#f9f9ff] border border-[#c3c6d6] rounded-xl focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] text-sm text-[#091c35] font-semibold"
                          />
                        </div>
                      </div>

                      {/* Pickup address text input */}
                      <div className="space-y-2 pt-2">
                        <label className="text-xs font-bold text-[#091c35] uppercase tracking-wide block">
                          Alamat Lengkap Penjemputan
                        </label>
                        <textarea
                          rows={3}
                          value={pickupAddress}
                          onChange={(e) => setPickupAddress(e.target.value)}
                          placeholder="Ketikkan alamat lengkap pengiriman & penjemputan..."
                          className="w-full px-4 py-3 bg-[#f9f9ff] border border-[#c3c6d6] rounded-xl focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] text-sm text-[#091c35]"
                        ></textarea>
                      </div>

                      {/* Special instructions notes text input */}
                      <div className="space-y-2 pt-2">
                        <label className="text-xs font-bold text-[#091c35] uppercase tracking-wide block">
                          Catatan Tambahan (Opsional)
                        </label>
                        <textarea
                          rows={2}
                          value={orderNotes}
                          onChange={(e) => setOrderNotes(e.target.value)}
                          placeholder="Contoh: Pisahkan pakaian putih, jas tolong digantung, kucek noda kopi..."
                          className="w-full px-4 py-3 bg-[#f9f9ff] border border-[#c3c6d6] rounded-xl focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] text-sm text-[#091c35]"
                        ></textarea>
                      </div>
                    </div>
                  </div>

                  {/* Right Column: Order Summary (Sticky 4 columns) */}
                  <div className="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
                    <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl overflow-hidden shadow-sm">
                      {/* Cost Header banner */}
                      <div className="bg-[#003d9b] p-5 text-white">
                        <h3 className="font-display font-extrabold text-lg">Ringkasan Pesanan</h3>
                        <p className="text-xs text-[#c4d2ff] font-medium mt-1">Rincian estimasi biaya laundry</p>
                      </div>

                      <div className="p-5 space-y-4">
                        <div className="flex justify-between text-sm">
                          <span className="text-[#434654] font-medium">Layanan Utama:</span>
                          <span className="font-bold text-[#091c35]">{currentService.name}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                          <span className="text-[#434654] font-medium">Estimasi Berat:</span>
                          <span className="font-bold text-[#091c35]">{parsedWeight} kg</span>
                        </div>

                        <div className="border-t border-[#c3c6d6]/40 pt-4 space-y-2 text-xs">
                          <div className="flex justify-between">
                            <span className="text-[#434654] font-medium">Biaya Layanan:</span>
                            <span className="font-bold text-[#091c35]">
                              Rp {rawServiceCost.toLocaleString('id-ID')}
                            </span>
                          </div>
                          {isExpress && (
                            <div className="flex justify-between text-[#003d9b]">
                              <span className="font-medium">Surcharge Ekspres (+Rp5k/kg):</span>
                              <span className="font-bold">
                                Rp {expressSurcharge.toLocaleString('id-ID')}
                              </span>
                            </div>
                          )}
                          <div className="flex justify-between">
                            <span className="text-[#434654] font-medium">Biaya Penjemputan:</span>
                            <span className="font-bold text-[#091c35]">
                              Rp {pickupFee.toLocaleString('id-ID')}
                            </span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-[#434654] font-medium">Biaya Pengantaran:</span>
                            <span className="font-bold text-[#091c35]">
                              Rp {deliveryFee.toLocaleString('id-ID')}
                            </span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-[#434654] font-medium">Biaya Penanganan Platform:</span>
                            <span className="font-bold text-[#091c35]">Rp 2.000</span>
                          </div>

                          {/* Promo code entry panel */}
                          <div className="pt-3 pb-1">
                            <p className="text-[10px] font-bold text-[#434654] uppercase tracking-wide mb-1.5">
                              Kupon Promo
                            </p>
                            <div className="flex gap-2">
                              <input
                                type="text"
                                placeholder="Contoh: BERSIHNOW"
                                value={promoCodeInput}
                                onChange={(e) => setPromoCodeInput(e.target.value)}
                                className="bg-[#f9f9ff] border border-[#c3c6d6] rounded-lg px-3 py-1.5 text-xs font-semibold focus:ring-1 focus:ring-[#003d9b] focus:outline-none flex-grow uppercase"
                              />
                              <button
                                onClick={handleApplyPromo}
                                className="bg-[#003d9b] hover:bg-[#0052cc] text-white px-3 py-1.5 rounded-lg text-xs font-bold shrink-0 transition-colors"
                              >
                                Pakai
                              </button>
                            </div>
                            {isPromoApplied && (
                              <div className="mt-1.5 text-[#006a65] font-semibold text-[11px] flex items-center gap-1 bg-[#6ff7ee]/10 p-1 rounded">
                                <Check className="w-3.5 h-3.5" /> Diskon BERSIHNOW 20% Terpasang (-Rp {calculatedDiscount.toLocaleString('id-ID')})
                              </div>
                            )}
                          </div>
                        </div>

                        {/* Payment Selection tabs */}
                        <div className="border-t border-[#c3c6d6]/40 pt-4 space-y-2">
                          <p className="text-[10px] font-bold text-[#434654] uppercase tracking-wider block">
                            Pilih Metode Pembayaran
                          </p>
                          <div className="grid grid-cols-2 gap-2">
                            <button
                              onClick={() => setPaymentMethod('QRIS')}
                              className={`p-2.5 border rounded-xl flex flex-col items-center gap-1 transition-all ${
                                paymentMethod === 'QRIS'
                                  ? 'border-[#003d9b] bg-[#dae2ff]/30 ring-1 ring-[#003d9b]'
                                  : 'border-[#c3c6d6]/60 hover:bg-[#f9f9ff]'
                              }`}
                            >
                              <QrCode className="w-4 h-4 text-[#003d9b]" />
                              <span className="text-[10px] font-extrabold text-[#091c35]">QRIS Otomatis</span>
                            </button>

                            <button
                              onClick={() => setPaymentMethod('Tunai')}
                              className={`p-2.5 border rounded-xl flex flex-col items-center gap-1 transition-all ${
                                paymentMethod === 'Tunai'
                                  ? 'border-[#003d9b] bg-[#dae2ff]/30 ring-1 ring-[#003d9b]'
                                  : 'border-[#c3c6d6]/60 hover:bg-[#f9f9ff]'
                              }`}
                            >
                              <Truck className="w-4 h-4 text-[#006a65]" />
                              <span className="text-[10px] font-extrabold text-[#091c35]">Bayar di Tempat</span>
                            </button>
                          </div>

                          {/* Conditional payment methods instructions */}
                          <div className="mt-2 text-xs bg-[#f0f3ff]/40 p-3 rounded-lg border border-[#c3c6d6]/30">
                            {paymentMethod === 'QRIS' ? (
                              <div className="space-y-2 text-center">
                                <p className="text-[10px] font-bold text-[#003d9b] uppercase">Scan QRIS Untuk Membayar</p>
                                <div className="w-28 h-28 bg-white p-2 rounded-lg border border-[#c3c6d6]/60 mx-auto">
                                  <img
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuB5x8FP7wsxCB5a9WpV8TLK_vtawusGrUTy0Wz5rKSJRY1WSTGNvF0qsBI0aIdsPr-QvLbdBcZxKHTlVdMFKnyvznpTBMXfyW7R7qYTdsa-Kk4H39r-jY-OjPv8iwKmXx4vS5iWacVTt4MUQlybJcjHMZzycLB9sibJcv5Hicizsvq9OqhT0R6_jHbLG7SBdgM6ifiJEFOKZhF6PdcP542B3WQc6LTmEAehmVaK3lNAYpPlPyHqcSKrw5gwL9_oZ1enhOf9EmZwPFvl44A"
                                    alt="QRIS payment code barcode"
                                    className="w-full h-full object-contain"
                                  />
                                </div>
                              </div>
                            ) : (
                              <p className="text-center font-medium text-[#434654] text-[11px] leading-relaxed">
                                Bayar tunai ke kurir laundry saat pakaian kotor Anda dijemput dari lokasi.
                              </p>
                            )}
                          </div>
                        </div>

                        {/* Grand Total box */}
                        <div className="bg-[#f0f3ff] p-4 rounded-xl flex justify-between items-center border border-[#c3c6d6]/30">
                          <span className="font-bold text-sm text-[#091c35]">Total Estimasi</span>
                          <span className="font-display text-2xl font-extrabold text-[#003d9b]">
                            Rp {calculatedTotal.toLocaleString('id-ID')}
                          </span>
                        </div>

                        {/* Visual Quality badge illustration */}
                        <div className="rounded-xl overflow-hidden h-24 relative shadow-inner border border-[#c3c6d6]/20">
                          <img
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDtrE-Ry8vEALfcvXhSMT-lA_uafslC_ujYxK6Wz84KWm79CvJTNk4A2-qOn2MnwHkYHM1XVYQMTTRLLBt6WQvpJCOwAoLh6wiIPWk8x-7QkoxmY8nQkUdYIZd5O-sDYMdMAeqfF7Yl-7S6qu1HzTZptabK9MrbCcM3ZxJjeU_Ki_3GH3Pd-E2CxoDwVT10eQxFgitcK3aAC5qpHlBQrRVjB1AdnvLdTcb0-UK91uSW41dpVFp93lfPZJfeK6vnBAtKSAV4oUuqykrG"
                            alt="Clean textile closeup"
                            className="w-full h-full object-cover"
                          />
                          <div className="absolute inset-0 bg-[#003d9b]/40 backdrop-blur-xs flex items-center justify-center">
                            <p className="text-white font-bold text-xs tracking-wide">
                              🛡️ Jaminan Kebersihan Higienis 100%
                            </p>
                          </div>
                        </div>

                        {/* Submit Actions buttons */}
                        <div className="pt-2 space-y-2">
                          <button
                            onClick={handlePlaceOrder}
                            disabled={isPlacingOrder}
                            className="w-full py-3.5 bg-[#003d9b] hover:bg-[#0052cc] disabled:opacity-60 text-white font-bold rounded-xl transition-all shadow-md active:scale-98 flex items-center justify-center gap-2 text-sm"
                          >
                            <ShoppingCart className="w-4.5 h-4.5" />
                            <span>{isPlacingOrder ? 'Memproses...' : 'Bayar & Buat Pesanan'}</span>
                          </button>
                          <button
                            onClick={() => {
                              showToast('Pesanan dibatalkan.');
                              setActiveTab('dashboard');
                            }}
                            className="w-full py-3 border border-[#c3c6d6] text-[#ba1a1a] hover:bg-[#ffdad6]/20 font-bold rounded-xl transition-all text-xs"
                          >
                            Batalkan
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Bottom Process Guide visualization */}
                <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 shadow-sm">
                  <h4 className="text-xs font-bold text-[#434654] uppercase tracking-widest mb-6">
                    Alur Kerja Penjemputan &amp; Pengolahan
                  </h4>
                  <div className="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    {[
                      { step: '1', title: 'Jemput Kurir', desc: 'Kurir datang membawa timbangan digital', icon: Truck },
                      { step: '2', title: 'Penyortiran', desc: 'Pakaian dipisah sesuai instruksi Anda', icon: Sparkles },
                      { step: '3', title: 'Pencucian', desc: 'Dicuci higienis memakai air steril', icon: CheckCircle2 },
                      { step: '4', title: 'Penyetrikaan', desc: 'Disetrika uap rapi antikuman', icon: Shirt },
                      { step: '5', title: 'Pengantaran', desc: 'Diantar harum wangi ke alamat Anda', icon: CheckCircle2 },
                    ].map((stepItem, idx) => (
                      <div key={idx} className="bg-[#f9f9ff] border border-[#c3c6d6]/40 p-4 rounded-xl space-y-2">
                        <div className="flex justify-between items-center">
                          <div className="w-8 h-8 rounded-full bg-[#dae2ff] text-[#003d9b] flex items-center justify-center font-bold text-xs">
                            {stepItem.step}
                          </div>
                          <stepItem.icon className="w-4 h-4 text-[#434654]" />
                        </div>
                        <h5 className="font-bold text-xs text-[#091c35] pt-1">{stepItem.title}</h5>
                        <p className="text-[10px] text-[#434654] leading-relaxed">{stepItem.desc}</p>
                      </div>
                    ))}
                  </div>
                </div>
              </motion.div>
            )}

            {/* VIEW 3: MY ORDERS & ORDER DETAIL PAGE */}
            {activeTab === 'my-orders' && (
              <motion.div
                key="my-orders"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                className="space-y-8"
              >
                {selectedOrderId ? (
                  /* ORDER DETAIL CHILD SCREEN */
                  <div className="space-y-8">
                    {/* Header Breadcrumbs and quick action triggers */}
                    <div className="flex flex-col sm:flex-row justify-between sm:items-end gap-4">
                      <div>
                        <div className="flex items-center gap-2 text-[#434654] mb-2 text-xs font-semibold">
                          <button
                            onClick={() => setSelectedOrderId(null)}
                            className="hover:text-[#003d9b] hover:underline"
                          >
                            Pesanan Saya
                          </button>
                          <ChevronRight className="w-3 h-3 text-[#c3c6d6]" />
                          <span className="text-[#003d9b] font-mono">{selectedOrder.id}</span>
                        </div>
                        <h2 className="font-display text-3xl font-extrabold text-[#091c35] tracking-tight">
                          Detail Pelacakan Pesanan
                        </h2>
                        <p className="text-[#434654] font-medium text-xs mt-1">
                          Dibuat pada {selectedOrder.orderDate} • {selectedOrder.orderTime}
                        </p>
                      </div>
                      
                      <div className="flex gap-2 flex-wrap">
                        <button
                          onClick={() => showToast('Simulasi: Kwitansi PDF berhasil diunduh ke perangkat Anda.')}
                          className="flex items-center gap-1.5 px-4 py-2.5 border border-[#c3c6d6] text-[#003d9b] font-bold text-xs rounded-xl hover:bg-[#f0f3ff] transition-all"
                        >
                          <Download className="w-4 h-4" />
                          <span>Unduh Kwitansi</span>
                        </button>
                        <button
                          onClick={() =>
                            showToast(`Menghubungkan ke Customer Support Laundry untuk pesanan ${selectedOrder.id}...`)
                          }
                          className="flex items-center gap-1.5 px-4 py-2.5 bg-[#003d9b] hover:bg-[#0052cc] text-white font-bold text-xs rounded-xl shadow-md transition-all"
                        >
                          <MessageSquare className="w-4 h-4 text-[#6ff7ee]" />
                          <span>Hubungi Laundry</span>
                        </button>
                      </div>
                    </div>

                    {/* Stepper tracking progress bar (high-density layout) */}
                    <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 sm:p-8 shadow-sm">
                      <div className="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-[#c3c6d6]/30 pb-4 mb-6">
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 bg-[#6ff7ee]/10 text-[#006a65] rounded-full flex items-center justify-center">
                            <Sparkles className="w-5 h-5 animate-spin" style={{ animationDuration: '8s' }} />
                          </div>
                          <div>
                            <h3 className="font-bold text-sm text-[#091c35]">Progres Langsung</h3>
                            <p className="text-xs text-[#434654] mt-0.5">
                              Estimasi Selesai: {selectedOrder.readyTimeLabel}
                            </p>
                          </div>
                        </div>
                        <div className="bg-[#6ff7ee]/20 text-[#006a65] px-4 py-1.5 rounded-full font-bold text-xs flex items-center gap-1.5 w-fit">
                          <span className="w-2.5 h-2.5 bg-[#006a65] rounded-full animate-ping"></span>
                          <span>{selectedOrder.statusLabelIndo}</span>
                        </div>
                      </div>

                      {/* Expanded 10-step horizontal stepper milestone tracker */}
                      <div className="overflow-x-auto pb-4">
                        <div className="min-w-[1000px] relative flex justify-between items-center px-4 pt-4">
                          {/* Stepper track background */}
                          <div className="absolute top-[32px] left-8 right-8 h-1 bg-[#c3c6d6]/20 -z-0"></div>
                          
                          {/* Stepper active track line */}
                          <div
                            className="absolute top-[32px] left-8 h-1 bg-[#003d9b] -z-0 transition-all duration-1000"
                            style={{ width: `${getProgressPercent(selectedOrder.status)}%` }}
                          ></div>

                          {[
                            { label: 'Konfirmasi', id: 'Konfirmasi', icon: CheckCircle2 },
                            { label: 'Menunggu Jemput', id: 'Menunggu Jemput', icon: Clock },
                            { label: 'Dalam Perjalanan', id: 'Dalam Perjalanan', icon: Truck },
                            { label: 'Dijemput', id: 'Dijemput', icon: User },
                            { label: 'Pencucian', id: 'Pencucian', icon: Sparkles },
                            { label: 'Pengeringan', id: 'Pengeringan', icon: CheckCircle2 },
                            { label: 'Penyetrikaan', id: 'Penyetrikaan', icon: Shirt },
                            { label: 'Siap', id: 'Siap', icon: Layers },
                            { label: 'Pengiriman', id: 'Pengiriman', icon: Truck },
                            { label: 'Selesai', id: 'Selesai', icon: CheckCircle2 },
                          ].map((step, idx) => {
                            const steps: OrderStatus[] = [
                              'Konfirmasi',
                              'Menunggu Jemput',
                              'Dalam Perjalanan',
                              'Dijemput',
                              'Pencucian',
                              'Pengeringan',
                              'Penyetrikaan',
                              'Siap',
                              'Pengiriman',
                              'Selesai',
                            ];
                            const currentIdx = steps.indexOf(selectedOrder.status);
                            const isCompleted = idx <= currentIdx;
                            const isActive = idx === currentIdx;

                            return (
                              <div key={idx} className="relative z-10 flex flex-col items-center gap-2">
                                <div
                                  className={`w-9 h-9 rounded-full flex items-center justify-center transition-all ${
                                    isActive
                                      ? 'bg-[#003d9b] text-white ring-4 ring-[#dae2ff] scale-110'
                                      : isCompleted
                                      ? 'bg-[#006a65] text-white'
                                      : 'bg-white border border-[#c3c6d6] text-[#434654]'
                                  }`}
                                >
                                  {isCompleted && !isActive ? (
                                    <Check className="w-4 h-4" />
                                  ) : (
                                    <step.icon className="w-4 h-4" />
                                  )}
                                </div>
                                <span
                                  className={`text-[9px] uppercase font-extrabold text-center leading-tight max-w-[80px] ${
                                    isActive ? 'text-[#003d9b]' : isCompleted ? 'text-[#006a65]' : 'text-[#434654]'
                                  }`}
                                >
                                  {step.label}
                                </span>
                              </div>
                            );
                          })}
                        </div>
                      </div>
                    </div>

                    {/* Split details layout: Live Map Card & Billing Tagihan info */}
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                      {/* Left: Animated courier tracking map container */}
                      <div className="lg:col-span-8 bg-white border border-[#c3c6d6]/60 rounded-2xl overflow-hidden flex flex-col h-[460px] shadow-sm">
                        <div className="p-4 sm:p-6 border-b border-[#c3c6d6]/30 flex justify-between items-center bg-[#f9f9ff]">
                          <div className="flex items-center gap-2.5">
                            <MapIcon className="w-5 h-5 text-[#003d9b]" />
                            <h3 className="font-display font-extrabold text-[#091c35] text-sm sm:text-base">
                              Peta Pelacakan Kurir Penjemputan
                            </h3>
                          </div>
                          <div className="text-right">
                            <p className="text-xs font-bold text-[#091c35]">
                              Kurir: {selectedOrder.kurirName || 'Budi Santoso'}
                            </p>
                            <p className="text-[10px] text-[#006a65] font-extrabold uppercase tracking-wide">
                              {selectedOrder.kurirEtaMinutes || '5'} Menit Menuju Alamat Anda
                            </p>
                          </div>
                        </div>

                        {/* Map Canvas with animated mock paths */}
                        <div className="flex-grow bg-[#e7eeff] relative overflow-hidden">
                          {/* Stylized streets background mock */}
                          <img
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjfMfi3jdwo6FIhBAKHqlDLWfsj1OcoJj4B4JqdHptLEHpu_OWTK2qPTQcR_ush6AJFeATkyDzql1XKYkNAf2YHq5Kv9ohz4IzTUgjQ8ndS-RPZ8-zHinsRfxQ10K4gMMOCqzL1q3eI2DzM-wm67oUdt6qcNiwd_v-HV8YGZwcFiXxFJDFqU6huGiNUj5FRjLcws9PCS9_J67lBx2Ch0wURegOBhSFmn6cOdSrK0xbkhvV1KCXmiAwPuA-XUJGNB8sUjcmlP-x2UJQ"
                            alt="Jakarta city map representation"
                            className="w-full h-full object-cover opacity-80"
                          />

                          {/* Customer House Marker (Destination) */}
                          <div className="absolute top-[28%] right-[25%] -translate-x-1/2 -translate-y-1/2 text-center z-15">
                            <div className="bg-[#ba1a1a] p-1.5 rounded-full shadow-lg border-2 border-white animate-pulse inline-block">
                              <MapPin className="w-5 h-5 text-white" />
                            </div>
                            <div className="bg-white/95 px-2.5 py-1 rounded shadow border border-[#c3c6d6] text-[9px] font-bold text-[#091c35] mt-1 whitespace-nowrap">
                              Apartemen Anda
                            </div>
                          </div>

                          {/* Dynamic Courier Motorcycle Marker */}
                          <div
                            className="absolute z-20 -translate-x-1/2 -translate-y-1/2 text-center transition-all duration-[2500ms] ease-linear"
                            style={{ left: `${courierPosition.x}%`, top: `${courierPosition.y}%` }}
                          >
                            <div className="bg-[#003d9b] p-2 rounded-full shadow-xl border-2 border-white inline-block">
                              <Truck className="w-5 h-5 text-white" />
                            </div>
                            <div className="bg-[#003d9b] text-white px-2.5 py-1 rounded shadow-md border border-white/20 text-[9px] font-extrabold mt-1 whitespace-nowrap uppercase tracking-wider">
                              {selectedOrder.kurirName || 'Budi'} (Kurir)
                            </div>
                          </div>
                        </div>
                      </div>

                      {/* Right: Tagihan Billing and Logistics summary cards */}
                      <div className="lg:col-span-4 flex flex-col gap-6">
                        {/* Billing status card */}
                        <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 shadow-sm">
                          <div className="flex justify-between items-start mb-4">
                            <h4 className="font-display font-extrabold text-sm text-[#091c35]">
                              Informasi Tagihan
                            </h4>
                            <span
                              className={`px-3 py-1 rounded text-[10px] font-bold ${
                                selectedOrder.paymentStatus === 'LUNAS'
                                  ? 'bg-[#6ff7ee]/15 text-[#006a65]'
                                  : 'bg-[#ffdad6] text-[#ba1a1a]'
                              }`}
                            >
                              {selectedOrder.paymentStatus}
                            </span>
                          </div>

                          <div className="space-y-3 border-b border-[#c3c6d6]/30 pb-4 text-xs">
                            <div className="flex justify-between text-[#434654]">
                              <span>{selectedOrder.serviceName} ({selectedOrder.weight} kg)</span>
                              <span className="font-bold text-[#091c35]">
                                Rp {selectedOrder.items[0].subtotal.toLocaleString('id-ID')}
                              </span>
                            </div>
                            {selectedOrder.discount > 0 && (
                              <div className="flex justify-between text-[#006a65]">
                                <span>Kupon Diskon Applied:</span>
                                <span className="font-bold">- Rp {selectedOrder.discount.toLocaleString('id-ID')}</span>
                              </div>
                            )}
                            <div className="flex justify-between text-[#434654]">
                              <span>Biaya Tambahan Logistik:</span>
                              <span className="font-bold text-[#091c35]">
                                Rp {Math.max(0, selectedOrder.totalPrice - selectedOrder.items[0].subtotal - selectedOrder.platformFee + selectedOrder.discount).toLocaleString('id-ID')}
                              </span>
                            </div>
                            <div className="flex justify-between text-[#434654]">
                              <span>Biaya Platform:</span>
                              <span className="font-bold text-[#091c35]">
                                Rp {selectedOrder.platformFee.toLocaleString('id-ID')}
                              </span>
                            </div>
                          </div>

                          <div className="flex justify-between items-center pt-3">
                            <span className="text-xs font-bold text-[#434654]">Total Tagihan</span>
                            <span className="font-display text-lg font-extrabold text-[#003d9b]">
                              Rp {selectedOrder.totalPrice.toLocaleString('id-ID')}
                            </span>
                          </div>
                          <p className="text-[9px] text-[#434654] text-right mt-2 font-semibold">
                            Metode: {selectedOrder.paymentMethod} • Status Pembayaran Real-time
                          </p>
                        </div>

                        {/* Logistics route overview details */}
                        <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 shadow-sm flex-grow">
                          <h4 className="font-display font-extrabold text-sm text-[#091c35] mb-4">
                            Detail Logistik
                          </h4>
                          <div className="space-y-4 text-xs">
                            <div className="flex gap-3">
                              <div className="w-8 h-8 rounded-lg bg-[#dae2ff] text-[#003d9b] flex items-center justify-center shrink-0">
                                <MapPin className="w-4 h-4" />
                              </div>
                              <div>
                                <p className="text-[10px] font-bold text-[#434654] uppercase tracking-wider">
                                  Alamat Penjemputan
                                </p>
                                <p className="text-[#091c35] font-medium mt-1 leading-relaxed">
                                  {selectedOrder.pickupAddress}
                                </p>
                              </div>
                            </div>

                            <div className="flex gap-3">
                              <div className="w-8 h-8 rounded-lg bg-[#6ff7ee]/10 text-[#006a65] flex items-center justify-center shrink-0">
                                <Truck className="w-4 h-4" />
                              </div>
                              <div>
                                <p className="text-[10px] font-bold text-[#434654] uppercase tracking-wider">
                                  Alamat Pengantaran
                                </p>
                                <p className="text-[#091c35] font-medium mt-1 leading-relaxed">
                                  {selectedOrder.deliveryAddress}
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    {/* Order Items List Table (Detailed) */}
                    <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl overflow-hidden shadow-sm">
                      <div className="px-6 py-4 border-b border-[#c3c6d6]/30 flex justify-between items-center bg-[#f9f9ff]">
                        <h3 className="font-display font-extrabold text-base text-[#091c35]">
                          Rincian Cucian Masuk ({selectedOrder.weight} KG Total)
                        </h3>
                        <span className="font-mono text-xs text-[#003d9b] bg-[#dae2ff] px-2.5 py-1 rounded-lg font-bold">
                          {selectedOrder.items.length} Kelompok Pakaian
                        </span>
                      </div>

                      <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                          <thead className="bg-[#f0f3ff]/50 text-[10px] font-bold text-[#434654] uppercase tracking-widest border-b border-[#c3c6d6]/30">
                            <tr>
                              <th className="px-6 py-3.5">Nama Item / Kelompok</th>
                              <th className="px-6 py-3.5">Jumlah / Berat</th>
                              <th className="px-6 py-3.5">Perawatan Khusus</th>
                              <th className="px-6 py-3.5 text-right">Harga Satuan</th>
                              <th className="px-6 py-3.5 text-right">Subtotal</th>
                            </tr>
                          </thead>
                          <tbody className="divide-y divide-[#c3c6d6]/25 text-xs text-[#091c35]">
                            {selectedOrder.items.map((item) => (
                              <tr key={item.id} className="hover:bg-[#f9f9ff] transition-colors">
                                <td className="px-6 py-4">
                                  <div className="flex items-center gap-3">
                                    <div className="w-12 h-12 bg-[#dae2ff]/30 rounded-lg overflow-hidden border border-[#c3c6d6]/40 shrink-0">
                                      <img
                                        src={item.imageUrl}
                                        alt={item.name}
                                        className="w-full h-full object-cover"
                                      />
                                    </div>
                                    <div>
                                      <p className="font-bold text-[#091c35] text-sm">{item.name}</p>
                                      <p className="text-[10px] text-[#434654] font-medium mt-0.5">{item.type}</p>
                                    </div>
                                  </div>
                                </td>
                                <td className="px-6 py-4 font-mono font-bold">{item.quantity}</td>
                                <td className="px-6 py-4">
                                  <span className="px-2 py-0.5 bg-[#003d9b]/10 text-[#003d9b] rounded-md text-[9px] font-extrabold uppercase tracking-wide">
                                    {item.treatment}
                                  </span>
                                </td>
                                <td className="px-6 py-4 text-right font-mono">{item.priceUnit}</td>
                                <td className="px-6 py-4 text-right font-mono font-extrabold">
                                  Rp {item.subtotal.toLocaleString('id-ID')}
                                </td>
                              </tr>
                            ))}
                          </tbody>
                        </table>
                      </div>
                    </div>

                    {/* Back to Orders list button */}
                    <div className="pt-4">
                      <button
                        onClick={() => setSelectedOrderId(null)}
                        className="px-6 py-3 border border-[#c3c6d6] text-[#091c35] font-bold text-xs rounded-xl hover:bg-[#f0f3ff] transition-all"
                      >
                        Kembali ke Daftar Pesanan
                      </button>
                    </div>
                  </div>
                ) : (
                  /* ORDERS LIST SCREEN */
                  <div className="space-y-6">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                      <div>
                        <h2 className="font-display text-3xl font-extrabold text-[#091c35] tracking-tight">
                          Daftar Pesanan Saya
                        </h2>
                        <p className="text-[#434654] font-medium text-sm mt-1">
                          Pantau status cucian, tagihan, dan kurir pengantaran secara real-time.
                        </p>
                      </div>

                      {/* List Filters */}
                      <div className="flex bg-[#f0f3ff] p-1 rounded-xl border border-[#c3c6d6]/40 text-xs font-bold gap-1 self-start">
                        {['Semua', 'Aktif', 'Selesai'].map((flt) => (
                          <button
                            key={flt}
                            onClick={() => setOrdersFilter(flt as any)}
                            className={`px-4 py-2 rounded-lg transition-all ${
                              ordersFilter === flt
                                ? 'bg-[#003d9b] text-white shadow'
                                : 'text-[#434654] hover:text-[#003d9b]'
                            }`}
                          >
                            {flt}
                          </button>
                        ))}
                      </div>
                    </div>

                    {/* Orders listing grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      {filteredOrdersList.length === 0 ? (
                        <div className="col-span-2 bg-white border border-[#c3c6d6]/60 rounded-2xl p-12 text-center text-sm text-[#434654] space-y-3">
                          <AlertCircle className="w-12 h-12 text-[#434654] mx-auto opacity-40 animate-bounce" />
                          <p className="font-bold">Tidak ada pesanan ditemukan.</p>
                          <p className="text-xs">Ubah filter kata kunci atau buat pesanan baru untuk ditambahkan.</p>
                        </div>
                      ) : (
                        filteredOrdersList.map((ord) => (
                          <div
                            key={ord.id}
                            onClick={() => setSelectedOrderId(ord.id)}
                            className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 hover:border-[#003d9b] hover:shadow-md transition-all cursor-pointer flex flex-col justify-between gap-4 group"
                          >
                            <div className="flex justify-between items-start">
                              <div>
                                <span className="font-mono font-extrabold text-[#003d9b] text-sm group-hover:underline">
                                  {ord.id}
                                </span>
                                <h4 className="font-bold text-[#091c35] text-sm mt-1">{ord.serviceName}</h4>
                                <p className="text-[10px] text-[#434654] font-medium mt-0.5">
                                  Dipesan: {ord.orderDate} • {ord.weight} kg
                                </p>
                              </div>
                              <span
                                className={`px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wide ${
                                  ord.status === 'Selesai'
                                    ? 'bg-[#6ff7ee]/15 text-[#006a65]'
                                    : 'bg-[#dae2ff] text-[#003d9b]'
                                }`}
                              >
                                {ord.statusLabelIndo}
                              </span>
                            </div>

                            <div className="flex justify-between items-end border-t border-[#c3c6d6]/20 pt-4 mt-2">
                              <div>
                                <p className="text-[10px] text-[#434654] uppercase tracking-wider font-bold">
                                  Estimasi Selesai
                                </p>
                                <p className="text-xs text-[#091c35] font-semibold mt-0.5">
                                  {ord.readyTimeLabel}
                                </p>
                              </div>
                              <div className="text-right">
                                <p className="text-[10px] text-[#434654] uppercase tracking-wider font-bold">Total</p>
                                <p className="text-sm font-extrabold text-[#003d9b] mt-0.5">
                                  Rp {ord.totalPrice.toLocaleString('id-ID')}
                                </p>
                              </div>
                            </div>
                          </div>
                        ))
                      )}
                    </div>
                  </div>
                )}
              </motion.div>
            )}

            {/* VIEW 4: PROFILE & TRANSACTION DETAILS */}
            {activeTab === 'profile' && (
              <motion.div
                key="profile"
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -15 }}
                className="space-y-8"
              >
                {/* Section Header */}
                <div className="flex flex-col sm:flex-row justify-between sm:items-end gap-4 border-b border-[#c3c6d6]/30 pb-4">
                  <div>
                    <h2 className="font-display text-3xl font-extrabold text-[#091c35] tracking-tight">
                      Informasi Profil Customer
                    </h2>
                    <p className="text-[#434654] font-medium text-sm mt-1">
                      Kelola informasi alamat pengantaran, password, dan pantau program keanggotaan Anda.
                    </p>
                  </div>
                  <div className="flex gap-2">
                    <button
                      onClick={() => setIsChangePasswordModalOpen(true)}
                      className="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-[#c3c6d6] text-[#003d9b] font-bold text-xs hover:bg-[#f0f3ff] transition-all"
                    >
                      <Lock className="w-4 h-4" />
                      <span>Ganti Password</span>
                    </button>
                    <button
                      onClick={() => setIsEditProfileModalOpen(true)}
                      className="flex items-center gap-1.5 px-4 py-2 bg-[#003d9b] hover:bg-[#0052cc] text-white font-bold text-xs rounded-xl shadow transition-all"
                    >
                      <User className="w-4 h-4 text-[#6ff7ee]" />
                      <span>Edit Profil</span>
                    </button>
                  </div>
                </div>

                {/* Profile Detail bento grid */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                  {/* Left Column: Personal info card */}
                  <div className="lg:col-span-8 bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row gap-8">
                    {/* Avatar box */}
                    <div className="relative shrink-0 mx-auto sm:mx-0">
                      <div className="w-32 h-32 rounded-2xl bg-[#dae2ff] border-4 border-[#f0f3ff] shadow-inner flex items-center justify-center text-5xl font-extrabold text-[#003d9b]">
                        {profile.name.charAt(0)}
                      </div>
                      <button
                        onClick={() => showToast('Foto profil dapat diubah dengan mengunggah gambar.')}
                        className="absolute bottom-[-8px] right-[-8px] bg-white border border-[#c3c6d6] text-[#003d9b] p-2 rounded-full shadow hover:scale-105 transition-transform"
                      >
                        <User className="w-4 h-4" />
                      </button>
                    </div>

                    {/* Input fields pre-populated display */}
                    <div className="flex-grow space-y-4">
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                          <span className="text-[10px] font-bold text-[#434654] uppercase tracking-wider block">
                            Nama Lengkap
                          </span>
                          <span className="font-bold text-[#091c35] text-lg block pt-1">{profile.name}</span>
                        </div>
                        <div>
                          <span className="text-[10px] font-bold text-[#434654] uppercase tracking-wider block">
                            Member ID
                          </span>
                          <span className="font-mono font-extrabold text-[#003d9b] text-base block pt-1">
                            {profile.memberId}
                          </span>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                          <span className="text-[10px] font-bold text-[#434654] uppercase tracking-wider block">
                            Alamat Email
                          </span>
                          <span className="font-semibold text-[#091c35] text-sm block pt-1">{profile.email}</span>
                        </div>
                        <div>
                          <span className="text-[10px] font-bold text-[#434654] uppercase tracking-wider block">
                            Nomor Telepon
                          </span>
                          <span className="font-semibold text-[#091c35] text-sm block pt-1">{profile.phone}</span>
                        </div>
                      </div>

                      {/* Saved addresses details display */}
                      <div className="pt-4 border-t border-[#c3c6d6]/20">
                        <span className="text-[10px] font-bold text-[#434654] uppercase tracking-wider block mb-2">
                          Alamat Utama Terdaftar
                        </span>
                        <div className="flex items-start gap-2.5 bg-[#f9f9ff] p-4 rounded-xl border border-[#c3c6d6]/40">
                          <MapPin className="w-4 h-4 text-[#003d9b] shrink-0 mt-0.5" />
                          <div>
                            <p className="font-bold text-xs text-[#091c35]">{profile.savedAddressLabel}</p>
                            <p className="text-xs text-[#434654] leading-relaxed mt-1">
                              {profile.savedAddressDetails}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Right Column: Statistics overview widgets */}
                  <div className="lg:col-span-4 space-y-6">
                    {/* Stat card 1 */}
                    <div className="bg-gradient-to-br from-[#003d9b] to-[#0052cc] text-white rounded-2xl p-6 shadow-md relative overflow-hidden group">
                      <div className="absolute right-[-20px] bottom-[-20px] opacity-10">
                        <ShoppingCart className="w-32 h-32" />
                      </div>
                      <p className="text-[#c4d2ff] text-[10px] font-bold uppercase tracking-wider">
                        Riwayat Pemesanan
                      </p>
                      <h4 className="font-display text-3xl font-extrabold text-white mt-2">
                        {profile.totalOrders} <span className="text-xs font-medium text-[#c4d2ff]">Orders</span>
                      </h4>
                      <p className="text-[10px] text-[#6ff7ee] font-semibold mt-4 flex items-center gap-1">
                        <TrendingUp className="w-3.5 h-3.5" /> +2 Orders ditambahkan bulan ini
                      </p>
                    </div>

                    {/* Stat card 2 */}
                    <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl p-6 shadow-sm">
                      <p className="text-[#434654] text-[10px] font-bold uppercase tracking-wider">
                        Total Pembayaran Sukses
                      </p>
                      <h4 className="font-display text-2xl font-extrabold text-[#003d9b] mt-2">
                        Rp {profile.totalSpending.toLocaleString('id-ID')}
                      </h4>
                      <p className="text-[10px] text-[#006a65] font-semibold mt-4">
                        Estimasi penghematan member premium: Rp 45.000
                      </p>
                    </div>
                  </div>
                </div>

                {/* Detailed transactions list history table */}
                <div className="bg-white border border-[#c3c6d6]/60 rounded-2xl overflow-hidden shadow-sm">
                  <div className="px-6 py-4 border-b border-[#c3c6d6]/30 flex justify-between items-center bg-[#f9f9ff]">
                    <h3 className="font-display font-extrabold text-base text-[#091c35]">
                      Riwayat Transaksi Terakhir
                    </h3>
                    <button
                      onClick={() => {
                        setOrdersFilter('Selesai');
                        setActiveTab('my-orders');
                      }}
                      className="text-[#003d9b] font-bold text-xs hover:underline flex items-center gap-1"
                    >
                      Lihat Semua Riwayat <ArrowRight className="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                      <thead className="bg-[#f0f3ff]/50 text-[10px] font-bold text-[#434654] uppercase tracking-wider">
                        <tr>
                          <th className="px-6 py-3">ID Transaksi</th>
                          <th className="px-6 py-3">Tanggal Waktu</th>
                          <th className="px-6 py-3">Paket Laundry</th>
                          <th className="px-6 py-3">Status</th>
                          <th className="px-6 py-3 text-right">Total Biaya</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-[#c3c6d6]/25 text-xs text-[#091c35]">
                        {orders.map((ord) => (
                          <tr key={ord.id} className="hover:bg-[#f9f9ff] transition-colors">
                            <td className="px-6 py-4 font-mono font-bold text-[#003d9b]">{ord.id}</td>
                            <td className="px-6 py-4 text-[#434654]">
                              {ord.orderDate}, {ord.orderTime}
                            </td>
                            <td className="px-6 py-4 font-semibold">{ord.serviceName}</td>
                            <td className="px-6 py-4">
                              <span className="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-[#6ff7ee]/10 text-[#006a65]">
                                {ord.statusLabelIndo}
                              </span>
                            </td>
                            <td className="px-6 py-4 text-right font-mono font-bold">
                              Rp {ord.totalPrice.toLocaleString('id-ID')}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>

                {/* Loyalty Tier Progress Section */}
                <div className="bg-[#dae2ff]/30 border border-[#c3c6d6]/40 rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row items-center gap-6 sm:gap-10">
                  <div className="flex-grow space-y-2 text-center md:text-left">
                    <h3 className="font-display font-extrabold text-lg text-[#091c35]">
                      Tingkatkan Tingkatan Keanggotaan Anda!
                    </h3>
                    <p className="text-xs text-[#434654] leading-relaxed max-w-2xl">
                      Dapatkan gratis antar jemput premium dan diskon tambahan 15% untuk layanan kilat jika total pengeluaran Anda mencapai <span className="font-bold text-[#003d9b]">Rp 1.000.000</span>. Pantau terus pesanan Anda!
                    </p>
                    <div className="w-full bg-[#c3c6d6]/40 h-3 rounded-full mt-4 overflow-hidden relative">
                      <div
                        className="bg-[#003d9b] h-full transition-all duration-1000"
                        style={{ width: `${Math.round((profile.totalSpending / profile.targetSpending) * 100)}%` }}
                      ></div>
                    </div>
                    <p className="text-[11px] font-semibold text-[#434654] pt-1">
                      Rp {profile.totalSpending.toLocaleString('id-ID')} / Rp {profile.targetSpending.toLocaleString('id-ID')} untuk naik ke Voucher Gold Member
                    </p>
                  </div>
                  <div className="shrink-0 flex items-center justify-center w-24 h-24 rounded-full bg-[#dae2ff] text-[#003d9b] relative shadow-lg ring-4 ring-white">
                    <Award className="w-12 h-12 text-[#003d9b] animate-bounce" />
                    <span className="absolute top-0 right-0 bg-[#006a65] text-[#6ff7ee] text-[8px] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider shadow">
                      Elite
                    </span>
                  </div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </main>
      </div>

      {/* MODAL 1: SUCCESS PLACE ORDER OVERLAY */}
      <AnimatePresence>
        {isSuccessModalOpen && (
          <div className="fixed inset-0 bg-[#091c35]/60 backdrop-blur-xs z-[100] flex items-center justify-center p-4">
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white p-6 sm:p-8 rounded-2xl shadow-2xl max-w-sm w-full text-center border border-[#c3c6d6]/60 relative space-y-4"
            >
              <div className="w-16 h-16 bg-[#6ff7ee]/25 text-[#006a65] rounded-full flex items-center justify-center mx-auto mb-2 shadow-inner">
                <CheckCircle2 className="w-10 h-10 text-[#006a65]" />
              </div>
              <h4 className="font-display font-extrabold text-xl text-[#091c35]">Pesanan Berhasil Dibuat!</h4>
              <p className="text-xs text-[#434654] leading-relaxed">
                Kurir laundry kami akan segera menjemput pakaian kotor Anda sesuai jadwal yang ditentukan. Anda dapat memantau posisi penjemputan secara real-time di halaman pesanan.
              </p>
              <div className="bg-[#f0f3ff] p-3.5 rounded-xl border border-[#c3c6d6]/20 text-xs font-mono font-bold text-[#003d9b]">
                ID Pesanan: {justCreatedOrderId}
              </div>
              <div className="pt-2">
                <button
                  onClick={handleViewNewOrderDetails}
                  className="w-full py-3 bg-[#003d9b] hover:bg-[#0052cc] text-white font-bold rounded-xl text-sm transition-all shadow-md"
                >
                  Lihat Detail Status
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      {/* MODAL 2: EDIT PROFILE */}
      <AnimatePresence>
        {isEditProfileModalOpen && (
          <div className="fixed inset-0 bg-[#091c35]/60 backdrop-blur-xs z-[100] flex items-center justify-center p-4">
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white p-6 sm:p-8 rounded-2xl shadow-2xl max-w-md w-full border border-[#c3c6d6]/60 space-y-4 relative"
            >
              <button
                onClick={() => setIsEditProfileModalOpen(false)}
                className="absolute top-4 right-4 text-[#434654] hover:text-black"
              >
                <X className="w-5 h-5" />
              </button>

              <h4 className="font-display font-extrabold text-xl text-[#091c35] border-b border-[#c3c6d6]/30 pb-2">
                Edit Informasi Profil
              </h4>

              <form onSubmit={handleSaveProfile} className="space-y-4">
                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-[#434654] uppercase block">Nama Lengkap</label>
                  <input
                    type="text"
                    value={editName}
                    onChange={(e) => setEditName(e.target.value)}
                    className="w-full px-3.5 py-2 border border-[#c3c6d6] rounded-xl focus:ring-1 focus:ring-[#003d9b] focus:outline-none text-xs font-semibold text-[#091c35]"
                    required
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-[#434654] uppercase block">Alamat Email</label>
                  <input
                    type="email"
                    value={editEmail}
                    onChange={(e) => setEditEmail(e.target.value)}
                    className="w-full px-3.5 py-2 border border-[#c3c6d6] rounded-xl focus:ring-1 focus:ring-[#003d9b] focus:outline-none text-xs font-semibold text-[#091c35]"
                    required
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-[#434654] uppercase block">Nomor Telepon</label>
                  <input
                    type="text"
                    value={editPhone}
                    onChange={(e) => setEditPhone(e.target.value)}
                    className="w-full px-3.5 py-2 border border-[#c3c6d6] rounded-xl focus:ring-1 focus:ring-[#003d9b] focus:outline-none text-xs font-semibold text-[#091c35]"
                    required
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-[#434654] uppercase block">Alamat Utama</label>
                  <textarea
                    rows={2}
                    value={editAddress}
                    onChange={(e) => setEditAddress(e.target.value)}
                    className="w-full px-3.5 py-2 border border-[#c3c6d6] rounded-xl focus:ring-1 focus:ring-[#003d9b] focus:outline-none text-xs text-[#091c35]"
                    required
                  />
                </div>

                <div className="pt-2 flex gap-3">
                  <button
                    type="button"
                    onClick={() => setIsEditProfileModalOpen(false)}
                    className="w-1/2 py-2.5 border border-[#c3c6d6] text-[#091c35] font-bold rounded-xl text-xs transition-colors hover:bg-[#f9f9ff]"
                  >
                    Batal
                  </button>
                  <button
                    type="submit"
                    className="w-1/2 py-2.5 bg-[#003d9b] hover:bg-[#0052cc] text-white font-bold rounded-xl text-xs transition-colors"
                  >
                    Simpan Profil
                  </button>
                </div>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      {/* MODAL 3: CHANGE PASSWORD */}
      <AnimatePresence>
        {isChangePasswordModalOpen && (
          <div className="fixed inset-0 bg-[#091c35]/60 backdrop-blur-xs z-[100] flex items-center justify-center p-4">
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white p-6 sm:p-8 rounded-2xl shadow-2xl max-w-sm w-full border border-[#c3c6d6]/60 space-y-4 relative"
            >
              <button
                onClick={() => setIsChangePasswordModalOpen(false)}
                className="absolute top-4 right-4 text-[#434654] hover:text-black"
              >
                <X className="w-5 h-5" />
              </button>

              <h4 className="font-display font-extrabold text-xl text-[#091c35] border-b border-[#c3c6d6]/30 pb-2">
                Ganti Password Akun
              </h4>

              <form onSubmit={handleChangePasswordSubmit} className="space-y-4">
                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-[#434654] uppercase block">Password Lama</label>
                  <input
                    type="password"
                    value={oldPassword}
                    onChange={(e) => setOldPassword(e.target.value)}
                    className="w-full px-3.5 py-2 border border-[#c3c6d6] rounded-xl focus:ring-1 focus:ring-[#003d9b] focus:outline-none text-xs font-semibold text-[#091c35]"
                    required
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-[#434654] uppercase block">Password Baru</label>
                  <input
                    type="password"
                    value={newPassword}
                    onChange={(e) => setNewPassword(e.target.value)}
                    className="w-full px-3.5 py-2 border border-[#c3c6d6] rounded-xl focus:ring-1 focus:ring-[#003d9b] focus:outline-none text-xs font-semibold text-[#091c35]"
                    required
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-[#434654] uppercase block">Konfirmasi Password Baru</label>
                  <input
                    type="password"
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    className="w-full px-3.5 py-2 border border-[#c3c6d6] rounded-xl focus:ring-1 focus:ring-[#003d9b] focus:outline-none text-xs font-semibold text-[#091c35]"
                    required
                  />
                </div>

                <div className="pt-2 flex gap-3">
                  <button
                    type="button"
                    onClick={() => setIsChangePasswordModalOpen(false)}
                    className="w-1/2 py-2.5 border border-[#c3c6d6] text-[#091c35] font-bold rounded-xl text-xs transition-colors hover:bg-[#f9f9ff]"
                  >
                    Batal
                  </button>
                  <button
                    type="submit"
                    className="w-1/2 py-2.5 bg-[#003d9b] hover:bg-[#0052cc] text-white font-bold rounded-xl text-xs transition-colors"
                  >
                    Perbarui Password
                  </button>
                </div>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}
