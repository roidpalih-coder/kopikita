@extends('layouts.auth')

@section('content')
<div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-slate-200/80">
    
    <!-- SISI KIRI (NAVY DARK MOCKUP #0f172a) -->
    <div class="md:w-5/12 bg-[#0f172a] p-8 md:p-12 text-white flex flex-col justify-between relative overflow-hidden">
        <!-- Background subtle glow -->
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div>
            <!-- Top Badges -->
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold tracking-wider mb-10">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
                    <span>Terminal Kasir Aktif</span>
                </div>
                <span>POS SUITE V2.4</span>
            </div>

            <!-- Logo Icon Kopi Oranye -->
            <div class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/30 flex items-center justify-center text-orange-500 text-3xl mb-8 shadow-inner">
                <i class="fa-solid fa-mug-hot"></i>
            </div>

            <!-- Headings -->
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white leading-tight mb-4">
                Sistem Informasi Kasir &amp; Manajemen Kafe Modern
            </h2>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">
                Platform operasional terintegrasi untuk efisiensi layanan meja, inventori cepat, dan omzet terukur.
            </p>

            <!-- 3 Poin Fitur Utama Sesuai Mockup -->
            <div class="space-y-5">
                <div class="flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-500 text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-200">Pencatatan Transaksi Cepat &amp; Akurat</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Desain ergonomis layar sentuh kasir berkecepatan tinggi.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-500 text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-200">Manajemen Inventaris Stok Real-time</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Sinkronisasi otomatis stok bahan biji kopi, susu, dan sirup.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-500 text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-200">Laporan Omzet Harian Otomatis</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Rekap penutupan shift kasir akurat tanpa selisih kas fisik.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Keamanan Bawah -->
        <div class="pt-10 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400 font-medium">
            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-wifi"></i>
                <span>Mode Offline Siap</span>
            </div>
            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                <span>Terenkripsi SHA-256</span>
            </div>
        </div>
    </div>

    <!-- SISI KANAN (FORM LOGIN PUTIH) -->
    <div class="md:w-7/12 p-8 md:p-14 bg-white flex flex-col justify-between">
        
        <!-- Outlet Pill Header -->
        <div class="flex justify-end mb-6">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                <span class="text-orange-500 font-bold uppercase text-[10px]">store</span>
                <span>Outlet: {{ config('pos.outlet_name', 'Pati - Sentral 01') }}</span>
            </span>
        </div>

        <!-- Form Container -->
        <div class="max-w-md mx-auto w-full">
            <div class="mb-8">
                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h3>
                <p class="text-slate-500 text-sm mt-1.5">Silakan masuk ke akun kasir Anda</p>
            </div>

            <!-- Error message alert -->
            @if(isset($errors) && $errors->has('login'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                    <span>{{ $errors->first('login') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-blue-500 text-base"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- Notice info banner (Lupa Password / Info) -->
            <div id="loginNoticeBanner" class="hidden mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 text-sm"></i>
                    <span id="loginNoticeText">Untuk reset kata sandi, silakan hubungi Administrator cabang.</span>
                </div>
                <button type="button" onclick="hideLoginNotice()" class="text-amber-500 hover:text-amber-700 p-0.5">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5" id="loginForm" onsubmit="return validateLoginForm(event)" novalidate>
                @csrf

                <!-- Input Username / Email -->
                <div>
                    <label for="loginInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username atau Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <input type="text" 
                               name="login" 
                               id="loginInput" 
                               value="{{ old('login', 'kasir') }}" 
                               oninput="clearAuthError('loginInput', 'loginError')"
                               placeholder="kasir.utama" 
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm font-medium text-slate-900 transition-all outline-none bg-slate-50/50 focus:bg-white">
                    </div>
                    <p id="loginError" class="hidden text-[11px] text-rose-500 font-semibold mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span>Username atau email wajib diisi</span>
                    </p>
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <label for="passwordInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="passwordInput" 
                               value="kasir123" 
                               oninput="clearAuthError('passwordInput', 'passwordError')"
                               placeholder="••••••••" 
                               class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm font-medium text-slate-900 transition-all outline-none bg-slate-50/50 focus:bg-white">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <i id="eyeIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <p id="passwordError" class="hidden text-[11px] text-rose-500 font-semibold mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span>Kata sandi wajib diisi</span>
                    </p>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-orange-500 focus:ring-orange-500 border-slate-300">
                        <span class="text-slate-600 font-medium">Ingat Saya di perangkat ini</span>
                    </label>
                    <a href="javascript:void(0)" onclick="showLoginNotice('Untuk reset kata sandi, silakan hubungi supervisor outlet.')" class="font-bold text-orange-600 hover:text-orange-700">Lupa Password?</a>
                </div>

                <!-- Tombol Submit Oranye Persis Mockup -->
                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white font-bold text-sm tracking-wide shadow-lg shadow-orange-500/30 hover:shadow-orange-500/40 transition-all flex items-center justify-center gap-2.5">
                    <i class="fa-solid fa-cash-register text-base"></i>
                    <span>Masuk ke Sistem Kasir</span>
                </button>
            </form>
        </div>

        <!-- Copyright Bawah -->
        <div class="text-center text-[11px] text-slate-400 mt-10">
            &copy; {{ date('Y') }} {{ config('pos.short_name', 'Kopi Kita') }} POS &bull; Versi {{ config('pos.version', '2.4.0') }} &bull; {{ config('pos.institution', 'SMK Tunas Harapan Pati') }}
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function showLoginNotice(message) {
        const banner = document.getElementById('loginNoticeBanner');
        const text = document.getElementById('loginNoticeText');
        if (text) text.innerText = message;
        if (banner) banner.classList.remove('hidden');
    }

    function hideLoginNotice() {
        const banner = document.getElementById('loginNoticeBanner');
        if (banner) banner.classList.add('hidden');
    }

    function clearAuthError(inputId, errorId) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(errorId);
        if (input) {
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20', 'bg-rose-50/20');
            input.classList.add('border-slate-200');
        }
        if (errorEl) {
            errorEl.classList.add('hidden');
        }
    }

    function setAuthError(inputId, errorId, message) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(errorId);
        if (input) {
            input.classList.remove('border-slate-200');
            input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20', 'bg-rose-50/20');
        }
        if (errorEl) {
            if (message) {
                const span = errorEl.querySelector('span');
                if (span) span.innerText = message;
            }
            errorEl.classList.remove('hidden');
        }
    }

    function validateLoginForm(event) {
        let isValid = true;
        let firstInvalid = null;

        const loginVal = document.getElementById('loginInput').value.trim();
        if (!loginVal) {
            setAuthError('loginInput', 'loginError', 'Username atau email wajib diisi');
            isValid = false;
            firstInvalid = firstInvalid || document.getElementById('loginInput');
        } else {
            clearAuthError('loginInput', 'loginError');
        }

        const passwordVal = document.getElementById('passwordInput').value;
        if (!passwordVal) {
            setAuthError('passwordInput', 'passwordError', 'Kata sandi wajib diisi');
            isValid = false;
            firstInvalid = firstInvalid || document.getElementById('passwordInput');
        } else {
            clearAuthError('passwordInput', 'passwordError');
        }

        if (!isValid) {
            if (event) event.preventDefault();
            if (firstInvalid) firstInvalid.focus();
            return false;
        }

        return true;
    }
</script>
@endsection
