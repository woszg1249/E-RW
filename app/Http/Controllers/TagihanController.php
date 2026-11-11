<?php

namespace App\Http\Controllers;

use App\Models\BankWargaRekening;
use App\Models\Keuangan;
use App\Models\NotifDismissed;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use App\Services\BankWargaService;
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

        // Rekening Bank Digital Warga untuk pembayaran instan
        $rekeningBankWarga = null;
        if ($user->isWarga()) {
            $rekeningBankWarga = BankWargaRekening::getOrCreateForUser($user);
        }

        return view('tagihan.index', compact('tagihanList', 'totalTunggakan', 'totalLunas', 'daftarWarga', 'rekeningBankWarga'));
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

        $namaWarga = $tagihan->user->nama ?? (auth()->user()->nama ?? 'Warga');
        $rtWarga = $tagihan->user->rt ?? (auth()->user()->rt ?? '06');
        $metode = $request->metode ?? 'QRIS Digital';

        // Jika metode pembayaran menggunakan Bank Digital Warga, potong saldo secara otomatis
        if ($metode === 'Bank Digital Warga') {
            $userPemilik = $tagihan->user ?? auth()->user();
            $rekening = BankWargaRekening::getOrCreateForUser($userPemilik);

            if ((float) $rekening->saldo < (float) $tagihan->jumlah) {
                return back()->with('error', 'Gagal memproses pembayaran: Saldo Bank Digital Warga tidak mencukupi (Saldo saat ini: Rp ' . number_format($rekening->saldo, 0, ',', '.') . '). Silakan Top-Up saldo atau setor sampah terlebih dahulu.');
            }

            try {
                BankWargaService::tarikDana(
                    $rekening,
                    (float) $tagihan->jumlah,
                    'bayar_iuran',
                    "Pembayaran {$tagihan->bulan_tahun} - {$namaWarga}",
                    'Bank Digital Warga',
                    (string) $tagihan->id_tagihan
                );
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        $tagihan->update([
            'status_bayar' => 'Lunas',
            'tgl_update' => now(),
        ]);

        TransaksiPembayaran::create([
            'order_id' => 'INV-' . strtoupper(Str::random(10)),
            'id_user' => $tagihan->id_user,
            'nama_warga' => $namaWarga,
            'jenis_kas' => 'masuk',
            'keterangan' => "Iuran {$tagihan->bulan_tahun} - {$namaWarga} (RT {$rtWarga}/RW 19)",
            'jumlah' => (int) $tagihan->jumlah,
            'id_tagihan' => $tagihan->id_tagihan,
            'metode' => $metode,
            'status' => 'success',
            'paid_at' => now(),
            'created_at' => now(),
        ]);

        Keuangan::create([
            'nama_warga' => $namaWarga,
            'jenis' => 'masuk',
            'keterangan' => "Iuran {$tagihan->bulan_tahun} - {$namaWarga} (RT {$rtWarga}/RW 19) [{$metode}]",
            'jumlah' => $tagihan->jumlah,
            'tanggal' => now()->toDateString(),
        ]);

        // Jika tagihan adalah cicilan pinjaman koperasi, otomatis sinkronkan ke angsuran Koperasi
        if (str_contains(strtolower($tagihan->bulan_tahun), 'koperasi')) {
            $pinjamanAktif = \App\Models\KoperasiPinjaman::where('id_user', $tagihan->id_user)
                ->where('status', 'disetujui')
                ->latest('id')
                ->first();
            if ($pinjamanAktif) {
                \App\Models\KoperasiCicilan::create([
                    'id_pinjaman' => $pinjamanAktif->id,
                    'jumlah' => $tagihan->jumlah,
                    'tanggal' => now(),
                    'catatan' => 'Pembayaran Tagihan Online: ' . $tagihan->bulan_tahun,
                ]);

                if ($pinjamanAktif->fresh()->sisa_pinjaman <= 0) {
                    $pinjamanAktif->update(['status' => 'lunas']);
                }
            }
        }

        return redirect()->route('tagihan.index')->with('success', 'Pembayaran tagihan berhasil dikonfirmasi dan dicatat ke Kas RT!');
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
