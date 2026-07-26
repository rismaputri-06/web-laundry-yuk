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
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="{{ route('customer.orders.create') }}"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg bg-[#dae2ff] text-[#001848] font-semibold text-left transition-all">
                <span class="material-symbols-outlined">add_shopping_cart</span>
                <span class="text-sm">Buat Pesanan Baru</span>
            </a>
            <a href="{{ route('customer.dashboard') }}"
               class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[#434654] hover:bg-[#dfe8ff]/50 text-left transition-all">
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
            <button type="button" onclick="alert('Customer Support: +62 812-3456-7890 (WhatsApp)')"
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
        <span class="text-xs font-bold text-[#737685]">Pelanggan Portal &gt; Pemesanan Baru</span>
        <div class="flex items-center gap-3">
            <p class="text-xs font-bold text-[#091c35]">{{ $user->name }}</p>
            <span class="px-2.5 py-0.5 bg-yellow-50 text-yellow-600 text-[10px] font-extrabold uppercase border border-yellow-100 rounded-full">Reguler</span>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="ml-[240px] pt-20 p-6 flex-grow overflow-y-auto">
        <div class="max-w-[1440px] mx-auto text-left">

            <div class="mb-6">
                <h2 class="font-display text-2xl font-bold text-[#003d9b]">Buat Pesanan Laundry</h2>
                <p class="text-sm text-[#434654]">Lengkapi formulir di bawah untuk mengatur jadwal penjemputan kotoran dan memilih jenis layanan laundry kiloan.</p>
            </div>

            <div id="error-box" class="hidden p-4 mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm items-start gap-2">
                <span class="material-symbols-outlined text-base">error</span>
                <span id="error-text"></span>
            </div>

            <form class="grid grid-cols-1 lg:grid-cols-12 gap-6" id="order-form">
                @csrf
                {{-- Left side --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Card 1: Service --}}
                    <div class="bg-white rounded-xl border border-[#c3c6d6] p-6 shadow-sm text-left">
                        <h3 class="text-sm font-extrabold text-[#003d9b] mb-4 uppercase tracking-wider">1. Pilih Jenis Layanan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="service-options">
                            <label class="service-option border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between border-[#c3c6d6] hover:bg-gray-50" data-value="Cuci Lipat" data-price="7000">
                                <input type="radio" name="serviceType" value="Cuci Lipat" class="hidden">
                                <div>
                                    <span class="material-symbols-outlined text-[#003d9b] text-2xl mb-2">dry_cleaning</span>
                                    <h4 class="font-bold text-xs text-[#091c35]">Cuci Lipat</h4>
                                    <p class="text-[10px] text-[#737685] mt-1 leading-relaxed">Cucian bersih dikeringkan dan dilipat rapi.</p>
                                </div>
                                <p class="text-xs font-extrabold text-[#003d9b] font-mono mt-4">Rp 7.000 / Kg</p>
                            </label>

                            <label class="service-option border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between border-[#003d9b] bg-[#dae2ff]/10 ring-2 ring-[#003d9b]/10" data-value="Cuci Setrika" data-price="10000">
                                <input type="radio" name="serviceType" value="Cuci Setrika" class="hidden" checked>
                                <div>
                                    <span class="material-symbols-outlined text-[#003d9b] text-2xl mb-2">iron</span>
                                    <h4 class="font-bold text-xs text-[#091c35]">Cuci Setrika</h4>
                                    <p class="text-[10px] text-[#737685] mt-1 leading-relaxed">Dicuci, disetrika dengan rapi, dipacking khusus.</p>
                                </div>
                                <p class="text-xs font-extrabold text-[#003d9b] font-mono mt-4">Rp 10.000 / Kg</p>
                            </label>

                            <label class="service-option border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between border-[#c3c6d6] hover:bg-gray-50" data-value="Setrika Saja" data-price="6000">
                                <input type="radio" name="serviceType" value="Setrika Saja" class="hidden">
                                <div>
                                    <span class="material-symbols-outlined text-[#003d9b] text-2xl mb-2">bolt</span>
                                    <h4 class="font-bold text-xs text-[#091c35]">Setrika Saja</h4>
                                    <p class="text-[10px] text-[#737685] mt-1 leading-relaxed">Hanya penyetrikaan uap pakaian bersih Anda.</p>
                                </div>
                                <p class="text-xs font-extrabold text-[#003d9b] font-mono mt-4">Rp 6.000 / Kg</p>
                            </label>
                        </div>
                    </div>

                    {{-- Card 2: Weight --}}
                    <div class="bg-white rounded-xl border border-[#c3c6d6] p-6 shadow-sm text-left">
                        <h3 class="text-sm font-extrabold text-[#003d9b] mb-4 uppercase tracking-wider">2. Estimasi Berat Cucian</h3>
                        <p class="text-xs text-[#434654] mb-4">Gunakan slider untuk memberikan estimasi berat cucian Anda. Penimbangan akurat akan dilakukan kembali oleh staf kami saat penjemputan.</p>
                        <div class="flex items-center gap-6">
                            <input class="w-full accent-[#003d9b] h-2 bg-gray-100 rounded-lg cursor-pointer"
                                   id="weight" name="weight" max="30" min="1" step="0.5" type="range" value="5">
                            <div class="px-5 py-2.5 bg-[#dae2ff] border border-[#c3c6d6] rounded-lg text-center shrink-0">
                                <span class="text-base font-extrabold text-[#001848] font-mono" id="weight-display">5</span>
                                <span class="text-xs font-bold text-[#001848] ml-1">Kg</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Pickup --}}
                    <div class="bg-white rounded-xl border border-[#c3c6d6] p-6 shadow-sm text-left space-y-4">
                        <h3 class="text-sm font-extrabold text-[#003d9b] mb-2 uppercase tracking-wider">3. Layanan Kurir & Penjemputan</h3>

                        <label class="flex items-center justify-between p-3.5 bg-[#f0f3ff] border border-[#c3c6d6] rounded-xl cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[#003d9b]">local_shipping</span>
                                <div>
                                    <p class="text-xs font-bold text-[#091c35]">Gunakan Jasa Antar Jemput Kurir (+ Rp 10.000)</p>
                                    <p class="text-[10px] text-[#737685]">Kurir akan menjemput cucian kotor dan mengantarkannya kembali saat selesai.</p>
                                </div>
                            </div>
                            <input class="w-4 h-4 rounded text-[#003d9b]" id="isPickupDelivery" type="checkbox" checked>
                        </label>

                        <div id="pickup-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-[#434654] uppercase">Tanggal Penjemputan</label>
                                <input class="w-full border border-[#c3c6d6] rounded-lg p-2.5 text-xs bg-white focus:ring-1 focus:ring-[#003d9b]"
                                       id="pickupDate" type="date">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-[#434654] uppercase">Jam Penjemputan</label>
                                <select class="w-full border border-[#c3c6d6] rounded-lg p-2.5 text-xs bg-white focus:ring-1 focus:ring-[#003d9b]" id="pickupTime">
                                    <option value="08:00 - 10:00">Pagi (08:00 - 10:00)</option>
                                    <option value="10:00 - 12:00">Siang (10:00 - 12:00)</option>
                                    <option value="13:00 - 15:00">Sore (13:00 - 15:00)</option>
                                    <option value="15:00 - 17:00">Sore Akhir (15:00 - 17:00)</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-[10px] font-bold text-[#434654] uppercase">Alamat Penjemputan / Pengiriman</label>
                                <textarea class="w-full border border-[#c3c6d6] rounded-lg p-2.5 text-xs bg-white focus:ring-1 focus:ring-[#003d9b] min-h-[64px]"
                                          id="pickupAddress" placeholder="Apartemen Taman Anggrek, Tower B Lt. 12...">{{ $user->address }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: Express & Notes --}}
                    <div class="bg-white rounded-xl border border-[#c3c6d6] p-6 shadow-sm text-left space-y-4">
                        <h3 class="text-sm font-extrabold text-[#003d9b] mb-2 uppercase tracking-wider">4. Opsi Kilat & Catatan Tambahan</h3>

                        <label class="flex items-center justify-between p-3.5 bg-yellow-50/50 border border-yellow-200 rounded-xl cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-yellow-600">bolt</span>
                                <div>
                                    <p class="text-xs font-bold text-yellow-800">Layanan Kilat / Express (+ Rp 15.000)</p>
                                    <p class="text-[10px] text-[#737685]">Cucian selesai diproses dalam waktu kurang dari 24 Jam.</p>
                                </div>
                            </div>
                            <input class="w-4 h-4 rounded text-yellow-600" id="isExpress" type="checkbox">
                        </label>

                        <div class="space-y-1 pt-2">
                            <label class="text-[10px] font-bold text-[#434654] uppercase">Catatan / Instruksi Khusus Penanganan</label>
                            <textarea class="w-full border border-[#c3c6d6] rounded-lg p-2.5 text-xs bg-white focus:ring-1 focus:ring-[#003d9b] min-h-[80px]"
                                      id="instructions" placeholder="Contoh: Pisahkan kemeja putih dari cucian berwarna gelap. Jangan disetrika terlalu panas untuk bahan sutra..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Right side: Billing --}}
                <div class="lg:col-span-4 bg-white border border-[#c3c6d6] rounded-xl p-6 shadow-sm h-fit text-left">
                    <h3 class="text-xs font-bold text-[#737685] uppercase tracking-wider mb-4">Ringkasan Biaya Laundry</h3>

                    <div class="space-y-3 text-xs border-b border-[#c3c6d6] pb-4">
                        <div class="flex justify-between">
                            <span class="text-[#434654]">Biaya Dasar (<span id="summary-weight">5</span> Kg)</span>
                            <span class="font-mono font-semibold" id="summary-base">Rp 50.000</span>
                        </div>
                        <div class="flex justify-between hidden" id="summary-express-row">
                            <span class="text-[#434654]">Penanganan Kilat</span>
                            <span class="font-mono font-semibold">Rp 15.000</span>
                        </div>
                        <div class="flex justify-between" id="summary-pickup-row">
                            <span class="text-[#434654]">Layanan Kurir Antar-Jemput</span>
                            <span class="font-mono font-semibold">Rp 10.000</span>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between items-center mb-6">
                        <span class="font-bold text-sm text-[#091c35]">Estimasi Total Biaya</span>
                        <span class="text-lg font-extrabold text-[#003d9b] font-mono" id="summary-total">Rp 60.000</span>
                    </div>

                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg text-[10px] text-[#003d9b] leading-relaxed mb-6">
                        <span class="font-bold">Info Pembayaran:</span><br>
                        • Pembayaran dapat diselesaikan via GoPay, Dana, atau Tunai saat kurir mengambil pakaian kotor Anda.<br>
                        • Timbangan final akan divalidasi langsung oleh staf logistik.
                    </div>

                    <button class="w-full py-3 bg-[#003d9b] text-white rounded-lg font-bold text-sm flex justify-center items-center gap-2 hover:opacity-90 active:scale-95 transition-all shadow-sm"
                        type="submit" id="submit-btn">
                        <span id="submit-text">Buat Pesanan Sekarang</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceOptions = document.querySelectorAll('.service-option');
    const weightInput = document.getElementById('weight');
    const weightDisplay = document.getElementById('weight-display');
    const summaryWeight = document.getElementById('summary-weight');
    const summaryBase = document.getElementById('summary-base');
    const summaryTotal = document.getElementById('summary-total');
    const summaryExpressRow = document.getElementById('summary-express-row');
    const summaryPickupRow = document.getElementById('summary-pickup-row');
    const pickupCheckbox = document.getElementById('isPickupDelivery');
    const expressCheckbox = document.getElementById('isExpress');
    const pickupFields = document.getElementById('pickup-fields');
    const form = document.getElementById('order-form');
    const errorBox = document.getElementById('error-box');
    const errorText = document.getElementById('error-text');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const csrfToken = document.querySelector('input[name="_token"]').value;

    function formatRupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function getSelectedPrice() {
        const checked = document.querySelector('input[name="serviceType"]:checked');
        const option = document.querySelector(`.service-option[data-value="${checked.value}"]`);
        return parseInt(option.dataset.price, 10);
    }

    function recalculate() {
        const weight = parseFloat(weightInput.value);
        weightDisplay.textContent = weight;
        summaryWeight.textContent = weight;

        const pricePerKg = getSelectedPrice();
        const base = Math.round(weight * pricePerKg);
        summaryBase.textContent = formatRupiah(base);

        const isPickup = pickupCheckbox.checked;
        const isExpress = expressCheckbox.checked;

        summaryExpressRow.classList.toggle('hidden', !isExpress);
        summaryPickupRow.classList.toggle('hidden', !isPickup);
        pickupFields.classList.toggle('hidden', !isPickup);

        const pickupFee = isPickup ? 10000 : 0;
        const expressFee = isExpress ? 15000 : 0;
        const total = base + pickupFee + expressFee;

        summaryTotal.textContent = formatRupiah(total);
    }

    serviceOptions.forEach(option => {
        option.addEventListener('click', function () {
            serviceOptions.forEach(o => {
                o.classList.remove('border-[#003d9b]', 'bg-[#dae2ff]/10', 'ring-2', 'ring-[#003d9b]/10');
                o.classList.add('border-[#c3c6d6]');
            });
            this.classList.remove('border-[#c3c6d6]');
            this.classList.add('border-[#003d9b]', 'bg-[#dae2ff]/10', 'ring-2', 'ring-[#003d9b]/10');
            this.querySelector('input[type="radio"]').checked = true;
            recalculate();
        });
    });

    weightInput.addEventListener('input', recalculate);
    pickupCheckbox.addEventListener('change', recalculate);
    expressCheckbox.addEventListener('change', recalculate);

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorBox.classList.add('hidden');
        errorBox.classList.remove('flex');
        submitBtn.disabled = true;
        submitText.textContent = 'Memproses...';

        const payload = {
            serviceType: document.querySelector('input[name="serviceType"]:checked').value,
            weight: parseFloat(weightInput.value),
            isPickupDelivery: pickupCheckbox.checked,
            isExpress: expressCheckbox.checked,
            pickupDate: document.getElementById('pickupDate').value,
            pickupTime: document.getElementById('pickupTime').value,
            pickupAddress: document.getElementById('pickupAddress').value,
            instructions: document.getElementById('instructions').value
        };

        try {
            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal mengirimkan pesanan.');
            }

            alert('Pesanan laundry Anda berhasil dibuat! Kurir kami akan segera menjemput pakaian kotor Anda.');
            window.location.href = '{{ route("customer.dashboard") }}';
        } catch (err) {
            errorText.textContent = err.message || 'Gagal menyimpan pesanan.';
            errorBox.classList.remove('hidden');
            errorBox.classList.add('flex');
        } finally {
            submitBtn.disabled = false;
            submitText.textContent = 'Buat Pesanan Sekarang';
        }
    });

    recalculate();
});
</script>
@endsection