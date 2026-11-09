<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MenuController extends Controller
{
    /**
     * Tampilkan data menu dengan filter & metrik ringkasan
     */
    public function index(Request $request): View
    {
        $query = Menu::query();

        // Filter Pencarian (Nama / SKU)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter Kategori
        if ($kategori = $request->input('kategori')) {
            if ($kategori !== 'all') {
                $query->where('kategori', $kategori);
            }
        }

        // Filter Status Stok
        if ($status = $request->input('status')) {
            $threshold = config('pos.low_stock_threshold', 15);
            if ($status === 'tersedia') {
                $query->where('stok', '>', $threshold);
            } elseif ($status === 'menipis') {
                $query->where('stok', '>', 0)->where('stok', '<=', $threshold);
            } elseif ($status === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        // Urutan
        $sort = $request->input('sort', 'terbaru');
        if ($sort === 'terbaru') {
            $query->latest();
        } elseif ($sort === 'harga_asc') {
            $query->orderBy('harga_jual', 'asc');
        } elseif ($sort === 'harga_desc') {
            $query->orderBy('harga_jual', 'desc');
        } elseif ($sort === 'stok_asc') {
            $query->orderBy('stok', 'asc');
        }

        $menus = $query->paginate(10)->withQueryString();

        // Metrik Ringkasan Atas
        $threshold = config('pos.low_stock_threshold', 15);
        $totalAktif = Menu::where('is_active', true)->count();
        $stokTersedia = Menu::where('is_active', true)->where('stok', '>', $threshold)->count();
        $stokMenipis = Menu::where('is_active', true)->where('stok', '>', 0)->where('stok', '<=', $threshold)->count();
        $stokHabis = Menu::where('is_active', true)->where('stok', '<=', 0)->count();
        $menuBaruMingguIni = Menu::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        return view('menu.index', compact(
            'menus',
            'totalAktif',
            'stokTersedia',
            'stokMenipis',
            'stokHabis',
            'menuBaruMingguIni'
        ));
    }

    /**
     * Simpan menu baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:menus,sku'],
            'kategori' => ['required', 'in:coffee,non-coffee,makanan,snack'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'harga_modal' => ['nullable', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nama.required' => 'Nama menu wajib diisi.',
            'kategori.required' => 'Kategori menu wajib dipilih.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'stok.required' => 'Jumlah stok awal wajib diisi.',
            'sku.unique' => 'SKU produk sudah terdaftar.',
        ]);

        // Auto-generate SKU jika kosong
        if (empty($validated['sku'])) {
            $prefix = match ($validated['kategori']) {
                'coffee' => 'KK-COF',
                'non-coffee' => 'KK-NCO',
                'makanan' => 'KK-FD',
                'snack' => 'KK-SNK',
                default => 'KK-GEN',
            };
            $count = Menu::where('kategori', $validated['kategori'])->count() + 1;
            $validated['sku'] = sprintf('%s-%03d', $prefix, $count);
        }

        $validated['harga_modal'] = $validated['harga_modal'] ?? 0;
        $validated['is_active'] = true;

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('menus', 'public');
        }

        Menu::create($validated);

        return redirect()->route('menu.index')->with('success', 'Menu baru berhasil ditambahkan ke etalase!');
    }

    /**
     * Update menu yang ada
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'sku' => ['required', 'string', 'max:50', 'unique:menus,sku,' . $menu->id],
            'kategori' => ['required', 'in:coffee,non-coffee,makanan,snack'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'harga_modal' => ['nullable', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['harga_modal'] = $validated['harga_modal'] ?? $menu->harga_modal;
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('gambar')) {
            if ($menu->gambar && !str_starts_with($menu->gambar, 'http') && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('menus', 'public');
        }

        $menu->update($validated);

        return redirect()->route('menu.index')->with('success', 'Perubahan data menu "' . $menu->nama . '" berhasil disimpan!');
    }

    /**
     * Hapus menu
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        $nama = $menu->nama;
        if ($menu->gambar && !str_starts_with($menu->gambar, 'http') && Storage::disk('public')->exists($menu->gambar)) {
            Storage::disk('public')->delete($menu->gambar);
        }
        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu "' . $nama . '" berhasil dihapus.');
    }

    /**
     * Import data menu dari file CSV
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'csv_file.required' => 'Silakan pilih file CSV terlebih dahulu.',
            'csv_file.mimes' => 'Format file harus berupa CSV (.csv) atau teks (.txt).',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file CSV.');
        }

        // Deteksi delimiter (, atau ;)
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        $header = fgetcsv($handle, 1000, $delimiter);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong.');
        }

        // Normalisasi header ke lowercase
        $header = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', $h)));
        }, $header);

        $imported = 0;
        $updated = 0;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (empty(array_filter($row))) continue;

            $data = [];
            foreach ($header as $i => $colName) {
                $data[$colName] = isset($row[$i]) ? trim($row[$i]) : null;
            }

            // Cari kolom berdasarkan variasi nama header
            $nama = $data['namamenu'] ?? $data['nama'] ?? $data['name'] ?? null;
            if (!$nama) continue;

            $sku = $data['sku'] ?? null;
            $kategoriRaw = strtolower($data['kategori'] ?? $data['category'] ?? 'coffee');
            $kategori = match (true) {
                str_contains($kategoriRaw, 'non') => 'non-coffee',
                str_contains($kategoriRaw, 'makan') => 'makanan',
                str_contains($kategoriRaw, 'snack') => 'snack',
                default => 'coffee',
            };

            $hargaJual = (float) preg_replace('/[^0-9.]/', '', $data['hargajualrp'] ?? $data['hargajual'] ?? $data['harga'] ?? 0);
            $hargaModal = (float) preg_replace('/[^0-9.]/', '', $data['hargamodalrp'] ?? $data['hargamodal'] ?? $data['modal'] ?? 0);
            $stok = (int) preg_replace('/[^0-9]/', '', $data['stok'] ?? $data['stock'] ?? 0);

            if (empty($sku)) {
                $prefix = match ($kategori) {
                    'coffee' => 'KK-COF',
                    'non-coffee' => 'KK-NCO',
                    'makanan' => 'KK-FD',
                    'snack' => 'KK-SNK',
                    default => 'KK-GEN',
                };
                $sku = sprintf('%s-%03d', $prefix, Menu::where('kategori', $kategori)->count() + 1);
            }

            $menu = Menu::where('sku', $sku)->first();
            if ($menu) {
                $menu->update([
                    'nama' => $nama,
                    'kategori' => $kategori,
                    'harga_jual' => $hargaJual > 0 ? $hargaJual : $menu->harga_jual,
                    'harga_modal' => $hargaModal >= 0 ? $hargaModal : $menu->harga_modal,
                    'stok' => $stok,
                    'is_active' => true,
                ]);
                $updated++;
            } else {
                Menu::create([
                    'sku' => $sku,
                    'nama' => $nama,
                    'kategori' => $kategori,
                    'harga_jual' => $hargaJual,
                    'harga_modal' => $hargaModal,
                    'stok' => $stok,
                    'is_active' => true,
                ]);
                $imported++;
            }
        }

        fclose($handle);

        return redirect()->route('menu.index')->with('success', "Import berhasil! {$imported} menu baru ditambahkan dan {$updated} menu diperbarui.");
    }

    /**
     * Export data menu ke format CSV
     */
    public function export(): StreamedResponse
    {
        $fileName = 'katalog_menu_kopi_kita_' . date('Ymd_His') . '.csv';
        $menus = Menu::orderBy('kategori')->orderBy('nama')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($menus) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'SKU', 'Nama Menu', 'Kategori', 'Harga Jual (Rp)', 'Harga Modal (Rp)', 'Stok', 'Status']);

            foreach ($menus as $idx => $m) {
                fputcsv($handle, [
                    $idx + 1,
                    $m->sku,
                    $m->nama,
                    ucfirst($m->kategori),
                    $m->harga_jual,
                    $m->harga_modal,
                    $m->stok,
                    $m->status_stok,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
