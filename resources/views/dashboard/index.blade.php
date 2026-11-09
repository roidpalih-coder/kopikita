@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="space-y-6">

    <!-- HEADER STATUS SHIFT & USER GREETING -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-fire"></i>
                <span>SHIFT 1 BERJALAN &bull; {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            @php
                $hour = (int) date('H');
                $salam = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
            @endphp
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ $salam }}, {{ Auth::user()->name }}
            </h2>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl bg-white border border-slate-200/90 shadow-sm text-xs font-semibold text-slate-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Status Laci Kas: <strong class="text-slate-900">Buka Kas: Rp 500.000</strong></span>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 shadow-sm text-xs font-semibold text-slate-600">
                <i class="fa-regular fa-clock text-slate-400"></i>
                <span>Durasi Shift: 04j : 28m</span>
            </div>
        </div>
    </div>

    <!-- 4 KARTU METRIK UTAMA PERSIS MOCKUP -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Penjualan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                <span>Total Penjualan Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">
                    Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        <span>+{{ $persenPerubahan >= 0 ? $persenPerubahan : 0 }}%</span>
                    </span>
                    <span class="text-[11px] text-slate-400">vs kemarin (Rp {{ number_format($penjualanKemarin / 1000, 0) }}k)</span>
                </div>
            </div>
        </div>

        <!-- 2. Jumlah Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                <span>Jumlah Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">
                    {{ $jumlahTransaksiHariIni }} <span class="text-base font-semibold text-slate-500">Struk</span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold {{ $selisihTransaksi >= 0 ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-rose-600 bg-rose-50 border-rose-200' }} px-2 py-0.5 rounded-md border">
                        <i class="fa-solid {{ $selisihTransaksi >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        <span>{{ $selisihTransaksi >= 0 ? '+' . $selisihTransaksi : $selisihTransaksi }} struk</span>
                    </span>
                    <span class="text-[11px] text-slate-400">vs kemarin</span>
                </div>
            </div>
        </div>

        <!-- 3. Menu Terdaftar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                <span>Menu Terdaftar</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">
                    {{ $menuTerdaftar }} <span class="text-base font-semibold text-slate-500">Produk</span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                        {{ $kategoriCount }} Kategori
                    </span>
                    <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                        {{ $stokKritisCount }} stok kritis
                    </span>
                </div>
            </div>
        </div>

        <!-- 4. Pesanan Terlayani -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                <span>Pesanan Terlayani</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">
                    {{ $pesananTerlayani }} <span class="text-base font-semibold text-slate-500">Items</span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200">
                        <i class="fa-solid fa-chart-pie"></i> {{ $rataRataItemPerTrx }}
                    </span>
                    <span class="text-[11px] text-slate-400">Rata-rata item / struk</span>
                </div>
            </div>
        </div>
    </div>

    <!-- BANNER AKSI CEPAT MEJA KASIR PERSIS MOCKUP -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm border border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-400 border border-orange-500/30 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white leading-tight">Aksi Cepat Meja Kasir</h3>
                <p class="text-xs text-slate-400 mt-0.5">Navigasi langsung untuk operasional shift aktif</p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('transaksi.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white text-xs font-bold shadow-md shadow-orange-500/30 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-cash-register"></i>
                <span>Buka Transaksi Baru (POS)</span>
            </a>
            <a href="{{ route('laporan.export.pdf') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 flex items-center gap-2 transition-all">
                <i class="fa-regular fa-file-pdf text-orange-400"></i>
                <span>Cetak Laporan Shift</span>
            </a>
            <button type="button" 
                    onclick="openTutupKasirModal()" 
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-lock"></i>
                <span>Tutup Kasir</span>
            </button>
        </div>
    </div>

    <!-- DUA PANEL: GRAFIK PENJUALAN HARIAN & 5 MENU TERLARIS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Sisi Kiri: Grafik Penjualan Harian (Chart.js) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Performa Arus Kas</span>
                        <h4 id="chartMainTitle" class="text-base font-extrabold text-slate-900 mt-0.5">Grafik Penjualan Harian</h4>
                    </div>
                    <!-- Filter Pills -->
                    <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 border border-slate-200/80 text-xs font-semibold">
                        <button type="button" id="btnPeriodToday" onclick="switchChartPeriod('today')" class="px-3 py-1 rounded-lg bg-orange-500 text-white shadow-sm transition-all">Hari Ini</button>
                        <button type="button" id="btnPeriodWeek" onclick="switchChartPeriod('week')" class="px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all">Minggu Ini</button>
                        <button type="button" id="btnPeriodMonth" onclick="switchChartPeriod('month')" class="px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all">Bulan Ini</button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-orange-500"></i>
                        <span id="chartSubTitle" class="text-slate-500">Rata-rata Penjualan / Jam:</span>
                        <strong id="chartAvgDisplay" class="text-slate-900">Rp 0</strong>
                    </div>
                    <div class="flex items-center gap-4 text-[11px] font-semibold">
                        <span class="flex items-center gap-1.5 text-orange-600">
                            <span class="w-3 h-3 rounded bg-orange-500"></span> Puncak Penjualan (Peak)
                        </span>
                        <span class="flex items-center gap-1.5 text-slate-500">
                            <span class="w-3 h-3 rounded bg-slate-300"></span> Reguler
                        </span>
                    </div>
                </div>
            </div>

            <!-- Canvas Grafik Bar Chart.js -->
            <div class="relative h-64 mt-4">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Sisi Kanan: 5 Menu Terlaris Sesuai Mockup -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Favorit Pelanggan</span>
                        <h4 class="text-base font-extrabold text-slate-900 mt-0.5">5 Menu Terlaris</h4>
                    </div>
                    <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg border border-orange-200">Shift 1</span>
                </div>

                <!-- List 5 Menu -->
                <div class="space-y-3.5">
                    @forelse($topMenus as $index => $top)
                    <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full {{ $index == 0 ? 'bg-orange-500 text-white font-bold' : ($index == 1 ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600') }} flex items-center justify-center text-xs flex-shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <img src="{{ $top->menu->gambar_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=120&q=80' }}" 
                                 alt="{{ $top->nama_menu }}" 
                                 class="w-10 h-10 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                            <div>
                                <h5 class="text-xs font-bold text-slate-900 leading-tight line-clamp-1">{{ $top->nama_menu }}</h5>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $top->total_terjual }} Terjual &bull; {{ ucfirst($top->menu->kategori ?? 'Minuman') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-extrabold text-slate-900">Rp {{ number_format($top->total_nominal / 1000, 0) }}k</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada transaksi tercatat hari ini.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('menu.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center justify-center gap-1.5">
                    <span>Lihat Semua Katalog Menu</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- TABEL RINGKASAN PESANAN TERBARU PERSIS MOCKUP -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas Terakhir</span>
                <h4 class="text-base font-extrabold text-slate-900 mt-0.5">Ringkasan Pesanan Terbaru</h4>
            </div>

            <!-- Search box -->
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           placeholder="Cari No. TRX / Meja..." 
                           class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 w-48 sm:w-64">
                </div>
                <button class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 text-xs">
                    <i class="fa-solid fa-sliders"></i>
                </button>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200/70">
                    <tr>
                        <th class="py-3.5 px-6">No. Transaksi</th>
                        <th class="py-3.5 px-6">Waktu</th>
                        <th class="py-3.5 px-6">Pelanggan / Lokasi</th>
                        <th class="py-3.5 px-6">Total Pembayaran</th>
                        <th class="py-3.5 px-6">Metode</th>
                        <th class="py-3.5 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $trx)
                    <tr class="hover:bg-slate-50/60 transition-all cursor-pointer" onclick="window.location='{{ route('laporan.index', ['highlight' => $trx->id]) }}'">
                        <td class="py-4 px-6 font-extrabold text-slate-900 font-mono">
                            #{{ $trx->kode_transaksi }}
                        </td>
                        <td class="py-4 px-6 text-slate-500">
                            {{ $trx->created_at->format('H:i') }} WIB
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <i class="{{ $trx->tipe_pesanan === 'dine-in' ? 'fa-solid fa-utensils text-slate-400' : 'fa-solid fa-bag-shopping text-orange-500' }}"></i>
                                <span class="font-bold text-slate-800">{{ ucfirst($trx->tipe_pesanan) }} &bull; {{ $trx->nomor_meja ?? $trx->nama_pelanggan }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-slate-900">
                            Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 font-bold text-slate-700 text-[11px] border border-slate-200">
                                <i class="{{ $trx->metode_pembayaran === 'qris' ? 'fa-solid fa-qrcode text-blue-600' : ($trx->metode_pembayaran === 'tunai' ? 'fa-solid fa-money-bill-wave text-emerald-600' : 'fa-solid fa-credit-card text-purple-600') }}"></i>
                                <span>{{ strtoupper($trx->metode_pembayaran) }}</span>
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Selesai</span>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada aktivitas pesanan hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination Info -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Menampilkan {{ $recentTransactions->count() }} transaksi terbaru</span>
            <a href="{{ route('laporan.index') }}" class="font-bold text-orange-600 hover:text-orange-700">Lihat Semua di Laporan &rarr;</a>
        </div>
    </div>

</div>

<!-- MODAL TUTUP SHIFT KASIR (Tanpa Popup Alert) -->
<div id="tutupKasirModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-fade-in">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 leading-tight">Tutup Shift Kasir</h3>
                    <span class="text-xs text-slate-400">Verifikasi Arus Kas Laci</span>
                </div>
            </div>
            <button onclick="closeTutupKasirModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="my-5 space-y-3">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5 text-xs">
                <div class="flex justify-between items-center text-slate-500">
                    <span>Modal Kas Awal Laci:</span>
                    <strong class="text-slate-800 font-bold">Rp 500.000</strong>
                </div>
                <div class="flex justify-between items-center text-slate-500">
                    <span>Total Penjualan Hari Ini:</span>
                    <strong class="text-slate-800 font-bold">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</strong>
                </div>
                <div class="border-t border-slate-200 pt-2 flex justify-between items-center">
                    <span class="font-extrabold text-slate-900">Total Fisik Uang di Laci:</span>
                    <strong class="text-base font-black text-emerald-600">Rp {{ number_format($totalPenjualanHariIni + 500000, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] leading-relaxed flex items-start gap-2">
                <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 text-xs flex-shrink-0"></i>
                <span>Pastikan jumlah uang tunai di laci dihitung fisik dan sesuai dengan total di atas sebelum serah terima kepada supervisor shift berikutnya.</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
            <a href="{{ route('laporan.export.pdf') }}" 
               onclick="closeTutupKasirModal()" 
               class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Laporan</span>
            </a>
            <button type="button" 
                    onclick="selesaiTutupShift()" 
                    class="px-5 py-2.5 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white text-xs font-bold transition-all shadow-md shadow-orange-500/25">
                Konfirmasi Tutup Shift
            </button>
        </div>
    </div>
</div>

<!-- SCRIPT INISIALISASI CHART.JS (Presisi Mockup: Peak hour highlight) -->
@push('scripts')
<script>
    function openTutupKasirModal() {
        document.getElementById('tutupKasirModal').classList.remove('hidden');
    }

    function closeTutupKasirModal() {
        document.getElementById('tutupKasirModal').classList.add('hidden');
    }

    function selesaiTutupShift() {
        closeTutupKasirModal();
        if (typeof showToast === 'function') {
            showToast('Shift berhasil ditutup! Silakan serahkan laporan fisik kepada supervisor.', 'success');
        }
    }
    let salesChartInstance = null;
    const chartPeriods = {
        today: {
            title: 'Grafik Penjualan Harian (per Jam)',
            sub: 'Rata-rata Penjualan / Jam:',
            labels: @json($hourlyLabels),
            values: @json($hourlyData)
        },
        week: {
            title: 'Grafik Penjualan Mingguan (Senin - Minggu)',
            sub: 'Rata-rata Penjualan / Hari:',
            labels: @json($weeklyLabels),
            values: @json($weeklyData)
        },
        month: {
            title: 'Grafik Penjualan Bulanan (per Periode)',
            sub: 'Rata-rata Penjualan / Periode:',
            labels: @json($monthlyLabels),
            values: @json($monthlyData)
        }
    };

    function getBarColors(values) {
        const maxVal = Math.max(...values);
        if (maxVal <= 0) {
            return values.map(() => '#e2e8f0');
        }
        return values.map(v => v === maxVal && v > 0 ? '#f97316' : '#cbd5e1');
    }

    function switchChartPeriod(period) {
        if (!salesChartInstance || !chartPeriods[period]) return;
        const p = chartPeriods[period];

        // Update button tabs
        const btnToday = document.getElementById('btnPeriodToday');
        const btnWeek = document.getElementById('btnPeriodWeek');
        const btnMonth = document.getElementById('btnPeriodMonth');

        const activeClass = 'px-3 py-1 rounded-lg bg-orange-500 text-white shadow-sm transition-all';
        const inactiveClass = 'px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all';

        btnToday.className = period === 'today' ? activeClass : inactiveClass;
        btnWeek.className = period === 'week' ? activeClass : inactiveClass;
        btnMonth.className = period === 'month' ? activeClass : inactiveClass;

        // Update title and average
        document.getElementById('chartMainTitle').innerText = p.title;
        document.getElementById('chartSubTitle').innerText = p.sub;

        const sum = p.values.reduce((a, b) => a + b, 0);
        const count = p.values.length || 1;
        const avg = Math.round(sum / count);
        document.getElementById('chartAvgDisplay').innerText = 'Rp ' + avg.toLocaleString('id-ID');

        // Update chart data
        salesChartInstance.data.labels = p.labels;
        salesChartInstance.data.datasets[0].data = p.values;
        salesChartInstance.data.datasets[0].backgroundColor = getBarColors(p.values);
        salesChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const current = chartPeriods.today;

        const sum = current.values.reduce((a, b) => a + b, 0);
        const avg = Math.round(sum / (current.values.length || 1));
        document.getElementById('chartAvgDisplay').innerText = 'Rp ' + avg.toLocaleString('id-ID');

        salesChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: current.labels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: current.values,
                    backgroundColor: getBarColors(current.values),
                    borderRadius: 6,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return (value / 1000) + 'k';
                            },
                            font: { family: 'Plus Jakarta Sans', size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
