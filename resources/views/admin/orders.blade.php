@extends('layouts.admin')

@section('title', '- Pesanan')
@section('page-title', 'Pesanan')

@php
    $statusBadge = function ($status) {
        return match ($status) {
            'Menunggu' => 'bg-amber-50 text-amber-600 border border-amber-100',
            'Diproses' => 'bg-blue-50 text-blue-600 border border-blue-100',
            'Dicuci', 'Dikeringkan' => 'bg-cyan-50 text-cyan-600 border border-cyan-100',
            'Disetrika' => 'bg-indigo-50 text-indigo-600 border border-indigo-100',
            'Diantar' => 'bg-[#6ff7ee]/20 text-[#00716b] border border-[#6ff7ee]/40',
            'Selesai' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
            default => 'bg-slate-50 text-slate-500 border border-slate-100',
        };
    };

    $initials = function (string $name) {
        $parts = array_values(array_filter(explode(' ', trim($name))));
        if (count($parts) === 0) return '?';
        if (count($parts) === 1) return mb_strtoupper(mb_substr($parts[0], 0, 2));
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    };
@endphp

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#003d9b] font-sans">Daftar Pesanan</h2>
            <p class="text-sm text-slate-500 font-medium">Kelola dan pantau seluruh transaksi laundry pelanggan.</p>
        </div>

        <form method="GET" action="{{ route('admin.orders') }}" class="flex gap-2">
            <select name="status" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-lg text-xs font-bold py-2 pl-3 pr-8 focus:ring-[#003d9b]/20 focus:border-[#003d9b] outline-none cursor-pointer">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if (request('status'))
                <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-slate-500 hover:underline self-center">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.orders') }}" class="relative max-w-md">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari ID pesanan, nama pelanggan..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-[#003d9b]/10 focus:border-[#003d9b]"
                >
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">ID Pesanan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Tipe Layanan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Total Harga</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Status Laundry</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Status Pembayaran</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr
                            onclick="window.location='{{ route('admin.orders.show', $order) }}'"
                            class="hover:bg-slate-50 transition-colors cursor-pointer group"
                        >
                            <td class="px-6 py-4 font-mono font-bold text-[#0052cc] text-xs whitespace-nowrap">
                                ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                @if ($order->is_express)
                                    <span class="block text-[9px] font-bold text-amber-600 mt-0.5 uppercase">Ekspres</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#dae2ff] text-[#003d9b] flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ $initials($order->user->name ?? '-') }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-800">{{ $order->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-700 font-medium">{{ $order->service_type }}</span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">{{ $order->weight }} kg &bull; {{ $order->pickup_method }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono font-bold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $statusBadge($order->status) }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php $payStatus = $order->transaction->payment_status ?? 'Belum Bayar'; @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $payStatus === 'Lunas' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $payStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    onclick="event.stopPropagation()"
                                    class="text-[#003d9b] hover:text-[#0052cc] text-xs font-bold hover:underline"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-xs text-slate-400 font-medium">
                                Tidak ada pesanan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
