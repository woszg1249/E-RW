@extends('layouts.app')

@section('title', 'Koperasi Warga & IoT')
@section('page_title', 'Koperasi Simpan Pinjam RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Saldo Koperasi</span>
            <h4 class="fw-bold text-primary mb-0 mt-1">Rp {{ number_format($saldoKoperasi, 0, ',', '.') }}</h4>
            <small class="text-muted">Total modal simpanan</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Anggota Aktif</span>
            <h4 class="fw-bold text-dark mb-0 mt-1">{{ $totalAnggota }} Warga</h4>
            <small class="text-muted">Terdaftar di Koperasi RT</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Pinjaman Beredar</span>
            <h4 class="fw-bold text-warning mb-0 mt-1">Rp {{ number_format($totalPinjamanDisetujui, 0, ',', '.') }}</h4>
            <small class="text-muted">Modal bergulir warga</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Cicilan Masuk</span>
            <h4 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format($totalCicilanMasuk, 0, ',', '.') }}</h4>
            <small class="text-muted">Pengembalian angsuran</small>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white">
            <ul class="nav nav-pills mb-4 border-bottom pb-3" id="koperasiTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active rounded-pill px-4" id="tab-saya-btn" data-bs-toggle="pill" data-bs-target="#tab-saya" type="button">
                        <i class="fa-solid fa-user-tie me-1"></i> Koperasi Saya
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4" id="tab-pinjam-btn" data-bs-toggle="pill" data-bs-target="#tab-pinjam" type="button">
                        <i class="fa-solid fa-calculator me-1"></i> Simulasi & Pengajuan Pinjaman
                    </button>
                </li>
                @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4" id="tab-admin-pinjaman-btn" data-bs-toggle="pill" data-bs-target="#tab-admin-pinjaman" type="button">
                            <i class="fa-solid fa-list-check me-1"></i> Persetujuan Pinjaman
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4" id="tab-admin-simpanan-btn" data-bs-toggle="pill" data-bs-target="#tab-admin-simpanan" type="button">
                            <i class="fa-solid fa-money-bill-transfer me-1"></i> Catat Simpanan / Cicilan
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4" id="tab-iot-btn" data-bs-toggle="pill" data-bs-target="#tab-iot" type="button">
                            <i class="fa-solid fa-microchip me-1"></i> IoT RFID Scanner
                        </button>
                    </li>
                @endif
            </ul>

            <div class="tab-content" id="koperasiTabContent">
                <!-- TAB 1: KOPERASI SAYA -->
                <div class="tab-pane fade show active" id="tab-saya" role="tabpanel">
                    @if(!$anggotaSaya)
                        <div class="p-5 text-center bg-light rounded-4 border">
                            <i class="fa-solid fa-hand-holding-dollar fs-1 text-success mb-3"></i>
                            <h4 class="fw-bold">Anda Belum Terdaftar sebagai Anggota Koperasi</h4>
                            <p class="text-muted small mx-auto" style="max-width: 500px;">
                                Koperasi RT 06 memfasilitasi tabungan simpanan warga dan pinjaman modal usaha ringan dengan bunga rendah serta transparan.
                            </p>
                            <form action="{{ route('koperasi.anggota') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary-custom rounded-pill px-4 py-2">
                                    <i class="fa-solid fa-user-plus me-1"></i> Gabung Menjadi Anggota Sekarang
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-4 border h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold mb-0 text-dark">Saldo Simpanan Saya</h6>
                                        <span class="badge bg-success rounded-pill px-3 py-1">Anggota Aktif</span>
                                    </div>
                                    <h3 class="fw-bold text-success mb-3">Rp {{ number_format($saldoSimpananSaya, 0, ',', '.') }}</h3>
                                    <small class="text-muted">Terdaftar sejak: {{ \Carbon\Carbon::parse($anggotaSaya->tanggal_gabung)->format('d F Y') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-4 border h-100">
                                    <h6 class="fw-bold mb-3 text-dark">Ringkasan Pinjaman Saya</h6>
                                    @php $pinjamanAktif = $pinjamanSaya->firstWhere('status', 'disetujui'); @endphp
                                    @if($pinjamanAktif)
                                        <div class="row g-2 small">
                                            <div class="col-6 text-muted">Pokok Pinjaman:</div>
                                            <div class="col-6 fw-bold">Rp {{ number_format($pinjamanAktif->jumlah, 0, ',', '.') }}</div>
                                            <div class="col-6 text-muted">Tenor:</div>
                                            <div class="col-6 fw-bold">{{ $pinjamanAktif->tenor }} Bulan (Bunga {{ $pinjamanAktif->bunga }}%)</div>
                                            <div class="col-6 text-muted">Status:</div>
                                            <div class="col-6">
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small">
                                                    <i class="fa-solid fa-bolt me-1"></i> Aktif (IoT e-KTP)
                                                </span>
                                            </div>
                                            <div class="col-6 text-muted">Sisa Pinjaman:</div>
                                            <div class="col-6 fw-bold text-danger fs-6">Rp {{ number_format($pinjamanAktif->sisa_pinjaman, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top">
                                            <a href="{{ route('tagihan.index') }}" class="btn btn-sm btn-outline-success rounded-pill w-100">
                                                <i class="fa-solid fa-receipt me-1"></i> Cek & Bayar Cicilan di Tagihan Iuran
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-muted small mb-0">Anda saat ini tidak memiliki pinjaman aktif.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Riwayat Simpanan -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-dark mb-3">Mutasi Simpanan Anda</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-secondary">
                                                <th>Tanggal</th>
                                                <th>Jenis Transaksi</th>
                                                <th>Nominal</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($simpananSaya as $s)
                                                <tr>
                                                    <td class="small">{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge {{ $s->jenis === 'setoran' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-1">
                                                            {{ ucfirst($s->jenis) }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold {{ $s->jenis === 'setoran' ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                                                    </td>
                                                    <td class="small text-muted">{{ $s->catatan ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat transaksi simpanan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- TAB 2: SIMULASI & PENGAJUAN PINJAMAN -->
                <div class="tab-pane fade" id="tab-pinjam" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold text-dark mb-0">Pinjaman Modal Mandiri Warga (IoT e-KTP)</h6>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 small fw-bold">
                                    <i class="fa-solid fa-bolt me-1"></i> Instan Otomatis (Tanpa Tunggu RT/RW)
                                </span>
                            </div>

                            <div class="alert alert-success bg-opacity-10 border border-success border-opacity-25 rounded-4 p-3 small mb-3">
                                <div class="d-flex gap-2">
                                    <i class="fa-solid fa-microchip text-success fs-5 mt-1"></i>
                                    <div>
                                        <strong class="text-success">Layanan Pinjaman Otomatis IoT:</strong>
                                        <p class="mb-0 text-dark">
                                            Warga tidak perlu lagi menunggu konfirmasi persetujuan dari RT/RW. Cukup tap atau verifikasi <strong>e-KTP</strong> Anda, pinjaman akan <strong>otomatis langsung disetujui</strong> dan nominal angsuran per bulan langsung masuk ke <strong>Tagihan Iuran Warga</strong> sebagai pengingat tagihan.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('koperasi.pinjaman') }}" method="POST">
                                @csrf
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="small">
                                            <span class="text-muted d-block">Identitas e-KTP Terhubung:</span>
                                            <strong class="text-dark">{{ auth()->user()->nama }} (NIK: {{ auth()->user()->nik ?? '-' }})</strong>
                                        </div>
                                        <span class="badge bg-primary rounded-pill px-3 py-1 small">
                                            <i class="fa-solid fa-id-card me-1"></i> e-KTP Siap Tap
                                        </span>
                                    </div>
                                    <input type="hidden" name="rfid_uid" value="{{ auth()->user()->rfid_uid ?: ('E28011700' . substr(strval(auth()->user()->nik ?: '1234567890'), -6)) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Jumlah Pinjaman (Rp)</label>
                                    <input type="number" name="jumlah" id="simulasiJumlah" class="form-control form-control-lg fw-bold text-primary" value="1000000" min="100000" step="100000" oninput="updateSimulasi()" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Jangka Waktu (Tenor)</label>
                                    <select name="tenor" id="simulasiTenor" class="form-select" onchange="updateSimulasi()" required>
                                        <option value="3">3 Bulan</option>
                                        <option value="6">6 Bulan</option>
                                        <option value="12" selected>12 Bulan (1 Tahun)</option>
                                        <option value="24">24 Bulan (2 Tahun)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Keperluan / Keterangan Pinjaman</label>
                                    <textarea name="keterangan" rows="2" class="form-control" placeholder="Contoh: Tambahan modal usaha warung sembako" required>Pinjaman modal usaha mandiri</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary-custom rounded-pill px-4 py-2 w-100 fw-bold">
                                    <i class="fa-solid fa-wifi me-2"></i> Tap e-KTP & Pinjam Otomatis (Instan Disetujui)
                                </button>
                            </form>
                        </div>

                        <div class="col-lg-6">
                            <div class="p-4 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-3">Kalkulator Simulasi Angsuran</h6>
                                    <div class="row g-3 small">
                                        <div class="col-6 text-muted">Pokok Pinjaman:</div>
                                        <div class="col-6 fw-bold text-dark" id="txtPokok">Rp 1.000.000</div>
                                        <div class="col-6 text-muted">Bunga Koperasi:</div>
                                        <div class="col-6 fw-bold text-primary">1.5% Flat / Bulan</div>
                                        <div class="col-6 text-muted">Total Pengembalian:</div>
                                        <div class="col-6 fw-bold text-dark" id="txtTotal">Rp 1.180.000</div>
                                        <div class="col-12"><hr class="my-1"></div>
                                        <div class="col-6 fw-bold fs-6">Cicilan per Bulan:</div>
                                        <div class="col-6 fw-bold text-success fs-5" id="txtCicilan">Rp 98.333</div>
                                    </div>
                                </div>

                                <div class="alert alert-warning border-0 rounded-3 p-3 small mt-4 mb-0">
                                    <i class="fa-solid fa-bell text-warning me-2"></i>
                                    <strong>Pengingat Tagihan Otomatis:</strong>
                                    <p class="mb-0 mt-1 text-muted">
                                        Setelah tombol di atas ditekan, cicilan sebesar <strong class="text-dark" id="txtCicilanNotif">Rp 98.333</strong> akan otomatis tercatat di menu <strong>Tagihan Iuran Warga</strong> sebagai kewajiban angsuran bulanan yang dapat dibayar secara langsung.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: ADMIN PERSETUJUAN PINJAMAN -->
                @if(auth()->user()->isAdmin())
                <div class="tab-pane fade" id="tab-admin-pinjaman" role="tabpanel">
                    <h6 class="fw-bold text-dark mb-3">Daftar Pengajuan Pinjaman Warga</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-secondary">
                                    <th>Peminjam</th>
                                    <th>Nominal</th>
                                    <th>Tenor</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi Keputusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($semuaPinjaman as $p)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $p->user->nama ?? '-' }}</div>
                                            <small class="text-muted">NIK: {{ $p->user->nik ?? '-' }}</small>
                                        </td>
                                        <td class="fw-bold text-dark">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ $p->tenor }} Bulan</td>
                                        <td class="small">{{ $p->keterangan }}</td>
                                        <td>
                                            <span class="badge {{ $p->status === 'disetujui' ? 'bg-success' : ($p->status === 'ditolak' ? 'bg-danger' : 'bg-warning') }} rounded-pill px-3 py-1">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($p->status === 'pending')
                                                <div class="d-flex justify-content-center gap-1">
                                                    <form action="{{ route('koperasi.pinjaman.status', $p->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="disetujui">
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Setujui</button>
                                                    </form>
                                                    <form action="{{ route('koperasi.pinjaman.status', $p->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="ditolak">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Tolak</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="small text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Tidak ada pengajuan pinjaman.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 4: ADMIN SIMPANAN & CICILAN -->
                <div class="tab-pane fade" id="tab-admin-simpanan" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-4 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark mb-3">Catat Simpanan / Penarikan Kas</h6>
                                <form action="{{ route('koperasi.simpanan') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Pilih Warga</label>
                                        <select name="id_user" class="form-select" required>
                                            <option value="">-- Pilih Warga --</option>
                                            @foreach($daftarWarga as $w)
                                                <option value="{{ $w->id }}">{{ $w->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Jenis Transaksi</label>
                                        <select name="jenis" class="form-select" required>
                                            <option value="setoran">Setoran Simpanan (+)</option>
                                            <option value="penarikan">Penarikan Saldo (-)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Nominal (Rp)</label>
                                        <input type="number" name="jumlah" class="form-control" placeholder="0" min="5000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Catatan</label>
                                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Simpanan Wajib Bulanan">
                                    </div>
                                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Transaksi</button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-4 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark mb-3">Catat Pembayaran Cicilan Pinjaman</h6>
                                <form action="{{ route('koperasi.cicilan') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Pilih Pinjaman Aktif</label>
                                        <select name="id_pinjaman" class="form-select" required>
                                            <option value="">-- Pilih Pinjaman Aktif --</option>
                                            @foreach($semuaPinjaman->where('status', 'disetujui') as $p)
                                                <option value="{{ $p->id }}">{{ $p->user->nama ?? '-' }} - Sisa: Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Nominal Angsuran (Rp)</label>
                                        <input type="number" name="jumlah" class="form-control" placeholder="0" min="1000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Catatan Pembayaran</label>
                                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Angsuran Bulan ke-2">
                                    </div>
                                    <button type="submit" class="btn btn-success rounded-pill px-4">Catat Angsuran</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: IOT RFID SCANNER -->
                <div class="tab-pane fade" id="tab-iot" role="tabpanel">
                    <div class="p-4 bg-light rounded-4 border mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-microchip text-primary me-2"></i> Integrasi IoT Reader RFID</h6>
                            <span class="badge bg-success rounded-pill px-3 py-1">API Endpoint Active</span>
                        </div>
                        <p class="text-muted small mb-3">
                            Endpoint <code>POST /api/iot/koperasi/scan</code> menerima payload JSON dari modul ESP32/RC522 untuk identifikasi kartu warga tap otomatis.
                        </p>
                    </div>

                    <h6 class="fw-bold text-dark mb-3">Riwayat Log Scan Kartu Terkini</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-secondary">
                                    <th>Waktu Tap</th>
                                    <th>ID Perangkat</th>
                                    <th>RFID UID</th>
                                    <th>Nama Warga Terdeteksi</th>
                                    <th>Status Identifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($iotScans as $scan)
                                    <tr>
                                        <td class="small">{{ \Carbon\Carbon::parse($scan->scanned_at)->format('d/m/Y H:i:s') }}</td>
                                        <td class="small fw-bold">{{ $scan->device_id }}</td>
                                        <td class="font-monospace small">{{ $scan->rfid_uid ?? '-' }}</td>
                                        <td class="fw-bold text-dark">{{ $scan->user->nama ?? 'Kartu Tidak Terdaftar' }}</td>
                                        <td>
                                            <span class="badge {{ $scan->status === 'processed' ? 'bg-success' : 'bg-warning' }} rounded-pill px-3 py-1">
                                                {{ $scan->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Belum ada riwayat tap RFID IoT.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function updateSimulasi() {
        const jumlah = parseFloat(document.getElementById('simulasiJumlah').value) || 0;
        const tenor = parseInt(document.getElementById('simulasiTenor').value) || 12;
        const bunga = 1.5; // 1.5% per bulan
        const totalBunga = jumlah * (bunga / 100) * tenor;
        const total = jumlah + totalBunga;
        const cicilan = total / tenor;

        document.getElementById('txtPokok').innerText = 'Rp ' + jumlah.toLocaleString('id-ID');
        document.getElementById('txtTotal').innerText = 'Rp ' + Math.round(total).toLocaleString('id-ID');
        document.getElementById('txtCicilan').innerText = 'Rp ' + Math.round(cicilan).toLocaleString('id-ID');
        const notifElem = document.getElementById('txtCicilanNotif');
        if (notifElem) {
            notifElem.innerText = 'Rp ' + Math.round(cicilan).toLocaleString('id-ID');
        }
    }
</script>
@endsection
