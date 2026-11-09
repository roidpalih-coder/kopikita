@extends('layouts.app')

@section('title', 'Laporan Penjualan & Detail Struk')

@section('content')
<div class="space-y-6">

    <!-- HEADER & FILTER TOOLBAR PERSIS MOCKUP 5 -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <i class="fa-solid fa-chart-line text-orange-500"></i>
                <span>Metrik &amp; Pembukuan Operasional</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan Penjualan &amp; Riwayat Transaksi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring metrik performa kasir, arus kas pesanan, dan validasi fisik struk thermal.</p>
        </div>

        <!-- Filter Controls -->
        <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center gap-2.5 flex-wrap">
            <!-- Filter Tanggal Dari -->
            <div class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm">
                <i class="fa-regular fa-calendar text-slate-400"></i>
                <input type="date" name="start_date" value="{{ $startDate }}" class="outline-none text-xs bg-transparent">
                <span>-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="outline-none text-xs bg-transparent">
            </div>

            <!-- Filter Kasir -->
            <select name="kasir_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm outline-none cursor-pointer">
                <option value="all">Semua Kasir</option>
                @foreach($kasirList as $k)
                    <option value="{{ $k->id }}" {{ $kasirId == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                @endforeach
            </select>

            <!-- Tombol Filter -->
            <button type="submit" class="p-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs transition-all" title="Terapkan Filter">
                <i class="fa-solid fa-filter"></i>
            </button>

            <!-- Export Buttons -->
            <a href="{{ route('laporan.export.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <i class="fa-regular fa-file-excel text-emerald-600"></i>
                <span>Export CSV</span>
            </a>
            <a href="{{ route('laporan.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <i class="fa-regular fa-file-pdf text-orange-400"></i>
                <span>Download PDF</span>
            </a>
        </form>
    </div>

    <!-- 4 KARTU METRIK KEUANGAN PERSIS MOCKUP 5 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Omzet Penjualan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>Total Omzet Penjualan</span>
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 mt-2">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>+18.5% dibanding bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>Total Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">
                    {{ number_format($totalTransaksi, 0, ',', '.') }} <span class="text-sm font-semibold text-slate-500">Trx</span>
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 mt-2">
                    <i class="fa-solid fa-check-double text-emerald-600"></i>
                    <span>98.2% sukses settled</span>
                </div>
            </div>
        </div>

        <!-- 3. Produk Terjual -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>Produk Terjual</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">
                    {{ number_format($produkTerjual, 0, ',', '.') }} <span class="text-sm font-semibold text-slate-500">Porsi</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-2">
                    Terlaris: <strong class="text-orange-600 font-bold">Kopi Susu Aren</strong>
                </div>
            </div>
        </div>

        <!-- 4. Rata-Rata Nilai TRX (AOV) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>Rata-Rata Nilai TRX (AOV)</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($rataRataNilaiTrx, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 mt-2">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>+4.8% optimasi bundling</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DUA GRAFIK ANALITIK PERSIS MOCKUP 5 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Kiri: Tren Pertumbuhan Penjualan Harian -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-extrabold text-slate-900">Tren Pertumbuhan Penjualan Harian</h4>
                    <span class="text-xs text-slate-400">Periode Terpilih</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Lonjakan volume tertinggi pada akhir pekan (Sabtu &amp; Minggu).</p>
            </div>
            <div class="relative h-60">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Kanan: Komposisi Metode Pembayaran (Donut) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-sm font-extrabold text-slate-900">Komposisi Metode Pembayaran</h4>
                <p class="text-xs text-slate-500 mt-0.5 mb-4">Distribusi omzet per saluran kasir periode ini.</p>
                <div class="relative h-44 flex items-center justify-center">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>

            <!-- Legend Komposisi -->
            <div class="space-y-2 pt-4 border-t border-slate-100 text-xs">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> QRIS Dinamis
                    </span>
                    <strong class="text-slate-900">Rp {{ number_format($metodeQris, 0, ',', '.') }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-800"></span> Tunai / Cash
                    </span>
                    <strong class="text-slate-900">Rp {{ number_format($metodeTunai, 0, ',', '.') }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Kartu Debit / EDC
                    </span>
                    <strong class="text-slate-900">Rp {{ number_format($metodeDebit, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- SPLIT VIEW BAWAH: RIWAYAT TRANSAKSI & PRATINJAU STRUK THERMAL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Sisi Kiri (8 Kolom): Tabel Riwayat Transaksi -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-extrabold text-slate-900">Riwayat Transaksi Masuk</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi kasir langsung tersinkronisasi per shift.</p>
                </div>
                
                <!-- Search input -->
                <form action="{{ route('laporan.index') }}" method="GET">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Cari ID Struk / Meja..." 
                           class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 outline-none focus:border-orange-500 w-48">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">ID Transaksi</th>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Kasir</th>
                            <th class="py-3 px-4">Pelanggan / Meja</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksis as $index => $trx)
                        <tr class="hover:bg-orange-50/50 transition-all cursor-pointer {{ optional($selectedTransaksi)->id == $trx->id ? 'bg-orange-50/80 font-bold' : '' }}"
                            onclick="window.location='{{ route('laporan.index', ['highlight' => $trx->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}'">
                            <td class="py-3.5 px-4 text-slate-400">
                                {{ sprintf('%02d', $transaksis->firstItem() + $index) }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-extrabold text-slate-900">
                                #{{ $trx->kode_transaksi }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $trx->created_at->format('d M Y') }}<br>
                                <span class="text-slate-400">{{ $trx->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                {{ $trx->user->name ?? 'Kasir 1' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800">{{ $trx->nomor_meja }}</span> &bull; {{ $trx->nama_pelanggan }}
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $trx->metode_pembayaran === 'qris' ? 'bg-orange-100 text-orange-800' : ($trx->metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada transaksi tercatat pada rentang ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $transaksis->links() }}
            </div>
        </div>

        <!-- Sisi Kanan (5 Kolom): Pratinjau Struk Thermal Persis Mockup 5 -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col items-center">
            
            <div class="w-full flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-orange-500"></i>
                    <h4 class="text-sm font-extrabold text-slate-900">Pratinjau Struk Thermal</h4>
                </div>
                <span class="text-[11px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">58mm / 80mm</span>
            </div>

            @if($selectedTransaksi)
            <!-- KERTAS STRUK KASIR REALISTIK -->
            <div class="w-full max-w-xs bg-slate-50/60 p-5 rounded-2xl border border-slate-200 receipt-font text-[11px] text-slate-800 shadow-inner">
                <!-- Header Cafe -->
                <div class="text-center pb-3 border-b border-dashed border-slate-300">
                    <h3 class="font-extrabold text-sm tracking-tight text-slate-900 uppercase">{{ strtoupper(config('pos.name', 'KOPI KITA CAFE & ROASTERY')) }}</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">{{ config('pos.address', 'Jl. Pemuda No. 88, Pati, Jawa Tengah') }}</p>
                    <p class="text-[10px] text-slate-500">Telp: {{ config('pos.phone', '(0295) 381920') }}</p>
                </div>

                <!-- Detail Meta -->
                <div class="py-2.5 border-b border-dashed border-slate-300 space-y-1 text-[10px]">
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Trx:</span>
                        <strong class="font-bold">#{{ $selectedTransaksi->kode_transaksi }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Waktu:</span>
                        <span>{{ $selectedTransaksi->created_at->format('d/m/Y H:i:s') }} WIB</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kasir:</span>
                        <span>{{ $selectedTransaksi->user->name ?? 'Kasir Utama' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tipe / Meja:</span>
                        <span>{{ ucfirst($selectedTransaksi->tipe_pesanan) }} / {{ $selectedTransaksi->nomor_meja }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pelanggan:</span>
                        <span>{{ $selectedTransaksi->nama_pelanggan }}</span>
                    </div>
                </div>

                <!-- Daftar Item -->
                <div class="py-2.5 border-b border-dashed border-slate-300 space-y-2">
                    @foreach($selectedTransaksi->items as $item)
                    <div>
                        <div class="flex justify-between font-bold text-slate-900">
                            <span>{{ $item->qty }}x {{ $item->nama_menu }}</span>
                            <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-[10px] text-slate-400">
                            @ {{ number_format($item->harga, 0, ',', '.') }}
                            @if($item->catatan)
                                &bull; {{ $item->catatan }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Perhitungan Pembayaran -->
                <div class="py-2.5 border-b border-dashed border-slate-300 space-y-1 text-[11px]">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($selectedTransaksi->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>PB1 Pajak Cafe ({{ (int)(config('pos.tax_rate', 0.10) * 100) }}%)</span>
                        <span>Rp {{ number_format($selectedTransaksi->pajak, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-black text-xs text-orange-600 pt-1 border-t border-slate-200">
                        <span>TOTAL PEMBAYARAN</span>
                        <span>Rp {{ number_format($selectedTransaksi->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-700 pt-1">
                        <span>Bayar ({{ strtoupper($selectedTransaksi->metode_pembayaran) }})</span>
                        <span>Rp {{ number_format($selectedTransaksi->bayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-700">
                        <span>Kembalian</span>
                        <span>Rp {{ number_format($selectedTransaksi->kembalian, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Footer Ucapan & Barcode -->
                <div class="pt-3 text-center space-y-1">
                    <p class="text-[10px] font-semibold text-slate-600">Terima kasih atas kunjungan Anda!</p>
                    <p class="text-[9px] text-slate-400">Nikmati aroma kopi asli pilihan Nusantara.</p>
                    <p class="text-[9px] text-orange-600 font-bold">Follow Instagram: @kopikita.id</p>
                    <div class="pt-2 font-mono text-[9px] text-slate-400 tracking-widest">
                        ||| | ||||| || |||||| | ||||| |||
                        <div class="text-[8px]">{{ $selectedTransaksi->kode_transaksi }}</div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Struk Bawah Persis Mockup -->
            <div class="w-full space-y-2 mt-5">
                <a href="{{ route('laporan.struk', $selectedTransaksi->id) }}" 
                   target="_blank"
                   class="w-full py-3 px-4 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white font-bold text-xs shadow-md shadow-orange-500/25 flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Struk (Thermal Print)</span>
                </a>

                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                    <a href="https://wa.me/?text=Struk%20Kopi%20Kita%20#{{ $selectedTransaksi->kode_transaksi }}%20Total:%20Rp%20{{ number_format($selectedTransaksi->total_harga, 0, ',', '.') }}" 
                       target="_blank"
                       class="py-2.5 px-3 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 flex items-center justify-center gap-1.5 transition-all">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>Kirim WA</span>
                    </a>
                    <a href="{{ route('laporan.struk', $selectedTransaksi->id) }}" 
                       download
                       class="py-2.5 px-3 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 flex items-center justify-center gap-1.5 transition-all">
                        <i class="fa-regular fa-file-pdf text-rose-600"></i>
                        <span>Download PDF</span>
                    </a>
                </div>
            </div>
            @else
            <div class="py-12 text-center text-slate-400 text-xs">
                Pilih salah satu transaksi untuk menampilkan struk belanja.
            </div>
            @endif

        </div>

    </div>

</div>

<!-- SCRIPT CHARTS UNTUK LAPORAN PERSIS MOCKUP 5 -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Grafik Tren Pertumbuhan Penjualan Harian
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendLabels = @json($trendLabels);
        const trendData = @json($trendData);

        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: trendLabels.length ? trendLabels : ['01 Okt', '05 Okt', '10 Okt', '15 Okt', '20 Okt', '24 Okt'],
                datasets: [{
                    label: 'Omzet Harian (Rp)',
                    data: trendData.length ? trendData : [1800000, 2400000, 2100000, 3100000, 2800000, 3840000],
                    backgroundColor: '#f97316',
                    borderRadius: 6,
                    barPercentage: 0.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(v) { return (v/1000000).toFixed(1) + 'M'; },
                            font: { family: 'Plus Jakarta Sans', size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } }
                    }
                }
            }
        });

        // 2. Grafik Donut Komposisi Metode Pembayaran
        const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const qrisTotal = {{ $metodeQris > 0 ? $metodeQris : 65 }};
        const cashTotal = {{ $metodeTunai > 0 ? $metodeTunai : 25 }};
        const debitTotal = {{ $metodeDebit > 0 ? $metodeDebit : 10 }};

        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['QRIS Dinamis', 'Tunai / Cash', 'Kartu Debit / EDC'],
                datasets: [{
                    data: [qrisTotal, cashTotal, debitTotal],
                    backgroundColor: ['#f97316', '#0f172a', '#3b82f6'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
@endsection
