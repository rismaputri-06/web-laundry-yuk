<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laundry Yuk - Admin @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>
<body class="bg-[#f9f9ff] text-[#091c35] font-sans">

    {{-- Toast (shown once after a redirect with a "status" flash message) --}}
    @if (session('status'))
        <div class="fixed bottom-6 right-6 bg-[#003d9b] text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 z-[150] border border-white/20">
            <span class="material-symbols-outlined text-[#6ff7ee] text-base">check_circle</span>
            <span class="text-xs font-semibold">{{ session('status') }}</span>
        </div>
    @endif

    {{-- Sidebar --}}
    <aside class="w-[260px] h-screen fixed left-0 top-0 bg-white border-r border-[#c3c6d6] flex flex-col p-6 z-40">
        <div class="mb-8">
            <h1 class="font-display !text-2xl !font-extrabold !text-[#003d9b] tracking-tight leading-none">Laundry Yuk!</h1>
            <p class="text-xs font-semibold text-[#434654] mt-1 tracking-wider uppercase opacity-85">Management Laundry</p>
        </div>

        <nav class="flex-1 space-y-2">
    @php
        $navItems = [
            ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'admin.orders', 'icon' => 'receipt', 'label' => 'Pesanan'],
            ['route' => 'admin.services', 'icon' => 'washer', 'label' => 'Layanan'],
            ['route' => 'admin.customers', 'icon' => 'users', 'label' => 'Pelanggan'],
            ['route' => 'admin.pickup-delivery', 'icon' => 'truck', 'label' => 'Pickup & Delivery'],
        ];
        $icons = [
            'dashboard' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
            'receipt' => '<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6"/><path d="M14 12h-4"/>',
            'washer' => '<path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'truck' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
        ];
    @endphp
    @foreach ($navItems as $item)
        <a href="{{ route($item['route']) }}"
           class="font-display !no-underline !font-semibold w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200
                  {{ request()->routeIs($item['route'])
                        ? '!bg-[#f0f3ff] !text-[#003d9b] border-r-4 !border-[#003d9b]'
                        : '!text-[#434654] hover:!bg-[#f9f9ff] hover:!text-[#003d9b]' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                {!! $icons[$item['icon']] !!}
            </svg>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>

        <div class="mt-auto pt-6 border-t border-[#c3c6d6]/40 space-y-1">
            <button type="button" onclick="openNewOrderModal()"
                class="w-full flex items-center justify-center gap-2 bg-[#003d9b] hover:bg-[#0052cc] text-white py-3 rounded-xl font-bold shadow-md active:scale-[0.98] transition-all mb-4">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span class="text-sm">Pesanan Baru</span>
            </button>

            <button type="button" onclick="alert('Pusat bantuan akan segera hadir.')"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-[#434654] hover:bg-[#f9f9ff] hover:text-[#003d9b] font-semibold text-sm transition-colors text-left">
                <span class="material-symbols-outlined text-[20px]">help</span>
                <span>Pusat Bantuan</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-[#ba1a1a] hover:bg-[#ffdad6]/20 rounded-xl transition-colors font-semibold text-sm text-left">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    @include('admin.new-order-modal')
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof openNewOrderModal === 'function') openNewOrderModal();
            });
        </script>
    @endif

    {{-- Main content --}}
    <main class="ml-[260px] min-h-screen">
        <header class="h-16 bg-white border-b border-[#c3c6d6]/60 flex items-center justify-between px-6 sticky top-0 z-30 shadow-sm shadow-[#091c35]/5">
            <div class="flex items-center gap-6 w-1/3">
                <div class="flex items-center bg-[#f0f3ff] px-4 py-2 rounded-xl border border-[#c3c6d6]/30 w-full focus-within:ring-2 focus-within:ring-[#003d9b]/20 transition-all">
                    <span class="material-symbols-outlined text-[#434654] text-[18px] mr-2">search</span>
                    <input type="text" placeholder="Cari pesanan, pelanggan, atau layanan..."
                        class="bg-transparent border-none focus:outline-none text-sm w-full text-[#091c35] placeholder-[#434654]/75">
                </div>
            </div>

            <div class="flex items-center gap-5">
                <button class="relative w-10 h-10 flex items-center justify-center rounded-full text-[#434654] hover:text-[#003d9b] hover:bg-[#f0f3ff] transition-all">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-[#ba1a1a] rounded-full border border-white"></span>
                </button>

                <div class="h-8 w-px bg-[#c3c6d6]/60"></div>

                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs font-bold text-[#091c35]">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-[#003d9b] font-semibold uppercase tracking-wider mt-0.5">Super Admin</p>
                    </div>
                    @php
                        $adminInitials = collect(explode(' ', auth()->user()->name))
                            ->filter()
                            ->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))
                            ->implode('');
                    @endphp
                    <div class="w-10 h-10 rounded-xl border-2 border-[#0052cc] bg-[#dae2ff] flex items-center justify-center text-[#003d9b] font-extrabold text-sm">
                        {{ $adminInitials }}
                    </div>
                </div>
            </div>
        </header>

        <div class="p-6 md:p-8 max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>
</body>
</html>