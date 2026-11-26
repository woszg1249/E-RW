@extends('layouts.app')

@section('title', 'Aktivasi Rekening Bank Warga')
@section('page_title', 'Bank Digital Warga RW 19')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-8 col-md-10 col-12">
        <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
            <!-- Header Kartu Terkunci -->
            <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-id-card text-warning fs-1"></i>
                </div>
                <h4 class="fw-bold mb-1">Rekening Bank Digital Belum Aktif</h4>
                <p class="text-white-50 small mb-0">Wajib verifikasi fisik kartu e-KTP melalui Sensor IoT Reader (ESP32) di Pos RT</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <!-- Info Status Akun Warga -->
                <div class="bg-light border rounded-3 p-3 mb-4">
                    <div class="row g-2 small">
                        <div class="col-4 text-muted">Nama Warga:</div>
                        <div class="col-8 fw-bold text-dark">{{ $user->nama }}</div>
                        <div class="col-4 text-muted">NIK KTP:</div>
                        <div class="col-8 fw-semibold font-monospace">{{ $user->nik }}</div>
                        <div class="col-4 text-muted">Wilayah:</div>
                        <div class="col-8">RT {{ $user->rt }} / RW {{ $user->rw }}</div>
                        <div class="col-4 text-muted">Status e-KTP:</div>
                        <div class="col-8">
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning px-2 py-1">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum Didaftarkan ke Sensor IoT
                            </span>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-circle-question text-primary me-2"></i>Bagaimana Cara Mengaktifkan Rekening?
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-4 col-12">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 28px; height: 28px; line-height: 22px;">1</div>
                            <h6 class="fw-bold small mb-1">Bawa Fisik e-KTP</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Bawa kartu e-KTP fisik Anda ke alat pembaca IoT (ESP32) di Pos RT.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 28px; height: 28px; line-height: 22px;">2</div>
                            <h6 class="fw-bold small mb-1">Tempelkan (Tap)</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Dekatkan kartu ke sensor RC-522. Mesin membaca kode chip UID unik e-KTP.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 28px; height: 28px; line-height: 22px;">3</div>
                            <h6 class="fw-bold small mb-1">Rekening Otomatis Terbit</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Sistem membuatkan Nomor Rekening Bank Warga yang terkunci pada kartu e-KTP Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Detektor Real-Time -->
                <div id="boxListening" class="alert alert-primary border-primary border-opacity-25 rounded-3 text-center py-3 mb-0">
                    <div class="spinner-grow spinner-grow-sm text-primary mb-2" role="status"></div>
                    <div class="fw-bold small text-primary mb-1">Sedang Mendengarkan Sensor IoT ESP32 Secara Real-Time...</div>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">
                        Begitu kartu fisik e-KTP Anda ditempelkan ke sensor ESP32, halaman ini akan <strong>otomatis mendeteksi</strong> untuk mendaftarkan kartu &amp; membuka buku tabungan digital Anda.
                    </p>
                </div>

                <!-- Box Kartu Terdeteksi Siap Daftarkan -->
                <div id="boxDetectedCard" class="alert alert-success border-success rounded-3 text-center py-3 mt-3 mb-0 d-none">
                    <div class="fw-bold text-success mb-1">
                        <i class="fa-solid fa-circle-check me-1"></i> Kartu e-KTP Terdeteksi!
                    </div>
                    <div class="small mb-2">
                        UID Kartu: <span id="detectedUidBadge" class="badge bg-dark font-monospace fs-6"></span>
                    </div>
                    <button type="button" id="btnQuickPair" class="btn btn-success rounded-pill px-4 btn-sm fw-bold">
                        <i class="fa-solid fa-link me-1"></i> Daftarkan e-KTP ke Akun {{ $user->nama }} &amp; Aktifkan Rekening
                    </button>
                    <div id="pairFeedback" class="small mt-2"></div>
                </div>
            </div>

            <div class="card-footer bg-white border-top p-3 text-center d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Ke Dashboard
                </a>
                <a href="{{ route('portal-iot.index') }}" class="btn btn-success rounded-pill px-4 btn-sm">
                    <i class="fa-solid fa-wifi me-1"></i> Buka Portal IoT Real-Time
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4 btn-sm" onclick="location.reload()">
                    <i class="fa-solid fa-rotate-right me-1"></i> Cek Status Ulang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let detectedUid = null;

    // Polling setiap 2 detik untuk mengecek apakah e-KTP warga ditempelkan ke ESP32
    setInterval(function() {
        fetch('{{ route("api.iot.rfid.latest") }}')
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'available' && data.data) {
                    if (data.data.user_id == {{ $user->id }}) {
                        location.reload();
                        return;
                    }
                    if (!data.data.registered && data.data.rfid_uid) {
                        detectedUid = data.data.rfid_uid;
                        document.getElementById('detectedUidBadge').textContent = detectedUid;
                        document.getElementById('boxDetectedCard').classList.remove('d-none');
                    }
                }
            })
            .catch(() => {});
    }, 2000);

    document.getElementById('btnQuickPair')?.addEventListener('click', function() {
        if (!detectedUid) return;
        const btn = this;
        const fb = document.getElementById('pairFeedback');
        btn.disabled = true;
        fb.textContent = 'Mendaftarkan kartu ke akun Anda...';

        fetch('{{ route("profil.rfid.pair") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ rfid_uid: detectedUid })
        })
        .then(async res => {
            const json = await res.json();
            if (res.ok && json.status === 'success') {
                fb.innerHTML = '<span class="text-success fw-bold">' + json.message + '</span>';
                setTimeout(() => location.reload(), 1000);
            } else {
                fb.innerHTML = '<span class="text-danger fw-bold">' + (json.message || 'Gagal mendaftarkan kartu.') + '</span>';
                btn.disabled = false;
            }
        })
        .catch(() => {
            fb.innerHTML = '<span class="text-danger">Gagal terhubung ke server.</span>';
            btn.disabled = false;
        });
    });
</script>
@endsection

