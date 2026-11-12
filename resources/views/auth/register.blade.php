<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Registrasi Warga - E-RT RW Digital Bersatu</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo_ert_rw.svg') }}">

    <!-- Pre-load View Mode Script -->
    <script>
        (function() {
            var saved = localStorage.getItem('erw_view_mode') || (window.innerWidth <= 768 ? 'phone' : 'desktop');
            document.documentElement.classList.add(saved === 'phone' ? 'mode-phone' : 'mode-desktop');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #059669 0%, #0284c7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            margin: 0;
            transition: background 0.3s;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 740px;
        }
        .auth-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding: 0 4px;
        }
        .view-mode-switcher {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(8px);
            padding: 3px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .view-mode-switcher .btn-mode {
            border: none;
            background: transparent;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .view-mode-switcher .btn-mode.active {
            background: #ffffff;
            color: #059669;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .register-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
            width: 100%;
            padding: 40px 36px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.92rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
        }
        .btn-register {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 12px;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            color: white;
            transform: translateY(-1px);
        }
        .option-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .option-card:hover {
            border-color: #059669;
            background-color: #f0fdf4;
        }
        .option-card.selected {
            border-color: #059669;
            background-color: #f0fdf4;
        }

        /* Mode Phone Responsif untuk HP */
        body.mode-phone {
            padding: 14px 10px;
        }
        body.mode-phone .auth-wrapper {
            max-width: 100%;
        }
        body.mode-phone .register-card {
            padding: 24px 18px;
            border-radius: 20px;
        }
        body.mode-phone .form-control,
        body.mode-phone .form-select {
            font-size: 16px !important;
            padding: 12px 14px;
        }
        body.mode-phone .btn-register {
            padding: 13px;
        }

        @media (max-width: 576px) {
            body {
                padding: 14px 10px;
            }
            .auth-wrapper {
                max-width: 100%;
            }
            .register-card {
                padding: 22px 16px;
                border-radius: 18px;
            }
            .form-control, .form-select {
                font-size: 16px !important;
            }
        }

        @media (max-width: 991.98px) {
            body.mode-desktop {
                min-width: 768px !important;
                overflow-x: auto !important;
            }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <!-- Header Bar: Kembali & Desktop/Phone Switcher -->
        <div class="auth-header-bar">
            <a href="{{ route('login') }}" class="text-white text-decoration-none small fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Login
            </a>

            <!-- Mode Switcher -->
            <div class="view-mode-switcher" title="Ganti Mode Tampilan">
                <button type="button" class="btn-mode btn-mode-desktop" onclick="setViewMode('desktop')">
                    <i class="fa-solid fa-desktop"></i> <span class="d-none d-sm-inline">Desktop</span>
                </button>
                <button type="button" class="btn-mode btn-mode-phone" onclick="setViewMode('phone')">
                    <i class="fa-solid fa-mobile-screen-button"></i> <span class="d-none d-sm-inline">Phone</span>
                </button>
            </div>
        </div>

        <div class="register-card">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" style="width: 56px; height: 56px;" class="mb-2">
            <h4 class="fw-bold text-dark mb-1">Pendaftaran Akun Warga Baru</h4>
            <p class="text-muted small">Lengkapi formulir pendaftaran warga resmi lingkungan RW 19 (RT 06 & RT 07)</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 p-3 small mb-3">
                <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Mohon periksa kembali formulir:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- 1. PILIHAN STATUS KELUARGA / KK -->
            <div class="mb-3">
                <label class="form-label fw-bold small text-dark">Status Pendaftaran Dalam Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="option-card d-flex align-items-center gap-2 w-100" id="card_kk_baru">
                            <input type="radio" name="tipe_kk" value="baru" id="radio_kk_baru" {{ old('tipe_kk', 'baru') === 'baru' ? 'checked' : '' }} onchange="updateTipeKk('baru')">
                            <div>
                                <div class="fw-bold text-dark small">Buat KK Baru (Kepala Keluarga)</div>
                                <div class="text-muted" style="font-size: 0.78rem;">Saya mendaftar sebagai Kepala Keluarga</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="option-card d-flex align-items-center gap-2 w-100" id="card_kk_gabung">
                            <input type="radio" name="tipe_kk" value="gabung" id="radio_kk_gabung" {{ old('tipe_kk') === 'gabung' ? 'checked' : '' }} onchange="updateTipeKk('gabung')">
                            <div>
                                <div class="fw-bold text-dark small">Gabung ke KK yang Ada (Anggota)</div>
                                <div class="text-muted" style="font-size: 0.78rem;">Saya adalah Istri, Anak, atau Famili</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Ahmad Fauzi" value="{{ old('nama') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                    <input type="text" name="nik" id="reg_nik" class="form-control" placeholder="16 digit NIK" value="{{ old('nik') }}" maxlength="16" required>
                    <div id="nik_feedback" class="small mt-1 d-none"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nomor Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                    <input type="text" name="no_kk" id="reg_kk" class="form-control" placeholder="16 digit No KK" value="{{ old('no_kk') }}" maxlength="16" required>
                    <div id="kk_feedback" class="small mt-1 d-none"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Hubungan Dalam Keluarga <span class="text-danger">*</span></label>
                    <select name="status_keluarga" id="reg_status_keluarga" class="form-select" required>
                        <option value="Kepala Keluarga" {{ old('status_keluarga') === 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                        <option value="Istri" {{ old('status_keluarga') === 'Istri' ? 'selected' : '' }}>Istri</option>
                        <option value="Anak" {{ old('status_keluarga') === 'Anak' ? 'selected' : '' }}>Anak</option>
                        <option value="Famili Lain" {{ old('status_keluarga') === 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                    </select>
                </div>

                <!-- BOX KONFIRMASI KELUARGA (Jika Gabung KK) -->
                <div class="col-12 d-none" id="boxKonfirmasiKeluarga">
                    <div class="p-3 rounded-3 border bg-light">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-users text-primary fs-5"></i>
                            <div class="fw-bold text-dark small" id="teksInfoKeluarga">Memeriksa data Kartu Keluarga...</div>
                        </div>
                        <p class="text-muted small mb-2" id="teksDetailKk">Pastikan nomor KK yang Anda masukkan benar milik keluarga Anda.</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="konfirmasi_kk" value="ya" id="check_konfirmasi_kk">
                            <label class="form-check-label small fw-semibold" for="check_konfirmasi_kk">
                                Ya, saya mengonfirmasi bahwa saya benar adalah anggota keluarga dari KK ini.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="kelamin" class="form-select" required>
                        <option value="Laki-laki" {{ old('kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('no_hp') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Username Login <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" placeholder="username unik (tanpa spasi)" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="{{ old('email') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Wilayah RT <span class="text-danger">*</span></label>
                    <select name="rt" id="reg_rt" class="form-select" required>
                        <option value="06" {{ old('rt') == '06' ? 'selected' : '' }}>RT 06 (Ketua: Dimas)</option>
                        <option value="07" {{ old('rt') == '07' ? 'selected' : '' }}>RT 07 (Ketua: Bambang)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Wilayah RW</label>
                    <input type="text" name="rw" id="reg_rw" class="form-control" value="19" readonly>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold small text-secondary">Alamat Domisili Lengkap</label>
                    <textarea name="alamat" id="reg_alamat" rows="2" class="form-control" placeholder="Jl. Melati Blok X No. Y">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-register w-100">
                    <i class="fa-solid fa-user-plus me-1"></i> Selesaikan Pendaftaran
                </button>
            </div>

            <div class="text-center mt-3">
                <span class="text-muted small">Sudah memiliki akun?</span>
                <a href="{{ route('login') }}" class="fw-bold text-success text-decoration-none small">Masuk di Sini</a>
            </div>
        </form>
        </div>
    </div>

    <script>
        function updateTipeKk(tipe) {
            const statusSelect = document.getElementById('reg_status_keluarga');
            const boxKk = document.getElementById('boxKonfirmasiKeluarga');
            const cardBaru = document.getElementById('card_kk_baru');
            const cardGabung = document.getElementById('card_kk_gabung');

            if (tipe === 'baru') {
                cardBaru.classList.add('selected');
                cardGabung.classList.remove('selected');
                boxKk.classList.add('d-none');
                statusSelect.innerHTML = '<option value="Kepala Keluarga" selected>Kepala Keluarga</option>';
            } else {
                cardBaru.classList.remove('selected');
                cardGabung.classList.add('selected');
                statusSelect.innerHTML = `
                    <option value="Istri">Istri</option>
                    <option value="Anak" selected>Anak</option>
                    <option value="Famili Lain">Famili Lain</option>
                `;
                cekKkRealtime();
            }
        }

        // Cek NIK Realtime
        const nikInput = document.getElementById('reg_nik');
        const nikFeedback = document.getElementById('nik_feedback');
        let nikTimeout;

        nikInput.addEventListener('input', function() {
            clearTimeout(nikTimeout);
            const val = this.value.trim();
            if (val.length === 16) {
                nikTimeout = setTimeout(() => {
                    fetch(`{{ route('ajax.check.nik') }}?nik=${val}`)
                        .then(res => res.json())
                        .then(data => {
                            nikFeedback.classList.remove('d-none');
                            if (data.exists) {
                                nikFeedback.className = 'small mt-1 text-danger fw-semibold';
                                nikFeedback.innerHTML = `<i class="fa-solid fa-circle-exclamation me-1"></i> ${data.message}`;
                            } else {
                                nikFeedback.className = 'small mt-1 text-success fw-semibold';
                                nikFeedback.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i> NIK valid & belum terdaftar.`;
                            }
                        });
                }, 300);
            } else {
                nikFeedback.classList.add('d-none');
            }
        });

        // Deteksi Kartu Keluarga Realtime & Auto-fill Alamat/RT
        const kkInput = document.getElementById('reg_kk');
        const kkFeedback = document.getElementById('kk_feedback');
        const alamatInput = document.getElementById('reg_alamat');
        const rtSelect = document.getElementById('reg_rt');
        const boxKonfirmasi = document.getElementById('boxKonfirmasiKeluarga');
        const teksInfo = document.getElementById('teksInfoKeluarga');
        const teksDetail = document.getElementById('teksDetailKk');
        let kkTimeout;

        function cekKkRealtime() {
            const val = kkInput.value.trim();
            const isGabung = document.getElementById('radio_kk_gabung').checked;

            if (val.length === 16) {
                fetch(`{{ route('ajax.check.kk') }}?no_kk=${val}`)
                    .then(res => res.json())
                    .then(data => {
                        kkFeedback.classList.remove('d-none');
                        if (data.found) {
                            if (isGabung) {
                                boxKonfirmasi.classList.remove('d-none');
                                teksInfo.innerHTML = `Kartu Keluarga Ditemukan: Kepala Keluarga: <strong>${data.kepala_keluarga}</strong> (${data.total_anggota} Anggota)`;
                                teksDetail.innerHTML = `Alamat: ${data.alamat || '-'} • Wilayah: RT ${data.rt}/RW 19. Alamat dan RT otomatis disinkronkan.`;
                            }
                            kkFeedback.className = 'small mt-1 text-primary fw-semibold';
                            kkFeedback.innerHTML = `<i class="fa-solid fa-users me-1"></i> Terdaftar atas Kepala Keluarga: <strong>${data.kepala_keluarga}</strong>.`;
                            if (data.alamat) alamatInput.value = data.alamat;
                            if (data.rt) rtSelect.value = data.rt;
                        } else {
                            boxKonfirmasi.classList.add('d-none');
                            if (isGabung) {
                                kkFeedback.className = 'small mt-1 text-danger fw-semibold';
                                kkFeedback.innerHTML = `<i class="fa-solid fa-circle-exclamation me-1"></i> No KK belum ada di sistem. Jika Anda Kepala Keluarga baru, pilih "Buat KK Baru".`;
                            } else {
                                kkFeedback.className = 'small mt-1 text-success fw-semibold';
                                kkFeedback.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i> Nomor KK baru. Anda terdaftar sebagai Kepala Keluarga.`;
                            }
                        }
                    });
            } else {
                kkFeedback.classList.add('d-none');
                boxKonfirmasi.classList.add('d-none');
            }
        }

        kkInput.addEventListener('input', function() {
            clearTimeout(kkTimeout);
            kkTimeout = setTimeout(cekKkRealtime, 300);
        });

        // Initialize state on load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedTipe = document.querySelector('input[name="tipe_kk"]:checked')?.value || 'baru';
            updateTipeKk(selectedTipe);
        });

        // Desktop & Phone Mode Script
        function setViewMode(mode) {
            localStorage.setItem('erw_view_mode', mode);
            applyViewMode(mode);
        }

        function applyViewMode(mode) {
            const body = document.body;
            body.classList.remove('mode-desktop', 'mode-phone');
            body.classList.add(mode === 'phone' ? 'mode-phone' : 'mode-desktop');

            document.querySelectorAll('.btn-mode-desktop').forEach(function(el) {
                el.classList.toggle('active', mode === 'desktop');
            });
            document.querySelectorAll('.btn-mode-phone').forEach(function(el) {
                el.classList.toggle('active', mode === 'phone');
            });

            var metaVp = document.querySelector('meta[name="viewport"]');
            if (metaVp) {
                if (mode === 'desktop' && window.innerWidth <= 768) {
                    metaVp.setAttribute('content', 'width=850, initial-scale=0.45, maximum-scale=2.0, user-scalable=yes');
                } else {
                    metaVp.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=5.0');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            var currentMode = localStorage.getItem('erw_view_mode') || (window.innerWidth <= 768 ? 'phone' : 'desktop');
            applyViewMode(currentMode);
        });
    </script>
</body>
</html>
