@extends('layouts.app')
@section('content')
@php
    $steps = ['Menunggu', 'Diproses', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Selesai', 'Diantar'];
    $stepIcons = [
        'Menunggu' => 'shopping_basket',
        'Diproses' => 'hourglass_empty',
        'Dicuci' => 'water_drop',
        'Dikeringkan' => 'air',
        'Disetrika' => 'iron',
        'Selesai' => 'inventory_2',
        'Diantar' => 'local_shipping',
    ];
    $currentIdx = array_search($order->status, $steps);
    $totalBeforeDiscount = $servicePrice + $handlingFee + $pickupFee + $deliveryFee;
@endphp
<div class="bg-[#f9f9ff] text-[#091c35] min-h-screen font-sans flex text-left">
    {{-- SideNavBar --}}
    <aside class="w-[240px] h-screen fixed left-0 top-0 bg-white border-r border-[#c3c6d6] flex flex-col py-6 z-50">
        <div class="px-6 mb-8 text-left">
            <h1 class="font-headline-md text-2xl font-bold text-[#003d9b]">Laundry Yuk!</h1>
            <p class="text-[10px] font-label-md uppercase tracking-wider text-[#737685]">Linen Logic System</p>
        </div>

        <nav class="flex-1 px-3 space-y-1">
            <a href="{{ route('customer.dashboard') }}"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="{{ route('customer.orders.create') }}"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
                <span class="material-symbols-outlined">add_shopping_cart</span>
                <span class="text-sm">Buat Pesanan Baru</span>
            </a>
            <a href="{{ route('customer.dashboard') }}"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg bg-[#dae2ff] text-[#001848] font-semibold text-left transition-all">
                <span class="material-symbols-outlined">local_shipping</span>
                <span class="text-sm">Detail & Tracking</span>
            </a>
            <a href="/customer/profile"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
                <span class="material-symbols-outlined">person</span>
                <span class="text-sm">Profile</span>
            </a>
        </nav>

        <div class="px-3 mt-auto pt-6 border-t border-[#c3c6d6] space-y-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#ba1a1a] hover:bg-red-50 text-left transition-all font-medium">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-sm">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- TopAppBar --}}
    <header class="fixed top-0 right-0 w-[calc(100%-240px)] h-16 bg-white border-b border-[#c3c6d6] flex justify-between items-center px-6 z-40">
        <span class="text-xs font-bold text-[#737685]">Pelanggan Portal &gt; Pelacakan Pesanan</span>
        <div class="flex items-center gap-3">
            <p class="text-xs font-bold text-[#091c35]">{{ $order->user->name }}</p>
        </div>
    </header>

    {{-- Main --}}
    <main class="ml-[240px] pt-20 flex-grow p-6 text-left">
        <div class="max-w-[1440px] mx-auto">
            <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <nav class="flex items-center gap-2 text-[#737685] text-xs mb-2 font-medium">
                        <a href="{{ route('customer.dashboard') }}" class="hover:text-[#003d9b]">Dashboard</a>
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                        <span class="text-[#091c35] font-semibold">Pelacakan Pesanan</span>
                    </nav>
                    <div class="flex items-center gap-4 flex-wrap">
                        <h2 class="font-display text-2xl font-bold text-[#091c35]">Pelacakan Pesanan {{ $displayId }}</h2>
                        </nav>
                        <div class="flex items-center gap-4 flex-wrap">
                            <h2 class="font-display text-2xl font-bold text-[#091c35]">Pelacakan Pesanan {{ $displayId }}</h2>
                            @php
                                $statusColor = match ($order->status) {
                                    'Menunggu' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'Diproses' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Dicuci', 'Dikeringkan' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                    'Disetrika' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'Selesai', 'Diantar' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                                };
                            @endphp
                            <span class="px-3 py-1 {{ $statusColor }} text-xs font-bold rounded-full border uppercase">{{ $order->status }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6">
                {{-- Left side --}}
                <div class="col-span-12 lg:col-span-8 space-y-6">

                    {{-- Stepper --}}
                    <div class="bg-white p-6 rounded-xl border border-[#c3c6d6] shadow-sm text-left">
                        <h3 class="text-xs font-bold text-[#737685] uppercase tracking-wider mb-6">Status Pengerjaan Cucian</h3>
                        <div class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-gray-100">
                            @foreach ($steps as $idx => $step)
                                @php $isDone = $idx <= $currentIdx; $isActive = $idx === $currentIdx; @endphp
                                @if ($isDone)
                                <div class="relative pl-8 text-left">
                                    <div class="absolute left-0 top-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all {{ $isActive ? 'bg-[#dae2ff] border-[#003d9b] ring-4 ring-[#003d9b]/15' : 'bg-green-50 border-green-300' }}">
                                    <span class="material-symbols-outlined text-[12px] {{ $isActive ? 'text-[#003d9b]' : 'text-green-600' }}">{{ $stepIcons[$step] }}</span>
                                </div>
                                    <p class="text-xs font-bold {{ $isActive ? 'text-[#003d9b]' : 'text-[#091c35]' }}">{{ $step }}</p>
                                    <p class="text-[10px] text-[#737685]">
                                        @if ($isActive)
                                            Update terakhir: {{ $order->updated_at->translatedFormat('d M Y, H:i') }}
                                        @else
                                            Selesai
                                        @endif
                                    </p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Ringkasan Pesanan (pengganti manifest per-item) --}}
                    <div class="bg-white rounded-xl border border-[#c3c6d6] overflow-hidden shadow-sm">
                        <div class="p-4 bg-gray-50 border-b border-[#c3c6d6] flex justify-between items-center">
                            <h3 class="text-xs font-bold text-[#737685] uppercase tracking-wider">Ringkasan Pesanan</h3>
                            <span class="text-xs font-bold text-[#003d9b]">Berat: {{ $order->weight }} Kg</span>
                        </div>
                        <div class="p-6 space-y-3 text-xs">
                            <div class="flex justify-between">
                                <span class="text-[#434654]">Jenis Layanan</span>
                                <span class="font-bold text-[#091c35]">{{ $order->service_type }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#434654]">Metode</span>
                                <span class="font-bold text-[#091c35]">{{ $order->pickup_method }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#434654]">Layanan Kilat</span>
                                <span class="font-bold text-[#091c35]">{{ $order->is_express ? 'Ya' : 'Tidak' }}</span>
                            </div>
                            @if ($order->pickupDelivery)
                            <div class="flex justify-between">
                                <span class="text-[#434654]">Alamat Penjemputan</span>
                                <span class="font-bold text-[#091c35] text-right">{{ $order->pickupDelivery->address }}</span>
                            </div>
                            @endif
                            @if ($order->notes)
                            <div class="pt-2 border-t border-[#c3c6d6]">
                                <span class="text-[#434654] block mb-1">Catatan Khusus</span>
                                <span class="text-[#091c35]">{{ $order->notes }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right side --}}
                <div class="col-span-12 lg:col-span-4 space-y-6">
                    <div class="bg-white border border-[#c3c6d6] rounded-xl p-5 shadow-sm text-left">
                        <h3 class="text-xs font-bold text-[#737685] uppercase tracking-wider mb-3">Live Tracking Map</h3>
                        <div class="h-64 w-full bg-[#edf2f8] rounded-lg relative overflow-hidden border border-[#c3c6d6]">
                            <div class="absolute inset-0 opacity-10 bg-[linear-gradient(to_right,#000_1px,transparent_1px),linear-gradient(to_bottom,#000_1px,transparent_1px)] bg-[size:20px_20px]"></div>
                            <div class="absolute left-[30%] top-[30%] flex flex-col items-center">
                                <div class="bg-[#003d9b] text-white p-1.5 rounded-full shadow">
                                    <span class="material-symbols-outlined text-xs">local_laundry_service</span>
                                </div>
                                <span class="text-[8px] font-bold text-[#003d9b] bg-white px-1 py-0.5 rounded shadow mt-1">Depot Pusat</span>
                            </div>
                            <div class="absolute left-[70%] top-[65%] flex flex-col items-center">
                                <div class="bg-yellow-500 text-white p-1.5 rounded-full shadow">
                                    <span class="material-symbols-outlined text-xs">home</span>
                                </div>
                                <span class="text-[8px] font-bold text-[#091c35] bg-white px-1 py-0.5 rounded shadow mt-1">Rumah Anda</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl border border-[#c3c6d6] shadow-sm text-left">
                        <h3 class="text-xs font-bold text-[#737685] uppercase tracking-wider mb-4">Invoice Pembayaran</h3>
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between">
                                <span class="text-[#434654]">Biaya Layanan</span>
                                <span class="font-mono font-semibold">Rp {{ number_format($servicePrice, 0, ',', '.') }}</span>
                            </div>
                            @if ($handlingFee > 0)
                            <div class="flex justify-between">
                                <span class="text-[#434654]">Layanan Ekspres</span>
                                <span class="font-mono font-semibold">Rp {{ number_format($handlingFee, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if ($pickupFee + $deliveryFee > 0)
                            <div class="flex justify-between">
                                <span class="text-[#434654]">Logistik Antar-Jemput</span>
                                <span class="font-mono font-semibold">Rp {{ number_format($pickupFee + $deliveryFee, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="pt-3 border-t border-[#c3c6d6] flex justify-between items-center text-sm font-bold">
                                <span>Total Pembayaran</span>
                                <span class="text-base font-extrabold text-[#003d9b] font-mono">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-4 p-2 bg-[#6ff7ee]/10 border border-[#6ff7ee]/20 rounded text-center text-[10px] font-extrabold text-[#00716b] uppercase tracking-wider">
                            Pembayaran via GoPay / Dana / Tunai saat kurir menjemput
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
