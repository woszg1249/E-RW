<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Pengaduan::with('user:id,nama,rt,rw');

        // Privasi akun warga: hanya tampilkan laporan milik sendiri
        if ($user->isWarga()) {
            $query->where('id_user', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                    ->orWhere('isi_laporan', 'like', "%{$s}%")
                    ->orWhere('kategori', 'like', "%{$s}%");
            });
        }

        $pengaduanList = $query->orderByDesc('id')->paginate(10)->withQueryString();

        $baseCount = Pengaduan::query();
        if ($user->isWarga()) {
            $baseCount->where('id_user', $user->id);
        }

        $totalLaporan = (clone $baseCount)->count();
        $totalMenunggu = (clone $baseCount)->where('status', 'Menunggu')->count();
        $totalDiproses = (clone $baseCount)->where('status', 'Diproses')->count();
        $totalSelesai = (clone $baseCount)->where('status', 'Selesai')->count();

        return view('pengaduan.index', compact(
            'pengaduanList',
            'totalLaporan',
            'totalMenunggu',
            'totalDiproses',
            'totalSelesai'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:100',
            'judul' => 'required|string|max:150',
            'isi_laporan' => 'required|string|max:2000',
            'lokasi' => 'nullable|string|max:150',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $filename = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'pengaduan_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('img/pengaduan');
            if (!File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }
            $file->move($destPath, $filename);
        }

        Pengaduan::create([
            'id_user' => auth()->id(),
            'kategori' => $validated['kategori'],
            'judul' => $validated['judul'],
            'isi_laporan' => $validated['isi_laporan'],
            'lokasi' => $validated['lokasi'] ?? null,
            'foto' => $filename,
            'status' => 'Menunggu',
        ]);

        return back()->with('success', 'Laporan / aspirasi Anda berhasil dikirim ke pengurus RT/RW!');
    }

    public function updateStatus(Request $request, $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
            'tanggapan_admin' => 'nullable|string|max:1500',
        ]);

        $pengaduan->update([
            'status' => $validated['status'],
            'tanggapan_admin' => $validated['tanggapan_admin'] ?? $pengaduan->tanggapan_admin,
            'ditanggapi_oleh' => auth()->user()->nama,
        ]);

        return back()->with('success', 'Status dan tanggapan laporan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        if (!auth()->user()->isAdmin() && $pengaduan->id_user !== auth()->id()) {
            abort(403, 'Anda tidak berhak menghapus laporan ini.');
        }

        if ($pengaduan->foto) {
            $path = public_path('img/pengaduan/' . $pengaduan->foto);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $pengaduan->delete();

        return back()->with('success', 'Laporan / aspirasi berhasil dihapus.');
    }
}
