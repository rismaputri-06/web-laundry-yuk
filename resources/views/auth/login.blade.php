@extends('layouts.app')
@section('content')
<main class="flex min-h-screen bg-[#f9f9ff]" id="login-screen">

    {{-- Left Section --}}
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

    {{-- Right Side: Login Form --}}
    <section class="w-full lg:w-2/5 flex flex-col items-center justify-between p-8 bg-white overflow-y-auto">
        <div class="flex-grow flex flex-col justify-center w-full max-w-md">

            <div class="lg:hidden flex flex-col items-center mb-8">
                <div class="p-3 bg-[#f0f3ff] rounded-2xl mb-2 text-[#003d9b]">
                    <span class="material-symbols-outlined text-[40px]">local_laundry_service</span>
                </div>
                <h1 class="font-display text-3xl font-extrabold text-[#003d9b]">Laundry Yuk!</h1>
                <p class="text-sm text-[#737685] mt-1">Linen Logic System</p>
            </div>

            <div class="mb-8 text-center lg:text-left">
                <h2 class="font-display text-3xl font-bold text-[#091c35] mb-2">Welcome Back</h2>
                <p class="text-sm text-[#434654]">Please enter your details to access your account.</p>
            </div>

            @if ($errors->any())
                <div class="p-4 mb-6 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm flex items-start gap-2">
                    <span class="material-symbols-outlined text-base">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-[#434654]" for="identity">
                        Email Address
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#737685] text-lg">mail</span>
                        <input
                            class="w-full pl-10 pr-4 py-3 bg-white border border-[#c3c6d6] rounded-lg text-sm text-[#091c35] placeholder-[#737685] focus:outline-none focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] transition-all"
                            id="identity" name="email" placeholder="name@company.com" required type="email"
                            value="{{ old('email') }}">
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-semibold uppercase tracking-wider text-[#434654]" for="password">
                            Password
                        </label>
                        <a class="text-xs font-semibold text-[#003d9b] hover:underline" href="#forgot">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#737685] text-lg">lock</span>
                        <input
                            class="w-full pl-10 pr-12 py-3 bg-white border border-[#c3c6d6] rounded-lg text-sm text-[#091c35] placeholder-[#737685] focus:outline-none focus:ring-2 focus:ring-[#003d9b]/20 focus:border-[#003d9b] transition-all"
                            id="password" name="password" placeholder="••••••••" required type="password">
                        <button type="button" id="toggle-password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#737685] hover:text-[#003d9b] transition-colors">
                            <span class="material-symbols-outlined" id="toggle-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input class="w-4 h-4 text-[#003d9b] border-[#c3c6d6] rounded focus:ring-[#003d9b] transition-all cursor-pointer"
                        id="remember" name="remember" type="checkbox">
                    <label class="ml-2 text-xs text-[#434654] select-none cursor-pointer" for="remember">
                        Keep me signed in for 30 days
                    </label>
                </div>

                <button
                    class="w-full py-3 px-4 bg-[#003d9b] text-white font-semibold text-sm rounded-lg shadow-sm hover:opacity-90 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 group"
                    type="submit">
                    Sign In
                    <span class="material-symbols-outlined text-white group-hover:translate-x-1 transition-transform text-lg">arrow_forward</span>
                </button>
            </form>

            <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg text-[11px] text-[#003d9b] leading-relaxed">
                <span class="font-bold">Info Akses Role:</span><br>
                • Admin: Gunakan email <span class="font-semibold">adminlaundry1@gmail.com</span> atau <span class="font-semibold">adminlaundry2@gmail.com</span><br>
                • Pelanggan: Gunakan akun Anda atau klik daftar di bawah.
            </div>

            <div class="my-6 flex items-center gap-4">
                <div class="h-[1px] flex-1 bg-[#c3c6d6]"></div>
                <span class="text-xs font-semibold text-[#737685] uppercase tracking-wider">Atau Masuk Dengan</span>
                <div class="h-[1px] flex-1 bg-[#c3c6d6]"></div>
            </div>

            <div class="flex justify-center gap-2">
                <form method="POST" action="{{ route('login.store') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="email" value="adminlaundry1@gmail.com">
                    <input type="hidden" name="isGoogleLogin" value="1">
                    <button type="submit"
                        class="flex items-center justify-center gap-3 w-full px-6 py-2.5 border border-[#c3c6d6] rounded-lg text-sm text-[#434654] bg-white hover:bg-[#f0f3ff] transition-all">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span>Masuk Admin via Google</span>
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm text-[#434654]">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-[#003d9b] font-bold hover:underline">Daftar Sekarang</a>
                </p>
            </div>
        </div>

        <footer class="pt-8 text-center">
            <p class="text-[10px] text-[#737685] uppercase tracking-[0.2em]">
                © 2026 LaundroTrack Pro • Operational Precision
            </p>
        </footer>
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