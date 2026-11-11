@extends('layouts.app')

@section('title', 'Buku Kas Keuangan')
@section('page_title', 'Buku Kas Wilayah RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Summary Header Cards -->
    <div class="col-md-4">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Saldo Kas Saat Ini</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase text-success">Total Kas Masuk</span>
            <h3 class="fw-bold text-success mb-0 mt-1">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase text-danger">Total Kas Keluar</span>
            <h3 class="fw-bold text-danger mb-0 mt-1">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Mutasi Keuangan Kas RT</h5>
                    <p class="text-muted small mb-0">Catatan transparansi kas masuk & pengeluaran operasional RT 06</p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('keuangan.laporan') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fa-solid fa-file-pdf me-1"></i> Cetak Laporan
                    </a>
                    @if(auth()->user()->isAdmin())
                        <button class="btn btn-primary-custom rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahKas">
                            <i class="fa-solid fa-plus me-1"></i> Catat Kas Baru
                        </button>
                    @endif
                </div>
            </div>

            <!-- Filter & Search Form -->
            <form action="{{ route('keuangan.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari keterangan / nama warga..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="jenis" class="form-select form-select-sm">
                        <option value="">Semua Jenis</option>
                        <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Kas Masuk (+)</option>
                        <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Kas Keluar (-)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="tgl_mulai" class="form-control form-control-sm" title="Tanggal Mulai" value="{{ request('tgl_mulai') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="tgl_selesai" class="form-control form-control-sm" title="Tanggal Selesai" value="{{ request('tgl_selesai') }}">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-sm btn-dark">Filter</button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Pihak / Warga</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Bukti Nota</th>
                            @if(auth()->user()->isAdmin())
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $index => $t)
                            <tr>
                                <td class="small">{{ $transaksi->firstItem() + $index }}</td>
                                <td class="small fw-semibold">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                                <td class="fw-semibold text-dark">{{ $t->keterangan }}</td>
                                <td class="small text-muted">{{ $t->nama_warga ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $t->jenis === 'masuk' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-3 py-1">
                                        {{ $t->jenis === 'masuk' ? '+ Masuk' : '- Keluar' }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $t->jenis === 'masuk' ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($t->bukti && $t->bukti->count() > 0)
                                        @foreach($t->bukti as $b)
                                            <a href="{{ asset('img/bukti_kas/' . $b->filename) }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2 rounded-2" title="Lihat Struk/Bukti">
                                                <i class="fa-solid fa-receipt text-primary"></i> Bukti
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td class="text-center">
                                        <form action="{{ route('keuangan.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan kas ini?');">
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
                                <td colspan="8" class="text-center text-muted py-4">Belum ada transaksi kas yang sesuai kriteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transaksi->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kas (Admin) -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalTambahKas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Catat Transaksi Kas RT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('keuangan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jenis Kas</label>
                        <select name="jenis" class="form-select" required>
                            <option value="masuk">Kas Masuk (+) Pemasukan / Iuran / Sumbangan</option>
                            <option value="keluar">Kas Keluar (-) Pengeluaran Operasional / Perbaikan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan Transaksi</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Pembelian lampu pos ronda / Iuran bulanan" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nominal (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" placeholder="0" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Warga / Pihak Terkait</label>
                        <input type="text" name="nama_warga" class="form-control" placeholder="Opsional (misal: Toko Bangunan / Nama Warga)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Upload Foto Struk / Nota (Opsional)</label>
                        <input type="file" name="bukti" class="form-control" accept="image/*">
                        <small class="text-muted">Maksimal 2MB (JPG, PNG)</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
