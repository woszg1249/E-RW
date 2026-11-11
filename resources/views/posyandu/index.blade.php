@extends('layouts.app')

@section('title', 'Layanan Posyandu Balita')
@section('page_title', 'Catatan Timbang & Posyandu Balita RT 06')

@section('content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">Pencatatan Tumbuh Kembang Balita (Posyandu)</h5>
                <p class="text-muted small mb-0">Pantau berat badan, tinggi badan, dan status gizi anak balita lingkungan RT 06</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('posyandu.cetak') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-print me-1"></i> Cetak Rekap Posyandu
                </a>
                <button class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahPosyandu">
                    <i class="fa-solid fa-plus me-1"></i> Catat Pemeriksaan
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <!-- Search -->
            <form action="{{ route('posyandu.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama balita / status gizi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Cari Data</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Nama Balita</th>
                            <th>L/P</th>
                            <th>Usia</th>
                            <th>Berat (BB)</th>
                            <th>Tinggi (TB)</th>
                            <th>Lingkar Kepala</th>
                            <th>Status Gizi / Kesehatan</th>
                            <th>Tanggal Periksa</th>
                            @if(auth()->user()->isAdmin())
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posyanduList as $index => $p)
                            <tr>
                                <td class="small">{{ $posyanduList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->nama_bayi }}</div>
                                    <small class="text-muted">Orang Tua: {{ $p->warga->nama ?? '-' }}</small>
                                </td>
                                <td class="small">{{ $p->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                                <td class="fw-semibold">{{ $p->usia_bulan }} Bulan</td>
                                <td class="fw-bold text-success">{{ $p->berat_badan }} kg</td>
                                <td>{{ $p->tinggi_badan ? $p->tinggi_badan . ' cm' : '-' }}</td>
                                <td>{{ $p->lingkar_kepala ? $p->lingkar_kepala . ' cm' : '-' }}</td>
                                <td>
                                    <span class="badge {{ str_contains(strtolower($p->status_kesehatan ?? ''), 'baik') || str_contains(strtolower($p->status_kesehatan ?? ''), 'sehat') ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }} rounded-pill px-3 py-1">
                                        {{ $p->status_kesehatan ?? 'Sehat' }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($p->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                                @if(auth()->user()->isAdmin())
                                    <td class="text-center">
                                        <form action="{{ route('posyandu.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus catatan posyandu ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Belum ada data pemeriksaan posyandu tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $posyanduList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Catatan Posyandu -->
<div class="modal fade" id="modalTambahPosyandu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Catat Pemeriksaan Posyandu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Balita / Bayi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bayi" class="form-control" placeholder="Contoh: Ananda Rizky" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Usia (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" name="usia_bulan" class="form-control" placeholder="0" min="0" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Berat (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_badan" class="form-control" placeholder="0.0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tinggi (cm)</label>
                            <input type="number" step="0.01" name="tinggi_badan" class="form-control" placeholder="0.0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Lingkar Kpl (cm)</label>
                            <input type="number" step="0.01" name="lingkar_kepala" class="form-control" placeholder="0.0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Orang Tua (Warga)</label>
                        <select name="id_warga" class="form-select">
                            <option value="">-- Pilih Orang Tua / Kepala Keluarga --</option>
                            @foreach($wargaList as $w)
                                <option value="{{ $w->id }}">{{ $w->nama }} ({{ $w->alamat ?? 'RT ' . $w->rt }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status Kesehatan</label>
                            <select name="status_kesehatan" class="form-select">
                                <option value="Gizi Baik / Sehat">Gizi Baik / Sehat</option>
                                <option value="Perlu Perhatian">Perlu Perhatian / Pantau BB</option>
                                <option value="Gizi Kurang">Gizi Kurang</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Kader / Vitamin / Imunisasi</label>
                        <textarea name="catatan" rows="2" class="form-control" placeholder="Contoh: Diberi Vitamin A dan imunisasi Polio"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
