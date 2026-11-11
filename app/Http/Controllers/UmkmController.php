<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $query = Umkm::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_usaha', 'like', "%{$s}%")
                  ->orWhere('deskripsi', 'like', "%{$s}%")
                  ->orWhere('produk_layanan', 'like', "%{$s}%");
            });
        }

        $umkmList = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        $kategoriList = Umkm::select('kategori')->distinct()->pluck('kategori');

        return view('umkm.index', compact('umkmList', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string|max:255',
            'jam_operasional' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'produk_layanan' => 'nullable|string',
            'estimasi_jarak' => 'nullable|string|max:100',
            'google_maps_link' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $filename = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'umkm_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('img');
            $file->move($destPath, $filename);
        }

        Umkm::create([
            'nama_usaha' => $validated['nama_usaha'],
            'kategori' => $validated['kategori'] ?? 'Umum',
            'deskripsi' => $validated['deskripsi'] ?? null,
            'alamat' => !empty($validated['alamat']) ? $validated['alamat'] : (auth()->user()->alamat ?? '-'),
            'jam_operasional' => $validated['jam_operasional'] ?? '08.00 - 21.00 WIB',
            'whatsapp' => !empty($validated['whatsapp']) ? $validated['whatsapp'] : (auth()->user()->no_hp ?? '-'),
            'foto' => $filename,
            'produk_layanan' => $validated['produk_layanan'] ?? null,
            'estimasi_jarak' => $validated['estimasi_jarak'] ?? '-',
            'google_maps_link' => $validated['google_maps_link'] ?? '-',
            'status' => 'Aktif',
        ]);

        return redirect()->route('umkm.index')->with('success', 'Profil UMKM berhasil ditambahkan ke etalase warga!');
    }

    public function update(Request $request, $id)
    {
        $umkm = Umkm::findOrFail($id);

        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string|max:255',
            'jam_operasional' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'produk_layanan' => 'nullable|string',
            'estimasi_jarak' => 'nullable|string|max:100',
            'google_maps_link' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Nonaktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'umkm_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('img');
            $file->move($destPath, $filename);
            $validated['foto'] = $filename;
        }

        $umkm->update($validated);

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $umkm = Umkm::findOrFail($id);
        $umkm->delete();

        return redirect()->route('umkm.index')->with('success', 'Data UMKM berhasil dihapus.');
    }
}
