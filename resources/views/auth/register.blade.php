@extends('layouts.app')
@section('content')
<main class="flex min-h-screen bg-[#f9f9ff]" id="signup-screen">
    <section class="hidden lg:flex w-3/5 relative overflow-hidden bg-[#003d9b]">
        <div class="absolute inset-0 z-0 opacity-80 bg-cover bg-center"
             style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuA5Um8Pb55Xjhnrpmf7ykITljgQrjyxwK81waGOxtlGx8FH2WEAlWQvM5gutm7OixB63C1Kx4kdY2ri1eZoWaEE5Sa3FGbaSrXz0y8sYnST09gtkGBi7gpyEnPlmAqBoF-0ZFzkQaDJNIrdvVUVJ_t3xq39betLrPKi7shM7f3WYijQpFXSYQYYkNb57brFOxD71EkM0XQQZcvQ6-IocL9JyJNsr6Y6guKvFG2hJwZJcDW7gwP9soyzfK_m_5yemkK8ulYOGwerBek')">
        </div>
        <div class="absolute inset-0 z-10 bg-gradient-to-tr from-[#003d9b]/85 to-transparent"></div>

        <div class="relative z-30 flex flex-col p-12 w-full justify-center h-full items-start text-left">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-white rounded-lg shadow-sm flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#003d9b] text-[32px]">local_laundry_service</span>
                </div>
                <span class="font-headline-md text-2xl font-extrabold text-white">Laundry Yuk!</span>
            </div>

            <div class="max-w-xl">
                <h1 class="font-display text-white mb-4 leading-tight text-[64px] font-bold">Laundry Yuk!</h1>
                <p class="font-body-lg text-lg text-[#dae2ff] opacity-90 leading-relaxed">
                    Daftar sekarang dan nikmati kemudahan memesan serta melacak proses laundry kapan saja.
                </p>
            </div>

            <footer class="text-[#d6e3ff]/60 font-body-sm text-sm absolute bottom-8">
                © 2026 Linen Logic System. Managed by Laundry Yuk!.
            </footer>
        </div>
    </section>

    <section class="w-full lg:w-2/5 bg-white flex flex-col justify-center items-center p-8 overflow-y-auto">
        <div class="w-full max-w-[440px] flex flex-col h-full py-4 justify-between">
            <div class="my-auto">
                <header class="mb-6 text-center lg:text-left">
                    <div class="lg:hidden flex justify-center items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-[#003d9b] text-[32px]">local_laundry_service</span>
                        <span class="font-headline-md text-xl font-extrabold text-[#003d9b]">Laundry Yuk!</span>
                    </div>
                    <h2 class="font-display text-2xl font-bold text-[#091c35] mb-2">Daftar Akun Baru Pelanggan</h2>
                    <p class="text-sm text-[#434654]">
                        Daftar sekarang untuk mulai menikmati layanan laundry terbaik secara praktis dan efisien.
                    </p>
                </header>

                @if ($errors->any())
                    <div class="p-4 mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm flex items-start gap-2">
                        <span class="material-symbols-outlined text-base">error</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form class="space-y-4" method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-[#434654]" for="full_name">
                            Nama Lengkap
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#737685] group-focus-within:text-[#003d9b] transition-colors">person</span>
                            <input
                                class="w-full pl-10 pr-4 py-2.5 bg-[#f9f9ff] border border-[#c3c6d6] rounded-lg text-sm text-[#091c35] focus:outline-none focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] transition-all"
                                id="full_name" name="name" placeholder="John Doe" required type="text" value="{{ old('name') }}">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-[#434654]" for="email">
                            Alamat Email
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#737685] group-focus-within:text-[#003d9b] transition-colors">mail</span>
                            <input
                                class="w-full pl-10 pr-4 py-2.5 bg-[#f9f9ff] border border-[#c3c6d6] rounded-lg text-sm text-[#091c35] focus:outline-none focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] transition-all"
                                id="email" name="email" placeholder="name@company.com" required type="email" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-[#434654]" for="phone">
                            Nomor Telepon
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#737685] group-focus-within:text-[#003d9b] transition-colors">call</span>
                            <input
                                class="w-full pl-10 pr-4 py-2.5 bg-[#f9f9ff] border border-[#c3c6d6] rounded-lg text-sm text-[#091c35] focus:outline-none focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] transition-all"
                                id="phone" name="phone" placeholder="0812xxxxxx" required type="tel" value="{{ old('phone') }}">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-[#434654]" for="address">
                            Alamat Lengkap (Untuk Penjemputan)
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-4 text-[#737685] group-focus-within:text-[#003d9b] transition-colors">location_on</span>
                            <textarea
                                class="w-full pl-10 pr-4 py-2.5 bg-[#f9f9ff] border border-[#c3c6d6] rounded-lg text-sm text-[#091c35] focus:outline-none focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] transition-all min-h-[64px]"
                                id="address" name="address" placeholder="Jl. Melati No. 12, Komplek Permata Hijau..." required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-[#434654]" for="password">
                            Kata Sandi
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#737685] group-focus-within:text-[#003d9b] transition-colors">lock</span>
                            <input
                                class="w-full pl-10 pr-12 py-2.5 bg-[#f9f9ff] border border-[#c3c6d6] rounded-lg text-sm text-[#091c35] focus:outline-none focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] transition-all"
                                id="password" name="password" placeholder="••••••••" required type="password">
                            <button type="button" id="toggle-password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#737685] hover:text-[#091c35] transition-colors">
                                <span class="material-symbols-outlined" id="toggle-icon">visibility</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#737685] italic mt-1 leading-relaxed">
                            Minimal 8 karakter dengan kombinasi huruf dan angka.
                        </p>
                        <p class="text-[11px] text-[#003d9b] mt-2 font-semibold">
                            Catatan: Pendaftaran ini hanya untuk akun pelanggan. Akun Admin dikelola oleh administrator sistem.
                        </p>
                    </div>

                    <div class="flex items-start gap-2 pt-1">
                        <input
                            class="mt-1 w-4 h-4 rounded border-[#c3c6d6] text-[#003d9b] focus:ring-[#003d9b]/30 cursor-pointer"
                            id="terms" required type="checkbox">
                        <label class="text-xs text-[#434654] select-none cursor-pointer" for="terms">
                            Saya menyetujui <a class="text-[#003d9b] font-semibold hover:underline" href="#terms">Syarat &amp; Ketentuan</a> serta <a class="text-[#003d9b] font-semibold hover:underline" href="#privacy">Kebijakan Privasi</a>.
                        </label>
                    </div>

                    <button
                        class="w-full bg-[#0052cc] text-white hover:bg-[#003d9b] active:scale-[0.98] transition-all py-3 rounded-lg font-bold text-sm flex justify-center items-center gap-2 shadow-sm"
                        type="submit">
                        <span>Daftar Sekarang</span>
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </form>
            </div>

            <p class="mt-8 text-center text-sm text-[#434654]">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-[#003d9b] font-bold hover:underline">Masuk Sekarang</a>
            </p>
        </div>
    </section>
</main>

<script>
document.getElementById('toggle-password').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggle-icon');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    icon.textContent = isPassword ? 'visibility_off' : 'visibility';
});
</script>
@endsection