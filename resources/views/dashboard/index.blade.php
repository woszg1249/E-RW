@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Informasi RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Welcome Banner -->
    <div class="col-12">
        <div class="card-custom p-4 text-white" style="background: linear-gradient(135deg, #059669 0%, #0284c7 100%);">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-white text-success px-3 py-1 rounded-pill mb-2 fw-semibold">
                        <i class="fa-solid fa-circle-check me-1"></i> Sistem Wilayah Aktif: RW 19 (RT 06 & RT 07)
                    </span>
                    <h3 class="fw-bold mb-1">Halo, {{ $user->nama }}! 👋</h3>
                    <p class="mb-0 opacity-90" style="font-size: 0.95rem;">
                        Selamat datang di portal pelayanan digital RW 19 (RT 06 & RT 07) Kelurahan Bekasi Jaya. Anda login sebagai <strong>{{ $user->role }}</strong>.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="{{ route('surat.index') }}" class="btn btn-light text-success fw-bold rounded-pill px-4 flex-grow-1 flex-sm-grow-0">
                            <i class="fa-solid fa-file-pen me-1"></i> Ajukan Surat
                        </a>
                        <a href="{{ route('tagihan.index') }}" class="btn btn-outline-light rounded-pill px-4 flex-grow-1 flex-sm-grow-0">
                            <i class="fa-solid fa-receipt me-1"></i> Cek Tagihan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Metric Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-3 p-xl-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small">Saldo Kas RT</span>
                <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-vault fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
            <div class="small text-muted d-flex gap-2">
                <span class="text-success"><i class="fa-solid fa-arrow-up"></i> Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                <span>•</span>
                <span class="text-danger"><i class="fa-solid fa-arrow-down"></i> Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-3 p-xl-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small">Total Warga Terdaftar</span>
                <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-users fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ number_format($totalWarga) }} Jiwa</h3>
            <div class="small text-muted mb-2">
                Terbagi dalam <strong>{{ $totalKK }}</strong> KK
            </div>
            <div class="d-flex gap-1">
                <span class="badge bg-success bg-opacity-10 text-success border">RT 06: {{ $totalWargaRt06 }}</span>
                <span class="badge bg-primary bg-opacity-10 text-primary border">RT 07: {{ $totalWargaRt07 }}</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-3 p-xl-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small">Tunggakan Iuran</span>
                <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger">
                    <i class="fa-solid fa-receipt fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-danger mb-1">{{ $tagihanBelumLunas }} Tagihan</h3>
            <div class="small text-muted">
                Nominal belum lunas: <strong>Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-3 p-xl-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold small">Pengajuan Surat</span>
                <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-envelope fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $suratMenunggu }} Menunggu</h3>
            <div class="small text-muted">
                <a href="{{ route('surat.index') }}" class="text-decoration-none text-warning fw-semibold">
                    Lihat antrean surat <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Chart & Activity Row -->
    <div class="col-lg-8">
        <div class="card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Grafik Arus Kas RT (6 Bulan Terakhir)</h5>
                    <small class="text-muted">Perbandingan pemasukan iuran vs pengeluaran kas bulanan</small>
                </div>
                <a href="{{ route('keuangan.laporan') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="fa-solid fa-print me-1"></i> Cetak Laporan
                </a>
            </div>
            <div style="height: 280px;">
                <canvas id="chartKas"></canvas>
            </div>
        </div>

        <!-- Transaksi Kas Terbaru -->
        <div class="card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Catatan Kas Terkini</h5>
                <a href="{{ route('keuangan.index') }}" class="text-success text-decoration-none small fw-bold">
                    Lihat Semua <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Pihak / Warga</th>
                            <th>Jenis</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasTerakhir as $kas)
                            <tr>
                                <td class="small">{{ \Carbon\Carbon::parse($kas->tanggal)->format('d M Y') }}</td>
                                <td class="fw-semibold text-dark">{{ $kas->keterangan }}</td>
                                <td class="small text-muted">{{ $kas->nama_warga ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $kas->jenis === 'masuk' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-3 py-1">
                                        {{ $kas->jenis === 'masuk' ? '+ Masuk' : '- Keluar' }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold {{ $kas->jenis === 'masuk' ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($kas->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada transaksi kas tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side Cards: Quick Modules & Agenda Kegiatan -->
    <div class="col-lg-4">
        <!-- Quick Stats Grid -->
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3">Layanan Terpadu RW 19 (RT 06 & RT 07)</h6>
            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('umkm.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light border text-center h-100">
                            <i class="fa-solid fa-store fs-3 text-info mb-1"></i>
                            <div class="fw-bold text-dark fs-5">{{ $totalUmkm }}</div>
                            <div class="text-muted small">UMKM Warga</div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('koperasi.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light border text-center h-100">
                            <i class="fa-solid fa-hand-holding-dollar fs-3 text-success mb-1"></i>
                            <div class="fw-bold text-dark fs-5">{{ $totalKoperasiAnggota }}</div>
                            <div class="text-muted small">Anggota Koperasi</div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('bank-sampah.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light border text-center h-100">
                            <i class="fa-solid fa-recycle fs-3 text-primary mb-1"></i>
                            <div class="fw-bold text-dark fs-5">{{ number_format($totalBankSampahKg, 1) }} kg</div>
                            <div class="text-muted small">Bank Sampah</div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('posyandu.index') }}" class="text-decoration-none">
                        <div class="p-3 rounded-3 bg-light border text-center h-100">
                            <i class="fa-solid fa-baby fs-3 text-danger mb-1"></i>
                            <div class="fw-bold text-dark fs-5">{{ $totalPosyandu }}</div>
                            <div class="text-muted small">Posyandu Anak</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Agenda Kegiatan RT -->
        <div class="card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Agenda Mendatang</h6>
                <a href="{{ route('kegiatan.index') }}" class="text-success text-decoration-none small fw-bold">Detail</a>
            </div>

            @forelse($kegiatanTerbaru as $k)
                <div class="p-3 rounded-3 border mb-2 bg-light bg-opacity-50">
                    <div class="fw-bold text-dark">{{ $k->judul }}</div>
                    <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                        <i class="fa-regular fa-clock text-secondary"></i>
                        <span>{{ \Carbon\Carbon::parse($k->tanggal_mulai)->format('d M Y') }}</span>
                        @if($k->waktu)
                            <span>({{ $k->waktu }})</span>
                        @endif
                    </div>
                    @if($k->lokasi)
                        <div class="small text-muted mt-1">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $k->lokasi }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text-muted py-3 small">Belum ada agenda kegiatan baru.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('chartKas').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bulanList) !!},
                datasets: [
                    {
                        label: 'Kas Masuk (Rp)',
                        data: {!! json_encode($grafikMasuk) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    },
                    {
                        label: 'Kas Keluar (Rp)',
                        data: {!! json_encode($grafikKeluar) !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
