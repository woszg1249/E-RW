<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $credentials['login'];
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $remember = $request->boolean('remember');

        $user = User::where($fieldType, $loginInput)->first();

        if ($user) {
            $passwordValid = false;

            // Cek apakah password tersimpan dalam bentuk hash bcrypt
            if (str_starts_with($user->password, '$2y$') || str_starts_with($user->password, '$2a$')) {
                $passwordValid = Hash::check($credentials['password'], $user->password);
            } elseif ($user->password === $credentials['password']) {
                // Mendukung password lama yang belum di-hash, lalu otomatis upgrade ke bcrypt
                $user->password = Hash::make($credentials['password']);
                $user->save();
                $passwordValid = true;
            }

            if ($passwordValid) {
                Auth::login($user, $remember);
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Selamat datang kembali, ' . $user->nama . '!');
            }
        }

        return back()->withErrors([
            'login' => 'Username/Email atau Password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'tipe_kk' => 'required|in:baru,gabung',
            'status_keluarga' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'nullable|string|max:15',
            'nik' => 'required|string|digits:16|unique:users,nik',
            'no_kk' => 'required|string|digits:16',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'konfirmasi_kk' => 'nullable|string',
        ], [
            'nik.digits' => 'NIK harus berjumlah 16 digit angka sesuai KTP.',
            'nik.unique' => 'NIK tersebut sudah terdaftar dalam sistem warga RW 19.',
            'no_kk.digits' => 'Nomor Kartu Keluarga (KK) harus berjumlah 16 digit angka.',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih username lain.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $noKk = $validated['no_kk'];
        $tipeKk = $validated['tipe_kk'];
        $statusKeluarga = $validated['status_keluarga'];
        $alamat = $validated['alamat'] ?? '-';
        $rt = $validated['rt'] ?? '06';

        if ($tipeKk === 'baru') {
            // Skenario 1: Buat KK Baru -> Wajib Kepala Keluarga
            $statusKeluarga = 'Kepala Keluarga';

            // Cek apakah No KK sudah terdaftar sebelumnya
            $existingKk = User::where('no_kk', $noKk)->first();
            if ($existingKk) {
                return back()->withInput()->withErrors([
                    'no_kk' => "Nomor KK {$noKk} sudah terdaftar di sistem atas nama Kepala Keluarga: {$existingKk->nama}. Silakan pilih opsi 'Gabung ke Kartu Keluarga yang Sudah Ada' jika Anda adalah anggota keluarga."
                ]);
            }
        } else {
            // Skenario 2: Gabung ke KK yang sudah ada -> Wajib anggota keluarga
            if ($statusKeluarga === 'Kepala Keluarga') {
                return back()->withInput()->withErrors([
                    'status_keluarga' => "Untuk bergabung ke KK yang sudah ada, silakan pilih hubungan: Istri, Anak, atau Famili Lain."
                ]);
            }

            $membersKk = User::where('no_kk', $noKk)->get();
            if ($membersKk->isEmpty()) {
                return back()->withInput()->withErrors([
                    'no_kk' => "Nomor KK {$noKk} belum terdaftar di sistem. Jika Anda adalah Kepala Keluarga, silakan pilih opsi 'Buat Kartu Keluarga Baru'."
                ]);
            }

            // Validasi duplikasi peran tunggal dalam satu KK
            if ($statusKeluarga === 'Istri' && $membersKk->where('status_keluarga', 'Istri')->isNotEmpty()) {
                return back()->withInput()->withErrors([
                    'status_keluarga' => "Status 'Istri' sudah terdaftar dalam Kartu Keluarga ini."
                ]);
            }

            // Warisi data alamat dan wilayah RT dari Kepala Keluarga
            $kepala = $membersKk->firstWhere('status_keluarga', 'Kepala Keluarga') ?? $membersKk->first();
            $alamat = $kepala->alamat ?? $alamat;
            $rt = $kepala->rt ?? $rt;
        }

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'alamat' => $alamat,
            'kelamin' => $validated['kelamin'],
            'no_hp' => $validated['no_hp'] ?? '-',
            'nik' => $validated['nik'],
            'no_kk' => $noKk,
            'rt' => $rt,
            'rw' => $validated['rw'] ?? '19',
            'role' => 'Warga',
            'foto' => 'profile.jpg',
            'pekerjaan' => 'Belum / Tidak Bekerja',
            'agama' => 'Islam',
            'status_keluarga' => $statusKeluarga,
            'status_perkawinan' => ($statusKeluarga === 'Kepala Keluarga' || $statusKeluarga === 'Istri') ? 'Kawin' : 'Belum Kawin',
            'pendidikan' => 'SLTA / Sederajat',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '2000-01-01',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di RT RW Digital.');
    }

    public function checkNik(Request $request)
    {
        $nik = trim($request->nik ?? '');
        if (!$nik || strlen($nik) < 10) {
            return response()->json(['valid' => true, 'exists' => false]);
        }

        $exists = User::where('nik', $nik)->exists();
        if ($exists) {
            return response()->json([
                'valid' => false,
                'exists' => true,
                'message' => "NIK {$nik} sudah terdaftar di sistem warga RW 19."
            ]);
        }

        return response()->json(['valid' => true, 'exists' => false, 'message' => 'NIK dapat digunakan.']);
    }

    public function checkKk(Request $request)
    {
        $noKk = trim($request->no_kk ?? '');
        if (!$noKk || strlen($noKk) < 10) {
            return response()->json(['found' => false]);
        }

        $members = User::where('no_kk', $noKk)->get();
        if ($members->isNotEmpty()) {
            $kepala = $members->firstWhere('status_keluarga', 'Kepala Keluarga') ?? $members->first();
            return response()->json([
                'found' => true,
                'no_kk' => $noKk,
                'kepala_keluarga' => $kepala->nama ?? '-',
                'alamat' => $kepala->alamat ?? '',
                'rt' => $kepala->rt ?? '06',
                'rw' => $kepala->rw ?? '19',
                'total_anggota' => $members->count(),
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
