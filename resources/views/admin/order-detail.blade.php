@extends('layouts.admin')

@section('title', '- Detail Pesanan')

@php
    $orderCode = 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
    $isCompleted = $order->status === 'Selesai';

    $initials = function (string $name) {
        $parts = array_values(array_filter(explode(' ', trim($name))));
        if (count($parts) === 0) return '?';
        if (count($parts) === 1) return mb_strtoupper(mb_substr($parts[0], 0, 2));
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    };

    $payment = $order->transaction;
@endphp

@section('content')
<div class="space-y-6">
    {{-- Breadcrumb & Title Row --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-slate-400 text-xs font-semibold mb-2">
                <a href="{{ route('admin.orders') }}" class="hover:text-[#003d9b] flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                    <span>Pesanan</span>
                </a>
                <span class="material-symbols-outlined text-[14px] text-slate-300">chevron_right</span>
                <span class="text-slate-600 font-medium">Detail Pesanan</span>
            </nav>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Pesanan {{ $orderCode }}</h2>
                <span class="px-3 py-1 bg-[#6ff7ee]/10 text-[#00716b] text-xs font-bold rounded-full border border-[#6ff7ee]/20">
                    {{ $order->status }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <button
                onclick="window.print()"
                class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 transition-colors flex items-center gap-1.5 shadow-sm"
            >
                <span class="material-symbols-outlined text-[15px]">print</span>
                Cetak Invoice
            </button>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="Selesai">
                <button
                    type="submit"
                    @disabled($isCompleted)
                    class="px-4 py-2 rounded-lg text-xs font-bold text-white transition-all flex items-center gap-1.5 {{ $isCompleted ? 'bg-slate-300 cursor-not-allowed' : 'bg-[#003d9b] hover:bg-[#0052cc]' }}"
                >
                    <span class="material-symbols-outlined text-[15px]">check_circle</span>
                    Tandai Selesai
                </button>
            </form>
        </div>
    </div>

    {{-- 2-Column Grid Details --}}
    <div class="grid grid-cols-12 gap-5">
        {{-- LEFT COL --}}
        <div class="col-span-12 lg:col-span-8 space-y-5">

            {{-- Lifecycle Tracker --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-8">Tracking Pesanan</h3>

                <div class="relative flex justify-between items-start">
                    <div class="absolute top-5 left-0 w-full h-[2px] bg-slate-100 -z-0"></div>
                    <div
                        class="absolute top-5 left-0 h-[2px] bg-[#0052cc] -z-0 transition-all duration-300"
                        style="width: {{ $currentIndex !== false ? ($currentIndex / (count($statuses) - 1)) * 100 : 0 }}%"
                    ></div>

                    @foreach ($statuses as $idx => $step)
                        @php
                            $isDone = $currentIndex !== false && $idx < $currentIndex;
                            $isActive = $idx === $currentIndex;
                        @endphp
                        <div class="relative z-10 flex flex-col items-center gap-2 max-w-[70px]">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all
                                {{ $isDone ? 'bg-[#6ff7ee]/20 text-[#00716b] border-[#6ff7ee]/50' : ($isActive ? 'bg-[#0052cc] text-white border-[#0052cc] ring-4 ring-[#dae2ff]' : 'bg-white text-slate-300 border-slate-200') }}">
                                @if ($isDone)
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                @else
                                    <span class="text-xs font-bold font-mono">{{ $idx + 1 }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold text-center leading-tight
                                {{ $isActive ? 'text-[#0052cc]' : ($isDone ? 'text-[#00716b]' : 'text-slate-400') }}">
                                {{ $step }}
                            </span>
                        </div>
                    @endforeach
                </div>

                @if ($nextStatus)
                    <div class="mt-8 p-4 bg-[#dae2ff]/20 border border-[#b2c5ff]/30 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[#0052cc] flex items-center justify-center text-white shrink-0">
                                <span class="material-symbols-outlined text-[20px]">local_laundry_service</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#003d9b]">Tahap Saat Ini: {{ $order->status }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">Diperbarui terakhir {{ $order->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                            <button type="submit" class="px-4 py-2 bg-[#003d9b] text-white rounded-lg text-xs font-bold hover:bg-[#0052cc] transition-all flex items-center gap-1">
                                Berikutnya: {{ $nextStatus }}
                                <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Manifest --}}
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rincian Pesanan</h3>
                    <div class="px-2.5 py-1 bg-slate-50 text-[#003d9b] text-xs font-bold rounded-lg border border-slate-200 font-mono">
                        Total Berat: {{ $order->weight }} Kg
                    </div>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Layanan</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($order->orderDetails as $detail)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-slate-50 border border-slate-100 flex items-center justify-center text-[#003d9b]">
                                            <span class="material-symbols-outlined text-[16px]">checkroom</span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-800">{{ $detail->service->service_name ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                    {{ $detail->quantity }} Pcs
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-mono font-bold text-slate-700">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-xs text-slate-400">
                                    Belum ada rincian layanan untuk pesanan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Special Instructions --}}
            @if ($order->notes)
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-[16px] text-amber-500">report</span>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instruksi Khusus</h3>
                    </div>
                    <div class="bg-amber-50/50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                        <p class="text-xs text-slate-700 leading-relaxed italic">&ldquo;{{ $order->notes }}&rdquo;</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- RIGHT COL --}}
        <div class="col-span-12 lg:col-span-4 space-y-5">
            {{-- Customer --}}
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Detail Pelanggan</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-12 h-12 rounded-full bg-[#dae2ff] flex items-center justify-center font-bold text-sm text-[#003d9b]">
                            {{ $initials($order->user->name ?? '-') }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">{{ $order->user->name ?? '-' }}</h4>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $order->user->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="space-y-3.5">
                        <div class="flex items-center gap-2.5 text-slate-600 text-xs">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">call</span>
                            <span class="font-medium">{{ $order->user->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-start gap-2.5 text-slate-600 text-xs">
                            <span class="material-symbols-outlined text-[16px] text-slate-400 mt-0.5">location_on</span>
                            <span class="font-medium leading-relaxed">{{ $order->pickupDelivery->address ?? ($order->user->address ?? '-') }}</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-slate-600 text-xs">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">event</span>
                            <span class="font-medium">Tanggal Pesan: {{ \Carbon\Carbon::parse($order->order_date)->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-2.5">
                    @forelse ($order->orderDetails as $detail)
                        <div class="flex justify-between text-xs font-medium text-slate-600">
                            <span>{{ $detail->service->service_name ?? 'Layanan' }} &times;{{ $detail->quantity }}</span>
                            <span class="font-mono">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @empty
                    @endforelse
                    @if ($order->is_express)
                        <div class="flex justify-between text-xs font-medium text-slate-600">
                            <span>Biaya Ekspres</span>
                            <span class="font-mono">Termasuk</span>
                        </div>
                    @endif
                    <div class="pt-2.5 border-t border-slate-100 flex justify-between items-center">
                        <span class="font-bold text-xs text-slate-800">Total Pembayaran</span>
                        <span class="text-sm font-bold text-[#003d9b] font-mono">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-2 p-2.5 rounded-lg border {{ ($payment->payment_status ?? 'Belum Bayar') === 'Lunas' ? 'bg-[#6ff7ee]/10 border-[#6ff7ee]/25 text-[#00716b]' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                    <span class="material-symbols-outlined text-[16px]">payments</span>
                    <span class="text-[10px] font-bold uppercase">
                        {{ $payment->payment_status ?? 'Belum Bayar' }}
                        @if ($payment && $payment->payment)
                            &bull; {{ $payment->payment->payment_method }}
                        @endif
                    </span>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4">Riwayat Pesanan</h3>

                <div class="space-y-5 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                    <div class="relative pl-7">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full border-2 flex items-center justify-center shadow-sm bg-white border-slate-200">
                            <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Pesanan Dibuat</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>

                    @if ($order->pickupDelivery)
                        <div class="relative pl-7">
                            <div class="absolute left-0 top-1 w-6 h-6 rounded-full border-2 flex items-center justify-center shadow-sm bg-white border-slate-200">
                                <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                            </div>
                            <p class="text-xs font-bold text-slate-700">Pickup &amp; Delivery: {{ $order->pickupDelivery->status }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->pickupDelivery->updated_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                    @endif

                    <div class="relative pl-7">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full border-2 flex items-center justify-center shadow-sm bg-[#003d9b] border-[#003d9b]">
                            <div class="w-2 h-2 rounded-full bg-white"></div>
                        </div>
                        <p class="text-xs font-bold text-[#003d9b]">Status Saat Ini: {{ $order->status }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Terakhir diperbarui {{ $order->updated_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
