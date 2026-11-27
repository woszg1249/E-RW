@extends('layouts.app')

@section('title', 'Manajemen Bank Digital Warga')
@section('page_title', 'Manajemen Bank Digital Warga RW 19')

@section('content')
<div class="row g-4">
    <!-- Header Summary Stats -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">Pusat Pengelolaan Bank Digital Warga RW 19</h5>
                <p class="text-muted small mb-0">Integrasi dompet digital kas warga untuk pembayaran iuran RT, integrasi bank sampah, dan tap IoT e-KTP.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('portal-iot.index') }}" class="btn btn-outline-dark rounded-pill px-3">
                    <i class="fa-solid fa-wifi me-1"></i> Portal IoT Tap e-KTP
                </a>
                <button class="btn btn-primary-custom rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBukaRekeningBaru">
                    <i class="fa-solid fa-user-plus me-1"></i> Buka Rekening Warga (Scan e-KTP)
                </button>
            </div>
        </div>
    </div>

    <!-- Status Alat ESP32 & Alur Aktivasi Rekening -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0"><i class="fa-solid fa-microchip text-success me-2"></i>Status Alat ESP32</h6>
                        <span id="adminBadgeDeviceStatus" class="badge {{ ($deviceOnline ?? false) ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                            <i class="fa-solid fa-circle me-1" style="font-size: 0.6rem;"></i>
                            <span id="adminTxtDeviceStatus">{{ ($deviceOnline ?? false) ? 'ESP32 ONLINE' : 'STANDBY / OFFLINE' }}</span>
                        </span>
                    </div>
                    <div class="bg-light rounded-3 p-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Device ID:</span>
                            <span id="adminTxtDeviceId" class="fw-bold font-monospace">{{ $deviceInfo['device_id'] ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">IP Lokal ESP32:</span>
                            <span id="adminTxtDeviceIp" class="fw-bold font-monospace">{{ $deviceInfo['ip'] ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Petugas Login:</span>
                            <span class="fw-bold text-success">{{ auth()->user()->nama }} ({{ auth()->user()->role }})</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card-custom p-3 h-100 d-flex flex-column justify-content-center">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-info text-primary me-2"></i>Prosedur Buka Rekening Bank Warga (Wajib Scan e-KTP)</h6>
                    <div class="row g-2 small">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-2 h-100">
                                <span class="badge bg-primary rounded-pill mb-1">1</span>
                                <div class="fw-bold">Cek ESP32 Online</div>
                                <div class="text-muted" style="font-size: 0.76rem;">Pastikan status alat di samping menyala hijau <strong>ESP32 ONLINE</strong>.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-2 h-100">
                                <span class="badge bg-primary rounded-pill mb-1">2</span>
                                <div class="fw-bold">Pilih Nama Warga</div>
                                <div class="text-muted" style="font-size: 0.76rem;">Klik tombol <strong>Buka Rekening Warga (Scan e-KTP)</strong> dan pilih warga.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-2 h-100">
                                <span class="badge bg-primary rounded-pill mb-1">3</span>
                                <div class="fw-bold">Tempel e-KTP Warga</div>
                                <div class="text-muted" style="font-size: 0.76rem;">Tap fisik e-KTP warga ke sensor RC-522 hingga UID terisi, lalu simpan.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Kartu Metrik -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-4">
                    <i class="fa-solid fa-vault fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Total Simpanan Warga</span>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalSaldoSeluruhWarga, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4">
                    <i class="fa-solid fa-users-viewfinder fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Nasabah Terdaftar</span>
                    <h5 class="fw-bold mb-0 text-dark">{{ $totalNasabah }} Warga</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-4">
                    <i class="fa-solid fa-recycle fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Total Dana Bank Sampah</span>
                    <h5 class="fw-bold mb-0 text-info">Rp {{ number_format($totalPencairanSampah, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4">
                    <i class="fa-solid fa-chart-line fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Mutasi Hari Ini</span>
                    <h5 class="fw-bold mb-0 text-dark">{{ $totalTrxHariIni }} Transaksi</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Rekening Warga -->
    <div class="col-12">
        <div class="card-custom p-4">
            <!-- Filter & Pencarian -->
            <form action="{{ route('bank-warga.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama warga / nomor rekening / NIK..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="rt" class="form-select form-select-sm">
                        <option value="">Semua Wilayah RT (RW 19)</option>
                        <option value="06" {{ request('rt') == '06' ? 'selected' : '' }}>RT 06 (Ketua: Dimas)</option>
                        <option value="07" {{ request('rt') == '07' ? 'selected' : '' }}>RT 07 (Ketua: Bambang)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter Data</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('bank-warga.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>No. Rekening</th>
                            <th>Nama Nasabah / Warga</th>
                            <th>Wilayah RT</th>
                            <th>NIK & No. KK</th>
                            <th>Metode Daftar</th>
                            <th class="text-end">Saldo Saat Ini</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekeningList as $index => $r)
                            <tr>
                                <td class="small">{{ $rekeningList->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold text-dark font-monospace">{{ $r->nomor_rekening }}</span>
                                    @if($r->rfid_uid)
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-id-card text-success"></i> {{ $r->rfid_uid }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $r->user->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $r->user->no_hp ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        RT {{ $r->user->rt ?? '06' }} / RW 19
                                    </span>
                                </td>
                                <td class="small">
                                    <div>NIK: {{ $r->nik ?? ($r->user->nik ?? '-') }}</div>
                                    <div class="text-muted">KK: {{ $r->no_kk ?? ($r->user->no_kk ?? '-') }}</div>
                                </td>
                                <td>
                                    @if($r->metode_daftar === 'iot_tap_ktp')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-nfc-directional me-1"></i> Tap IoT e-KTP
                                        </span>
                                    @elseif($r->metode_daftar === 'otomatis_bank_sampah')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-recycle me-1"></i> Dari Bank Sampah
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-user-check me-1"></i> Akun Warga
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success fs-6">
                                    Rp {{ number_format($r->saldo, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($r->status === 'aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">{{ ucfirst($r->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" data-bs-toggle="modal" data-bs-target="#modalAdminTopUp{{ $r->id }}">
                                        <i class="fa-solid fa-plus me-1"></i> Setor / Top Up
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Belum ada data rekening Bank Digital Warga terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar (10 baris per tab) -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <div class="small text-muted">
                    Menampilkan <strong>{{ $rekeningList->firstItem() ?? 0 }}</strong> – <strong>{{ $rekeningList->lastItem() ?? 0 }}</strong> dari total <strong>{{ $rekeningList->total() }}</strong> rekening warga (10 baris per halaman).
                </div>
                <div>
                    {{ $rekeningList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buka Rekening Warga (Wajib Tap e-KTP Fisik di ESP32) -->
<div class="modal fade" id="modalBukaRekeningBaru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Aktivasi Rekening Bank Warga (Tap e-KTP)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-warga.daftar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Pilih warga RW 19 lalu tempelkan fisik e-KTP ke alat ESP32. Rekening tidak bisa dibuat otomatis tanpa tap e-KTP.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Warga</label>
                        <select name="id_user" id="selectWargaBukaRekening" class="form-select" required>
                            <option value="">-- Pilih Warga (Belum Berrekening) --</option>
                            @foreach($daftarWargaBelumPunya as $wb)
                                <option value="{{ $wb->id }}" data-uid="{{ $wb->rfid_uid ?? '' }}">{{ $wb->nama }} (NIK: {{ $wb->nik ?? '-' }} • RT {{ $wb->rt ?? '06' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">UID Kartu e-KTP (Wajib Tap di ESP32)</label>
                        <input type="text" name="rfid_uid" id="inpAdminBukaRfidUid" class="form-control font-monospace text-uppercase bg-light" placeholder="Tempelkan e-KTP ke sensor ESP32..." readonly required>
                    </div>
                    <div id="adminTapListeningBox" class="alert alert-primary border-primary border-opacity-25 rounded-3 text-center py-2 mb-0 small">
                        <div class="spinner-grow spinner-grow-sm text-primary me-1" role="status"></div>
                        <strong>Sedang Mendengarkan Sensor IoT ESP32...</strong>
                        <div class="text-muted" style="font-size: 0.78rem;">Tempelkan fisik e-KTP warga ke sensor RC-522 agar UID terisi otomatis.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitBukaRekening" class="btn btn-primary-custom rounded-pill px-4" disabled>
                        <i class="fa-solid fa-id-card me-1"></i> Buka Rekening
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Admin Bantu Top Up untuk Warga -->
@foreach($rekeningList as $rekItem)
<div class="modal fade" id="modalAdminTopUp{{ $rekItem->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Setor Tunai / Top-Up Saldo Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-warga.topup') }}" method="POST">
                @csrf
                <input type="hidden" name="id_user" value="{{ $rekItem->id_user }}">
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="small text-muted">Nama Nasabah:</div>
                        <div class="fw-bold">{{ $rekItem->user->nama ?? '-' }} (RT {{ $rekItem->user->rt ?? '06' }})</div>
                        <div class="small text-muted mt-1">No Rekening: <span class="font-monospace fw-bold">{{ $rekItem->nomor_rekening }}</span></div>
                        <div class="small text-muted">Saldo Saat Ini: <span class="fw-bold text-success">Rp {{ number_format($rekItem->saldo, 0, ',', '.') }}</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nominal Setoran (Rp)</label>
                        <input type="number" name="jumlah" class="form-control" value="25000" min="5000" step="5000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Metode Setoran</label>
                        <select name="metode" class="form-select">
                            <option value="Setoran Tunai di Pos RT">Setor Tunai di Pos RT / Kantor RW</option>
                            <option value="Transfer Bank Pengurus">Transfer Bank ke Pengurus RT</option>
                            <option value="QRIS Pos RT">QRIS Pos RT</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Proses Setoran Saldo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalBukaRekeningBaru');
    const selectWarga = document.getElementById('selectWargaBukaRekening');
    const inpUid = document.getElementById('inpAdminBukaRfidUid');
    const btnSubmit = document.getElementById('btnSubmitBukaRekening');
    const listenBox = document.getElementById('adminTapListeningBox');
    let pollTimer = null;

    function refreshAdminDeviceStatus() {
        fetch('{{ route("portal-iot.status") }}')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('adminBadgeDeviceStatus');
                const txtStatus = document.getElementById('adminTxtDeviceStatus');
                if (badge && txtStatus) {
                    if (data.device_online) {
                        badge.className = 'badge bg-success rounded-pill px-3 py-2';
                        txtStatus.textContent = 'ESP32 ONLINE';
                    } else {
                        badge.className = 'badge bg-secondary rounded-pill px-3 py-2';
                        txtStatus.textContent = 'STANDBY / OFFLINE';
                    }
                }
                if (data.device_info) {
                    const idEl = document.getElementById('adminTxtDeviceId');
                    const ipEl = document.getElementById('adminTxtDeviceIp');
                    if (idEl) idEl.textContent = data.device_info.device_id || '-';
                    if (ipEl) ipEl.textContent = data.device_info.ip || '-';
                }
            })
            .catch(() => {});
    }

    refreshAdminDeviceStatus();
    setInterval(refreshAdminDeviceStatus, 2500);

    function updateSubmitState() {
        const hasUid = inpUid && inpUid.value.trim().length > 0;
        const hasWarga = selectWarga && selectWarga.value.length > 0;
        if (btnSubmit) btnSubmit.disabled = !(hasUid && hasWarga);
    }

    selectWarga?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const existingUid = opt ? (opt.getAttribute('data-uid') || '') : '';
        if (existingUid) {
            inpUid.value = existingUid;
            listenBox.className = 'alert alert-success border-success rounded-3 text-center py-2 mb-0 small';
            listenBox.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> e-KTP warga ini sudah terdaftar (UID: <b>' + existingUid + '</b>). Siap aktivasi rekening.';
        } else {
            inpUid.value = '';
            listenBox.className = 'alert alert-primary border-primary border-opacity-25 rounded-3 text-center py-2 mb-0 small';
            listenBox.innerHTML = '<div class="spinner-grow spinner-grow-sm text-primary me-1" role="status"></div><strong>Sedang Mendengarkan Sensor IoT ESP32...</strong><div class="text-muted" style="font-size: 0.78rem;">Tempelkan fisik e-KTP warga ke sensor RC-522 agar UID terisi otomatis.</div>';
        }
        updateSubmitState();
    });

    modalEl?.addEventListener('shown.bs.modal', function() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(function() {
            fetch('{{ route("api.iot.rfid.latest") }}')
                .then(r => r.json())
                .then(data => {
                    if (data && data.status === 'available' && data.data && data.data.rfid_uid) {
                        inpUid.value = data.data.rfid_uid;
                        listenBox.className = 'alert alert-success border-success rounded-3 text-center py-2 mb-0 small';
                        listenBox.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> <strong>Kartu e-KTP Terdeteksi!</strong> UID: <b class="font-monospace">' + data.data.rfid_uid + '</b>';
                        updateSubmitState();
                    }
                })
                .catch(() => {});
        }, 2000);
    });

    modalEl?.addEventListener('hidden.bs.modal', function() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    });
});
</script>
@endsection
