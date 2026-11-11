@extends('layouts.app')

@section('title', 'Tagihan Iuran Warga')
@section('page_title', 'Tagihan Iuran Kas & Kebersihan RT')

@section('content')
<div class="row g-4">
    <!-- Stat Cards -->
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Tunggakan Belum Lunas</span>
                <span class="badge bg-danger rounded-pill px-3 py-1">Belum Dibayar</span>
            </div>
            <h3 class="fw-bold text-danger mb-0">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Total Iuran Terbayar</span>
                <span class="badge bg-success rounded-pill px-3 py-1">Lunas</span>
            </div>
            <h3 class="fw-bold text-success mb-0">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Main Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Tagihan Iuran Bulanan</h5>
                    <p class="text-muted small mb-0">Kelola dan pantau status iuran kas RT, keamanan, & kebersihan warga</p>
                </div>

                @if(auth()->user()->isAdmin())
                    <button class="btn btn-primary-custom rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalGenerateTagihan">
                        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Buat / Generate Tagihan
                    </button>
                @endif
            </div>

            <!-- Filter -->
            <form action="{{ route('tagihan.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-4">
                    <input type="text" name="bulan_tahun" class="form-control form-control-sm" placeholder="Cari bulan & tahun (misal: November 2026)" value="{{ request('bulan_tahun') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status Bayar</option>
                        <option value="Belum Lunas" {{ request('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Cari Data</button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Nama Warga</th>
                            <th>NIK / No KK</th>
                            <th>Bulan / Periode</th>
                            <th>Jumlah Iuran</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihanList as $index => $item)
                            <tr>
                                <td class="small">{{ $tagihanList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->user->nama ?? 'Warga #' . $item->id_user }}</div>
                                    <small class="text-muted">RT {{ $item->user->rt ?? '06' }} • RW {{ $item->user->rw ?? '19' }}</small>
                                </td>
                                <td class="small text-muted">
                                    {{ $item->user->nik ?? '-' }}
                                </td>
                                <td class="fw-semibold">{{ $item->bulan_tahun }}</td>
                                <td class="fw-bold text-dark">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->status_bayar === 'Lunas')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-clock me-1"></i> Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $item->status_bayar === 'Lunas' && $item->tgl_update ? \Carbon\Carbon::parse($item->tgl_update)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($item->status_bayar === 'Belum Lunas')
                                        <button class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $item->id_tagihan }}">
                                            <i class="fa-solid fa-credit-card me-1"></i> Bayar
                                        </button>

                                        <!-- Modal Bayar -->
                                        <div class="modal fade" id="modalBayar{{ $item->id_tagihan }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 rounded-4 shadow">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title fw-bold">Konfirmasi Pembayaran Iuran</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('tagihan.bayar', $item->id_tagihan) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body text-center">
                                                            <div class="p-3 bg-light rounded-3 mb-3 text-start">
                                                                <div class="row g-2 small">
                                                                    <div class="col-5 text-muted">Nama Warga:</div>
                                                                    <div class="col-7 fw-bold">{{ $item->user->nama ?? '-' }}</div>
                                                                    <div class="col-5 text-muted">Periode Iuran:</div>
                                                                    <div class="col-7 fw-bold">{{ $item->bulan_tahun }}</div>
                                                                    <div class="col-5 text-muted">Total Pembayaran:</div>
                                                                    <div class="col-7 fw-bold text-success fs-6">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <p class="small text-muted mb-2">Pindai QRIS di bawah ini melalui GoPay/OVO/Dana/BCA/Mandiri:</p>
                                                                <img src="{{ asset('img/logo_Qris.jpeg') }}" alt="QRIS Iuran RT" class="img-fluid rounded border" style="max-height: 220px;" onerror="this.src='https://placehold.co/240x240?text=QRIS+RT+06'">
                                                            </div>

                                                            <div class="mb-3 text-start">
                                                                <label class="form-label fw-semibold small">Metode Pembayaran</label>
                                                                <select name="metode" class="form-select form-select-sm">
                                                                    <option value="QRIS Digital">QRIS Digital (GoPay, OVO, ShopeePay, DANA)</option>
                                                                    <option value="Transfer Bank">Transfer Bank (BCA / Mandiri)</option>
                                                                    <option value="Tunai / Cash">Tunai ke Pengurus RT</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                                                <i class="fa-solid fa-check me-1"></i> Konfirmasi Telah Dibayar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-success small fw-semibold"><i class="fa-solid fa-check-double"></i> Terverifikasi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Tidak ada data tagihan yang sesuai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tagihanList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Tagihan (Admin) -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalGenerateTagihan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat / Generate Tagihan Iuran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tagihan.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Periode Bulan & Tahun</label>
                        <input type="text" name="bulan_tahun" class="form-control" value="{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}" required>
                        <small class="text-muted">Contoh: November 2026</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nominal Iuran (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" value="25000" min="1000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Jatuh Tempo</label>
                            <input type="date" name="tanggal_tagihan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Target Penerima Tagihan</label>
                        <select name="target" class="form-select" id="targetSelect" onchange="toggleUserSelect(this.value)">
                            <option value="semua">Semua Warga Terdaftar (Otomatis Masal)</option>
                            <option value="individu">Satu Warga Tertentu</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="userSelectContainer">
                        <label class="form-label fw-semibold small">Pilih Warga</label>
                        <select name="id_user" class="form-select">
                            <option value="">-- Pilih Warga --</option>
                            @foreach($daftarWarga as $w)
                                <option value="{{ $w->id }}">{{ $w->nama }} ({{ $w->username }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Generate Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleUserSelect(val) {
        const container = document.getElementById('userSelectContainer');
        if (val === 'individu') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }
</script>
@endif
@endsection
