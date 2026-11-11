<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::with('pembuat')->orderBy('tanggal_mulai', 'desc')->paginate(12);
        return view('kegiatan.index', compact('kegiatan'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('kegiatan.index')->with('error', 'Hanya Pengurus / Admin RT yang dapat mempublikasikan agenda kegiatan.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'waktu' => 'nullable|string|max:50',
            'lokasi' => 'required|string|max:255',
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['tanggal_dibuat'] = now();

        Kegiatan::create($validated);

        return redirect()->route('kegiatan.index')->with('success', 'Agenda kegiatan berhasil dipublikasikan!');
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        if (!auth()->user()->isAdmin() && $kegiatan->dibuat_oleh !== auth()->id()) {
            return redirect()->route('kegiatan.index')->with('error', 'Anda tidak memiliki hak untuk mengubah kegiatan ini.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'waktu' => 'nullable|string|max:50',
            'lokasi' => 'required|string|max:255',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('kegiatan.index')->with('success', 'Agenda kegiatan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::find($id);

        if (!$kegiatan) {
            return redirect()->route('kegiatan.index')->with('info', 'Kegiatan dengan ID #' . $id . ' sudah tidak ada atau telah dihapus sebelumnya.');
        }

        if (!auth()->user()->isAdmin() && $kegiatan->dibuat_oleh !== auth()->id()) {
            return redirect()->route('kegiatan.index')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk menghapus kegiatan ini.');
        }

        $judul = $kegiatan->judul;
        $kegiatan->delete();

        return redirect()->route('kegiatan.index')->with('success', 'Agenda kegiatan "' . $judul . '" berhasil dihapus.');
    }
}
