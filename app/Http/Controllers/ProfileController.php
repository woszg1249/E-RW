<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password_lama' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'foto_user_' . $user->id . '_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('img');
            $file->move($destPath, $filename);
            $user->foto = $filename;
        }

        if ($request->filled('password')) {
            if (!Hash::check($request->password_lama, $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama tidak sesuai!']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'] ?? $user->no_hp;
        $user->alamat = $validated['alamat'] ?? $user->alamat;
        $user->nik = $validated['nik'] ?? $user->nik;
        $user->no_kk = $validated['no_kk'] ?? $user->no_kk;
        $user->rt = $validated['rt'] ?? $user->rt;
        $user->rw = $validated['rw'] ?? $user->rw;
        $user->pekerjaan = $validated['pekerjaan'] ?? $user->pekerjaan;
        $user->status_perkawinan = $validated['status_perkawinan'] ?? $user->status_perkawinan;
        $user->pendidikan = $validated['pendidikan'] ?? $user->pendidikan;
        $user->agama = $validated['agama'] ?? $user->agama;
        $user->tempat_lahir = $validated['tempat_lahir'] ?? $user->tempat_lahir;
        if (!empty($validated['tanggal_lahir'])) {
            $user->tanggal_lahir = $validated['tanggal_lahir'];
        }

        $user->save();

        return redirect()->route('profil.index')->with('success', 'Profil Anda telah berhasil diperbarui!');
    }
}
