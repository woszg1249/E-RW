<?php

namespace App\Http\Controllers;

use App\Models\BankSampah;
use App\Models\User;
use App\Services\BankWargaService;
use Illuminate\Http\Request;

class BankSampahController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = BankSampah::with('user');

        if ($user->isWarga()) {
            $query->where('id_user', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksiList = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalKg = (float) (($user->isWarga()
            ? BankSampah::where('id_user', $user->id)->where('status', 'approved')->sum('berat_kg')
            : BankSampah::where('status', 'approved')->sum('berat_kg')) ?? 0);

        $totalRupiah = (float) (($user->isWarga()
            ? BankSampah::where('id_user', $user->id)->where('status', 'approved')->sum('total_harga')
            : BankSampah::where('status', 'approved')->sum('total_harga')) ?? 0);

        $wargaList = $user->isAdmin() ? User::where('role', 'Warga')->orderBy('nama', 'asc')->get() : [];

        return view('bank_sampah.index', compact('transaksiList', 'totalKg', 'totalRupiah', 'wargaList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'nullable|exists:users,id',
            'jenis_sampah' => 'required|string|max:100',
            'berat_kg' => 'required|numeric|min:0.1',
            'harga_per_kg' => 'required|numeric|min:100',
            'catatan' => 'nullable|string',
        ]);

        $userId = auth()->user()->isAdmin() && !empty($validated['id_user'])
            ? $validated['id_user']
            : auth()->id();

        $targetUser = User::find($userId);
        $totalHarga = $validated['berat_kg'] * $validated['harga_per_kg'];

        $status = auth()->user()->isAdmin() ? 'approved' : 'pending';

        $item = BankSampah::create([
            'id_user' => $userId,
            'nama_warga' => $targetUser->nama ?? auth()->user()->nama,
            'jenis_sampah' => $validated['jenis_sampah'],
            'berat_kg' => $validated['berat_kg'],
            'harga_per_kg' => $validated['harga_per_kg'],
            'total_harga' => $totalHarga,
            'status' => $status,
            'tanggal_pengumpulan' => now(),
            'tanggal_diproses' => ($status === 'approved') ? now() : null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $pesan = 'Setoran bank sampah berhasil dicatat!';
        if ($status === 'approved') {
            BankWargaService::transferDariBankSampah($item);
            $pesan = 'Setoran bank sampah berhasil dicatat & dana Rp ' . number_format($totalHarga, 0, ',', '.') . ' otomatis ditransfer ke Bank Warga!';
        }

        return redirect()->route('bank-sampah.index')->with('success', $pesan);
    }

    public function updateStatus(Request $request, $id)
    {
        $item = BankSampah::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $item->update([
            'status' => $request->status,
            'tanggal_diproses' => now(),
        ]);

        $pesan = 'Status setoran bank sampah diperbarui!';
        if ($request->status === 'approved') {
            BankWargaService::transferDariBankSampah($item);
            $pesan = 'Setoran bank sampah disetujui & dana Rp ' . number_format($item->total_harga, 0, ',', '.') . ' otomatis ditransfer ke Bank Warga!';
        }

        return redirect()->route('bank-sampah.index')->with('success', $pesan);
    }

    public function destroy($id)
    {
        $item = BankSampah::findOrFail($id);
        $item->delete();

        return redirect()->route('bank-sampah.index')->with('success', 'Data bank sampah berhasil dihapus.');
    }
}
