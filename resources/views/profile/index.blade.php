@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page_title', 'Pengaturan & Biodata Akun')

@section('content')
<div class="row g-4">
    <!-- Profile Card Summary -->
    <div class="col-lg-4">
        <div class="card-custom p-4 bg-white text-center">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ asset('img/' . ($user->foto ?? 'profile.jpg')) }}" 
                     alt="{{ $user->nama }}" 
                     class="rounded-circle border border-3 border-success shadow-sm" 
                     style="width: 120px; height: 120px; object-fit: cover;"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama) }}&background=059669&color=fff&size=128'">
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $user->nama }}</h5>
            <p class="text-muted small mb-2">{{ '@' . $user->username }} • {{ $user->email }}</p>
            <span class="badge-role {{ $user->isAdmin() ? 'admin' : 'warga' }} mb-3 d-inline-block">
                {{ $user->role }} • RT {{ $user->rt ?? '06' }} / RW {{ $user->rw ?? '19' }}
            </span>

            <div class="border-top pt-3 text-start small">
                <div class="mb-2"><strong>NIK:</strong> {{ $user->nik ?? '-' }}</div>
                <div class="mb-2"><strong>No. KK:</strong> {{ $user->no_kk ?? '-' }}</div>
                <div class="mb-2"><strong>Status Keluarga:</strong> {{ $user->status_keluarga ?? '-' }}</div>
                <div class="mb-2"><strong>Pekerjaan:</strong> {{ $user->pekerjaan ?? '-' }}</div>
                <div class="mb-0"><strong>Alamat:</strong> {{ $user->alamat ?? '-' }}</div>
            </div>
        </div>

        <!-- Kartu Akses IoT e-KTP & Portal Perumahan -->
        <div class="card-custom p-4 bg-white mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-id-card text-success me-2"></i>Akses IoT e-KTP</h6>
                @if($user->rfid_uid)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Terdaftar</span>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1">Belum Aktif</span>
                @endif
            </div>

            @if($user->rfid_uid)
                <div class="bg-light rounded-3 p-3 mb-3 small">
                    <div class="text-muted mb-1">UID Kartu e-KTP:</div>
                    <div class="fw-bold font-monospace fs-6 text-dark">{{ $user->rfid_uid }}</div>
                    <div class="text-success mt-2" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-door-open me-1"></i> Akses Portal Perumahan & Bank Warga aktif.
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="button" id="btnStartScanRfid" class="btn btn-outline-success btn-sm rounded-pill">
                        <i class="fa-solid fa-wifi me-1"></i> Ganti / Scan Ulang e-KTP
                    </button>
                    <form action="{{ route('profil.rfid.unbind') }}" method="POST" onsubmit="return confirm('Lepas kartu e-KTP dari akun Anda? Akses portal perumahan akan dinonaktifkan.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100">
                            <i class="fa-solid fa-link-slash me-1"></i> Lepas Kartu e-KTP
                        </button>
                    </form>
                </div>
            @else
                <p class="text-muted small mb-3">
                    Daftarkan e-KTP fisik Anda melalui sensor ESP32 agar bisa membuka <strong>Portal Perumahan</strong> dan mengakses <strong>Bank Warga</strong>.
                </p>
                <div class="d-grid">
                    <button type="button" id="btnStartScanRfid" class="btn btn-success btn-sm rounded-pill">
                        <i class="fa-solid fa-wifi me-1"></i> Scan & Daftarkan e-KTP
                    </button>
                </div>
            @endif

            <div id="rfidScanBox" class="alert alert-info mt-3 mb-0 py-2 px-3 small d-none">
                <div class="d-flex align-items-center gap-2 fw-semibold text-primary">
                    <span class="spinner-border spinner-border-sm"></span>
                    <span>Menunggu tap e-KTP pada alat ESP32...</span>
                </div>
                <div id="rfidScanStatus" class="text-muted mt-1" style="font-size: 0.78rem;">
                    Tempelkan kartu e-KTP Anda ke sensor RC-522 sekarang.
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="col-lg-8">
        <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold mb-1">Perbarui Data Pribadi</h5>
            <p class="text-muted small mb-4">Pastikan data kependudukan Anda sesuai dengan e-KTP dan Kartu Keluarga terbaru</p>

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik', $user->nik) }}" placeholder="16 digit NIK">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nomor Kartu Keluarga (KK)</label>
                        <input type="text" name="no_kk" class="form-control" value="{{ old('no_kk', $user->no_kk) }}" placeholder="16 digit No KK">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nomor WhatsApp / HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">RT</label>
                        <input type="text" name="rt" class="form-control" value="{{ old('rt', $user->rt ?? '06') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">RW</label>
                        <input type="text" name="rw" class="form-control" value="{{ old('rw', $user->rw ?? '19') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $user->tempat_lahir ?? 'Bekasi') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '2000-01-01') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Agama</label>
                        <input type="text" name="agama" class="form-control" value="{{ old('agama', $user->agama ?? 'Islam') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan', $user->pekerjaan ?? 'Belum / Tidak Bekerja') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Status Hubungan Keluarga</label>
                        <select name="status_keluarga" class="form-select">
                            <option value="Kepala Keluarga" {{ $user->status_keluarga === 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                            <option value="Istri" {{ $user->status_keluarga === 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Anak" {{ $user->status_keluarga === 'Anak' ? 'selected' : '' }}>Anak</option>
                            <option value="Famili Lain" {{ $user->status_keluarga === 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" class="form-control">{{ old('alamat', $user->alamat) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Ganti Foto Profil (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF (Maks. 2MB)</small>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top">
                        <h6 class="fw-bold mb-2">Ganti Password (Biarkan kosong jika tidak diubah)</h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Password Lama</label>
                        <input type="password" name="password_lama" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="mt-4 pt-3">
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnScan = document.getElementById('btnStartScanRfid');
    const scanBox = document.getElementById('rfidScanBox');
    const scanStatus = document.getElementById('rfidScanStatus');
    let pollTimer = null;
    let isPairing = false;
    let initialScanTime = null;

    if (btnScan) {
        btnScan.addEventListener('click', function() {
            if (pollTimer) return;
            scanBox.classList.remove('d-none', 'alert-danger', 'alert-success');
            scanBox.classList.add('alert-info');
            scanStatus.textContent = 'Tempelkan kartu e-KTP Anda ke sensor RC-522 sekarang...';
            btnScan.disabled = true;

            // Catat timestamp scan sebelumnya agar tidak langsung mengambil scan lama
            fetch('{{ route("api.iot.rfid.latest") }}')
                .then(r => r.json())
                .then(init => {
                    if (init && init.data && init.data.scanned_at) {
                        initialScanTime = init.data.scanned_at;
                    }
                    pollTimer = setInterval(checkScan, 2000);
                })
                .catch(() => {
                    pollTimer = setInterval(checkScan, 2000);
                });
        });
    }

    function checkScan() {
        if (isPairing) return;
        fetch('{{ route("api.iot.rfid.latest") }}')
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'available' && data.data && data.data.rfid_uid) {
                    if (initialScanTime && data.data.scanned_at === initialScanTime) {
                        return;
                    }
                    isPairing = true;
                    clearInterval(pollTimer);
                    pollTimer = null;
                    pairUidToCurrentUser(data.data.rfid_uid);
                }
            })
            .catch(() => {});
    }

    function pairUidToCurrentUser(uid) {
        scanStatus.textContent = 'Kartu terdeteksi (' + uid + '). Mendaftarkan ke akun Anda...';
        fetch('{{ route("profil.rfid.pair") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ rfid_uid: uid })
        })
        .then(async res => {
            const json = await res.json();
            if (res.ok && json.status === 'success') {
                scanBox.classList.remove('alert-info');
                scanBox.classList.add('alert-success');
                scanStatus.innerHTML = '<strong>Berhasil!</strong> ' + json.message;
                setTimeout(() => location.reload(), 1200);
            } else {
                scanBox.classList.remove('alert-info');
                scanBox.classList.add('alert-danger');
                scanStatus.innerHTML = '<strong>Gagal:</strong> ' + (json.message || 'Terjadi kesalahan.');
                btnScan.disabled = false;
                isPairing = false;
            }
        })
        .catch(() => {
            scanBox.classList.remove('alert-info');
            scanBox.classList.add('alert-danger');
            scanStatus.textContent = 'Gagal menghubungi server.';
            btnScan.disabled = false;
            isPairing = false;
        });
    }
});
</script>
@endsection
