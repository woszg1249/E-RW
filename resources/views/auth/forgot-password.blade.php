<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Lupa Password - E-RT RW Digital Bersatu</title>
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
            padding: 24px 16px;
            margin: 0;
            transition: background 0.3s;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 460px;
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
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
            width: 100%;
            padding: 36px 32px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .brand-icon {
            width: 58px;
            height: 58px;
            object-fit: contain;
            margin-bottom: 12px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
        }
        .btn-submit {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 12px;
            border-radius: 12px;
            width: 100%;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            transform: translateY(-1px);
            color: white;
        }

        /* Mode Phone Responsif untuk HP */
        body.mode-phone {
            padding: 14px 12px;
        }
        body.mode-phone .auth-wrapper {
            max-width: 100%;
        }
        body.mode-phone .login-card {
            padding: 24px 20px;
            border-radius: 20px;
        }
        body.mode-phone .form-control {
            font-size: 16px !important;
            padding: 13px 14px;
        }
        body.mode-phone .btn-submit {
            padding: 13px;
        }

        @media (max-width: 576px) {
            body {
                padding: 14px 12px;
            }
            .auth-wrapper {
                max-width: 100%;
            }
            .login-card {
                padding: 24px 20px;
                border-radius: 20px;
            }
            .form-control {
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
                <i class="fa-solid fa-arrow-left me-1"></i> Ke Login
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

        <div class="login-card">
            <div class="text-center mb-4">
                <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" class="brand-icon">
                <h4 class="fw-bold text-dark mb-1">Pemulihan Kata Sandi</h4>
                <p class="text-muted small">Verifikasi identitas warga RW 19 untuk mereset kata sandi akun Anda secara aman.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 p-2 small mb-3">
                    <i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 p-2 small mb-3">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="p-3 bg-light rounded-3 border mb-3 small text-muted">
                <i class="fa-solid fa-shield-halved text-success me-1"></i>
                Untuk keamanan, masukkan <strong>Username/Email</strong> beserta <strong>NIK KTP</strong> Anda yang terdaftar pada sistem RW 19.
            </div>

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Username, Email, atau NIK</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" name="identitas" class="form-control border-start-0" placeholder="e.g. budi.santoso atau budi@email.com" value="{{ old('identitas') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Nomor Induk Kependudukan (NIK KTP)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-id-card text-muted"></i></span>
                        <input type="text" name="nik" class="form-control border-start-0 font-monospace" placeholder="16 Digit NIK KTP Anda" value="{{ old('nik') }}" maxlength="16" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small">Nomor Kartu Keluarga (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-address-card text-muted"></i></span>
                        <input type="text" name="no_kk" class="form-control border-start-0 font-monospace" placeholder="16 Digit No KK (Bila diminta)" value="{{ old('no_kk') }}" maxlength="16">
                    </div>
                </div>

                <button type="submit" class="btn btn-submit mb-3">
                    <i class="fa-solid fa-key me-2"></i> Verifikasi & Lanjut Reset
                </button>

                <div class="text-center pt-2 border-top">
                    <a href="{{ route('login') }}" class="text-decoration-none small text-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Ingat kata sandi? <strong>Masuk di sini</strong>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop & Phone Mode Script -->
    <script>
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
                    metaVp.setAttribute('content', 'width=800, initial-scale=0.45, maximum-scale=2.0, user-scalable=yes');
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
