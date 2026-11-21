<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        // Proteksi privasi: Halaman data induk warga hanya untuk Pengurus RT / Admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak! Daftar Induk Warga hanya dapat diakses oleh Pengurus / Admin RT demi privasi data warga.');
        }

        $query = User::query();

        if ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status_akun')) {
            $query->where('status_akun', $request->status_akun);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('no_kk', 'like', "%{$s}%")
                  ->orWhere('alamat', 'like', "%{$s}%");
            });
        }

        $wargaList = $query->orderByRaw("CASE WHEN status_akun = 'pending' THEN 0 ELSE 1 END")->orderBy('nama', 'asc')->paginate(20)->withQueryString();
        $totalWarga = User::where('role', 'Warga')->count();
        $totalWargaRt06 = User::where('role', 'Warga')->where('rt', '06')->count();
        $totalWargaRt07 = User::where('role', 'Warga')->where('rt', '07')->count();
        $totalPending = User::where('role', 'Warga')->where('status_akun', 'pending')->count();
        $totalKK = User::whereNotNull('no_kk')->where('no_kk', '!=', '')->distinct()->count('no_kk');

        return view('warga.index', compact('wargaList', 'totalWarga', 'totalWargaRt06', 'totalWargaRt07', 'totalPending', 'totalKK'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'nullable|string|max:15',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'role' => 'required|in:Admin,Warga',
            'status_akun' => 'nullable|in:aktif,pending,ditolak',
            'status_keluarga' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
        ]);

        User::create([
            'nama' => $validated['nama'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'nik' => $validated['nik'] ?? null,
            'no_kk' => $validated['no_kk'] ?? null,
            'alamat' => $validated['alamat'] ?? '-',
            'kelamin' => $validated['kelamin'],
            'no_hp' => $validated['no_hp'] ?? '-',
            'rt' => $validated['rt'] ?? '06',
            'rw' => $validated['rw'] ?? '19',
            'role' => $validated['role'],
            'status_akun' => 'aktif',
            'foto' => 'profile.jpg',
            'status_keluarga' => $validated['status_keluarga'] ?? 'Kepala Keluarga',
            'pekerjaan' => $validated['pekerjaan'] ?? 'Belum / Tidak Bekerja',
            'status_perkawinan' => $validated['status_perkawinan'] ?? 'Kawin',
            'pendidikan' => $validated['pendidikan'] ?? 'SLTA / Sederajat',
            'agama' => $validated['agama'] ?? 'Islam',
            'tempat_lahir' => $validated['tempat_lahir'] ?? 'Bekasi',
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? '2000-01-01',
        ]);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'nullable|string|max:15',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'role' => 'required|in:Admin,Warga',
            'status_akun' => 'nullable|in:aktif,pending,ditolak',
            'status_keluarga' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus.');
    }

    public function kelolaKK(Request $request)
    {
        $currentUser = auth()->user();
        $search = $request->search;
        $rt = $request->rt;

        $kkListQuery = User::whereNotNull('no_kk')
            ->where('no_kk', '!=', '')
            ->select('no_kk', 'alamat', 'rt', 'rw')
            ->groupBy('no_kk', 'alamat', 'rt', 'rw');

        // PROTEKSI PRIVASI: JIKA ROLE WARGA, HANYA BISA MELIHAT KK MILIKNYA SENDIRI
        if ($currentUser->isWarga()) {
            if (empty($currentUser->no_kk)) {
                $families = [];
                $kkList = $kkListQuery->where('no_kk', '__NO_KK_NOT_FOUND__')->paginate(1);
                return view('warga.kk', compact('families', 'kkList'));
            }
            $kkListQuery->where('no_kk', $currentUser->no_kk);
        } else {
            // ADMIN: BISA MELIHAT SEMUA & FILTER
            if ($rt) {
                $kkListQuery->where('rt', $rt);
            }

            if ($search) {
                $kkListQuery->where(function ($q) use ($search) {
                    $q->where('no_kk', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%");
                });
            }
        }

        $kkList = $kkListQuery->paginate(12)->withQueryString();

        // Ambil anggota keluarga untuk setiap KK dengan urutan resmi (Kepala Keluarga selalu di atas)
        $families = [];
        foreach ($kkList as $kk) {
            $members = User::where('no_kk', $kk->no_kk)->orderFamilyHierarchy()->get();
            $kepala = $members->firstWhere('status_keluarga', 'Kepala Keluarga') ?? $members->first();
            $families[] = [
                'no_kk' => $kk->no_kk,
                'alamat' => $kk->alamat,
                'rt' => $kk->rt,
                'rw' => $kk->rw,
                'kepala' => $kepala,
                'members' => $members,
            ];
        }

        return view('warga.kk', compact('families', 'kkList'));
    }

    public function cetakKK($no_kk)
    {
        $currentUser = auth()->user();

        // PROTEKSI PRIVASI: JIKA ROLE WARGA, DILARANG MENCETAK KK MILIK ORANG LAIN
        if ($currentUser->isWarga() && $currentUser->no_kk !== $no_kk) {
            abort(403, 'Akses Ditolak! Demi privasi kependudukan, Anda hanya berhak mencetak Kartu Keluarga milik Anda sendiri.');
        }

        // Ambil anggota keluarga dengan urutan resmi (Kepala Keluarga selalu No. 1 di atas)
        $members = User::where('no_kk', $no_kk)->orderFamilyHierarchy()->get();
        if ($members->isEmpty()) {
            abort(404, 'Data Kartu Keluarga tidak ditemukan.');
        }

        $kepala = $members->firstWhere('status_keluarga', 'Kepala Keluarga') ?? $members->first();

        return view('warga.cetak_kk', compact('members', 'kepala', 'no_kk'));
    }

    /**
     * Setujui (ACC) pendaftaran warga baru oleh Ketua RT
     */
    public function accWarga($id)
    {
        $user = User::findOrFail($id);
        $user->status_akun = 'aktif';
        $user->save();

        // Buat rekening Bank Digital Warga otomatis jika belum ada
        if (class_exists(\App\Models\BankWargaRekening::class)) {
            \App\Models\BankWargaRekening::firstOrCreate(
                ['id_user' => $user->id],
                [
                    'nomor_rekening' => 'BW-1906-00' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'nik' => $user->nik,
                    'no_kk' => $user->no_kk,
                    'saldo' => 0.00,
                    'status' => 'aktif',
                    'sumber_rekening' => 'otomatis_akun',
                ]
            );
        }

        return back()->with('success', "Akun warga atas nama {$user->nama} (@{$user->username}) berhasil di-ACC (disetujui). Warga bersangkutan sekarang sudah dapat login ke website.");
    }

    /**
     * Tolak pendaftaran warga baru oleh Ketua RT
     */
    public function tolakWarga($id)
    {
        $user = User::findOrFail($id);
        $user->status_akun = 'ditolak';
        $user->save();

        return back()->with('success', "Pendaftaran akun warga atas nama {$user->nama} (@{$user->username}) telah ditolak.");
    }
}
