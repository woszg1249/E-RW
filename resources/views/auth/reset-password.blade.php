<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Buat Password Baru - E-RT RW Digital Bersatu</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo_ert_rw.svg') }}">

    <!-- Pre-load View Mode & Theme Script -->
    <script>
        (function() {
            var savedMode = localStorage.getItem('erw_view_mode') || (window.innerWidth <= 768 ? 'phone' : 'desktop');
            document.documentElement.classList.add(savedMode === 'phone' ? 'mode-phone' : 'mode-desktop');

            var savedTheme = localStorage.getItem('erw_theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            }
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
            max-width: 440px;
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
        /* Dark Mode Styling */
        html.dark-theme body,
        body.dark-theme {
            background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%) !important;
        }

        body.dark-theme .login-card {
            background: #151f32 !important;
            border-color: #1e293b !important;
            color: #f8fafc !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
        }

        body.dark-theme .text-dark,
        body.dark-theme h4 {
            color: #f8fafc !important;
        }

        body.dark-theme .text-muted,
        body.dark-theme .text-secondary {
            color: #94a3b8 !important;
        }

        body.dark-theme .form-control {
            background-color: #0b1322 !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .form-control:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2) !important;
        }

        body.dark-theme .input-group-text {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }

        body.dark-theme .border-top {
            border-color: #1e293b !important;
        }

        /* Toggle Button Theme Style */
        .theme-toggle-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(8px);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.05);
        }

        body.dark-theme .theme-toggle-btn {
            background: #1e293b !important;
            color: #facc15 !important;
            border-color: #334155 !important;
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

            <div class="d-flex align-items-center gap-2">
                <!-- Theme Mode Toggle (Dark / Light) -->
                <button type="button" class="theme-toggle-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Ubah Mode Gelap / Terang">
                    <i class="fa-solid fa-moon theme-icon-dark"></i>
                    <i class="fa-solid fa-sun theme-icon-light d-none"></i>
                </button>

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
        </div>

        <div class="login-card">
            <div class="text-center mb-4">
                <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" class="brand-icon">
                <h4 class="fw-bold text-dark mb-1">Buat Kata Sandi Baru</h4>
                <p class="text-muted small">Tentukan kata sandi baru untuk akun warga Anda.</p>
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

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Alamat Email Terdaftar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $email) }}" readonly required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Kata Sandi Baru</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="Minimal 6 karakter" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small">Ulangi Kata Sandi Baru</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-lock-open text-muted"></i></span>
                        <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Ketik ulang kata sandi baru" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit mb-3">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Password Baru
                </button>

                <div class="text-center pt-2 border-top">
                    <a href="{{ route('login') }}" class="text-decoration-none small text-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Batal & Kembali ke <strong>Login</strong>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop/Phone & Theme Mode Script -->
    <script>
        function toggleTheme() {
            var currentTheme = localStorage.getItem('erw_theme') || (document.documentElement.classList.contains('dark-theme') ? 'dark' : 'light');
            var newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        }

        function setTheme(theme) {
            localStorage.setItem('erw_theme', theme);
            applyTheme(theme);
        }

        function applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
                document.body.classList.add('dark-theme');
                document.querySelectorAll('.theme-icon-dark').forEach(el => el.classList.add('d-none'));
                document.querySelectorAll('.theme-icon-light').forEach(el => el.classList.remove('d-none'));
            } else {
                document.documentElement.classList.remove('dark-theme');
                document.body.classList.remove('dark-theme');
                document.querySelectorAll('.theme-icon-dark').forEach(el => el.classList.remove('d-none'));
                document.querySelectorAll('.theme-icon-light').forEach(el => el.classList.add('d-none'));
            }
        }

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
            var currentTheme = localStorage.getItem('erw_theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            applyTheme(currentTheme);

            var currentMode = localStorage.getItem('erw_view_mode') || (window.innerWidth <= 768 ? 'phone' : 'desktop');
            applyViewMode(currentMode);
        });
    </script>
</body>
</html>
