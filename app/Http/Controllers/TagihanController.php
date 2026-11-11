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

        $tagihanList = $query->orderBy('id_tagihan', 'desc')->paginate(15)->withQueryString();

        $totalTunggakan = $user->isWarga()
            ? Tagihan::where('id_user', $user->id)->where('status_bayar', 'Belum Lunas')->sum('jumlah')
            : Tagihan::where('status_bayar', 'Belum Lunas')->sum('jumlah');

        $totalLunas = $user->isWarga()
            ? Tagihan::where('id_user', $user->id)->where('status_bayar', 'Lunas')->sum('jumlah')
            : Tagihan::where('status_bayar', 'Lunas')->sum('jumlah');

        $daftarWarga = $user->isAdmin() ? User::where('role', 'Warga')->orderBy('nama', 'asc')->get() : [];

        return view('tagihan.index', compact('tagihanList', 'totalTunggakan', 'totalLunas', 'daftarWarga'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'bulan_tahun' => 'required|string', // misal: "November 2026"
            'tanggal_tagihan' => 'required|date',
            'jumlah' => 'required|numeric|min:1000',
            'target' => 'required|in:semua,individu',
            'id_user' => 'nullable|required_if:target,individu|exists:users,id',
        ]);

        if ($validated['target'] === 'semua') {
            $wargaList = User::where('role', 'Warga')->get();
            $count = 0;
            foreach ($wargaList as $w) {
                // Hindari duplikasi tagihan untuk bulan yang sama
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
            return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dibuat untuk warga terpilih.');
        }
    }

    public function bayar(Request $request, $id)
    {
        $tagihan = Tagihan::with('user')->findOrFail($id);

        if (auth()->user()->isWarga() && $tagihan->id_user !== auth()->id()) {
            abort(403, 'Akses tidak sah');
        }

        $tagihan->update([
            'status_bayar' => 'Lunas',
            'tgl_update' => now(),
        ]);

        // Catat otomatis ke Kas Masuk RT
        Keuangan::create([
            'nama_warga' => $tagihan->user->nama ?? 'Warga RT',
            'jenis' => 'masuk',
            'keterangan' => 'Pembayaran Iuran Kas RT (' . $tagihan->bulan_tahun . ') - Tagihan #' . $tagihan->id_tagihan,
            'jumlah' => $tagihan->jumlah,
            'tanggal' => now()->toDateString(),
        ]);

        // Catat riwayat transaksi
        TransaksiPembayaran::create([
            'order_id' => 'INV-' . strtoupper(Str::random(10)),
            'id_user' => $tagihan->id_user,
            'nama_warga' => $tagihan->user->nama ?? 'Warga',
            'jenis_kas' => 'masuk',
            'keterangan' => 'Iuran ' . $tagihan->bulan_tahun,
            'jumlah' => $tagihan->jumlah,
            'id_tagihan' => $tagihan->id_tagihan,
            'metode' => $request->input('metode', 'Manual Admin / Transfer'),
            'status' => 'settlement',
            'created_at' => now(),
            'paid_at' => now(),
        ]);

        return redirect()->route('tagihan.index')->with('success', 'Tagihan telah berhasil dibayar & tercatat di Buku Kas RT!');
    }

    public function dismissNotif($id)
    {
        NotifDismissed::firstOrCreate([
            'id_tagihan' => $id,
            'id_user' => auth()->id(),
        ], [
            'created_at' => now(),
        ]);

        return back()->with('success', 'Notifikasi disembunyikan.');
    }
}
