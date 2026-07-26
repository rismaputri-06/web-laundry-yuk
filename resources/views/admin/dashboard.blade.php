@extends('layouts.admin')

@section('title', '- Dashboard')

@section('content')
@php
    $width = 600; $height = 200; $padding = 40;
    $chartWidth = $width - $padding * 2;
    $chartHeight = $height - $padding * 2;
    $maxVal = max(1000000, collect($revenueTrend)->max('amount') * 1.15);

    $points = collect($revenueTrend)->values()->map(function ($d, $index) use ($padding, $chartWidth, $chartHeight, $maxVal, $revenueTrend) {
        $x = $padding + ($index * $chartWidth) / max(count($revenueTrend) - 1, 1);
        $y = $padding + $chartHeight - ($d['amount'] * $chartHeight) / $maxVal;
        return ['x' => $x, 'y' => $y, 'day' => $d['day'], 'amount' => $d['amount']];
    });

    $pathD = '';
    if ($points->count() > 0) {
        $pathD = 'M ' . $points[0]['x'] . ' ' . $points[0]['y'];
        for ($i = 0; $i < $points->count() - 1; $i++) {
            $p0 = $points[$i]; $p1 = $points[$i + 1];
            $step = $chartWidth / max($points->count() - 1, 1) / 2;
            $pathD .= " C {$p0['x']} {$p0['y']}, " . ($p1['x'] - $step) . " {$p1['y']}, {$p1['x']} {$p1['y']}";
        }
    }
    $areaD = $pathD ? "{$pathD} L {$points->last()['x']} " . ($height - $padding) . " L {$points->first()['x']} " . ($height - $padding) . ' Z' : '';

    $gridLines = [];
    $step = $maxVal / 3;
    for ($g = 0; $g <= 3; $g++) {
        $gridLines[] = $g * $step;
    }

    $finalStatuses = ['Selesai', 'Diantar'];
    $badgeClass = function ($status) {
        return match ($status) {
            'Menunggu' => 'bg-amber-50 text-amber-600 border border-amber-100',
            'Diproses' => 'bg-blue-50 text-blue-600 border border-blue-100',
            'Dicuci', 'Dikeringkan' => 'bg-cyan-50 text-cyan-600 border border-cyan-100',
            'Disetrika' => 'bg-indigo-50 text-indigo-600 border border-indigo-100',
            'Selesai', 'Diantar' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
            default => 'bg-slate-50 text-slate-500 border border-slate-100',
        };
    };
@endphp

<div class="space-y-6">
    {{-- Top Section --}}
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-bold text-[#003d9b] font-sans">Dashboard</h2>
            <p class="text-sm text-slate-500 font-medium">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex gap-2.5">
            <button type="button" onclick="alert('Fitur ekspor PDF akan segera hadir.')"
                class="flex items-center gap-1.5 px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[14px]">download</span>
                Ekspor PDF
            </button>
            <button type="button" onclick="openNewOrderModal()"
                class="flex items-center gap-1.5 px-4 py-2 bg-[#003d9b] text-white rounded-lg text-xs font-semibold hover:bg-[#0052cc] active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[14px]">add</span>
                Buat Pesanan
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:border-[#003d9b]/30 transition-all">
            <div class="flex justify-between items-start mb-3">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Pesanan Hari Ini</span>
                <div class="bg-[#6ff7ee]/20 p-2 rounded-lg text-[#00716b]">
                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-[#003d9b] font-mono">{{ $stats['ordersToday'] }}</div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:border-[#003d9b]/30 transition-all">
            <div class="flex justify-between items-start mb-3">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pendapatan Bulan Ini</span>
                <div class="bg-[#dae2ff] p-2 rounded-lg text-[#003d9b]">
                    <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-[#003d9b] font-mono">Rp {{ number_format($stats['revenueThisMonth'], 0, ',', '.') }}</div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:border-[#003d9b]/30 transition-all">
            <div class="flex justify-between items-start mb-3">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pelanggan Aktif</span>
                <div class="bg-slate-100 p-2 rounded-lg text-slate-700">
                    <span class="material-symbols-outlined text-[18px]">group</span>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-[#003d9b] font-mono">{{ $stats['activeCustomers'] }}</div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-xl shadow-sm hover:border-[#003d9b]/30 transition-all">
            <div class="flex justify-between items-start mb-3">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Laundry Tertunda</span>
                <div class="bg-red-50 p-2 rounded-lg text-[#ba1a1a]">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-[#003d9b] font-mono">
                {{ $stats['pendingOrders'] }} <span class="text-xs font-medium text-slate-500">unit</span>
            </div>
        </div>
    </div>

    {{-- Center Layout: Chart + Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        {{-- Chart --}}
        <div class="lg:col-span-8 bg-white border border-slate-200 p-6 rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-[#003d9b]">Grafik Pendapatan</h3>
                <span class="text-xs font-bold text-slate-400">7 Hari Terakhir</span>
            </div>

            <div class="relative w-full overflow-hidden" style="height: {{ $height }}px;">
                <svg viewBox="0 0 {{ $width }} {{ $height }}" class="w-full h-full overflow-visible">
                    @foreach ($gridLines as $gridVal)
                        @php $gy = $padding + $chartHeight - ($gridVal * $chartHeight) / $maxVal; @endphp
                        <line x1="{{ $padding }}" y1="{{ $gy }}" x2="{{ $width - $padding }}" y2="{{ $gy }}" stroke="#f1f5f9" stroke-width="1" />
                        <text x="{{ $padding - 8 }}" y="{{ $gy + 4 }}" text-anchor="end" class="fill-slate-400 font-mono" style="font-size: 10px;">
                            Rp {{ number_format($gridVal / 1000000, 1) }}jt
                        </text>
                    @endforeach

                    <path d="{{ $areaD }}" fill="url(#area-gradient)" />
                    <path d="{{ $pathD }}" fill="none" stroke="#0052cc" stroke-width="3" stroke-linecap="round" />

                    @foreach ($points as $p)
                        <g>
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" class="fill-[#0052cc] stroke-white" stroke-width="2">
                                <title>{{ $p['day'] }}: Rp {{ number_format($p['amount'], 0, ',', '.') }}</title>
                            </circle>
                            <text x="{{ $p['x'] }}" y="{{ $height - $padding + 18 }}" text-anchor="middle" class="fill-slate-500" style="font-size: 11px; font-weight: 500;">
                                {{ $p['day'] }}
                            </text>
                        </g>
                    @endforeach

                    <defs>
                        <linearGradient id="area-gradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#0052cc" stop-opacity="0.15" />
                            <stop offset="100%" stop-color="#0052cc" stop-opacity="0.00" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col justify-between">
            <div class="p-4.5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-[#003d9b]">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.pickup-delivery') }}" class="text-xs font-semibold text-[#0052cc] hover:underline">Lihat Semua</a>
            </div>

            <div class="flex-1 overflow-y-auto p-4.5 space-y-4" style="max-height: 220px;">
                @forelse ($activities as $act)
                    <div class="flex gap-3 items-start hover:bg-slate-50 p-1.5 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm {{ $act['type'] === 'order' ? 'bg-[#6ff7ee]/20 text-[#00716b]' : 'bg-[#dae2ff] text-[#003d9b]' }}">
                            <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-800 leading-tight">{{ $act['text'] }}</p>
                            <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $act['timeAgo'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Operational Queue Table --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-[#003d9b]">Antrean Proses Saat Ini</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4.5 text-xs font-bold text-slate-500 uppercase tracking-wider">ID Pesanan</th>
                        <th class="px-6 py-4.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-4.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Berat</th>
                        <th class="px-6 py-4.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                        <th class="px-6 py-4.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-[#0052cc]" style="font-size: 12px;">
                                ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800" style="font-size: 12px;">
                                {{ $order->user->name ?? $order->guest_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium" style="font-size: 12px;">
                                {{ $order->service_type }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full font-bold uppercase tracking-tight {{ $badgeClass($order->status) }}" style="font-size: 10px;">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 font-mono" style="font-size: 12px;">
                                {{ $order->weight }} kg
                            <td class="px-6 py-4 text-slate-400" style="font-size: 12px;">
                                {{ $order->notes ? \Illuminate\Support\Str::limit($order->notes, 30) : '-' }}
                            </td>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders', ['search' => $order->id]) }}"
                                    class="font-bold text-[#003d9b] hover:text-[#0052cc] hover:underline inline-flex items-center gap-0.5" style="font-size: 12px;">
                                    Detail
                                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-slate-400">
                                        <span class="material-symbols-outlined text-3xl">inbox</span>
                                        <p class="text-sm font-medium">Belum ada pesanan yang masuk hari ini</p>
                                    </div>
                                </td>
                            </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
