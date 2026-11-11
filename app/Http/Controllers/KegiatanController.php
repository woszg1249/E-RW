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
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return redirect()->route('kegiatan.index')->with('success', 'Agenda kegiatan berhasil dihapus.');
    }
}
