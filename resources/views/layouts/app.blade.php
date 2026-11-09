<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — {{ config('pos.short_name', 'Kopi Kita') }} POS</title>
    <meta name="pos-tax-rate" content="{{ config('pos.tax_rate', 0.10) }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        },
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#0b1329',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Courier New', 'monospace'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .receipt-font { font-family: 'JetBrains Mono', monospace; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- SIDEBAR NAVIGASI (Presisi Mockup: Navy Dark #0f172a) -->
    <aside class="w-64 bg-[#0f172a] text-white flex flex-col flex-shrink-0 z-30 transition-all duration-300">
        <!-- Logo & Brand Header -->
        <div class="px-6 py-6 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/30 flex items-center justify-center text-orange-500 shadow-sm">
                    <i class="fa-solid fa-mug-hot text-xl"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-lg tracking-tight leading-none text-white">Kopi Kita</h1>
                    <span class="text-[11px] font-medium text-slate-400 tracking-wider">POS & Cafe Ops</span>
                </div>
            </div>
        </div>

        <!-- Menu Navigation Items -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-[#f97316] text-white shadow-lg shadow-orange-500/25 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-grip text-base w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <!-- 2. POS / Transaksi -->
            <a href="{{ route('transaksi.index') }}" 
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('transaksi.*') ? 'bg-[#f97316] text-white shadow-lg shadow-orange-500/25 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-cart-shopping text-base w-5 text-center"></i>
                <span>POS / Transaksi</span>
            </a>

            <!-- 3. Data Menu -->
            <a href="{{ route('menu.index') }}" 
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('menu.*') ? 'bg-[#f97316] text-white shadow-lg shadow-orange-500/25 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-mug-saucer text-base w-5 text-center"></i>
                <span>Data Menu</span>
            </a>

            <!-- 4. Laporan Penjualan -->
            <a href="{{ route('laporan.index') }}" 
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('laporan.*') ? 'bg-[#f97316] text-white shadow-lg shadow-orange-500/25 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-chart-simple text-base w-5 text-center"></i>
                <span>Laporan Penjualan</span>
            </a>
        </nav>

        <!-- Logout Button (Bawah Sidebar) -->
        <div class="p-4 border-t border-slate-800/80">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all">
                    <i class="fa-solid fa-arrow-right-from-bracket text-base w-5 text-center"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA & TOPBAR -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- TOPBAR PERSIS MOCKUP -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between flex-shrink-0 z-20">
            <!-- Left Info Pill: Shift Info & Device Clock -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                    <i class="fa-regular fa-calendar text-slate-500"></i>
                    <span id="liveDeviceDate">Hari ini</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-xs font-mono font-bold text-slate-800">
                    <i class="fa-regular fa-clock text-orange-500"></i>
                    <span id="liveDeviceClock">--:--:--</span>
                </div>
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-orange-50 border border-orange-200 text-xs font-semibold text-orange-700">
                    <i class="fa-solid fa-store text-orange-500"></i>
                    <span>Outlet: {{ config('pos.outlet_name', 'Pati - Sentral 01') }}</span>
                </div>
            </div>

            <!-- Right Profile & Status -->
            <div class="flex items-center gap-4">
                <!-- Tombol Keranjang Hold Order (Pengganti Notifikasi) -->
                <button type="button" 
                        onclick="openHeldOrdersModal()" 
                        class="relative px-3 py-2 text-slate-600 hover:text-orange-600 bg-slate-50 hover:bg-orange-50/80 rounded-xl border border-slate-200 hover:border-orange-200 transition-all flex items-center gap-2 shadow-sm" 
                        title="Lihat Pesanan Tertunda (Hold Order)">
                    <i class="fa-solid fa-cart-shopping text-base text-orange-500"></i>
                    <span class="text-xs font-bold text-slate-700 hidden sm:inline">Hold Order</span>
                    <span id="heldOrdersBadge" class="hidden px-2 py-0.5 rounded-full bg-orange-500 text-white text-[10px] font-extrabold shadow-sm min-w-[20px] text-center">0</span>
                </button>

                <!-- User Profile Badge -->
                <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                    <div class="text-right">
                        <div class="text-xs font-bold text-slate-800 leading-tight">
                            {{ Auth::user()->name ?? 'Kasir Utama' }}
                        </div>
                        <div class="flex items-center justify-end gap-1.5 text-[11px] font-semibold text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                            <span>Online &bull; KASIR</span>
                        </div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" 
                         alt="Avatar" 
                         class="w-9 h-9 rounded-full object-cover ring-2 ring-orange-500/30">
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFICATION ALERTS -->
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-fade-in" role="alert">
                    <div class="flex items-center gap-2.5 text-sm font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-fade-in" role="alert">
                    <div class="flex items-center gap-2.5 text-sm font-medium">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center gap-2 text-sm font-bold mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                        <span>Perhatian Form:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-amber-700 ml-4">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- MAIN SCROLLABLE CONTENT BODY -->
        <main class="flex-1 overflow-y-auto px-8 py-6">
            @yield('content')
        </main>
    </div>

    <!-- GLOBAL INLINE TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed top-20 right-8 z-50 flex flex-col gap-2.5 pointer-events-none"></div>

    <!-- MODAL KERANJANG HOLD ORDER (Pesanan Tertunda) -->
    <div id="heldOrdersModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-fade-in flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-tight">Daftar Hold Order</h3>
                        <span class="text-xs text-slate-400">Keranjang pesanan yang disimpan sementara</span>
                    </div>
                </div>
                <button type="button" onclick="closeHeldOrdersModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Scrollable Content List -->
            <div id="heldOrdersContainer" class="flex-1 overflow-y-auto my-4 space-y-3 pr-1">
                <!-- Dynamically populated -->
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between flex-shrink-0">
                <button type="button" onclick="clearAllHeldOrders()" class="text-xs text-rose-500 hover:text-rose-700 font-bold">
                    Kosongkan Semua
                </button>
                <button type="button" onclick="closeHeldOrdersModal()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function updateLiveDeviceClock() {
            const clockEl = document.getElementById('liveDeviceClock');
            const dateEl = document.getElementById('liveDeviceDate');
            if (!clockEl && !dateEl) return;

            const now = new Date();
            if (clockEl) {
                clockEl.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            }
            if (dateEl) {
                dateEl.innerText = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
            }
        }
        setInterval(updateLiveDeviceClock, 1000);
        updateLiveDeviceClock();

        function getHeldOrders() {
            try {
                return JSON.parse(localStorage.getItem('kopikita_held_orders') || '[]');
            } catch(e) {
                return [];
            }
        }

        function saveHeldOrders(orders) {
            localStorage.setItem('kopikita_held_orders', JSON.stringify(orders));
            updateHeldOrdersBadge();
        }

        function updateHeldOrdersBadge() {
            const badge = document.getElementById('heldOrdersBadge');
            if (!badge) return;
            const orders = getHeldOrders();
            if (orders.length > 0) {
                badge.innerText = orders.length;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
        document.addEventListener('DOMContentLoaded', updateHeldOrdersBadge);

        function openHeldOrdersModal() {
            renderHeldOrdersList();
            document.getElementById('heldOrdersModal').classList.remove('hidden');
        }

        function closeHeldOrdersModal() {
            document.getElementById('heldOrdersModal').classList.add('hidden');
        }

        function renderHeldOrdersList() {
            const container = document.getElementById('heldOrdersContainer');
            if (!container) return;
            const orders = getHeldOrders();

            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="py-12 text-center text-slate-400">
                        <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3 text-lg">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-600">Belum ada pesanan yang di-hold</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Gunakan tombol logo keranjang di samping tombol bayar pada halaman POS untuk menyimpan antrean sementara.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = orders.map((order, index) => {
                const itemCount = order.cart ? order.cart.reduce((sum, item) => sum + item.qty, 0) : 0;
                const subtotal = order.cart ? order.cart.reduce((sum, item) => sum + (item.price * item.qty), 0) : 0;
                const taxRate = parseFloat(document.querySelector('meta[name="pos-tax-rate"]')?.content || '0.10');
                const grandTotal = subtotal + Math.round(subtotal * taxRate);

                return `
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-orange-300 transition-all">
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200/60">
                            <div>
                                <span class="text-xs font-extrabold text-slate-900">${order.table || 'Meja Bebas'} &bull; ${order.customer || 'Pelanggan'}</span>
                                <span class="text-[10px] text-slate-400 block">${order.savedAt || 'Hari ini'} &bull; ${order.type === 'takeaway' ? 'Take Away' : 'Dine In'}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-orange-600">Rp ${grandTotal.toLocaleString('id-ID')}</span>
                                <span class="text-[10px] text-slate-400 block">${itemCount} item</span>
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-600 space-y-1 mb-3">
                            ${(order.cart || []).map(i => `
                                <div class="flex justify-between">
                                    <span>${i.qty}x ${i.name}</span>
                                    <span class="font-semibold text-slate-800">Rp ${(i.price * i.qty).toLocaleString('id-ID')}</span>
                                </div>
                            `).join('')}
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200/60">
                            <button type="button" onclick="deleteHeldOrder(${index})" class="px-3 py-1.5 rounded-xl border border-slate-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition-all">
                                <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                            </button>
                            <button type="button" onclick="restoreHeldOrder(${index})" class="px-4 py-1.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm shadow-orange-500/25">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Pulihkan Pesanan
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function deleteHeldOrder(index) {
            const orders = getHeldOrders();
            orders.splice(index, 1);
            saveHeldOrders(orders);
            renderHeldOrdersList();
            showToast('Pesanan berhasil dihapus dari Hold Order.', 'info');
        }

        function clearAllHeldOrders() {
            saveHeldOrders([]);
            renderHeldOrdersList();
            showToast('Semua pesanan tertunda telah dibersihkan.', 'info');
        }

        function restoreHeldOrder(index) {
            const orders = getHeldOrders();
            const selected = orders[index];
            if (!selected) return;

            if (typeof restoreOrderToPos === 'function') {
                restoreOrderToPos(selected);
                orders.splice(index, 1);
                saveHeldOrders(orders);
                closeHeldOrdersModal();
                showToast('Pesanan berhasil dipulihkan ke kasir!', 'success');
            } else {
                sessionStorage.setItem('kopikita_pending_restore', JSON.stringify(selected));
                orders.splice(index, 1);
                saveHeldOrders(orders);
                window.location.href = "{{ route('transaksi.index') }}";
            }
        }

        function showToast(message, type = 'warning') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            const bgClass = type === 'success' 
                ? 'bg-emerald-600 text-white' 
                : (type === 'error' ? 'bg-rose-600 text-white' : (type === 'warning' ? 'bg-amber-600 text-white' : 'bg-slate-900 text-white'));
            const iconClass = type === 'success' 
                ? 'fa-circle-check' 
                : (type === 'error' ? 'fa-circle-xmark' : (type === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-info'));

            toast.className = `pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-2xl shadow-xl text-xs font-bold transition-all duration-300 transform translate-y-2 opacity-0 ${bgClass}`;
            toast.innerHTML = `
                <i class="fa-solid ${iconClass} text-sm"></i>
                <span>${message}</span>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            // Auto dismiss after 3 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3200);
        }
    </script>

    @stack('scripts')
</body>
</html>
