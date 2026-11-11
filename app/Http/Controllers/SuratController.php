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
            $query->where('status', $request->status);
        }

        $suratList = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        $totalMenunggu = $user->isWarga()
            ? SuratPengajuan::where('id_user', $user->id)->where('status', 'Menunggu')->count()
            : SuratPengajuan::where('status', 'Menunggu')->count();

        $totalDisetujui = $user->isWarga()
            ? SuratPengajuan::where('id_user', $user->id)->where('status', 'Disetujui')->count()
            : SuratPengajuan::where('status', 'Disetujui')->count();

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

        SuratPengajuan::create([
            'id_user' => $user->id,
            'nama' => $user->nama,
            'jenis_surat' => $validated['jenis_surat'],
            'nik' => $user->nik ?? '-',
            'ttl' => ($user->tempat_lahir ?? 'Bekasi') . ', ' . ($user->tanggal_lahir ? $user->tanggal_lahir->format('d-m-Y') : '01-01-2000'),
            'alamat' => $user->alamat ?? '-',
            'tujuan' => $validated['tujuan'],
            'nama_rt' => $validated['nama_rt'] ?? ('06 / ' . ($user->rt ?? '06')),
            'nama_rw' => $validated['nama_rw'] ?? ('19 / ' . ($user->rw ?? '19')),
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
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
        ]);

        $surat->update(['status' => $request->status]);

        return redirect()->route('surat.index')->with('success', 'Status pengajuan surat berhasil diubah menjadi ' . $request->status);
    }

    public function cetak($id)
    {
        $surat = SuratPengajuan::with('user')->findOrFail($id);
        return view('surat.cetak', compact('surat'));
    }
}
