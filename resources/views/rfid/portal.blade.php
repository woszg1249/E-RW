@extends('layouts.app')

@section('title', 'Portal IoT & CRUD Pemindai Kartu RFID')
@section('page_title', 'Portal IoT & CRUD Pemindai Kartu RFID')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Kolom Kiri: Status Perangkat & Live Scanner -->
    <div class="col-lg-5">
        <div class="card-custom p-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-microchip text-success me-2"></i>Status Alat ESP32</h6>
                <span id="badgeDeviceStatus" class="badge {{ $deviceOnline ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                    <i class="fa-solid fa-circle me-1" style="font-size: 0.6rem;"></i>
                    <span id="txtDeviceStatus">{{ $deviceOnline ? 'ESP32 ONLINE' : 'STANDBY / OFFLINE' }}</span>
                </span>
            </div>
            <div class="bg-light rounded-3 p-3 small">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Device ID:</span>
                    <span id="txtDeviceId" class="fw-bold font-monospace">{{ $deviceInfo['device_id'] ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">IP Lokal ESP32:</span>
                    <span id="txtDeviceIp" class="fw-bold font-monospace">{{ $deviceInfo['ip'] ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Akun Login Saat Ini:</span>
                    <span class="fw-bold text-success">{{ $user->nama }}</span>
                </div>
            </div>

            <div class="mt-3 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold text-muted">Status e-KTP Anda:</span>
                    @if($user->rfid_uid)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success font-monospace">{{ $user->rfid_uid }}</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Belum Terdaftar</span>
                    @endif
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="toggleAutoPair">
                    <label class="form-check-label small fw-semibold" for="toggleAutoPair">
                        Mode Auto-Daftar ke Akun Saya saat kartu ditempel
                    </label>
                </div>
            </div>
        </div>

        <!-- Live Card Detector -->
        <div class="card-custom p-4 bg-white">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-rss text-primary me-2"></i>Pemindai Kartu Real-Time</h6>
            <div id="liveWaitingBox" class="alert alert-primary border-primary border-opacity-25 text-center py-4 mb-0">
                <div class="spinner-grow text-primary mb-2" role="status"></div>
                <div class="fw-bold text-primary">Menunggu Tap Kartu e-KTP di ESP32...</div>
                <p class="text-muted small mb-0 mt-1">Tempelkan kartu e-KTP ke sensor RC-522. Data muncul otomatis dalam 1–2 detik.</p>
            </div>

            <div id="liveResultBox" class="d-none">
                <div id="liveResultAlert" class="alert rounded-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-uppercase">UID Terdeteksi</span>
                        <span id="liveScanTime" class="badge bg-dark font-monospace">-</span>
                    </div>
                    <div id="liveScanUid" class="fs-4 fw-bold font-monospace mb-2">-</div>
                    <div id="liveScanOwner" class="small fw-semibold">-</div>
                </div>

                <div id="actionUnregistered" class="d-none">
                    <button type="button" id="btnPairSelf" class="btn btn-success w-100 rounded-pill fw-bold mb-2">
                        <i class="fa-solid fa-id-card me-1"></i> Daftarkan ke Akun Saya ({{ $user->nama }})
                    </button>

                    @if($user->isAdmin())
                        <div class="border-top pt-3 mt-2">
                            <label class="form-label small fw-bold text-muted">Atau Daftarkan ke Warga Lain (Admin):</label>
                            <div class="input-group input-group-sm">
                                <select id="selectWargaAdmin" class="form-select">
                                    <option value="">-- Pilih Warga --</option>
                                    @foreach($wargaList as $w)
                                        <option value="{{ $w->id }}">{{ $w->nama }} (RT {{ $w->rt }} - {{ $w->nik }})</option>
                                    @endforeach
                                </select>
                                <button type="button" id="btnPairAdmin" class="btn btn-primary fw-bold">Daftarkan</button>
                            </div>
                        </div>
                    @endif
                </div>

                <div id="liveActionFeedback" class="small mt-2"></div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: CRUD Kartu Terdaftar + Riwayat Tap -->
    <div class="col-lg-7">
        <!-- CRUD Kartu Terdaftar -->
        <div class="card-custom p-4 bg-white mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-address-card text-primary me-2"></i>Data Kartu e-KTP Terdaftar (CRUD)</h6>
                    <small class="text-muted">Tambah, ubah, atau hapus akses kartu RFID/e-KTP Portal Perumahan</small>
                </div>
                <button type="button" class="btn btn-primary-custom btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahKartu">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Kartu
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pemilik</th>
                            <th>UID e-KTP</th>
                            <th>Akses Portal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registeredCards as $card)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $card->nama }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">NIK: {{ $card->nik ?? '-' }} • RT {{ $card->rt ?? '06' }}/RW {{ $card->rw ?? '19' }}</div>
                                </td>
                                <td><span class="badge bg-dark font-monospace fs-6">{{ $card->rfid_uid }}</span></td>
                                <td><span class="badge bg-success bg-opacity-10 text-success border border-success">AKTIF</span></td>
                                <td class="text-end">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1 me-1 btn-edit-kartu"
                                            data-id="{{ $card->id }}"
                                            data-nama="{{ $card->nama }}"
                                            data-uid="{{ $card->rfid_uid }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditKartu">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                    <form action="{{ route('portal-iot.cards.destroy', $card->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus/nonaktifkan kartu e-KTP milik {{ $card->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada kartu e-KTP yang terdaftar. Klik <strong>Tambah Kartu</strong> atau tempelkan kartu ke ESP32.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Log Real-Time -->
        <div class="card-custom p-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i>Riwayat Tap &amp; Portal Perumahan (Real-Time)</h6>
                    <small class="text-muted">Otomatis diperbarui setiap 2 detik dari sensor ESP32</small>
                </div>
                <span class="badge bg-light text-dark border"><i class="fa-solid fa-rotate me-1 fa-spin"></i> Live</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>UID e-KTP</th>
                            <th>Warga</th>
                            <th>Device</th>
                            <th>Akses Portal</th>
                            <th class="text-end">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyRecentScans">
                        @forelse($recentScans as $s)
                            <tr>
                                <td class="text-muted">{{ $s['waktu'] }}</td>
                                <td class="font-monospace fw-bold">{{ $s['rfid_uid'] }}</td>
                                <td>
                                    @if($s['nama'])
                                        <div class="fw-semibold text-dark">{{ $s['nama'] }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $s['rt_rw'] }}</div>
                                    @else
                                        <span class="text-muted fst-italic">Belum terdaftar</span>
                                    @endif
                                </td>
                                <td class="font-monospace">{{ $s['device_id'] }}</td>
                                <td>
                                    @if($s['status'] === 'processed')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">DIIZINKAN</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">DITOLAK</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($user->isAdmin() || ($s['id_user'] && $s['id_user'] == $user->id))
                                        <form action="{{ route('portal-iot.scans.destroy', $s['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus baris log scan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Hapus Log">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat tap kartu e-KTP.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kartu (Create) -->
<div class="modal fade" id="modalTambahKartu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('portal-iot.cards.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle text-success me-2"></i>Tambah / Daftarkan Kartu e-KTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($user->isAdmin())
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pilih Warga Pemilik Kartu</label>
                            <select name="user_id" class="form-select" required>
                                <option value="{{ $user->id }}">{{ $user->nama }} (Akun Saya)</option>
                                @foreach($wargaList as $w)
                                    @if($w->id !== $user->id)
                                        <option value="{{ $w->id }}">{{ $w->nama }} (RT {{ $w->rt }} - {{ $w->nik }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">UID Kartu e-KTP</label>
                        <div class="input-group">
                            <input type="text" name="rfid_uid" id="inpCreateUid" class="form-control font-monospace text-uppercase" placeholder="Contoh: E3A82C10" required>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAmbilUidCreate">
                                <i class="fa-solid fa-wifi me-1"></i> Ambil Scan Terakhir
                            </button>
                        </div>
                        <small class="text-muted">Tempelkan kartu ke ESP32 lalu klik <strong>Ambil Scan Terakhir</strong> atau ketik manual.</small>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Kartu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kartu (Update) -->
<div class="modal fade" id="modalEditKartu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="formEditKartu" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit UID Kartu e-KTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pemilik Kartu</label>
                        <input type="text" id="editNamaWarga" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">UID Kartu e-KTP Baru</label>
                        <div class="input-group">
                            <input type="text" name="rfid_uid" id="inpEditUid" class="form-control font-monospace text-uppercase" required>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAmbilUidEdit">
                                <i class="fa-solid fa-wifi me-1"></i> Ambil Scan Terakhir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let lastHandledTimestamp = null;
    let currentScannedUid = null;
    const isAdmin = {{ $user->isAdmin() ? 'true' : 'false' }};
    const currentUserId = {{ $user->id }};
    const csrfToken = '{{ csrf_token() }}';

    // Handle modal edit population
    document.querySelectorAll('.btn-edit-kartu').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('editNamaWarga').value = this.dataset.nama;
            document.getElementById('inpEditUid').value = this.dataset.uid;
            document.getElementById('formEditKartu').action = '{{ url("/portal-iot/cards") }}/' + id;
        });
    });

    function ambilScanKeInput(targetInputId) {
        fetch('{{ route("api.iot.rfid.latest") }}')
            .then(r => r.json())
            .then(res => {
                if (res && res.status === 'available' && res.data && res.data.rfid_uid) {
                    document.getElementById(targetInputId).value = res.data.rfid_uid;
                } else {
                    alert('Belum ada kartu yang ditap dalam 5 menit terakhir. Silakan tempelkan e-KTP ke ESP32 dulu.');
                }
            })
            .catch(() => alert('Gagal mengambil data scan terakhir.'));
    }

    document.getElementById('btnAmbilUidCreate')?.addEventListener('click', () => ambilScanKeInput('inpCreateUid'));
    document.getElementById('btnAmbilUidEdit')?.addEventListener('click', () => ambilScanKeInput('inpEditUid'));

    function refreshPortalStatus() {
        fetch('{{ route("portal-iot.status") }}')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('badgeDeviceStatus');
                const txtStatus = document.getElementById('txtDeviceStatus');
                if (data.device_online) {
                    badge.className = 'badge bg-success rounded-pill px-3 py-2';
                    txtStatus.textContent = 'ESP32 ONLINE';
                } else {
                    badge.className = 'badge bg-secondary rounded-pill px-3 py-2';
                    txtStatus.textContent = 'STANDBY / OFFLINE';
                }
                if (data.device_info) {
                    document.getElementById('txtDeviceId').textContent = data.device_info.device_id || '-';
                    document.getElementById('txtDeviceIp').textContent = data.device_info.ip || '-';
                }

                if (data.latest_scan && data.latest_scan.rfid_uid) {
                    const scan = data.latest_scan;
                    currentScannedUid = scan.rfid_uid;

                    document.getElementById('liveWaitingBox').classList.add('d-none');
                    document.getElementById('liveResultBox').classList.remove('d-none');
                    document.getElementById('liveScanTime').textContent = scan.scanned_time || '-';
                    document.getElementById('liveScanUid').textContent = scan.rfid_uid;

                    const alertEl = document.getElementById('liveResultAlert');
                    const ownerEl = document.getElementById('liveScanOwner');
                    const actionUnreg = document.getElementById('actionUnregistered');

                    if (scan.registered) {
                        alertEl.className = 'alert alert-success border-success rounded-3 mb-3';
                        ownerEl.innerHTML = '<i class="fa-solid fa-door-open me-1"></i> <strong>Portal Perumahan: DIIZINKAN</strong> — Pemilik: ' + (scan.user_nama || '-') + ' (' + (scan.user_nik || '-') + ')';
                        actionUnreg.classList.add('d-none');
                    } else {
                        alertEl.className = 'alert alert-warning border-warning rounded-3 mb-3';
                        ownerEl.innerHTML = '<i class="fa-solid fa-shield-halved me-1"></i> <strong>Belum Terdaftar</strong> (Akses Portal Ditolak hingga didaftarkan).';
                        actionUnreg.classList.remove('d-none');

                        if (document.getElementById('toggleAutoPair').checked && scan.scanned_at !== lastHandledTimestamp) {
                            lastHandledTimestamp = scan.scanned_at;
                            pairToSelf(scan.rfid_uid);
                        }
                    }
                }

                if (Array.isArray(data.recent_scans)) {
                    const tbody = document.getElementById('tbodyRecentScans');
                    if (data.recent_scans.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat tap kartu e-KTP.</td></tr>';
                    } else {
                        tbody.innerHTML = data.recent_scans.map(s => {
                            const wargaHtml = s.nama
                                ? `<div class="fw-semibold text-dark">${s.nama}</div><div class="text-muted" style="font-size:0.72rem;">${s.rt_rw || ''}</div>`
                                : '<span class="text-muted fst-italic">Belum terdaftar</span>';
                            const badgeHtml = s.status === 'processed'
                                ? '<span class="badge bg-success bg-opacity-10 text-success border border-success">DIIZINKAN</span>'
                                : '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger">DITOLAK</span>';
                            const canDelete = isAdmin || (s.id_user && s.id_user == currentUserId);
                            const delHtml = canDelete
                                ? `<form action="{{ url('/portal-iot/scans') }}/${s.id}" method="POST" class="d-inline" onsubmit="return confirm('Hapus baris log scan ini?')">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Hapus Log"><i class="fa-solid fa-trash-can"></i></button>
                                   </form>`
                                : '<span class="text-muted">-</span>';
                            return `<tr>
                                <td class="text-muted">${s.waktu}</td>
                                <td class="font-monospace fw-bold">${s.rfid_uid}</td>
                                <td>${wargaHtml}</td>
                                <td class="font-monospace">${s.device_id || '-'}</td>
                                <td>${badgeHtml}</td>
                                <td class="text-end">${delHtml}</td>
                            </tr>`;
                        }).join('');
                    }
                }
            })
            .catch(() => {});
    }

    function pairToSelf(uid) {
        const fb = document.getElementById('liveActionFeedback');
        fb.innerHTML = '<span class="text-primary">Mendaftarkan UID ' + uid + ' ke akun Anda...</span>';
        fetch('{{ route("profil.rfid.pair") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ rfid_uid: uid })
        })
        .then(async r => {
            const json = await r.json();
            if (r.ok && json.status === 'success') {
                fb.innerHTML = '<span class="text-success fw-bold">' + json.message + '</span>';
                setTimeout(() => location.reload(), 1000);
            } else {
                fb.innerHTML = '<span class="text-danger fw-bold">' + (json.message || 'Gagal mendaftar.') + '</span>';
            }
        });
    }

    document.getElementById('btnPairSelf')?.addEventListener('click', function() {
        if (currentScannedUid) pairToSelf(currentScannedUid);
    });

    document.getElementById('btnPairAdmin')?.addEventListener('click', function() {
        const userId = document.getElementById('selectWargaAdmin').value;
        const fb = document.getElementById('liveActionFeedback');
        if (!currentScannedUid || !userId) {
            fb.innerHTML = '<span class="text-danger">Pilih warga terlebih dahulu.</span>';
            return;
        }
        fetch('{{ route("portal-iot.pair-warga") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ rfid_uid: currentScannedUid, user_id: userId })
        })
        .then(async r => {
            const json = await r.json();
            if (r.ok && json.status === 'success') {
                fb.innerHTML = '<span class="text-success fw-bold">' + json.message + '</span>';
                setTimeout(() => location.reload(), 900);
            } else {
                fb.innerHTML = '<span class="text-danger fw-bold">' + (json.message || 'Gagal mendaftar.') + '</span>';
            }
        });
    });

    refreshPortalStatus();
    setInterval(refreshPortalStatus, 2000);
});
</script>
@endsection
