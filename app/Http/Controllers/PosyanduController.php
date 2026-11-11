<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $query = Posyandu::with('warga');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('nama_bayi', 'like', "%{$s}%")
                  ->orWhere('status_kesehatan', 'like', "%{$s}%");
        }

        $posyanduList = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(15)->withQueryString();
        $wargaList = User::where('role', 'Warga')->orderBy('nama', 'asc')->get();

        return view('posyandu.index', compact('posyanduList', 'wargaList'));
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
            'status_kesehatan' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
            'tanggal_pemeriksaan' => 'required|date',
            'pemeriksaan_oleh' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        if (empty($validated['status_kesehatan'])) {
            $bb = $validated['berat_badan'];
            $usia = $validated['usia_bulan'];
            if ($bb < 6 && $usia > 6) {
                $validated['status_kesehatan'] = 'Perlu Perhatian';
            } else {
                $validated['status_kesehatan'] = 'Gizi Baik / Sehat';
            }
        }

        $validated['pemeriksaan_oleh'] = $validated['pemeriksaan_oleh'] ?? auth()->user()->nama;

        Posyandu::create($validated);

        return redirect()->route('posyandu.index')->with('success', 'Catatan pemeriksaan Posyandu berhasil disimpan!');
    }

    public function destroy($id)
    {
        $p = Posyandu::findOrFail($id);
        $p->delete();

        return redirect()->route('posyandu.index')->with('success', 'Data posyandu berhasil dihapus.');
    }

    public function cetak()
    {
        $data = Posyandu::with('warga')->orderBy('tanggal_pemeriksaan', 'desc')->get();
        return view('posyandu.cetak', compact('data'));
    }
}
