@extends('layouts.app')

@section('title', 'Inventaris Aset RT')
@section('page_title', 'Inventaris & Peminjaman Aset RT 06')

@section('content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">Inventaris & Fasilitas Milik Warga RT 06</h5>
                <p class="text-muted small mb-0">Warga dapat mengajukan peminjaman fasilitas (kursi, tenda, sound system, proyektor) secara online</p>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPinjamAset">
                    <i class="fa-solid fa-hand-holding-box me-1"></i> Ajukan Pinjam Aset
                </button>
                @if(auth()->user()->isAdmin())
                    <button class="btn btn-primary-custom rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahAset">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Aset
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Inventory Cards Grid -->
    <div class="col-12">
        <h6 class="fw-bold text-dark mb-3">Katalog Fasilitas & Inventaris RT</h6>
        <div class="row g-3">
            @forelse($asetList as $a)
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom p-4 bg-white h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-light text-primary border rounded-pill px-3 py-1 small">
                                    {{ $a->kategori ?? 'Umum' }}
                                </span>
                                <span class="badge {{ $a->kondisi === 'Baik' ? 'bg-success' : 'bg-warning' }} rounded-pill px-2 py-1 small">
                                    {{ $a->kondisi ?? 'Baik' }}
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">{{ $a->nama }}</h5>
                            <p class="text-secondary small mb-3">{{ $a->deskripsi ?? 'Tidak ada rincian spesifikasi.' }}</p>

                            <div class="small text-muted mb-1">
                                <i class="fa-solid fa-boxes-stacked text-secondary me-1"></i> <strong>Jumlah:</strong> {{ $a->jumlah }} Unit
                            </div>
                            <div class="small text-muted">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i> <strong>Penyimpanan:</strong> {{ $a->lokasi ?? 'Gudang RT 06' }}
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-sm btn-outline-success w-100 rounded-pill" onclick="pilihAset({{ $a->id }}, '{{ addslashes($a->nama) }}')">
                                <i class="fa-solid fa-calendar-plus me-1"></i> Pinjam Barang Ini
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card-custom p-4 text-center text-muted bg-white">
                        Belum ada aset inventaris yang dicatat.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Peminjaman History Table -->
    <div class="col-12 mt-4">
        <div class="card-custom p-4 bg-white">
            <h6 class="fw-bold text-dark mb-3">Daftar Peminjaman Aset</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>Barang yang Dipinjam</th>
                            <th>Jumlah</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            @if(auth()->user()->isAdmin())
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamanList as $idx => $p)
                            <tr>
                                <td class="small">{{ $idx + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $p->user->nama ?? '-' }}</td>
                                <td class="fw-semibold text-primary">{{ $p->aset->nama ?? '-' }}</td>
                                <td>{{ $p->jumlah_pinjam }} Unit</td>
                                <td class="small">{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}</td>
                                <td class="small">{{ $p->tanggal_kembali ? \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($p->status === 'Disetujui')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Disetujui / Dipinjam</span>
                                    @elseif($p->status === 'Dikembalikan')
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">Telah Dikembalikan</span>
                                    @elseif($p->status === 'Ditolak')
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">Menunggu</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-pill px-3 dropdown-toggle" data-bs-toggle="dropdown">
                                                Update Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <form action="{{ route('aset.pinjam.status', $p->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="Disetujui">
                                                        <button class="dropdown-item text-success"><i class="fa-solid fa-check me-2"></i> Setujui Pinjam</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('aset.pinjam.status', $p->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="Dikembalikan">
                                                        <button class="dropdown-item text-primary"><i class="fa-solid fa-rotate-left me-2"></i> Sudah Dikembalikan</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('aset.pinjam.status', $p->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="Ditolak">
                                                        <button class="dropdown-item text-danger"><i class="fa-solid fa-xmark me-2"></i> Tolak</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Belum ada riwayat peminjaman aset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pinjam Aset -->
<div class="modal fade" id="modalPinjamAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Permohonan Pinjam Aset RT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('aset.pinjam') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Barang / Fasilitas</label>
                        <select name="id_aset" id="selectAsetModal" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($asetList as $a)
                                <option value="{{ $a->id }}">{{ $a->nama }} (Tersedia: {{ $a->jumlah }} Unit)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah yang Dipinjam</label>
                        <input type="number" name="jumlah_pinjam" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Pinjam</label>
                            <input type="date" name="tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Rencana Kembali</label>
                            <input type="date" name="tanggal_kembali" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keperluan Peminjaman</label>
                        <textarea name="catatan" rows="2" class="form-control" placeholder="Contoh: Acara syukuran keluarga"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Aset (Admin) -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalTambahAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Aset / Fasilitas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('aset.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Aset / Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Tenda Terop 4x6 Meter" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="Tenda & Panggung">Tenda & Panggung</option>
                                <option value="Kursi & Meja">Kursi & Meja</option>
                                <option value="Sound System & Audio">Sound System & Audio</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Alat Kebersihan">Alat Kebersihan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Jumlah Unit <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lokasi Penyimpanan</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Balai Warga RT 06">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kondisi Barang</label>
                        <select name="kondisi" class="form-select">
                            <option value="Baik">Baik & Siap Pakai</option>
                            <option value="Cukup Baik">Cukup Baik</option>
                            <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi Spesifikasi</label>
                        <textarea name="deskripsi" rows="2" class="form-control" placeholder="Merk, ukuran, warna, kelengkapan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    function pilihAset(id, nama) {
        document.getElementById('selectAsetModal').value = id;
        new bootstrap.Modal(document.getElementById('modalPinjamAset')).show();
    }
</script>
@endsection
