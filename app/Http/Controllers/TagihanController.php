<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\NotifDismissed;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Tagihan::with('user');

        if ($user->isWarga()) {
            $query->where('id_user', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        if ($request->filled('bulan_tahun')) {
            $query->where('bulan_tahun', 'like', "%{$request->bulan_tahun}%");
        }

        if ($request->filled('rt')) {
            $rt = $request->rt;
            $query->whereHas('user', function($q) use ($rt) {
                $q->where('rt', $rt);
            });
        }

        $tagihanList = $query->orderBy('id_tagihan', 'desc')->paginate(15)->withQueryString();

        $totalTunggakanQuery = Tagihan::where('status_bayar', 'Belum Lunas');
        $totalLunasQuery = Tagihan::where('status_bayar', 'Lunas');

        if ($user->isWarga()) {
            $totalTunggakanQuery->where('id_user', $user->id);
            $totalLunasQuery->where('id_user', $user->id);
        } elseif ($request->filled('rt')) {
            $rt = $request->rt;
            $totalTunggakanQuery->whereHas('user', function($q) use ($rt) { $q->where('rt', $rt); });
            $totalLunasQuery->whereHas('user', function($q) use ($rt) { $q->where('rt', $rt); });
        }

        $totalTunggakan = $totalTunggakanQuery->sum('jumlah');
        $totalLunas = $totalLunasQuery->sum('jumlah');

        $daftarWarga = $user->isAdmin() ? User::where('role', 'Warga')->orderBy('nama', 'asc')->get() : [];

        return view('tagihan.index', compact('tagihanList', 'totalTunggakan', 'totalLunas', 'daftarWarga'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|string', // misal: "November 2026"
            'tanggal_tagihan' => 'required|date',
            'jumlah' => 'required|numeric|min:1000',
            'target' => 'required|in:semua,rt06,rt07,individu',
            'id_user' => 'nullable|required_if:target,individu|exists:users,id',
        ]);

        if (in_array($validated['target'], ['semua', 'rt06', 'rt07'])) {
            $wargaQuery = User::where('role', 'Warga');
            if ($validated['target'] === 'rt06') {
                $wargaQuery->where('rt', '06');
            } elseif ($validated['target'] === 'rt07') {
                $wargaQuery->where('rt', '07');
            }

            $wargaList = $wargaQuery->get();
            $count = 0;
            foreach ($wargaList as $w) {
                $ada = Tagihan::where('id_user', $w->id)->where('bulan_tahun', $validated['bulan_tahun'])->first();
                if (!$ada) {
                    Tagihan::create([
                        'id_user' => $w->id,
                        'bulan_tahun' => $validated['bulan_tahun'],
                        'tanggal_tagihan' => $validated['tanggal_tagihan'],
                        'jumlah' => $validated['jumlah'],
                        'status_bayar' => 'Belum Lunas',
                        'tgl_buat' => now(),
                    ]);
                    $count++;
                }
            }
            return redirect()->route('tagihan.index')->with('success', "Berhasil membuat {$count} tagihan iuran baru!");
        } else {
            Tagihan::create([
                'id_user' => $validated['id_user'],
                'bulan_tahun' => $validated['bulan_tahun'],
                'tanggal_tagihan' => $validated['tanggal_tagihan'],
                'jumlah' => $validated['jumlah'],
                'status_bayar' => 'Belum Lunas',
                'tgl_buat' => now(),
            ]);

            return redirect()->route('tagihan.index')->with('success', 'Tagihan individu berhasil dibuat!');
        }
    }

    public function bayar(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if (auth()->user()->isWarga() && $tagihan->id_user !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $request->validate([
            'metode' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        $tagihan->update([
            'status_bayar' => 'Lunas',
            'tgl_update' => now(),
        ]);

        TransaksiPembayaran::create([
            'id_user' => $tagihan->id_user,
            'id_tagihan' => $tagihan->id_tagihan,
            'jumlah_bayar' => $tagihan->jumlah,
            'metode_pembayaran' => $request->metode,
            'status' => 'success',
            'tanggal_bayar' => now(),
            'order_id' => 'INV-' . strtoupper(Str::random(10)),
        ]);

        $namaWarga = $tagihan->user->nama ?? 'Warga';
        $rtWarga = $tagihan->user->rt ?? '06';

        Keuangan::create([
            'kategori' => 'Iuran Warga',
            'deskripsi' => "Iuran {$tagihan->bulan_tahun} - {$namaWarga} (RT {$rtWarga}/RW 19) [{$request->metode}]",
            'jumlah' => $tagihan->jumlah,
            'jenis' => 'masuk',
            'tanggal' => now()->toDateString(),
            'nama_penyetor' => $namaWarga,
            'status' => 'diterima',
            'created_at' => now(),
        ]);

        return redirect()->route('tagihan.index')->with('success', 'Pembayaran tagihan iuran berhasil dikonfirmasi dan dicatat ke Kas RT!');
    }

    public function dismissNotif()
    {
        $user = auth()->user();
        $latest = Tagihan::where('id_user', $user->id)->where('status_bayar', 'Belum Lunas')->latest('id_tagihan')->first();

        if ($latest) {
            NotifDismissed::firstOrCreate([
                'id_user' => $user->id,
                'id_tagihan' => $latest->id_tagihan,
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
