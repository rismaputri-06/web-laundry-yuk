@extends('layouts.admin')

@section('title', '- Penjemputan & Pengantaran')

@section('content')
<div id="pickup-delivery-view" class="space-y-6">
    {{-- Top action row --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#003d9b] font-sans">Penjemputan &amp; Pengantaran</h2>
            <p class="text-sm text-slate-500 font-medium">Sistem Pemantauan Armada Logistik Kurir Terintegrasi.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold px-4 py-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    {{-- Main split logistics grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        {{-- Left Section: Map + Schedule table --}}
        <section class="col-span-12 lg:col-span-8 flex flex-col gap-5">

            {{-- Map (dummy/statis) --}}
            <div class="h-[360px] bg-slate-900 rounded-xl border border-slate-800 overflow-hidden relative shadow-md group">
                <div class="absolute top-4 left-4 z-10 flex gap-2">
                    <div class="bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-lg border border-slate-100 flex items-center gap-2 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-slate-700">{{ $drivers->where('status', 'Aktif')->count() }} Kurir Aktif</span>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-lg border border-slate-100 flex items-center gap-2 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0052cc]"></span>
                        <span class="text-xs font-bold text-slate-700">{{ $activeTasks }} Tugas Antrean</span>
                    </div>
                </div>

                <div class="w-full h-full relative bg-slate-50 flex items-center justify-center">
                    <svg viewBox="0 0 800 400" class="w-full h-full absolute inset-0 opacity-80">
                        <path d="M 50 0 L 50 400 M 150 0 L 150 400 M 250 0 L 250 400 M 350 0 L 350 400 M 450 0 L 450 400 M 550 0 L 550 400 M 650 0 L 650 400" stroke="#e2e8f0" stroke-width="1" />
                        <path d="M 0 50 L 800 50 M 0 150 L 800 150 M 0 250 L 800 250 M 0 350 L 800 350" stroke="#e2e8f0" stroke-width="1" />
                        <path d="M 0 100 Q 200 80, 400 220 T 800 200" fill="none" stroke="#cbd5e1" stroke-width="6" stroke-linecap="round" />
                        <path d="M 100 400 Q 300 180, 500 300 T 700 0" fill="none" stroke="#94a3b8" stroke-width="4" stroke-linecap="round" stroke-dasharray="5,5" />
                        <path d="M 150 150 L 350 150 L 350 250 L 550 250" fill="none" stroke="#0052cc" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="animate-pulse" />
                        <path d="M 250 350 L 250 250 L 450 250" fill="none" stroke="#00716b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                        <g>
                            <circle cx="150" cy="150" r="14" fill="#0052cc" fill-opacity="0.2" />
                            <circle cx="150" cy="150" r="5" fill="#003d9b" />
                            <text x="150" y="132" text-anchor="middle" class="fill-slate-800 text-[10px] font-bold">Sudirman Park</text>
                        </g>
                        <g>
                            <circle cx="550" cy="250" r="14" fill="#006a65" fill-opacity="0.2" />
                            <circle cx="550" cy="250" r="5" fill="#00716b" />
                            <text x="550" y="232" text-anchor="middle" class="fill-slate-800 text-[10px] font-bold">Kemang</text>
                        </g>
                        <g>
                            <rect x="335" y="135" width="30" height="30" rx="6" fill="#003d9b" />
                            <text x="350" y="154" text-anchor="middle" class="fill-white font-mono text-[9px] font-black">LY</text>
                            <text x="350" y="180" text-anchor="middle" class="fill-[#003d9b] text-[11px] font-extrabold">HQ Outlet</text>
                        </g>
                    </svg>

                    <div class="absolute top-[138px] left-[238px] w-6 h-6 rounded-full bg-teal-500 border-2 border-white flex items-center justify-center text-white text-[9px] font-bold shadow-md animate-bounce" title="Budi Santoso">
                        BS
                    </div>
                </div>

                <div class="absolute bottom-4 right-4 flex flex-col gap-1.5 shadow-md">
                    <button type="button" class="bg-white p-2 border border-slate-200 rounded-t-lg hover:bg-slate-50 transition-colors text-slate-600">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                    </button>
                    <button type="button" class="bg-white p-2 border border-slate-200 border-t-0 rounded-b-lg hover:bg-slate-50 transition-colors text-slate-600">
                        <span class="material-symbols-outlined text-[16px]">remove</span>
                    </button>
                </div>
            </div>

            {{-- Table view: Scheduled requests (dummy/statis dulu) --}}
            <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden shadow-sm">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-[#003d9b]">Jadwal Logistik</h3>
                    <div class="flex gap-1.5">
                        <button type="button" class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">filter_list</span> Filter
                        </button>
                        <button type="button" class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">download</span> Ekspor
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase">Pelanggan</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase">Tipe Tugas</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase">Status</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase">Kurir</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase">Estimasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $logisticsSchedule = [
                                    ['customer' => 'Bapak Heru Setiawan', 'address' => 'Sudirman Park, Apt A-12', 'type' => 'Penjemputan', 'status' => 'DALAM PERJALANAN', 'driver' => 'Budi Santoso', 'color' => 'bg-teal-500', 'time' => '14:45 PM'],
                                    ['customer' => 'Ibu Siti Aminah', 'address' => 'Kemang Village, Tower Empire', 'type' => 'Pengantaran', 'status' => 'TERJADWAL', 'driver' => 'Belum Ditugaskan', 'color' => 'bg-slate-300', 'time' => '15:30 PM'],
                                    ['customer' => 'Ibu Maya Indriani', 'address' => 'Jl. Dharmawangsa No. 10', 'type' => 'Penjemputan', 'status' => 'SELESAI', 'driver' => 'Adi Wijaya', 'color' => 'bg-emerald-500', 'time' => '11:00 AM'],
                                ];
                            @endphp
                            @foreach ($logisticsSchedule as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-xs text-slate-800">{{ $item['customer'] }}</span>
                                            <span class="text-[10px] text-slate-400 mt-0.5">{{ $item['address'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 text-xs font-bold {{ $item['type'] === 'Penjemputan' ? 'text-[#0052cc]' : 'text-[#006a65]' }}">
                                            <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold
                                            {{ $item['status'] === 'DALAM PERJALANAN' ? 'bg-blue-50 text-blue-700' : ($item['status'] === 'TERJADWAL' ? 'bg-slate-100 text-slate-600' : 'bg-emerald-50 text-emerald-700') }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2.5 h-2.5 rounded-full {{ $item['color'] }}"></div>
                                            <span class="text-xs font-semibold text-slate-700">{{ $item['driver'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono font-medium text-slate-400">{{ $item['time'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Right Section: Fleet performance card --}}
        <aside class="col-span-12 lg:col-span-4 flex flex-col gap-5">
            {{-- Driver Status List (data asli) --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-[#003d9b] mb-4">Status Kurir Aktif</h3>
                <div class="space-y-3">
                    @forelse ($drivers as $driver)
                        <div class="p-3 bg-slate-50 rounded-xl flex items-center justify-between border border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-extrabold text-xs">
                                    {{ collect(explode(' ', $driver->name))->map(fn ($n) => mb_substr($n, 0, 1))->implode('') }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">{{ $driver->name }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $driver->vehicle }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $driver->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $driver->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-4">Belum ada kurir terdaftar.</p>
                    @endforelse
                </div>
            </div>

            {{-- Logistics Performance card (data asli) --}}
            <div class="flex-1 bg-[#003d9b] p-6 rounded-xl relative overflow-hidden flex flex-col justify-between shadow-lg text-white">
                <div class="absolute inset-0 opacity-15 pointer-events-none">
                    <svg width="100%" height="100%" viewBox="0 0 400 400" preserveAspectRatio="none">
                        <path d="M 0 320 C 100 240, 200 120, 300 280 T 400 200 L 400 400 L 0 400 Z" fill="#ffffff" />
                        <path d="M 0 320 C 100 240, 200 120, 300 280 T 400 200" fill="none" stroke="#ffffff" stroke-width="3" />
                    </svg>
                </div>

                <div class="relative z-10">
                    <h3 class="text-lg font-bold">Performa Hari Ini</h3>
                    <p class="text-[#c4d2ff] text-xs font-medium mt-1 leading-relaxed">
                        Ikhtisar logistik penjemputan dan pengantaran real-time.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/5">
                            <span class="text-[#c4d2ff] text-[9px] font-bold uppercase tracking-wider block">Selesai</span>
                            <div class="text-2xl font-black font-mono mt-1">{{ $completedToday }}</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/5">
                            <span class="text-[#c4d2ff] text-[9px] font-bold uppercase tracking-wider block">Waktu Rata-rata</span>
                            <div class="text-2xl font-black font-mono mt-1">{{ $avgMinutes ? round($avgMinutes) . 'm' : '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 relative z-10">
                    <div class="flex justify-between items-center bg-white/5 border border-white/10 p-3.5 rounded-lg">
                        <div>
                            <p class="text-[10px] text-slate-300 font-bold uppercase tracking-wider">Efisiensi Kurir</p>
                            <p class="text-xs font-bold text-white mt-1">Sangat Baik</p>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-[#6ff7ee]">trending_up</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection