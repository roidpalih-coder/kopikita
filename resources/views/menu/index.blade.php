@extends('layouts.app')

@section('title', 'Manajemen Data Menu (CRUD)')

@section('content')
<div class="space-y-6">

    <!-- BREADCRUMBS & HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <span>&rsaquo;</span>
                <span>Produk &amp; Inventaris</span>
                <span>&rsaquo;</span>
                <span class="text-orange-600 font-bold">Data Menu</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Data Menu</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola katalog produk katering, harga modal, persediaan porsi, dan ketersediaan etalase kasir.</p>
        </div>

        <!-- Tombol Aksi Kanan Sesuai Mockup -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('menu.export') }}" 
               class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                <i class="fa-regular fa-file-excel text-emerald-600"></i>
                <span>Export Excel</span>
            </a>

            <!-- Form Hidden untuk Import CSV -->
            <form id="importMenuForm" action="{{ route('menu.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="file" id="importCsvFileInput" name="csv_file" accept=".csv, text/csv, text/plain" onchange="handleCsvImport(this)">
            </form>

            <button type="button" 
                    onclick="document.getElementById('importCsvFileInput').click()" 
                    class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-from-bracket text-blue-600"></i>
                <span>Import Data</span>
            </button>
            <button type="button" 
                    onclick="openAddMenuModal()" 
                    class="px-4 py-2.5 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white text-xs font-bold shadow-md shadow-orange-500/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Menu Baru</span>
            </button>
        </div>
    </div>

    <!-- 4 KARTU RINGKASAN STOK PERSIS MOCKUP -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Menu Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Menu Aktif</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalAktif }}</div>
                <div class="flex items-center gap-1 text-[11px] font-bold text-emerald-600 mt-1">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>{{ $menuBaruMingguIni }} item baru minggu ini</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-slate-700 text-xl">
                <i class="fa-solid fa-mug-saucer"></i>
            </div>
        </div>

        <!-- 2. Stok Tersedia -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Stok Tersedia</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $stokTersedia }}</div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 mt-1">
                    <span>{{ $totalAktif > 0 ? round(($stokTersedia / $totalAktif) * 100, 1) : 0 }}% dari total menu</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 text-xl">
                <i class="fa-regular fa-circle-check"></i>
            </div>
        </div>

        <!-- 3. Stok Menipis -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Stok Menipis</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stokMenipis }}</div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 mt-1">
                    <span>Perlu restock hari ini</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <!-- 4. Habis / Out of Stock -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Habis / Out of Stock</span>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $stokHabis }}</div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-400 mt-1">
                    <span>Non-aktif di terminal kasir</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 text-xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>
    </div>

    <!-- TOOLBAR FILTER PERSIS MOCKUP -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Search Input -->
        <form action="{{ route('menu.index') }}" method="GET" class="relative flex-1 w-full md:max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Cari nama menu atau SKU produk..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
        </form>

        <!-- Dropdowns -->
        <div class="flex items-center gap-2.5 w-full md:w-auto flex-wrap">
            <!-- Filter Kategori -->
            <select onchange="window.location='{{ route('menu.index') }}?kategori=' + this.value + '&status={{ request('status') }}&search={{ request('search') }}'" 
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white outline-none cursor-pointer">
                <option value="all" {{ request('kategori') == 'all' || !request('kategori') ? 'selected' : '' }}>Semua Kategori</option>
                <option value="coffee" {{ request('kategori') == 'coffee' ? 'selected' : '' }}>Coffee</option>
                <option value="non-coffee" {{ request('kategori') == 'non-coffee' ? 'selected' : '' }}>Non-Coffee</option>
                <option value="makanan" {{ request('kategori') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                <option value="snack" {{ request('kategori') == 'snack' ? 'selected' : '' }}>Snack</option>
            </select>

            <!-- Filter Status -->
            <select onchange="window.location='{{ route('menu.index') }}?status=' + this.value + '&kategori={{ request('kategori') }}&search={{ request('search') }}'" 
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white outline-none cursor-pointer">
                <option value="" {{ !request('status') ? 'selected' : '' }}>Semua Status</option>
                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
            </select>

            <!-- Sort -->
            <select onchange="window.location='{{ route('menu.index') }}?sort=' + this.value" 
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white outline-none cursor-pointer">
                <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="harga_asc" {{ request('sort') == 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="harga_desc" {{ request('sort') == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                <option value="stok_asc" {{ request('sort') == 'stok_asc' ? 'selected' : '' }}>Stok Terendah</option>
            </select>

            <!-- Reset Button -->
            <a href="{{ route('menu.index') }}" 
               class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 text-xs transition-all" title="Reset Filter">
                <i class="fa-solid fa-arrows-rotate"></i>
            </a>
        </div>
    </div>

    <!-- TABEL DATA MENU PERSIS MOCKUP 4 -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-4 px-6">No</th>
                        <th class="py-4 px-6">Foto</th>
                        <th class="py-4 px-6">Nama Menu &amp; SKU</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Harga (Jual / Modal)</th>
                        <th class="py-4 px-6">Stok Saat Ini</th>
                        <th class="py-4 px-6">Status Stok</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($menus as $index => $menu)
                    <tr class="hover:bg-slate-50/70 transition-all">
                        <td class="py-4 px-6 font-bold text-slate-500">
                            {{ $menus->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-6">
                            <img src="{{ $menu->gambar_url }}" 
                                 alt="{{ $menu->nama }}" 
                                 class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm">
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-extrabold text-slate-900 text-sm">{{ $menu->nama }}</div>
                            <div class="text-[11px] font-mono text-slate-400 mt-0.5">SKU: {{ $menu->sku }}</div>
                        </td>
                        <td class="py-4 px-6">
                            @php
                                $badgeStyle = match($menu->kategori) {
                                    'coffee' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'non-coffee' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'makanan' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'snack' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                };
                                $iconStyle = match($menu->kategori) {
                                    'coffee' => 'fa-mug-hot',
                                    'non-coffee' => 'fa-glass-water',
                                    'makanan' => 'fa-bowl-food',
                                    'snack' => 'fa-cookie-bite',
                                    default => 'fa-utensils'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border {{ $badgeStyle }}">
                                <i class="fa-solid {{ $iconStyle }} text-[11px]"></i>
                                <span>{{ ucfirst($menu->kategori) }}</span>
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-extrabold text-slate-900 text-sm">
                                Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Modal: Rp {{ number_format($menu->harga_modal, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-extrabold text-sm {{ $menu->stok <= 10 ? 'text-amber-600' : 'text-slate-900' }}">
                                {{ $menu->stok }}
                            </span>
                            <span class="text-xs text-slate-400">Porsi</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($menu->stok > 15)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Tersedia</span>
                                </span>
                            @elseif($menu->stok > 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Stok Menipis</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    <span>Habis</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Tombol Edit -->
                                <button type="button" 
                                        onclick="openEditMenuModal({{ json_encode($menu) }})" 
                                        class="p-2 rounded-lg text-slate-500 hover:text-orange-600 hover:bg-orange-50 transition-all" title="Edit Menu">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </button>

                                <!-- Tombol Delete -->
                                <form action="{{ route('menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu \'{{ $menu->nama }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Hapus Menu">
                                        <i class="fa-regular fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">Tidak ada data menu yang cocok dengan filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer Persis Mockup -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div>
                Menampilkan <strong>{{ $menus->firstItem() ?? 0 }} - {{ $menus->lastItem() ?? 0 }}</strong> dari <strong>{{ $menus->total() }}</strong> data menu
            </div>
            <div>
                {{ $menus->links() }}
            </div>
        </div>
    </div>

</div>

<!-- MODAL TAMBAH & EDIT MENU -->
<div id="menuModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 md:p-8 shadow-2xl border border-slate-200 animate-fade-in relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 id="modalTitle" class="text-lg font-extrabold text-slate-900">Tambah Menu Baru</h3>
            <button onclick="closeMenuModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="menuForm" method="POST" enctype="multipart/form-data" onsubmit="return validateMenuForm(event)" class="space-y-4 mt-4">
            @csrf
            <div id="methodContainer"></div>

            <!-- Nama Menu -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Menu</label>
                <input type="text" name="nama" id="formNama" oninput="clearInputError('formNama', 'formNamaError')" placeholder="Contoh: Kopi Susu Gula Aren" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-orange-500 transition-all">
                <p id="formNamaError" class="hidden text-[10px] text-rose-500 font-semibold mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[9px]"></i>
                    <span>Nama menu wajib diisi</span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- SKU -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SKU Produk</label>
                    <input type="text" name="sku" id="formSku" placeholder="KK-COF-001 (Opsional)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-medium text-slate-900 outline-none focus:border-orange-500 transition-all">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                    <select name="kategori" id="formKategori" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 outline-none focus:border-orange-500 transition-all">
                        <option value="coffee">Coffee</option>
                        <option value="non-coffee">Non-Coffee</option>
                        <option value="makanan">Makanan</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Harga Jual -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" id="formHargaJual" oninput="clearInputError('formHargaJual', 'formHargaJualError')" min="0" placeholder="18000" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-orange-500 transition-all">
                    <p id="formHargaJualError" class="hidden text-[10px] text-rose-500 font-semibold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[9px]"></i>
                        <span>Harga jual harus lebih dari 0</span>
                    </p>
                </div>

                <!-- Harga Modal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Harga Modal / HPP (Rp)</label>
                    <input type="number" name="harga_modal" id="formHargaModal" min="0" placeholder="8500" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-orange-500 transition-all">
                </div>
            </div>

            <!-- Stok -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jumlah Stok (Porsi)</label>
                <input type="number" name="stok" id="formStok" oninput="clearInputError('formStok', 'formStokError')" min="0" placeholder="45" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-900 outline-none focus:border-orange-500 transition-all">
                <p id="formStokError" class="hidden text-[10px] text-rose-500 font-semibold mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[9px]"></i>
                    <span>Jumlah stok awal wajib diisi</span>
                </p>
            </div>

            <!-- Foto Menu -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Upload Foto Menu</label>
                <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
            </div>

            <!-- Tombol Aksi Form -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeMenuModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white text-xs font-bold shadow-md shadow-orange-500/25">Simpan Data Menu</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddMenuModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Menu Baru';
        document.getElementById('menuForm').action = "{{ route('menu.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('formNama').value = '';
        document.getElementById('formSku').value = '';
        document.getElementById('formKategori').value = 'coffee';
        document.getElementById('formHargaJual').value = '';
        document.getElementById('formHargaModal').value = '';
        document.getElementById('formStok').value = '';
        resetMenuFormErrors();
        document.getElementById('menuModal').classList.remove('hidden');
    }

    function openEditMenuModal(menu) {
        document.getElementById('modalTitle').innerText = 'Edit Menu: ' + menu.nama;
        document.getElementById('menuForm').action = "/menu/" + menu.id;
        document.getElementById('methodContainer').innerHTML = '@method("PUT")';
        document.getElementById('formNama').value = menu.nama;
        document.getElementById('formSku').value = menu.sku || '';
        document.getElementById('formKategori').value = menu.kategori;
        document.getElementById('formHargaJual').value = parseInt(menu.harga_jual);
        document.getElementById('formHargaModal').value = parseInt(menu.harga_modal) || '';
        document.getElementById('formStok').value = menu.stok;
        resetMenuFormErrors();
        document.getElementById('menuModal').classList.remove('hidden');
    }

    function closeMenuModal() {
        document.getElementById('menuModal').classList.add('hidden');
        resetMenuFormErrors();
    }

    function resetMenuFormErrors() {
        clearInputError('formNama', 'formNamaError');
        clearInputError('formHargaJual', 'formHargaJualError');
        clearInputError('formStok', 'formStokError');
    }

    function clearInputError(inputId, errorId) {
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

    function setInputError(inputId, errorId, message) {
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

    function validateMenuForm(event) {
        let isValid = true;
        let firstInvalid = null;

        const nama = document.getElementById('formNama').value.trim();
        if (!nama) {
            setInputError('formNama', 'formNamaError', 'Nama menu wajib diisi');
            isValid = false;
            firstInvalid = firstInvalid || document.getElementById('formNama');
        } else {
            clearInputError('formNama', 'formNamaError');
        }

        const hargaJualVal = document.getElementById('formHargaJual').value;
        const hargaJual = parseFloat(hargaJualVal);
        if (!hargaJualVal || isNaN(hargaJual) || hargaJual <= 0) {
            setInputError('formHargaJual', 'formHargaJualError', 'Harga jual harus lebih dari Rp 0');
            isValid = false;
            firstInvalid = firstInvalid || document.getElementById('formHargaJual');
        } else {
            clearInputError('formHargaJual', 'formHargaJualError');
        }

        const stokVal = document.getElementById('formStok').value;
        const stok = parseInt(stokVal);
        if (stokVal === '' || isNaN(stok) || stok < 0) {
            setInputError('formStok', 'formStokError', 'Stok minimal 0 porsi');
            isValid = false;
            firstInvalid = firstInvalid || document.getElementById('formStok');
        } else {
            clearInputError('formStok', 'formStokError');
        }

        if (!isValid) {
            if (event) event.preventDefault();
            if (firstInvalid) firstInvalid.focus();
            if (typeof showToast === 'function') {
                showToast('Mohon periksa kolom yang ditandai merah!', 'warning');
            }
            return false;
        }

        return true;
    }

    function handleCsvImport(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            if (typeof showToast === 'function') {
                showToast('Mengunggah & memproses file: ' + fileName + '...', 'info');
            }
            document.getElementById('importMenuForm').submit();
        }
    }
</script>
@endpush
@endsection
