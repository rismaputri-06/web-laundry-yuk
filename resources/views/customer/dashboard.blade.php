@extends('layouts.app')
@section('content')
<div class="bg-[#f9f9ff] text-[#091c35] min-h-screen font-sans flex">
    {{-- SideNavBar --}}
    <aside class="w-[240px] h-screen fixed left-0 top-0 bg-white border-r border-[#c3c6d6] flex flex-col py-6 z-50">
        <div class="px-6 mb-8 text-left">
            <h1 class="font-headline-md text-2xl font-bold text-[#003d9b]">Laundry Yuk!</h1>
            <p class="text-[10px] font-label-md uppercase tracking-wider text-[#737685]">Linen Logic System</p>
        </div>

        <nav class="flex-1 px-3 space-y-1">
            <a href="{{ route('customer.dashboard') }}"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg bg-[#dae2ff] text-[#001848] font-semibold text-left transition-all">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="/customer/orders/create"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
                <span class="material-symbols-outlined">add_shopping_cart</span>
                <span class="text-sm">Buat Pesanan Baru</span>
            </a>
            <button id="nav-tracking"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
                <span class="material-symbols-outlined">local_shipping</span>
                <span class="text-sm">Detail & Tracking</span>
            </button>
            <a href="/customer/profile"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
                <span class="material-symbols-outlined">person</span>
                <span class="text-sm">Profile</span>
            </a>
        </nav>

        <div class="px-3 mt-auto pt-6 border-t border-[#c3c6d6] space-y-1">
            <button onclick="alert('Customer Support: +62 812-3456-7890 (WhatsApp)')"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#737685] hover:bg-gray-100 text-left transition-all">
                <span class="material-symbols-outlined">chat</span>
                <span class="text-sm">Hubungi Laundry</span>
            </button>
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
    <header class="h-16 fixed top-0 right-0 left-[240px] z-40 bg-white border-b border-[#c3c6d6] flex justify-between items-center px-6">
        <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-[#003d9b]">Halo, {{ $firstName }}!</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="hover:bg-[#f0f3ff] rounded-full p-2 cursor-pointer transition-all relative">
                <span class="material-symbols-outlined text-[#434654]">notifications</span>
                <span class="absolute top-1 right-1 w-2 h-2 bg-[#ba1a1a] rounded-full"></span>
            </div>

            <div class="h-8 w-[1px] bg-[#c3c6d6]"></div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-[#091c35]">{{ $user->name }}</p>
                    <p class="text-[10px] text-yellow-600 font-extrabold uppercase tracking-wider">Reguler Member</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#dae2ff] flex items-center justify-center text-[#003d9b] font-bold">
                    {{ $initials }}
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="ml-[240px] pt-20 p-6 flex-grow overflow-y-auto">
        <div class="max-w-[1440px] mx-auto text-left">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
                <div>
                    <h2 class="font-display text-2xl font-bold text-[#003d9b] leading-tight">Beranda Pelanggan</h2>
                    <p class="text-sm text-[#434654]">Kelola pesanan laundry Anda, cek status pengerjaan, dan nikmati diskon khusus member.</p>
                </div>
                <a href="/customer/orders/create"
                   class="flex items-center px-5 py-2.5 bg-[#003d9b] text-white rounded-lg text-sm font-bold hover:opacity-90 active:scale-95 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-lg mr-2">add_shopping_cart</span> Buat Pesanan Baru
                </a>
            </div>

            {{-- Quick Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gradient-to-r from-[#003d9b] to-[#0052cc] text-white p-5 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-[#dae2ff] tracking-wider">Level Keanggotaan</p>
                        <p class="text-xl font-extrabold mt-1">Reguler Member</p>
                        <p class="text-xs text-[#dae2ff]/80 mt-1">Diskon otomatis tetap Rp 5.000 setiap memesan!</p>
                    </div>
                    <div class="p-3 bg-white/10 rounded-xl text-yellow-300">
                        <span class="material-symbols-outlined text-3xl">workspace_premium</span>
                    </div>
                </div>

                <div class="bg-white border border-[#c3c6d6] p-5 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-[#737685] uppercase">Pesanan Aktif</p>
                        <p class="text-2xl font-extrabold text-[#003d9b] mt-1">
                            <span id="stat-active">0</span> <span class="text-xs font-normal text-[#737685]">Pesanan</span>
                        </p>
                    </div>
                    <div class="p-3 bg-[#e7eeff] text-[#003d9b] rounded-xl">
                        <span class="material-symbols-outlined text-3xl">local_laundry_service</span>
                    </div>
                </div>

                <div class="bg-white border border-[#c3c6d6] p-5 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-[#737685] uppercase">Total Laundry Selesai</p>
                        <p class="text-2xl font-extrabold text-green-700 mt-1">
                            <span id="stat-done">0</span> <span class="text-xs font-normal text-[#737685]">Transaksi</span>
                        </p>
                    </div>
                    <div class="p-3 bg-green-50 text-green-700 rounded-xl">
                        <span class="material-symbols-outlined text-3xl">task_alt</span>
                    </div>
                </div>
            </div>

            {{-- Active Tracking Hero Card --}}
            <div id="hero-tracking"></div>

            {{-- Past Laundry Table --}}
            <div>
                <h3 class="font-display text-lg font-bold text-[#003d9b] mb-4">Riwayat Laundry Anda</h3>

                <div class="bg-white border border-[#c3c6d6] rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-[#f0f3ff] border-b border-[#c3c6d6]">
                                <tr>
                                    <th class="px-6 py-3.5 text-xs font-bold text-[#434654] uppercase tracking-wider">ID Pesanan</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-[#434654] uppercase tracking-wider">Layanan</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-[#434654] uppercase tracking-wider">Berat</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-[#434654] uppercase tracking-wider">Biaya</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-[#434654] uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-[#434654] uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="order-rows" class="divide-y divide-[#c3c6d6]">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-[#737685]">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerId = {{ $user->id }};
    const FINAL_STATUSES = ['Selesai', 'Diantar'];
    const STEPS = ['Menunggu', 'Diproses', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Selesai', 'Diantar'];
    const STEP_ICONS = {
        'Menunggu': 'shopping_basket',
        'Diproses': 'hourglass_empty',
        'Dicuci': 'water_drop',
        'Dikeringkan': 'air',
        'Disetrika': 'iron',
        'Selesai': 'inventory_2',
        'Diantar': 'local_shipping'
    };

    let activeOrder = null;

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function renderHero(order) {
        const heroEl = document.getElementById('hero-tracking');
        if (!order) {
            heroEl.innerHTML = '';
            return;
        }
        const currentIdx = STEPS.indexOf(order.status);

        const stepsHtml = STEPS.map((step, idx) => {
            const isDone = idx <= currentIdx;
            const isActive = idx === currentIdx;
            const boxClass = isActive
                ? 'bg-[#dae2ff] border-[#003d9b] ring-2 ring-[#003d9b]/20'
                : (isDone ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-100 opacity-60');
            const iconClass = isActive ? 'text-[#003d9b] animate-spin' : (isDone ? 'text-green-600' : 'text-[#737685]');
            const textClass = isActive ? 'text-[#003d9b]' : 'text-[#091c35]';

            return `<div class="p-3 border rounded-lg transition-all ${boxClass}">
                <span class="material-symbols-outlined block mb-1 text-lg ${iconClass}">${STEP_ICONS[step]}</span>
                <p class="text-[10px] font-bold ${textClass}">${step}</p>
            </div>`;
        }).join('');

        heroEl.innerHTML = `
        <div class="bg-white border border-[#c3c6d6] rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#c3c6d6] pb-4 mb-4 gap-2">
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#737685] tracking-wider">Pelacakan Pesanan Terkini</span>
                    <div class="flex items-center gap-3 mt-1">
                        <h3 class="font-display font-extrabold text-lg text-[#003d9b]">${order.id}</h3>
                        <span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-800 border border-yellow-200 text-[10px] font-bold rounded-full uppercase tracking-wider">${order.status}</span>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-[11px] text-[#737685] font-semibold">Estimasi Selesai / Kirim:</p>
                    <p class="text-sm font-bold text-[#091c35]">${order.estimatedDelivery ?? '-'}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-7 gap-2 text-center pt-2">${stepsHtml}</div>
            <div class="mt-4 text-right">
                <a href="/customer/orders/${order.rawId}" class="text-xs text-[#003d9b] font-bold hover:underline inline-flex items-center gap-1 ml-auto">
                    Detail Pelacakan & Peta Kurir
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>`;
    }

    function renderRows(orders) {
        const tbody = document.getElementById('order-rows');
        if (orders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-[#737685]">Anda belum pernah melakukan pemesanan laundry.</td></tr>`;
            return;
        }
        tbody.innerHTML = orders.map(order => {
            const isDone = FINAL_STATUSES.includes(order.status);
            const badgeClass = isDone
                ? 'bg-green-100 text-green-800 border border-green-200'
                : 'bg-yellow-100 text-yellow-800 border border-yellow-200';
            return `<tr class="hover:bg-[#f0f3ff]/40 transition-colors">
                <td class="px-6 py-4 font-mono font-bold text-[#003d9b] text-xs">${order.id}</td>
                <td class="px-6 py-4 text-xs font-bold text-[#091c35]">${order.serviceType}</td>
                <td class="px-6 py-4 text-xs text-[#434654]">${order.weight} Kg</td>
                <td class="px-6 py-4 text-xs font-mono font-semibold text-[#091c35]">${formatRupiah(order.totalPrice)}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider ${badgeClass}">${order.status}</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="/customer/orders/${order.rawId}" class="text-[#003d9b] hover:underline font-bold text-xs bg-[#003d9b]/5 hover:bg-[#003d9b]/10 px-3 py-1.5 rounded inline-block">Lacak</a>
                </td>
            </tr>`;
        }).join('');
    }

    async function fetchOrders() {
        try {
            const res = await fetch(`/api/orders?customerId=${customerId}`);
            const data = await res.json();
            const orders = data.orders || [];

            activeOrder = orders.find(o => !FINAL_STATUSES.includes(o.status)) || orders[0] || null;

            document.getElementById('stat-active').textContent = orders.filter(o => !FINAL_STATUSES.includes(o.status)).length;
            document.getElementById('stat-done').textContent = orders.filter(o => FINAL_STATUSES.includes(o.status)).length;

            renderHero(activeOrder);
            renderRows(orders);
        } catch (err) {
            console.error('Error fetching customer orders:', err);
        }
    }

    document.getElementById('nav-tracking').addEventListener('click', function () {
        if (activeOrder) {
            window.location.href = `/customer/orders/${activeOrder.rawId}`;
        } else {
            alert('Anda tidak memiliki pesanan aktif saat ini.');
        }
    });

    fetchOrders();
});
</script>
@endsection