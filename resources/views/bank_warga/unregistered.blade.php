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
                            <h6 class="fw-bold small mb-1">Datang ke Pos RT / RW</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Datang menemui Ketua RT / RW atau Admin dengan membawa fisik kartu e-KTP Anda.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 28px; height: 28px; line-height: 22px;">2</div>
                            <h6 class="fw-bold small mb-1">Scan RFID oleh RT / Admin</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Petugas RT/RW menempelkan e-KTP Anda ke alat ESP32 untuk aktivasi rekening.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="border rounded-3 p-3 text-center h-100">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 28px; height: 28px; line-height: 22px;">3</div>
                            <h6 class="fw-bold small mb-1">Rekening &amp; Saldo Aktif</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Nomor rekening &amp; saldo Bank Digital Warga langsung tampil otomatis di akun Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Detektor Real-Time -->
                <div class="alert alert-primary border-primary border-opacity-25 rounded-3 text-center py-3 mb-0">
                    <div class="spinner-grow spinner-grow-sm text-primary mb-2" role="status"></div>
                    <div class="fw-bold small text-primary mb-1">Menunggu Aktivasi Rekening oleh Petugas RT / RW / Admin...</div>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">
                        Begitu e-KTP Anda selesai di-scan dan didaftarkan oleh Ketua RT / Admin, halaman ini akan <strong>otomatis terbuka</strong> menampilkan nomor rekening dan saldo Anda.
                    </p>
                </div>
            </div>

            <div class="card-footer bg-white border-top p-3 text-center d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Ke Dashboard
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
    // Polling setiap 2 detik untuk otomatis membuka buku tabungan begitu RT/RW/Admin selesai mendaftarkan e-KTP
    setInterval(function() {
        fetch('{{ route("api.iot.rfid.latest") }}')
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'available' && data.data && data.data.user_id == {{ $user->id }}) {
                    location.reload();
                }
            })
            .catch(() => {});
    }, 2000);
</script>
@endsection

