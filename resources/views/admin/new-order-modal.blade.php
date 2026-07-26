{{-- Modal Buat Pesanan Baru (Admin) --}}
@php
    $customersForModal = $customersForModal ?? \App\Models\User::where('role', 'customer')->orderBy('name')->get();
@endphp
<div id="new-order-modal" class="hidden fixed inset-0 z-[100] items-center justify-center">
    <div onclick="closeNewOrderModal()" class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px] transition-opacity"></div>

    <div class="relative bg-white w-full max-w-xl rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden mx-4">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-sm font-black text-[#003d9b] uppercase tracking-wider">Buat Pesanan Laundry Baru</h3>
            <button type="button" onclick="closeNewOrderModal()" class="p-1.5 hover:bg-slate-200 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form id="newOrderModalForm" method="POST" action="{{ route('admin.orders.store') }}" class="flex-grow overflow-y-auto p-6 space-y-4">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-xs font-semibold px-3 py-2.5 rounded-lg">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div id="modalErrorBox" class="hidden bg-red-50 border border-red-200 text-red-600 text-xs font-semibold px-3 py-2.5 rounded-lg"></div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Pelanggan</label>
                <input
                    type="text"
                    name="customer_name"
                    id="modalCustomerName"
                    list="customer-suggestions"
                    required
                    autocomplete="off"
                    value="{{ old('customer_name') }}"
                    placeholder="Ketik nama pelanggan..."
                    onchange="toggleNewCustomerFields()"
                    oninput="toggleNewCustomerFields()"
                    class="w-full border border-slate-200 rounded-lg text-xs font-semibold py-2 px-3 outline-none focus:ring-2 focus:ring-[#003d9b]/10 focus:border-[#003d9b]"
                >
                <datalist id="customer-suggestions">
                    @foreach ($customersForModal as $c)
                        <option value="{{ $c->name }}">{{ $c->email }}</option>
                    @endforeach
                </datalist>
                <p class="text-[10px] text-slate-400 mt-1">
                    Ketik nama yang sudah terdaftar untuk pesanan pelanggan lama, atau nama baru untuk pelanggan walk-in &mdash; akun pelanggannya akan dibuat otomatis.
                </p>
            </div>

            <div id="newCustomerFields" class="grid grid-cols-2 gap-4 bg-slate-50 border border-slate-200 rounded-lg p-3">
                <div class="col-span-2">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Data pelanggan baru (opsional, khusus nama yang belum terdaftar)</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">No. Telepon</label>
                    <input
                        type="text"
                        name="customer_phone"
                        value="{{ old('customer_phone') }}"
                        placeholder="0812xxxxxxx"
                        class="w-full border border-slate-200 rounded-lg text-xs font-semibold py-2 px-3 outline-none focus:ring-2 focus:ring-[#003d9b]/10 focus:border-[#003d9b]"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Alamat</label>
                    <input
                        type="text"
                        name="customer_address"
                        value="{{ old('customer_address') }}"
                        placeholder="Alamat pelanggan..."
                        class="w-full border border-slate-200 rounded-lg text-xs font-semibold py-2 px-3 outline-none focus:ring-2 focus:ring-[#003d9b]/10 focus:border-[#003d9b]"
                    >
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tipe Layanan</label>
                    <select id="modalServiceType" name="serviceType" required onchange="recalcModalTotal()"
                        class="w-full border border-slate-200 rounded-lg text-xs font-semibold py-2 px-3 outline-none focus:ring-2 focus:ring-[#003d9b]/10">
                        <option value="Cuci Lipat" data-price="7000">Cuci Lipat (Rp 7.000/kg)</option>
                        <option value="Cuci Setrika" data-price="10000" selected>Cuci Setrika (Rp 10.000/kg)</option>
                        <option value="Setrika Saja" data-price="6000">Setrika Saja (Rp 6.000/kg)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Berat (Kg)</label>
                    <input id="modalWeight" name="weight" type="number" min="0.5" step="0.1" value="1" required
                        oninput="recalcModalTotal()"
                        class="w-full border border-slate-200 rounded-lg text-xs font-semibold py-2 px-3 outline-none focus:ring-2 focus:ring-[#003d9b]/10">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-2 p-2.5 border border-slate-200 rounded-lg cursor-pointer">
                    <input type="checkbox" id="modalExpress" name="isExpress" value="1" onchange="recalcModalTotal()" class="rounded border-slate-300 text-[#003d9b]">
                    <span class="text-xs font-semibold text-slate-600">Ekspres (+Rp5.000/kg)</span>
                </label>
                <label class="flex items-center gap-2 p-2.5 border border-slate-200 rounded-lg cursor-pointer">
                    <input type="checkbox" id="modalPickup" name="isPickupDelivery" value="1" onchange="toggleModalPickup(); recalcModalTotal();" class="rounded border-slate-300 text-[#003d9b]">
                    <span class="text-xs font-semibold text-slate-600">Antar-Jemput (+Rp10.000)</span>
                </label>
            </div>

            <div id="modalPickupFields" class="hidden grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tanggal Pickup</label>
                    <input type="date" name="pickupDate" id="modalPickupDate"
                        class="w-full border border-slate-200 rounded-lg text-xs font-semibold py-2 px-3 outline-none focus:ring-2">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Jam Pickup</label>
                    <input type="text" name="pickupTime" placeholder="misal: 14:00 - 16:00"
                        class="w-full border border-slate-200 rounded-lg text-xs font-semibold py-2 px-3 outline-none focus:ring-2">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Alamat Penjemputan</label>
                    <textarea name="pickupAddress" rows="2" placeholder="Alamat lengkap..."
                        class="w-full border border-slate-200 rounded-lg text-xs font-medium py-2 px-3 outline-none focus:ring-2"></textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Instruksi Khusus (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Mohon hati-hati dengan pakaian luntur, dll..."
                    class="w-full border border-slate-200 rounded-lg text-xs font-medium py-2 px-3 outline-none focus:ring-2 resize-none"></textarea>
            </div>

            <div class="bg-[#dae2ff]/20 border border-[#b2c5ff]/30 rounded-lg p-3.5 flex justify-between items-center">
                <span class="text-xs font-bold text-[#003d9b] uppercase">Total Estimasi Harga</span>
                <span id="modalTotalDisplay" class="text-lg font-black text-[#003d9b] font-mono">Rp 12.000</span>
            </div>

            <div class="pt-4 border-t border-slate-100 flex gap-2 justify-end">
                <button type="button" onclick="closeNewOrderModal()"
                    class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" id="modalSubmitBtn"
                    class="px-5 py-2 bg-[#003d9b] text-white rounded-lg text-xs font-bold hover:bg-[#0052cc] transition-all">
                    Simpan Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const existingCustomerNames = @json($customersForModal->pluck('name')->map(fn ($n) => strtolower($n))->values());

function toggleNewCustomerFields() {
    const typed = document.getElementById('modalCustomerName').value.trim().toLowerCase();
    const isExisting = existingCustomerNames.includes(typed);
    document.getElementById('newCustomerFields').classList.toggle('hidden', isExisting || typed === '');
}

function openNewOrderModal() {
    document.getElementById('new-order-modal').classList.remove('hidden');
    document.getElementById('new-order-modal').classList.add('flex');
    recalcModalTotal();
    toggleNewCustomerFields();
}
function closeNewOrderModal() {
    document.getElementById('new-order-modal').classList.add('hidden');
    document.getElementById('new-order-modal').classList.remove('flex');
}
function toggleModalPickup() {
    const checked = document.getElementById('modalPickup').checked;
    document.getElementById('modalPickupFields').classList.toggle('hidden', !checked);
}
function recalcModalTotal() {
    const select = document.getElementById('modalServiceType');
    const pricePerKg = parseInt(select.options[select.selectedIndex].dataset.price, 10);
    const weight = parseFloat(document.getElementById('modalWeight').value) || 0;
    const isExpress = document.getElementById('modalExpress').checked;
    const isPickup = document.getElementById('modalPickup').checked;

    const base = weight * pricePerKg;
    const express = isExpress ? weight * 5000 : 0;
    const logistics = isPickup ? 10000 : 0;
    const platform = 2000;
    const total = Math.round(base + express + logistics + platform);

    document.getElementById('modalTotalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function () {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const dateField = document.getElementById('modalPickupDate');
    if (dateField) dateField.value = `${yyyy}-${mm}-${dd}`;
});
</script>
