@extends('layouts.app')

@section('title', 'Bank Sampah Digital')
@section('page_title', 'Program Bank Sampah RT 06')

@section('content')
<div class="row g-4">
    <!-- Stat Cards -->
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Total Sampah Terkumpul</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format((float) ($totalKg ?? 0), 2) }} kg</h3>
            <small class="text-muted">Partisipasi warga dalam daur ulang & kebersihan lingkungan</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Nilai Ekonomi Terkumpul</span>
            <h3 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format((float) ($totalRupiah ?? 0), 0, ',', '.') }}</h3>
            <small class="text-muted">Total konversi rupiah dari tabungan sampah warga</small>
        </div>
    </div>

    <!-- Main Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Setoran Bank Sampah</h5>
                    <p class="text-muted small mb-0">Catat dan konversikan sampah anorganik (plastik, kardus, logam, jelantah) menjadi saldo rupiah</p>
                </div>

                <button class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalSetorSampah">
                    <i class="fa-solid fa-plus me-1"></i> Catat Setoran Sampah
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Nama Warga</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (kg)</th>
                            <th>Harga / kg</th>
                            <th>Total Nominal</th>
                            <th>Tanggal Pengumpulan</th>
                            <th>Status</th>
                            @if(auth()->user()->isAdmin())
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiList as $index => $item)
                            <tr>
                                <td class="small">{{ $transaksiList->firstItem() + $index }}</td>
                                <td class="fw-bold text-dark">{{ $item->nama_warga ?? ($item->user->nama ?? '-') }}</td>
                                <td>
                                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1">
                                        {{ $item->jenis_sampah }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">{{ $item->berat_kg }} kg</td>
                                <td class="small">Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                <td class="small text-muted">{{ $item->tanggal_pengumpulan ? \Carbon\Carbon::parse($item->tanggal_pengumpulan)->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    @if($item->status === 'approved')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-check me-1"></i> Terverifikasi
                                        </span>
                                    @elseif($item->status === 'rejected')
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-xmark me-1"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-clock me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if($item->status === 'pending')
                                                <form action="{{ route('bank-sampah.status', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Setujui">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('bank-sampah.status', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Tolak">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('bank-sampah.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus setoran ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger border-0" title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada catatan transaksi bank sampah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transaksiList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Setor Sampah -->
<div class="modal fade" id="modalSetorSampah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Pencatatan Setoran Sampah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-sampah.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if(auth()->user()->isAdmin())
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pilih Warga Penyetor</label>
                            <select name="id_user" class="form-select" required>
                                <option value="">-- Pilih Warga --</option>
                                @foreach($wargaList as $w)
                                    <option value="{{ $w->id }}">{{ $w->nama }} (RT {{ $w->rt ?? '06' }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori / Jenis Sampah</label>
                        <select name="jenis_sampah" class="form-select" id="jenisSampahSelect" onchange="autoFillHarga(this.value)" required>
                            <option value="Botol Plastik PET" data-harga="3000">Botol Plastik PET (Rp 3.000 / kg)</option>
                            <option value="Kardus Bekas / Box" data-harga="1500">Kardus Bekas / Box (Rp 1.500 / kg)</option>
                            <option value="Kertas Arsip / HVS" data-harga="2000">Kertas Arsip / HVS (Rp 2.000 / kg)</option>
                            <option value="Kaleng Minuman / Logam" data-harga="8000">Kaleng Minuman / Logam (Rp 8.000 / kg)</option>
                            <option value="Minyak Jelantah" data-harga="5000">Minyak Jelantah (Rp 5.000 / kg)</option>
                            <option value="Besi Tua" data-harga="4000">Besi Tua (Rp 4.000 / kg)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Berat (kg)</label>
                            <input type="number" step="0.01" name="berat_kg" id="inputBerat" class="form-control" placeholder="0.0" oninput="hitungTotal()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Harga per kg (Rp)</label>
                            <input type="number" name="harga_per_kg" id="inputHarga" class="form-control" value="3000" oninput="hitungTotal()" required>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 text-center">
                        <span class="small text-muted d-block">Estimasi Nilai Tabungan:</span>
                        <h4 class="fw-bold text-success mb-0" id="estimasiTotal">Rp 0</h4>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Kondisi kering dan bersih">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Setoran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function autoFillHarga(val) {
        const select = document.getElementById('jenisSampahSelect');
        const selectedOption = select.options[select.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga') || 3000;
        document.getElementById('inputHarga').value = harga;
        hitungTotal();
    }

    function hitungTotal() {
        const berat = parseFloat(document.getElementById('inputBerat').value) || 0;
        const harga = parseFloat(document.getElementById('inputHarga').value) || 0;
        const total = berat * harga;
        document.getElementById('estimasiTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endsection
