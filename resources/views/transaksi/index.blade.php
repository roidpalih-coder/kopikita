@extends('layouts.app')

@section('title', 'POS Transaksi Kasir')

@section('content')
<div class="h-full flex flex-col lg:flex-row gap-6 -my-2">
    
    <!-- SISI KIRI & TENGAH: KATALOG MENU (Presisi Mockup 3) -->
    <div class="flex-1 flex flex-col min-w-0 bg-transparent overflow-hidden">
        
        <!-- Top Toolbar: Search, Status, Sort -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 flex-shrink-0">
            <!-- Search bar -->
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" 
                       id="menuSearchInput" 
                       placeholder="Cari menu kopi, makanan, snack..." 
                       class="w-full pl-11 pr-4 py-2.5 rounded-2xl bg-white border border-slate-200/90 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 shadow-sm transition-all">
            </div>

            <!-- Status Kasir & Urutkan -->
            <div class="flex items-center gap-2.5">
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Kasir Siap &bull; <strong id="menuAvailableCount">{{ $menus->count() }}</strong> Menu Tersedia</span>
                </div>
                <select id="sortSelect" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm outline-none cursor-pointer">
                    <option value="default">Urutkan: Rekomendasi</option>
                    <option value="price-asc">Harga Terendah</option>
                    <option value="price-desc">Harga Tertinggi</option>
                    <option value="name">Nama Menu</option>
                </select>
            </div>
        </div>

        <!-- Filter Kategori Tabs Sesuai Mockup -->
        <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-2 flex-shrink-0">
            <button type="button" 
                    onclick="filterCategory('all', this)" 
                    class="category-tab active px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-[#0f172a] text-white shadow-sm flex-shrink-0">
                <i class="fa-solid fa-utensils text-orange-400"></i>
                <span>Semua</span>
                <span class="px-1.5 py-0.5 rounded-md bg-white/20 text-[10px]">{{ $categoryCounts['all'] }}</span>
            </button>

            <button type="button" 
                    onclick="filterCategory('coffee', this)" 
                    class="category-tab px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 shadow-sm flex-shrink-0">
                <i class="fa-solid fa-mug-hot text-amber-600"></i>
                <span>Coffee</span>
                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-[10px] text-slate-600">{{ $categoryCounts['coffee'] }}</span>
            </button>

            <button type="button" 
                    onclick="filterCategory('non-coffee', this)" 
                    class="category-tab px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 shadow-sm flex-shrink-0">
                <i class="fa-solid fa-glass-water text-purple-600"></i>
                <span>Non-Coffee</span>
                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-[10px] text-slate-600">{{ $categoryCounts['non-coffee'] }}</span>
            </button>

            <button type="button" 
                    onclick="filterCategory('makanan', this)" 
                    class="category-tab px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 shadow-sm flex-shrink-0">
                <i class="fa-solid fa-bowl-food text-orange-600"></i>
                <span>Makanan</span>
                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-[10px] text-slate-600">{{ $categoryCounts['makanan'] }}</span>
            </button>

            <button type="button" 
                    onclick="filterCategory('snack', this)" 
                    class="category-tab px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 shadow-sm flex-shrink-0">
                <i class="fa-solid fa-cookie-bite text-emerald-600"></i>
                <span>Snack</span>
                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-[10px] text-slate-600">{{ $categoryCounts['snack'] }}</span>
            </button>
        </div>

        <!-- Grid Katalog Produk Menu (Scrollable) -->
        <div class="flex-1 overflow-y-auto pr-1">
            <div id="menuGrid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 pb-6">
                @foreach($menus as $menu)
                <div class="menu-card bg-white rounded-2xl border border-slate-200/80 p-3 flex flex-col justify-between shadow-sm hover:shadow-md transition-all group"
                     id="menu-card-{{ $menu->id }}"
                     data-id="{{ $menu->id }}"
                     data-name="{{ $menu->nama }}"
                     data-price="{{ $menu->harga_jual }}"
                     data-category="{{ $menu->kategori }}"
                     data-stock="{{ $menu->stok }}"
                     data-image="{{ $menu->gambar_url }}">
                    
                    <!-- Foto & Category Badge -->
                    <div class="relative w-full h-32 rounded-xl overflow-hidden bg-slate-100 mb-3">
                        <img src="{{ $menu->gambar_url }}" 
                             alt="{{ $menu->nama }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                        
                        @php $lowStockThreshold = config('pos.low_stock_threshold', 15); @endphp
                        <!-- Badge Kategori / Stok -->
                        <div class="absolute top-2 right-2 flex flex-col items-end gap-1" id="menu-badge-{{ $menu->id }}">
                            @if($menu->stok <= 0)
                                <span class="px-2 py-0.5 rounded-md bg-slate-900/90 backdrop-blur-sm text-white text-[10px] font-extrabold shadow-sm">
                                    Habis
                                </span>
                            @elseif($menu->stok <= $lowStockThreshold)
                                <span class="px-2 py-0.5 rounded-md bg-rose-500/90 backdrop-blur-sm text-white text-[10px] font-extrabold shadow-sm">
                                    Stok Menipis
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-900/70 backdrop-blur-sm text-white text-[10px] font-bold capitalize shadow-sm">
                                    {{ $menu->kategori }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Menu -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 leading-snug line-clamp-1 group-hover:text-orange-600 transition-colors">
                            {{ $menu->nama }}
                        </h4>
                        
                        <div class="flex items-center justify-between mt-2">
                            <div>
                                <div class="text-sm font-extrabold text-slate-900">
                                    Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}
                                </div>
                                <div id="menu-stock-{{ $menu->id }}" class="text-[11px] {{ $menu->stok <= $lowStockThreshold ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                    {{ $menu->stok <= $lowStockThreshold ? ($menu->stok <= 0 ? 'Habis' : 'Sisa ' . $menu->stok) : 'Stok ' . $menu->stok }}
                                </div>
                            </div>
                            
                            <!-- Tombol Tambah ke Keranjang -->
                            <button type="button" 
                                    id="menu-btn-{{ $menu->id }}"
                                    onclick="addToCart({{ $menu->id }})" 
                                    {{ $menu->stok <= 0 ? 'disabled' : '' }}
                                    class="py-1.5 px-3 rounded-xl {{ $menu->stok <= 0 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-50 hover:bg-orange-500 text-slate-700 hover:text-white border border-slate-200 hover:border-orange-500' }} text-xs font-bold transition-all flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- SISI KANAN: PANEL PESANAN AKTIF (Presisi Mockup 3) -->
    <div class="w-full lg:w-96 bg-white rounded-3xl border border-slate-200/90 p-5 flex flex-col justify-between shadow-sm flex-shrink-0">
        
        <!-- Header Panel Pesanan -->
        <div class="pb-4 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 leading-tight">Pesanan Aktif</h3>
                        <span class="text-[11px] text-slate-400">Shift 1 &bull; {{ Auth::user()->name }}</span>
                    </div>
                </div>

                <!-- Kode Order Badge -->
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-mono text-xs font-bold text-slate-800 border border-slate-200">
                    {{ $orderCode }}
                </span>
            </div>

            <!-- Toggle Dine In / Take Away Sesuai Mockup -->
            <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-100 border border-slate-200/80 mb-3 text-xs font-bold">
                <button type="button" 
                        onclick="setOrderType('dine-in')" 
                        id="btnDineIn"
                        class="py-1.5 rounded-lg bg-white text-slate-900 shadow-sm transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-utensils text-orange-500"></i>
                    <span>Dine In</span>
                </button>
                <button type="button" 
                        onclick="setOrderType('takeaway')" 
                        id="btnTakeAway"
                        class="py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>Take Away</span>
                </button>
            </div>

            <!-- Input Meja & Pelanggan -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <div class="relative">
                        <i class="fa-solid fa-chair absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               id="nomorMejaInput" 
                               value="" 
                               oninput="clearFieldError('nomorMejaInput', 'nomorMejaError')"
                               placeholder="Nomor Meja (cth: 07)" 
                               class="w-full pl-8 pr-2.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 outline-none focus:bg-white focus:border-orange-500 transition-all">
                    </div>
                    <p id="nomorMejaError" class="hidden text-[10px] text-rose-500 font-semibold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[9px]"></i>
                        <span>Nomor meja wajib diisi</span>
                    </p>
                </div>
                <div>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               id="namaPelangganInput" 
                               value="" 
                               oninput="clearFieldError('namaPelangganInput', 'namaPelangganError')"
                               placeholder="Nama Pelanggan" 
                               class="w-full pl-8 pr-2.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 outline-none focus:bg-white focus:border-orange-500 transition-all">
                    </div>
                    <p id="namaPelangganError" class="hidden text-[10px] text-rose-500 font-semibold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[9px]"></i>
                        <span>Nama pelanggan wajib diisi</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Inline Warning / Notice Banner (Tanpa alert popup) -->
        <div id="cartNoticeBanner" class="hidden mx-0.5 mt-2 p-2.5 rounded-xl border text-xs font-semibold flex items-center justify-between transition-all">
            <div class="flex items-center gap-2">
                <i id="cartNoticeIcon" class="fa-solid fa-triangle-exclamation text-sm"></i>
                <span id="cartNoticeText"></span>
            </div>
            <button type="button" onclick="hideCartNotice()" class="text-slate-400 hover:text-slate-600 p-0.5"><i class="fa-solid fa-xmark text-xs"></i></button>
        </div>

        <!-- Daftar Item Pesanan dalam Keranjang (Scrollable) -->
        <div id="cartItemsContainer" class="flex-1 overflow-y-auto py-3 space-y-3 min-h-[180px] max-h-[300px] pr-1">
            <!-- Diisi secara dinamis via JavaScript -->
        </div>

        <!-- Rincian Kalkulasi & Pembayaran -->
        <div class="pt-3 border-t border-slate-100 flex-shrink-0 space-y-2">
            
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Subtotal</span>
                <span class="font-bold text-slate-800" id="subtotalDisplay">Rp 0</span>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>PB1 Restoran ({{ (int)(config('pos.tax_rate', 0.10) * 100) }}%)</span>
                <span class="font-bold text-slate-800" id="taxDisplay">Rp 0</span>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Diskon Member (0%)</span>
                <span class="font-bold text-slate-800">Rp 0</span>
            </div>

            <!-- Total Akhir Oranye Besar Persis Mockup -->
            <div class="p-3 rounded-2xl bg-orange-50/70 border border-orange-200/80 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-700">Total Tagihan</span>
                    <div class="text-[11px] text-slate-500 font-semibold" id="cartItemCountDisplay">0 Item Pesanan</div>
                </div>
                <div class="text-xl font-black text-[#f97316]" id="grandTotalDisplay">
                    Rp 0
                </div>
            </div>

            <!-- Pilihan Metode Pembayaran Cepat -->
            <div class="pt-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Metode Pembayaran Cepat:</span>
                <div class="grid grid-cols-3 gap-2 text-xs font-bold">
                    <button type="button" 
                            onclick="setPaymentMethod('tunai')" 
                            id="btnPayTunai"
                            class="payment-method-btn py-2 px-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition-all flex flex-col items-center gap-1">
                        <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
                        <span>Tunai</span>
                    </button>
                    <button type="button" 
                            onclick="setPaymentMethod('qris')" 
                            id="btnPayQris"
                            class="payment-method-btn active py-2 px-1 rounded-xl bg-[#0f172a] text-white border border-[#0f172a] shadow-sm transition-all flex flex-col items-center gap-1">
                        <i class="fa-solid fa-qrcode text-orange-400"></i>
                        <span>QRIS</span>
                    </button>
                    <button type="button" 
                            onclick="setPaymentMethod('debit')" 
                            id="btnPayDebit"
                            class="payment-method-btn py-2 px-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition-all flex flex-col items-center gap-1">
                        <i class="fa-solid fa-credit-card text-blue-600"></i>
                        <span>Debit/EDC</span>
                    </button>
                </div>
            </div>

            <!-- Field Input Nominal Bayar (Tampil saat Tunai) -->
            <div id="cashInputContainer" class="hidden pt-1">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" 
                               id="cashReceivedInput" 
                               oninput="calculateChange(); clearFieldError('cashReceivedInput', 'cashReceivedError')"
                               placeholder="Jumlah Uang Diterima" 
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-orange-500 transition-all">
                    </div>
                    <div class="text-right px-2 flex-shrink-0">
                        <span class="text-[10px] text-slate-400 block">Kembalian:</span>
                        <strong class="text-xs text-emerald-600 font-extrabold" id="cashChangeDisplay">Rp 0</strong>
                    </div>
                </div>
                <p id="cashReceivedError" class="hidden text-[10px] text-rose-500 font-bold mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[9px]"></i>
                    <span id="cashReceivedErrorText">Uang pembayaran kurang dari total tagihan!</span>
                </p>
            </div>

            <!-- FORM POST KE BACKEND -->
            <form action="{{ route('transaksi.store') }}" method="POST" id="checkoutForm" onsubmit="return handleCheckout(event)">
                @csrf
                <input type="hidden" name="items" id="cartPayloadInput">
                <input type="hidden" name="tipe_pesanan" id="tipePesananInput" value="dine-in">
                <input type="hidden" name="nomor_meja" id="finalNomorMejaInput">
                <input type="hidden" name="nama_pelanggan" id="finalNamaPelangganInput">
                <input type="hidden" name="metode_pembayaran" id="metodePembayaranInput" value="qris">
                <input type="hidden" name="bayar" id="finalBayarInput">

                <!-- Tombol Hold Order (Logo Keranjang) & Bayar Sekarang Berdampingan -->
                <div class="flex items-center gap-2 mt-3">
                    <button type="button" 
                            onclick="holdCurrentOrder()" 
                            id="btnHoldOrder"
                            title="Simpan Pesanan ke Hold Order"
                            class="py-3.5 px-4 rounded-2xl bg-slate-100 hover:bg-orange-100/70 border border-slate-200 hover:border-orange-300 text-slate-700 hover:text-orange-600 transition-all flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-cart-shopping text-base"></i>
                    </button>
                    <button type="submit" 
                            id="btnSubmitCheckout"
                            class="flex-1 py-3.5 px-4 rounded-2xl bg-[#f97316] hover:bg-[#ea580c] text-white font-extrabold text-sm tracking-wide shadow-lg shadow-orange-500/30 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cash-register"></i>
                        <span id="btnCheckoutText">BAYAR SEKARANG (Rp 0)</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<!-- MODAL POPUP SELESAI DIBAYAR (CETAK STRUK & TRANSAKSI BARU) -->
<div id="checkoutSuccessModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-fade-in text-center">
        <!-- Icon Success -->
        <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mb-4 shadow-sm">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h3 class="text-lg font-black text-slate-900">Pembayaran Berhasil!</h3>
        <p class="text-xs text-slate-500 mt-1 mb-5">Transaksi telah dicatat dan stok menu otomatis diperbarui.</p>

        <!-- Kartu Rincian Transaksi -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs space-y-2 mb-5">
            <div class="flex justify-between">
                <span class="text-slate-400">Kode Transaksi:</span>
                <strong id="modalTrxCode" class="font-mono font-bold text-slate-900">-</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Pelanggan / Meja:</span>
                <span id="modalTrxCustomer" class="font-bold text-slate-800">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Metode Bayar:</span>
                <span id="modalTrxMethod" class="font-bold text-slate-800">-</span>
            </div>
            <div class="border-t border-slate-200 pt-2 flex justify-between">
                <span class="text-slate-600 font-bold">Total Pembayaran:</span>
                <strong id="modalTrxTotal" class="text-emerald-600 font-extrabold text-sm">Rp 0</strong>
            </div>
            <div id="modalCashRow" class="flex justify-between text-slate-500">
                <span>Diterima / Kembalian:</span>
                <span id="modalTrxCash" class="font-semibold text-slate-700">Rp 0 / Rp 0</span>
            </div>
        </div>

        <!-- Tombol Aksi: Cetak Struk & Transaksi Baru -->
        <div class="flex flex-col gap-2.5">
            <a id="btnModalPrintReceipt" 
               href="#" 
               target="_blank" 
               class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs tracking-wide shadow-md transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-print text-orange-400"></i>
                <span>Cetak Struk Thermal (PDF)</span>
            </a>
            <button type="button" 
                    onclick="startNewTransaction()" 
                    class="w-full py-3 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs tracking-wide shadow-md shadow-orange-500/25 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Transaksi Baru</span>
            </button>
            <a id="linkModalViewReport" 
               href="{{ route('laporan.index') }}" 
               class="text-[11px] font-bold text-slate-400 hover:text-slate-700 pt-1">
                Buka Riwayat di Laporan Penjualan &rarr;
            </a>
        </div>
    </div>
</div>

<!-- JAVASCRIPT KERANJANG & POS CONTROLLER -->
@push('scripts')
<script>
    // State Menu & Keranjang
    const allMenus = @json($menus);
    let cart = [];
    let currentOrderType = 'dine-in';
    let currentPaymentMethod = 'qris';

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        container.innerHTML = '';

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                    <i class="fa-solid fa-basket-shopping text-3xl mb-2 text-slate-300"></i>
                    <p class="text-xs font-semibold">Keranjang masih kosong</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih menu dari katalog di sebelah kiri</p>
                </div>
            `;
            updateCalculations(0);
            return;
        }

        let subtotal = 0;
        let totalItems = 0;

        cart.forEach((item, index) => {
            const itemSubtotal = item.price * item.qty;
            subtotal += itemSubtotal;
            totalItems += item.qty;

            const itemEl = document.createElement('div');
            itemEl.className = 'p-3 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex flex-col gap-2 hover:bg-slate-100/60 transition-all';
            itemEl.innerHTML = `
                <div class="flex items-start justify-between">
                    <div>
                        <h5 class="text-xs font-bold text-slate-900 leading-tight">${item.name}</h5>
                        <div class="text-[11px] text-slate-400 mt-0.5">Rp ${item.price.toLocaleString('id-ID')} / porsi</div>
                        ${item.notes ? `<div class="text-[10px] text-orange-600 font-semibold mt-0.5 flex items-center gap-1"><i class="fa-solid fa-circle-info text-[9px]"></i> ${item.notes}</div>` : ''}
                    </div>
                    <div class="text-xs font-extrabold text-slate-900">
                        Rp ${itemSubtotal.toLocaleString('id-ID')}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="updateQty(${index}, -1)" class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center text-xs font-bold">
                            <i class="fa-solid fa-minus text-[9px]"></i>
                        </button>
                        <span class="w-8 text-center text-xs font-extrabold text-slate-900">${item.qty}</span>
                        <button type="button" onclick="updateQty(${index}, 1)" class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center text-xs font-bold">
                            <i class="fa-solid fa-plus text-[9px]"></i>
                        </button>
                    </div>

                    <button type="button" onclick="removeFromCart(${index})" class="text-slate-400 hover:text-rose-500 transition-colors p-1" title="Hapus menu">
                        <i class="fa-regular fa-trash-can text-xs"></i>
                    </button>
                </div>
            `;
            container.appendChild(itemEl);
        });

        updateCalculations(subtotal, totalItems);
    }

    function addToCart(menuId) {
        const menu = allMenus.find(m => m.id === menuId);
        if (!menu) return;

        const existing = cart.find(item => item.id === menuId);
        if (existing) {
            if (existing.qty < menu.stok) {
                existing.qty += 1;
            } else {
                showToast(`Stok maksimal "${menu.nama}" hanya ${menu.stok} porsi.`, 'warning');
                return;
            }
        } else {
            cart.push({
                id: menu.id,
                name: menu.nama,
                price: parseFloat(menu.harga_jual),
                qty: 1,
                notes: ''
            });
        }
        hideCartNotice();
        renderCart();
    }

    function updateQty(index, delta) {
        const item = cart[index];
        const menu = allMenus.find(m => m.id === item.id);
        const newQty = item.qty + delta;

        if (newQty <= 0) {
            cart.splice(index, 1);
        } else {
            if (menu && newQty > menu.stok) {
                showToast(`Stok maksimal "${menu.nama}" hanya ${menu.stok} porsi.`, 'warning');
                return;
            }
            item.qty = newQty;
        }
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateCalculations(subtotal, totalItems = 0) {
        const taxRate = parseFloat(document.querySelector('meta[name="pos-tax-rate"]')?.content || '0.10');
        const tax = Math.round(subtotal * taxRate);
        const grandTotal = subtotal + tax;

        document.getElementById('subtotalDisplay').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('taxDisplay').innerText = 'Rp ' + tax.toLocaleString('id-ID');
        document.getElementById('grandTotalDisplay').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        document.getElementById('btnCheckoutText').innerText = `BAYAR SEKARANG (Rp ${grandTotal.toLocaleString('id-ID')})`;
        document.getElementById('cartItemCountDisplay').innerText = `${totalItems} Item Pesanan`;

        calculateChange();
    }

    function setOrderType(type) {
        currentOrderType = type;
        const btnDineIn = document.getElementById('btnDineIn');
        const btnTakeAway = document.getElementById('btnTakeAway');
        const nomorMejaInput = document.getElementById('nomorMejaInput');

        if (type === 'dine-in') {
            btnDineIn.className = 'py-1.5 rounded-lg bg-white text-slate-900 shadow-sm transition-all flex items-center justify-center gap-1.5';
            btnTakeAway.className = 'py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all flex items-center justify-center gap-1.5';
            if (nomorMejaInput.value === 'Takeaway') {
                nomorMejaInput.value = '';
            }
            nomorMejaInput.placeholder = 'Nomor Meja (cth: 07)';
        } else {
            btnTakeAway.className = 'py-1.5 rounded-lg bg-white text-slate-900 shadow-sm transition-all flex items-center justify-center gap-1.5';
            btnDineIn.className = 'py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all flex items-center justify-center gap-1.5';
            nomorMejaInput.value = 'Takeaway';
            nomorMejaInput.placeholder = 'Takeaway';
            clearFieldError('nomorMejaInput', 'nomorMejaError');
        }
        document.getElementById('tipePesananInput').value = type;
    }

    function setPaymentMethod(method) {
        currentPaymentMethod = method;
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            btn.className = 'payment-method-btn py-2 px-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition-all flex flex-col items-center gap-1';
        });

        const activeBtn = document.getElementById(method === 'tunai' ? 'btnPayTunai' : (method === 'qris' ? 'btnPayQris' : 'btnPayDebit'));
        if (activeBtn) {
            activeBtn.className = 'payment-method-btn active py-2 px-1 rounded-xl bg-[#0f172a] text-white border border-[#0f172a] shadow-sm transition-all flex flex-col items-center gap-1';
        }

        const cashContainer = document.getElementById('cashInputContainer');
        if (method === 'tunai') {
            cashContainer.classList.remove('hidden');
        } else {
            cashContainer.classList.add('hidden');
            clearFieldError('cashReceivedInput', 'cashReceivedError');
        }

        document.getElementById('metodePembayaranInput').value = method;
    }

    function calculateChange() {
        const grandTotalText = document.getElementById('grandTotalDisplay').innerText.replace(/[^0-9]/g, '');
        const grandTotal = parseInt(grandTotalText) || 0;
        const cashReceived = parseInt(document.getElementById('cashReceivedInput').value) || 0;
        const change = Math.max(0, cashReceived - grandTotal);

        document.getElementById('cashChangeDisplay').innerText = 'Rp ' + change.toLocaleString('id-ID');
    }

    function setFieldError(inputId, errorId, message) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(errorId);
        if (input) {
            input.classList.remove('border-slate-200', 'bg-slate-50');
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

    function clearFieldError(inputId, errorId) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(errorId);
        if (input) {
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20', 'bg-rose-50/20');
            input.classList.add('border-slate-200', 'bg-slate-50');
        }
        if (errorEl) {
            errorEl.classList.add('hidden');
        }
    }

    function showCartNotice(message, type = 'warning') {
        const banner = document.getElementById('cartNoticeBanner');
        const text = document.getElementById('cartNoticeText');
        const icon = document.getElementById('cartNoticeIcon');
        if (!banner || !text) return;

        text.innerText = message;
        if (type === 'warning' || type === 'error') {
            banner.className = 'mx-0.5 mt-2 p-2.5 rounded-xl border bg-rose-50 border-rose-200 text-rose-700 text-xs font-semibold flex items-center justify-between transition-all';
            icon.className = 'fa-solid fa-triangle-exclamation text-rose-500 text-sm';
        } else {
            banner.className = 'mx-0.5 mt-2 p-2.5 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center justify-between transition-all';
            icon.className = 'fa-solid fa-circle-check text-emerald-500 text-sm';
        }
        banner.classList.remove('hidden');
    }

    function hideCartNotice() {
        const banner = document.getElementById('cartNoticeBanner');
        if (banner) banner.classList.add('hidden');
    }

    function handleCheckout(event) {
        let isValid = true;

        // 1. Validasi Keranjang Kosong
        if (cart.length === 0) {
            showCartNotice('Keranjang pesanan masih kosong! Pilih menu dari katalog di kiri.', 'error');
            isValid = false;
        }

        // 2. Validasi Nomor Meja (jika Dine-In)
        const mejaInput = document.getElementById('nomorMejaInput');
        if (currentOrderType === 'dine-in' && !mejaInput.value.trim()) {
            setFieldError('nomorMejaInput', 'nomorMejaError', 'Nomor meja wajib diisi untuk Dine In');
            if (isValid) mejaInput.focus();
            isValid = false;
        } else {
            clearFieldError('nomorMejaInput', 'nomorMejaError');
        }

        // 3. Validasi Nama Pelanggan
        const namaInput = document.getElementById('namaPelangganInput');
        if (!namaInput.value.trim()) {
            setFieldError('namaPelangganInput', 'namaPelangganError', 'Nama pelanggan wajib diisi');
            if (isValid) namaInput.focus();
            isValid = false;
        } else {
            clearFieldError('namaPelangganInput', 'namaPelangganError');
        }

        // 4. Validasi Nominal Tunai
        const grandTotalText = document.getElementById('grandTotalDisplay').innerText.replace(/[^0-9]/g, '');
        const grandTotal = parseInt(grandTotalText) || 0;
        const cashInput = document.getElementById('cashReceivedInput');
        const cash = parseInt(cashInput.value) || 0;

        if (currentPaymentMethod === 'tunai') {
            if (cash < grandTotal) {
                const kekurangan = grandTotal - cash;
                setFieldError('cashReceivedInput', 'cashReceivedError', `Uang tunai kurang Rp ${kekurangan.toLocaleString('id-ID')}!`);
                if (isValid) cashInput.focus();
                isValid = false;
            } else {
                clearFieldError('cashReceivedInput', 'cashReceivedError');
            }
        }

        if (!isValid) {
            return false;
        }

        // Siapkan payload form
        const form = document.getElementById('checkoutForm');
        document.getElementById('cartPayloadInput').value = JSON.stringify(cart);
        document.getElementById('finalNomorMejaInput').value = mejaInput.value.trim() || 'Takeaway';
        document.getElementById('finalNamaPelangganInput').value = namaInput.value.trim();
        document.getElementById('finalBayarInput').value = currentPaymentMethod === 'tunai' ? cash : grandTotal;

        const submitBtn = document.getElementById('btnSubmitCheckout');
        const origBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Memproses...</span>';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Gagal memproses transaksi.'); });
            }
            return response.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origBtnText;

            if (data.success) {
                // 1. Update stok di memori & DOM
                if (data.menus) {
                    updateMenusStockInDom(data.menus);
                }

                // 2. Buka Popup Transaksi Berhasil
                showCheckoutSuccessModal(data.transaksi);

                // 3. Reset form & keranjang
                cart = [];
                renderCart();
                document.getElementById('nomorMejaInput').value = '';
                document.getElementById('namaPelangganInput').value = '';
                document.getElementById('cashReceivedInput').value = '';
                hideCartNotice();
            } else {
                showToast(data.message || 'Gagal memproses transaksi.', 'error');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origBtnText;
            showToast(err.message, 'error');
        });

        return false;
    }

    function updateMenusStockInDom(menusList) {
        const lowStock = {{ config('pos.low_stock_threshold', 15) }};
        menusList.forEach(m => {
            const target = allMenus.find(item => item.id === m.id);
            if (target) {
                target.stok = m.stok;
            }

            const card = document.getElementById(`menu-card-${m.id}`);
            if (card) {
                card.dataset.stock = m.stok;
            }

            const stockLabel = document.getElementById(`menu-stock-${m.id}`);
            if (stockLabel) {
                if (m.stok <= 0) {
                    stockLabel.className = 'text-[11px] text-rose-600 font-bold';
                    stockLabel.innerText = 'Habis';
                } else if (m.stok <= lowStock) {
                    stockLabel.className = 'text-[11px] text-rose-600 font-bold';
                    stockLabel.innerText = 'Sisa ' + m.stok;
                } else {
                    stockLabel.className = 'text-[11px] text-slate-400';
                    stockLabel.innerText = 'Stok ' + m.stok;
                }
            }

            const badge = document.getElementById(`menu-badge-${m.id}`);
            if (badge) {
                if (m.stok <= 0) {
                    badge.innerHTML = `<span class="px-2 py-0.5 rounded-md bg-slate-900/90 backdrop-blur-sm text-white text-[10px] font-extrabold shadow-sm">Habis</span>`;
                } else if (m.stok <= lowStock) {
                    badge.innerHTML = `<span class="px-2 py-0.5 rounded-md bg-rose-500/90 backdrop-blur-sm text-white text-[10px] font-extrabold shadow-sm">Stok Menipis</span>`;
                } else {
                    badge.innerHTML = `<span class="px-2 py-0.5 rounded-md bg-slate-900/70 backdrop-blur-sm text-white text-[10px] font-bold capitalize shadow-sm">${m.kategori}</span>`;
                }
            }

            const btn = document.getElementById(`menu-btn-${m.id}`);
            if (btn) {
                if (m.stok <= 0) {
                    btn.disabled = true;
                    btn.className = 'py-1.5 px-3 rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed text-xs font-bold transition-all flex items-center gap-1 shadow-sm';
                } else {
                    btn.disabled = false;
                    btn.className = 'py-1.5 px-3 rounded-xl bg-slate-50 hover:bg-orange-500 text-slate-700 hover:text-white border border-slate-200 hover:border-orange-500 text-xs font-bold transition-all flex items-center gap-1 shadow-sm';
                }
            }
        });
    }

    function showCheckoutSuccessModal(trx) {
        document.getElementById('modalTrxCode').innerText = '#' + trx.kode_transaksi;
        document.getElementById('modalTrxCustomer').innerText = `${trx.nama_pelanggan} (${trx.nomor_meja})`;
        document.getElementById('modalTrxMethod').innerText = trx.metode_pembayaran;
        document.getElementById('modalTrxTotal').innerText = 'Rp ' + Number(trx.total_harga).toLocaleString('id-ID');

        const cashRow = document.getElementById('modalCashRow');
        if (trx.metode_pembayaran === 'TUNAI') {
            document.getElementById('modalTrxCash').innerText = `Rp ${Number(trx.bayar).toLocaleString('id-ID')} / Rp ${Number(trx.kembalian).toLocaleString('id-ID')}`;
            cashRow.classList.remove('hidden');
        } else {
            cashRow.classList.add('hidden');
        }

        document.getElementById('btnModalPrintReceipt').href = trx.struk_url;
        document.getElementById('linkModalViewReport').href = trx.laporan_url || "{{ route('laporan.index') }}";

        document.getElementById('checkoutSuccessModal').classList.remove('hidden');
    }

    function closeCheckoutSuccessModal() {
        document.getElementById('checkoutSuccessModal').classList.add('hidden');
    }

    function startNewTransaction() {
        closeCheckoutSuccessModal();
        const searchInput = document.getElementById('menuSearchInput');
        if (searchInput) searchInput.focus();
    }

    function holdCurrentOrder() {
        if (cart.length === 0) {
            showCartNotice('Keranjang kosong, tidak ada pesanan untuk di-hold.', 'warning');
            return;
        }
        const tableVal = document.getElementById('nomorMejaInput').value.trim();
        const customerVal = document.getElementById('namaPelangganInput').value.trim();

        const heldOrder = {
            id: 'HOLD-' + Date.now(),
            table: tableVal || 'Meja Bebas',
            customer: customerVal || 'Pelanggan Walk-In',
            type: currentOrderType,
            cart: JSON.parse(JSON.stringify(cart)),
            savedAt: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
        };

        const orders = getHeldOrders();
        orders.unshift(heldOrder);
        saveHeldOrders(orders);

        cart = [];
        document.getElementById('nomorMejaInput').value = '';
        document.getElementById('namaPelangganInput').value = '';
        renderCart();

        showToast('Pesanan berhasil disimpan di Keranjang Hold Order!', 'success');
        showCartNotice('Pesanan disimpan sementara. Buka tombol Hold Order di atas untuk memulihkan.', 'success');
    }

    function restoreOrderToPos(order) {
        if (!order || !order.cart) return;
        cart = JSON.parse(JSON.stringify(order.cart));
        currentOrderType = order.type || 'dine-in';
        setOrderType(currentOrderType);
        if (order.table && order.table !== 'Takeaway' && order.table !== 'Meja Bebas') {
            document.getElementById('nomorMejaInput').value = order.table;
        }
        if (order.customer && order.customer !== 'Pelanggan Walk-In') {
            document.getElementById('namaPelangganInput').value = order.customer;
        }
        renderCart();
        hideCartNotice();
    }

    // Filter Kategori
    function filterCategory(category, button) {
        document.querySelectorAll('.category-tab').forEach(b => {
            b.className = 'category-tab px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80 shadow-sm flex-shrink-0';
        });
        button.className = 'category-tab active px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-[#0f172a] text-white shadow-sm flex-shrink-0';

        let visibleCount = 0;
        document.querySelectorAll('.menu-card').forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('menuAvailableCount').innerText = visibleCount;
    }

    // Search Filtering
    document.getElementById('menuSearchInput').addEventListener('input', function (e) {
        const query = e.target.value.toLowerCase();
        let visibleCount = 0;
        document.querySelectorAll('.menu-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            if (name.includes(query)) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('menuAvailableCount').innerText = visibleCount;
    });

    // Inisialisasi awal saat load
    document.addEventListener('DOMContentLoaded', () => {
        renderCart();

        // Restore pesanan dari session jika diarahkan dari halaman lain
        const pendingRestore = sessionStorage.getItem('kopikita_pending_restore');
        if (pendingRestore) {
            try {
                const ord = JSON.parse(pendingRestore);
                restoreOrderToPos(ord);
                sessionStorage.removeItem('kopikita_pending_restore');
                showToast('Pesanan berhasil dipulihkan dari antrean!', 'success');
            } catch(e) {}
        }

        // Tampilkan popup jika ada session transaksi sukses (fallback form non-ajax)
        @if(session('transaksi_sukses'))
            const sessTrx = @json(session('transaksi_sukses'));
            showCheckoutSuccessModal({
                kode_transaksi: sessTrx.kode,
                nama_pelanggan: sessTrx.pelanggan,
                nomor_meja: sessTrx.meja,
                metode_pembayaran: sessTrx.metode,
                total_harga: sessTrx.total,
                bayar: sessTrx.bayar,
                kembalian: sessTrx.kembalian,
                struk_url: sessTrx.struk_url,
                laporan_url: sessTrx.laporan_url || "{{ route('laporan.index') }}"
            });
        @endif
    });
</script>
@endpush
@endsection
