@extends('layouts.app')

@section('title', 'Portal IoT & Daftar RFID Real-Time')
@section('page_title', 'Portal IoT & Daftar RFID Real-Time')

@section('content')
<div class="row g-4">
    <!-- Status Perangkat & Live Scanner -->
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

                <!-- Aksi Daftar untuk User Login -->
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

    <!-- Tabel Log Real-Time -->
    <div class="col-lg-7">
        <div class="card-custom p-4 bg-white h-100">
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat tap kartu e-KTP.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let lastHandledTimestamp = null;
    let currentScannedUid = null;

    function refreshPortalStatus() {
        fetch('{{ route("portal-iot.status") }}')
            .then(r => r.json())
            .then(data => {
                // 1. Update status ESP32 Online/Offline
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

                // 2. Update Live Card Detector
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

                        // Auto-daftar jika toggle aktif dan ini scan baru
                        if (document.getElementById('toggleAutoPair').checked && scan.scanned_at !== lastHandledTimestamp) {
                            lastHandledTimestamp = scan.scanned_at;
                            pairToSelf(scan.rfid_uid);
                        }
                    }
                }

                // 3. Update Tabel Riwayat
                if (Array.isArray(data.recent_scans)) {
                    const tbody = document.getElementById('tbodyRecentScans');
                    if (data.recent_scans.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat tap kartu e-KTP.</td></tr>';
                    } else {
                        tbody.innerHTML = data.recent_scans.map(s => {
                            const wargaHtml = s.nama
                                ? `<div class="fw-semibold text-dark">${s.nama}</div><div class="text-muted" style="font-size:0.72rem;">${s.rt_rw || ''}</div>`
                                : '<span class="text-muted fst-italic">Belum terdaftar</span>';
                            const badgeHtml = s.status === 'processed'
                                ? '<span class="badge bg-success bg-opacity-10 text-success border border-success">DIIZINKAN</span>'
                                : '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger">DITOLAK</span>';
                            return `<tr>
                                <td class="text-muted">${s.waktu}</td>
                                <td class="font-monospace fw-bold">${s.rfid_uid}</td>
                                <td>${wargaHtml}</td>
                                <td class="font-monospace">${s.device_id || '-'}</td>
                                <td>${badgeHtml}</td>
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
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ rfid_uid: currentScannedUid, user_id: userId })
        })
        .then(async r => {
            const json = await r.json();
            if (r.ok && json.status === 'success') {
                fb.innerHTML = '<span class="text-success fw-bold">' + json.message + '</span>';
                refreshPortalStatus();
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
