@extends('layouts.app')

@section('title', 'Migrasi & Database')
@section('page_title', 'Pusat Migrasi Data & Pengelolaan Database')

@section('content')
<div class="row g-4">
    <!-- Feedback Alert -->
    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('terminal_output'))
        <div class="col-12">
            <div class="card-custom p-3 bg-dark text-light border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                    <span class="small font-monospace text-warning"><i class="fa-solid fa-terminal me-2"></i>Output Terminal Artisan:</span>
                    <button class="btn btn-sm btn-outline-light py-0 px-2" onclick="navigator.clipboard.writeText(document.getElementById('artisanOutput').innerText)">Salin</button>
                </div>
                <pre id="artisanOutput" class="small font-monospace mb-0 text-white-50" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto;">{{ session('terminal_output') }}</pre>
            </div>
        </div>
    @endif

    <!-- Database Info Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-4 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Nama Database</span>
                    <h5 class="fw-bold text-primary mb-0 mt-1 font-monospace">{{ $dbName }}</h5>
                    <small class="text-muted">Driver: {{ strtoupper($dbDriver) }}</small>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="fa-solid fa-database fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-4 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Server Host</span>
                    <h5 class="fw-bold text-dark mb-0 mt-1 font-monospace">{{ $dbHost }}</h5>
                    <small class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Terhubung Aktif</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="fa-solid fa-server fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-4 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Tabel</span>
                    <h4 class="fw-bold text-primary mb-0 mt-1">{{ count($tables) }} Tabel</h4>
                    <small class="text-muted">Skema siap & tersinkron</small>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                    <i class="fa-solid fa-table-list fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-4 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Migrasi Terdaftar</span>
                    <h4 class="fw-bold text-success mb-0 mt-1">{{ count($migrations) }} Batch</h4>
                    <small class="text-muted">Semua migrasi telah dieksekusi</small>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Control Panel -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white shadow-sm border">
            <h5 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-sliders text-primary me-2"></i>Panel Kontrol Migrasi & Data Seeder
            </h5>
            <p class="text-muted small mb-4">
                Jalankan migrasi skema tabel dan isi test data awal (seeder) langsung dari antarmuka web tanpa perlu membuka konsol SSH.
            </p>

            <div class="row g-3">
                <!-- Tombol Jalankan Migrasi -->
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100 bg-light d-flex flex-column justify-content-between">
                        <div>
                            <div class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-bolt text-warning me-1"></i> Migrasi Skema Database
                            </div>
                            <p class="text-muted small mb-3">
                                Mengeksekusi seluruh file migrasi pending untuk memperbarui struktur tabel database di server.
                            </p>
                        </div>
                        <form action="{{ route('admin.migrasi.run') }}" method="POST" onsubmit="return confirm('Jalankan migrasi database sekarang?');">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                                <i class="fa-solid fa-play me-1"></i> Jalankan Migrasi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tombol Jalankan Test Data Seeder -->
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100 bg-light d-flex flex-column justify-content-between">
                        <div>
                            <div class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-seedling text-success me-1"></i> Jalankan Test Data Seeder
                            </div>
                            <p class="text-muted small mb-3">
                                Mengisi data contoh/uji coba lengkap ke modul Kegiatan, Posyandu Balita, UMKM Warga, Aset RT, Surat, dan Bank Sampah.
                            </p>
                        </div>
                        <form action="{{ route('admin.migrasi.seed') }}" method="POST" onsubmit="return confirm('Isi test data seeder ke database sekarang? Data yang sudah ada tidak akan terduplikasi.');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
                                <i class="fa-solid fa-seedling me-1"></i> Jalankan Test Data Seeder
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tombol Bersihkan Cache Aplikasi -->
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100 bg-light d-flex flex-column justify-content-between">
                        <div>
                            <div class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-broom text-secondary me-1"></i> Bersihkan Cache Sistem
                            </div>
                            <p class="text-muted small mb-3">
                                Membersihkan config, route, view, dan cache internal aplikasi (menjalankan <code>php artisan optimize:clear</code>).
                            </p>
                        </div>
                        <form action="{{ route('admin.migrasi.cache') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100 rounded-pill py-2">
                                <i class="fa-solid fa-arrows-rotate me-1"></i> Bersihkan Cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables List & Row Counts -->
    <div class="col-lg-7">
        <div class="card-custom p-4 bg-white shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Daftar Tabel & Jumlah Data</h5>
                    <small class="text-muted">Total {{ count($tables) }} tabel aktif pada database {{ $dbName }}</small>
                </div>
                <input type="text" id="searchTable" class="form-control form-control-sm w-auto" placeholder="Cari nama tabel..." onkeyup="filterTables()">
            </div>

            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table table-hover align-middle table-sm" id="tableList">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="ps-3">Nama Tabel</th>
                            <th class="text-end pe-3">Jumlah Baris</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $tbl)
                            <tr>
                                <td class="ps-3 font-monospace fw-semibold text-dark">
                                    <i class="fa-regular fa-folder me-2 text-primary opacity-75"></i>{{ $tbl['name'] }}
                                </td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                        {{ number_format($tbl['rows'], 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($tbl['rows'] > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Terisi</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-2">Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Tidak ada tabel ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Migration History -->
    <div class="col-lg-5">
        <div class="card-custom p-4 bg-white shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Riwayat File Migrasi</h5>
                    <small class="text-muted">Catatan eksekusi migrasi Laravel</small>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table table-hover align-middle table-sm">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="ps-3">File Migrasi</th>
                            <th class="text-center pe-3">Batch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($migrations as $mig)
                            <tr>
                                <td class="ps-3 font-monospace text-truncate" style="max-width: 240px;" title="{{ $mig->migration }}">
                                    <i class="fa-solid fa-code-commit text-success me-2"></i>{{ $mig->migration }}
                                </td>
                                <td class="text-center pe-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2">
                                        Batch #{{ $mig->batch }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">Belum ada data riwayat migrasi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function filterTables() {
    const input = document.getElementById('searchTable');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#tableList tbody tr');

    rows.forEach(row => {
        const text = row.querySelector('td').innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
</script>
@endsection

