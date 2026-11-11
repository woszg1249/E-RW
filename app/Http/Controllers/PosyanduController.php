<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\User;
use App\Services\PosyanduGrowthService;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $query = Posyandu::with('warga');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_bayi', 'like', "%{$s}%")
                  ->orWhere('status_kesehatan', 'like', "%{$s}%");
            });
        }

        if ($request->filled('rt')) {
            $rt = $request->rt;
            $query->whereHas('warga', function($q) use ($rt) {
                $q->where('rt', $rt);
            });
        }

        $posyanduList = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(15)->withQueryString();
        $wargaList = User::where('role', 'Warga')->orderBy('nama', 'asc')->get();
        $standards = PosyanduGrowthService::STANDARDS;

        return view('posyandu.index', compact('posyanduList', 'wargaList', 'standards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_warga' => 'nullable|exists:users,id',
            'nama_bayi' => 'required|string|max:100',
            'usia_bulan' => 'required|integer|min:0',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'nullable|numeric|min:0',
            'lingkar_kepala' => 'nullable|numeric|min:0',
            'status_kesehatan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'tanggal_pemeriksaan' => 'required|date',
            'pemeriksaan_oleh' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        // Hitung otomatis status kesehatan dari standar ukuran bayi normal (1-12 bulan)
        $eval = PosyanduGrowthService::evaluate(
            $validated['jenis_kelamin'],
            (int) $validated['usia_bulan'],
            (float) $validated['berat_badan'],
            isset($validated['tinggi_badan']) && $validated['tinggi_badan'] > 0 ? (float) $validated['tinggi_badan'] : null,
            isset($validated['lingkar_kepala']) && $validated['lingkar_kepala'] > 0 ? (float) $validated['lingkar_kepala'] : null
        );

        // Jika status_kesehatan kosong atau bernilai default / auto, gunakan kalkulasi otomatis
        if (empty($validated['status_kesehatan']) || $validated['status_kesehatan'] === 'auto') {
            $validated['status_kesehatan'] = $eval['status'];
        }

        $validated['pemeriksaan_oleh'] = $validated['pemeriksaan_oleh'] ?? auth()->user()->nama;

        Posyandu::create($validated);

        return redirect()->route('posyandu.index')->with('success', 'Catatan pemeriksaan Posyandu berhasil disimpan dengan analisis status kesehatan otomatis!');
    }

    public function destroy($id)
    {
        $p = Posyandu::findOrFail($id);
        $p->delete();

        return redirect()->route('posyandu.index')->with('success', 'Data posyandu berhasil dihapus.');
    }

    public function cetak(Request $request)
    {
        $query = Posyandu::with('warga');
        if ($request->filled('rt')) {
            $rt = $request->rt;
            $query->whereHas('warga', function($q) use ($rt) {
                $q->where('rt', $rt);
            });
        }
        $data = $query->orderBy('tanggal_pemeriksaan', 'desc')->get();
        return view('posyandu.cetak', compact('data'));
    }
}
