@extends('layouts.admin')

@section('title', '- Pelanggan')

@section('content')
<div id="customers-view-container" class="space-y-6 relative">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#003d9b] font-sans">Pelanggan</h2>
            <p class="text-sm text-slate-500 font-medium">Kelola data pelanggan dan riwayat transaksi mereka secara efisien.</p>
        </div>
        <form method="GET" action="{{ route('admin.customers') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pelanggan..."
                class="bg-white border border-slate-200 text-xs font-medium px-4 py-2 rounded-lg focus:ring-[#003d9b]/25 focus:border-[#003d9b] outline-none w-56">
            <button type="submit" class="bg-[#003d9b] text-white text-xs font-bold px-4 py-2 rounded-lg flex items-center gap-1.5 hover:bg-[#0052cc] active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[15px]">search</span>
                Cari
            </button>
        </form>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 flex flex-col justify-between shadow-sm">
            <div class="p-2 bg-[#003d9b]/10 text-[#003d9b] rounded-lg w-fit mb-4">
                <span class="material-symbols-outlined text-[18px]">group</span>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Pelanggan</p>
                <h3 class="text-2xl font-extrabold text-slate-800 font-mono mt-1">{{ $totalCustomers }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 flex flex-col justify-between shadow-sm">
            <div class="p-2 bg-[#6ff7ee]/20 text-[#00716b] rounded-lg w-fit mb-4">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pelanggan Baru (Bulan Ini)</p>
                <h3 class="text-2xl font-extrabold text-slate-800 font-mono mt-1">{{ $newThisMonth }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 flex flex-col justify-between shadow-sm">
            <div class="p-2 bg-[#dae2ff] text-[#003d9b] rounded-lg w-fit mb-4">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Pesanan</p>
                <h3 class="text-2xl font-extrabold text-slate-800 font-mono mt-1">{{ $totalOrders }}</h3>
            </div>
        </div>
    </div>

    {{-- Customer Data Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Nama Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Email &amp; Telepon</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Statistik</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Bergabung</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $c)
                        <tr class="hover:bg-slate-50 transition-colors cursor-pointer group"
                            onclick="openCustomerDrawer({{ $c->id }})">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#003d9b]/10 text-[#003d9b] flex items-center justify-center font-bold text-xs">
                                        {{ mb_substr($c->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ $c->name }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Reguler Member</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-slate-700 font-medium">{{ $c->email }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $c->phone ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-slate-700 font-semibold">{{ $c->orders_count }} Pesanan</p>
                                <p class="font-mono text-xs text-[#0052cc] mt-0.5">Rp {{ number_format($c->orders_sum_total_price ?? 0, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 font-mono">
                                {{ $c->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" onclick="event.stopPropagation(); openCustomerDrawer({{ $c->id }})"
                                    class="text-[#003d9b] hover:text-[#0052cc] font-bold text-xs bg-[#003d9b]/5 px-4 py-1.5 rounded-lg transition-all">
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-xs text-slate-400 font-medium">Belum ada pelanggan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    {{-- SIDE DRAWER --}}
    <div id="drawer-wrapper" class="hidden fixed inset-0 z-[60] justify-end">
        <div id="drawer-backdrop" onclick="closeCustomerDrawer()" class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity"></div>

        <div id="drawer-panel" class="relative w-full max-w-[480px] h-full bg-white shadow-2xl flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-start">
                <div class="flex gap-4">
                    <div id="drawerAvatar" class="w-14 h-14 rounded-xl bg-[#003d9b]/10 flex items-center justify-center text-[#003d9b] text-lg font-black border border-[#003d9b]/15 shadow-sm shrink-0">
                        -
                    </div>
                    <div>
                        <h3 id="drawerName" class="text-lg font-extrabold text-slate-800">-</h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="px-2 py-0.5 rounded bg-[#006a65]/10 text-[#00716b] text-[10px] font-bold uppercase">Aktif</span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-bold uppercase">Reguler</span>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="closeCustomerDrawer()" class="p-1.5 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="flex-grow overflow-y-auto p-6 space-y-6">
                <section>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3.5">Informasi Kontak</h4>
                    <div class="space-y-3">
                        <div class="p-3.5 bg-slate-50 rounded-xl flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] text-[#003d9b]">mail</span>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Email</p>
                                <p id="drawerEmail" class="text-xs font-semibold text-slate-800 mt-0.5">-</p>
                            </div>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] text-[#003d9b]">call</span>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Telepon</p>
                                <p id="drawerPhone" class="text-xs font-semibold text-slate-800 mt-0.5">-</p>
                            </div>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl flex items-start gap-3">
                            <span class="material-symbols-outlined text-[18px] text-[#003d9b] mt-0.5">location_on</span>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Alamat Pengiriman</p>
                                <p id="drawerAddress" class="text-xs font-semibold text-slate-800 mt-0.5 leading-relaxed">-</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-3 gap-2 border-y border-slate-100 py-5">
                    <div class="text-center">
                        <p id="drawerTotalOrders" class="text-xl font-extrabold text-slate-800 font-mono">0</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Total Orders</p>
                    </div>
                    <div class="text-center border-x border-slate-100">
                        <p id="drawerSpending" class="text-xl font-extrabold text-[#0052cc] font-mono">Rp 0</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Spending</p>
                    </div>
                    <div class="text-center">
                        <p id="drawerActive" class="text-xl font-extrabold text-[#00716b] font-mono">0</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Active</p>
                    </div>
                </section>

                <section>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Pesanan Terakhir</h4>
                    <div id="drawerOrders" class="space-y-3">
                        <p class="text-xs text-slate-400 italic text-center py-4">Memuat...</p>
                    </div>
                </section>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100">
                <button type="button" onclick="closeCustomerDrawer()"
                    class="w-full px-5 py-2.5 border border-slate-200 text-slate-600 bg-white font-bold rounded-lg hover:bg-slate-50 transition-all text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
async function openCustomerDrawer(userId) {
    const wrapper = document.getElementById('drawer-wrapper');
    wrapper.classList.remove('hidden');
    wrapper.classList.add('flex');

    document.getElementById('drawerOrders').innerHTML = '<p class="text-xs text-slate-400 italic text-center py-4">Memuat...</p>';

    try {
        const res = await fetch(`/admin/customers/${userId}/detail`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        document.getElementById('drawerAvatar').textContent = data.name ? data.name.charAt(0).toUpperCase() : '-';
        document.getElementById('drawerName').textContent = data.name || '-';
        document.getElementById('drawerEmail').textContent = data.email || '-';
        document.getElementById('drawerPhone').textContent = data.phone || '-';
        document.getElementById('drawerAddress').textContent = data.address || '-';
        document.getElementById('drawerTotalOrders').textContent = data.totalOrders ?? 0;
        document.getElementById('drawerSpending').textContent = 'Rp ' + (data.totalSpending ?? 0).toLocaleString('id-ID');
        document.getElementById('drawerActive').textContent = data.activeOrders ?? 0;

        const ordersBox = document.getElementById('drawerOrders');
        if (!data.recentOrders || data.recentOrders.length === 0) {
            ordersBox.innerHTML = '<p class="text-xs text-slate-400 italic text-center py-4">Belum ada pesanan terdaftar.</p>';
        } else {
            ordersBox.innerHTML = data.recentOrders.map(o => `
                <a href="${o.showUrl}" class="block p-4 border border-slate-100 rounded-xl hover:border-[#0052cc]/30 transition-all bg-slate-50/50">
                    <div class="flex justify-between items-start mb-2.5">
                        <div>
                            <p class="text-xs font-extrabold text-[#003d9b] font-mono">${o.id}</p>
                            <p class="text-[10px] text-slate-400 font-medium mt-0.5">${o.date} • ${o.serviceType}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-[#0052cc]/10 text-[#003d9b] text-[9px] font-bold rounded uppercase">${o.status}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-800 font-mono">Rp ${o.totalPrice.toLocaleString('id-ID')}</p>
                    </div>
                </a>
            `).join('');
        }
    } catch (err) {
        document.getElementById('drawerOrders').innerHTML = '<p class="text-xs text-red-500 text-center py-4">Gagal memuat data.</p>';
    }
}

function closeCustomerDrawer() {
    const wrapper = document.getElementById('drawer-wrapper');
    wrapper.classList.add('hidden');
    wrapper.classList.remove('flex');
}
</script>
@endsection
