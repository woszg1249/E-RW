<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuan;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = SuratPengajuan::with('user');

        if ($user->isWarga()) {
            $query->where('id_user', $user->id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Disetujui') {
                $query->whereIn('status', ['Disetujui', 'ACC']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('rt')) {
            $rt = $request->rt;
            $query->where(function($q) use ($rt) {
                $q->where('nama_rt', 'like', "%{$rt}%")
                  ->orWhereHas('user', function($uq) use ($rt) {
                      $uq->where('rt', $rt);
                  });
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('jenis_surat', 'like', "%{$s}%")
                  ->orWhere('tujuan', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%");
            });
        }

        $suratList = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        $totalMenunggu = $user->isWarga()
            ? SuratPengajuan::where('id_user', $user->id)->whereIn('status', ['Menunggu', 'Proses'])->count()
            : SuratPengajuan::whereIn('status', ['Menunggu', 'Proses'])->count();

        $totalDisetujui = $user->isWarga()
            ? SuratPengajuan::where('id_user', $user->id)->whereIn('status', ['Disetujui', 'ACC'])->count()
            : SuratPengajuan::whereIn('status', ['Disetujui', 'ACC'])->count();

        return view('surat.index', compact('suratList', 'totalMenunggu', 'totalDisetujui'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'jenis_surat' => 'required|string|max:100',
            'tujuan' => 'required|string',
            'nama_rt' => 'nullable|string|max:100',
            'nama_rw' => 'nullable|string|max:100',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'detail_usaha' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $rtPilihan = $validated['nama_rt'] ?? ('RT ' . ($user->rt ?? '06'));
        if (!str_starts_with(strtoupper($rtPilihan), 'RT')) {
            $rtPilihan = 'RT ' . str_pad($rtPilihan, 2, '0', STR_PAD_LEFT);
        }

        SuratPengajuan::create([
            'id_user' => $user->id,
            'nama' => $user->nama,
            'jenis_surat' => $validated['jenis_surat'],
            'nik' => $user->nik ?? '-',
            'ttl' => ($user->tempat_lahir ?? 'Bekasi') . ', ' . ($user->tanggal_lahir ? $user->tanggal_lahir->format('d-m-Y') : '01-01-2000'),
            'alamat' => $user->alamat ?? '-',
            'tujuan' => $validated['tujuan'],
            'nama_rt' => $rtPilihan,
            'nama_rw' => $validated['nama_rw'] ?? 'RW 19',
            'kelurahan' => $validated['kelurahan'] ?? 'Bekasi Jaya',
            'kecamatan' => $validated['kecamatan'] ?? 'Bekasi Timur',
            'kota' => $validated['kota'] ?? 'Kota Bekasi',
            'tanggal_permohonan' => now()->toDateString(),
            'detail_usaha' => $validated['detail_usaha'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'Menunggu',
            'created_at' => now(),
        ]);

        return redirect()->route('surat.index')->with('success', 'Pengajuan surat berhasil dikirim! Menunggu persetujuan Pengurus RT.');
    }

    public function updateStatus(Request $request, $id)
    {
        $surat = SuratPengajuan::findOrFail($id);
        $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,ACC,Ditolak',
        ]);

        $statusToSave = ($request->status === 'ACC') ? 'Disetujui' : $request->status;
        $surat->update(['status' => $statusToSave]);

        return redirect()->route('surat.index')->with('success', 'Status pengajuan surat #' . $surat->id . ' berhasil diubah menjadi: ' . $statusToSave);
    }

    public function cetak($id)
    {
        $surat = SuratPengajuan::with('user')->findOrFail($id);
        return view('surat.cetak', compact('surat'));
    }
}
