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
            'alamat' => 'nullable|string',
            'kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'nullable|string|max:15',
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'pekerjaan' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'alamat' => $validated['alamat'] ?? '-',
            'kelamin' => $validated['kelamin'],
            'no_hp' => $validated['no_hp'] ?? '-',
            'nik' => $validated['nik'] ?? null,
            'no_kk' => $validated['no_kk'] ?? null,
            'rt' => $validated['rt'] ?? '06',
            'rw' => $validated['rw'] ?? '19',
            'role' => 'Warga',
            'foto' => 'profile.jpg',
            'pekerjaan' => $validated['pekerjaan'] ?? 'Belum / Tidak Bekerja',
            'agama' => $validated['agama'] ?? 'Islam',
            'status_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Kawin',
            'pendidikan' => 'SLTA / Sederajat',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '2000-01-01',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di RT RW Digital.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
