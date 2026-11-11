<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\PeminjamanAset;
use Illuminate\Http\Request;

class AsetController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $asetList = Aset::with('peminjaman')->orderBy('id', 'desc')->paginate(12);

        $peminjamanQuery = PeminjamanAset::with(['aset', 'user']);
        if ($user->isWarga()) {
            $peminjamanQuery->where('id_user', $user->id);
        }
        $peminjamanList = $peminjamanQuery->orderBy('id', 'desc')->paginate(10);

        return view('aset.index', compact('asetList', 'peminjamanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'kondisi' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'status' => 'nullable|string|max:50',
        ]);

        Aset::create($validated);

        return redirect()->route('aset.index')->with('success', 'Inventaris aset RT berhasil ditambahkan!');
    }

    public function pinjam(Request $request)
    {
        $validated = $request->validate([
            'id_aset' => 'required|exists:aset,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        PeminjamanAset::create([
            'id_aset' => $validated['id_aset'],
            'id_user' => auth()->id(),
            'jumlah_pinjam' => $validated['jumlah_pinjam'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => $validated['tanggal_kembali'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'Menunggu',
        ]);

        return redirect()->route('aset.index')->with('success', 'Permohonan pinjam aset berhasil diajukan! Menunggu persetujuan Pengurus.');
    }

    public function updateStatusPeminjaman(Request $request, $id)
    {
        $p = PeminjamanAset::findOrFail($id);
        $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak,Dikembalikan',
        ]);

        $p->update(['status' => $request->status]);

        return redirect()->route('aset.index')->with('success', 'Status peminjaman aset diperbarui menjadi ' . $request->status);
    }
}
