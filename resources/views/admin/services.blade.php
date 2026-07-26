@extends('layouts.admin')

@section('title', '- Layanan')

@section('content')
<div id="services-view" class="space-y-6">

    {{-- Top Header --}}
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#003d9b] font-sans">Daftar Layanan</h2>
            <p class="text-sm text-slate-500 font-medium">Daftar layanan laundry yang tersedia dan status pemantauan kapasitas.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button type="button" class="flex items-center gap-1.5 border border-slate-200 px-4 py-2 rounded-lg bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-[14px]">filter_list</span>
                Filter
            </button>
            <button type="button" class="flex items-center gap-1.5 border border-slate-200 px-4 py-2 rounded-lg bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                Rentang Tanggal
            </button>
            <button type="button" class="flex items-center gap-1.5 border border-slate-200 px-4 py-2 rounded-lg bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-[14px]">download</span>
                Ekspor
            </button>
        </div>
    </section>

    {{-- Quick Filters --}}
    <div class="flex items-center gap-3 overflow-x-auto py-1.5">
        <span class="text-xs font-bold text-slate-400 shrink-0 uppercase tracking-wider">Filter Cepat:</span>
        @php
            $filters = [
                '' => 'Semua',
                'proses' => 'Proses',
                'siap' => 'Siap Diambil',
                'belum' => 'Belum Bayar',
                'mendesak' => 'Mendesak',
            ];
            $activeFilter = request('filter', '');
        @endphp
        @foreach ($filters as $value => $label)
            <a href="{{ route('admin.services', $value ? ['filter' => $value] : []) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ $activeFilter === $value ? 'bg-[#0052cc] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-[#003d9b]' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Orders Table --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ID Pesanan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Pelanggan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe Layanan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Total Harga</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status Pembayaran</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status Laundry</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($orders as $order)
                    @php
                        $paymentStatus = $order->transaction->payment_status ?? 'Belum Bayar';
                        $customerName = $order->user->name ?? '-';
                        $initial = mb_substr($customerName, 0, 1);
                    @endphp
                    <tr onclick="window.location='{{ route('admin.orders.show', $order) }}'" class="hover:bg-slate-50 transition-colors cursor-pointer">
                        <td class="px-6 py-4 font-mono font-bold text-[#0052cc] text-xs">
                            ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#dae2ff] text-[#003d9b] flex items-center justify-center font-bold text-[10px]">
                                    {{ $initial }}
                                </div>
                                <span class="text-xs font-semibold text-slate-800">{{ $customerName }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-medium text-slate-700">{{ $order->service_type }}</span>
                                <span class="text-[10px] text-slate-400 mt-0.5">{{ $order->weight }} kg &bull; Standar</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-mono font-bold text-slate-800">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $paymentStatus === 'Lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                <span class="material-symbols-outlined text-[12px]">{{ $paymentStatus === 'Lunas' ? 'check_circle' : 'error' }}</span>
                                {{ $paymentStatus }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-bold rounded-full uppercase">
                                {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-xs text-slate-400 font-medium">
                            Tidak ada pesanan untuk filter terpilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Grid Bottom: Real-time Live Monitoring + Monthly Goal tracker --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 p-6 bg-[#0052cc]/5 border border-[#0052cc]/15 rounded-xl relative overflow-hidden flex flex-col justify-between shadow-sm">
            <div class="relative z-10">
                <h3 class="text-lg font-bold text-[#003d9b] flex items-center gap-1.5 mb-2">
                    <span class="material-symbols-outlined text-[20px] animate-pulse">local_laundry_service</span>
                    Pemantauan Proses Langsung
                </h3>
                <p class="text-slate-500 text-xs font-medium max-w-lg mb-6 leading-relaxed">
                    Pantau kapasitas operasional outlet Anda secara real-time berdasarkan status pesanan yang berjalan saat ini.
                </p>

                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 flex flex-col items-center p-3.5 bg-white rounded-xl shadow-sm border border-slate-100">
                        <span class="font-mono text-2xl font-black text-[#003d9b]">{{ str_pad($statusCounts['Dicuci'], 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[9px] uppercase font-bold text-slate-400 mt-1">Pencucian</span>
                    </div>
                    <div class="w-6 h-[2px] bg-slate-200"></div>
                    <div class="flex-1 flex flex-col items-center p-3.5 bg-white rounded-xl shadow-sm border border-slate-100">
                        <span class="font-mono text-2xl font-black text-[#00716b]">{{ str_pad($statusCounts['Dikeringkan'], 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[9px] uppercase font-bold text-slate-400 mt-1">Pengeringan</span>
                    </div>
                    <div class="w-6 h-[2px] bg-slate-200"></div>
                    <div class="flex-1 flex flex-col items-center p-3.5 bg-white rounded-xl shadow-sm border border-slate-100">
                        <span class="font-mono text-2xl font-black text-amber-600">{{ str_pad($statusCounts['Disetrika'], 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[9px] uppercase font-bold text-slate-400 mt-1">Penyetrikaan</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 text-white p-6 rounded-xl relative flex flex-col justify-between shadow-lg overflow-hidden">
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <span class="text-[10px] font-bold bg-[#00716b] text-[#6ff7ee] px-3 py-1 rounded-full border border-[#00716b]/35 uppercase tracking-wide">
                        Target Bulanan
                    </span>
                    <span class="material-symbols-outlined text-[#6ff7ee] text-[20px]">trending_up</span>
                </div>

                <div class="mb-4">
                    <h4 class="text-slate-400 text-[10px] uppercase tracking-widest font-bold">Pendapatan Terkumpul</h4>
                    <p class="text-2xl font-black font-sans text-white mt-1">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                </div>

                <div class="w-full bg-slate-800 h-2.5 rounded-full overflow-hidden mt-4">
                    <div class="bg-[#6ff7ee] h-full rounded-full shadow-lg transition-all duration-1000" style="width: {{ $targetPercent }}%"></div>
                </div>
                <p class="text-[11px] mt-2 text-slate-400 font-medium">
                    {{ $targetPercent }}% dari target bulanan (Rp {{ number_format($monthlyTarget, 0, ',', '.') }}) tercapai.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection