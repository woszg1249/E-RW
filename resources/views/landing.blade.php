<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-RT RW Bersatu - Portal Resmi RW 19 Bekasi Jaya</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo_ert_rw.svg') }}">

    <!-- Pre-load View Mode Script -->
    <script>
        (function() {
            var saved = localStorage.getItem('erw_view_mode') || (window.innerWidth <= 768 ? 'phone' : 'desktop');
            document.documentElement.classList.add(saved === 'phone' ? 'mode-phone' : 'mode-desktop');
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --primary-light: #ecfdf5;
            --accent: #10b981;
            --dark: #0f172a;
            --slate: #475569;
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            color: var(--dark);
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Navbar */
        .landing-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 0;
            transition: all 0.3s ease;
        }

        .landing-nav .nav-link {
            font-weight: 600;
            color: var(--slate);
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .landing-nav .nav-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }

        /* Hero Section */
        .hero-section {
            background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.08) 0%, rgba(255, 255, 255, 0) 50%),
                        radial-gradient(circle at 90% 80%, rgba(5, 150, 105, 0.08) 0%, rgba(255, 255, 255, 0) 50%);
            padding: 90px 0 80px;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #dcfce7;
            color: #166534;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
        }

        .hero-title {
            font-size: 3.1rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .hero-title .text-gradient {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: #475569;
            line-height: 1.6;
            max-width: 620px;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border: none;
            padding: 14px 32px;
            font-weight: 700;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-primary:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(16, 185, 129, 0.4);
        }

        .btn-hero-secondary {
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            padding: 14px 28px;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: var(--primary);
        }

        /* Mockup Card */
        .hero-mockup-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            position: relative;
        }

        .stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            transition: all 0.25s;
        }

        .stat-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
            border-color: #bbf7d0;
        }

        /* Feature Card */
        .feature-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 28px;
            height: 100%;
            transition: all 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.06);
            border-color: #10b981;
        }

        .feature-icon-wrapper {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        /* Panduan Step Card */
        .step-number {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .nav-pills-custom .nav-link {
            color: #64748b;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 24px;
            background: #f1f5f9;
            margin: 0 4px;
            transition: all 0.2s;
        }

        .nav-pills-custom .nav-link.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .footer-landing {
            background: #0f172a;
            color: #94a3b8;
            padding: 60px 0 30px;
        }

        .footer-landing h6 {
            color: #f8fafc;
            font-weight: 700;
        }

        .footer-landing a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-landing a:hover {
            color: #10b981;
        }

        /* ===== DESKTOP & PHONE MODE SWITCHER STYLES ===== */
        .view-mode-switcher {
            display: inline-flex;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
        }

        .view-mode-switcher .btn-mode {
            border: none;
            background: transparent;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .view-mode-switcher .btn-mode.active {
            background: #ffffff;
            color: var(--primary);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .floating-mode-badge {
            position: fixed;
            bottom: 22px;
            right: 22px;
            z-index: 1060;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(8px);
            padding: 5px 8px;
            border-radius: 999px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s;
        }

        .floating-mode-badge .btn-float {
            border: none;
            background: transparent;
            color: #94a3b8;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .floating-mode-badge .btn-float.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
        }

        /* Mode Phone untuk Landing Page */
        body.mode-phone {
            background: #0f172a !important;
        }

        body.mode-phone #landing-shell-container {
            max-width: 480px;
            margin: 20px auto;
            background: #ffffff;
            min-height: calc(100vh - 40px);
            border-radius: 36px;
            overflow-x: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 0 10px #1e293b;
            position: relative;
        }

        body.mode-phone .phone-notch-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 24px 6px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        body.mode-phone .hero-title {
            font-size: 2rem !important;
        }

        @media (max-width: 576px) {
            body.mode-phone {
                background: #ffffff !important;
            }
            body.mode-phone #landing-shell-container {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                border: none;
            }
            body.mode-phone .phone-notch-bar {
                display: none;
            }
        }

        /* Mode Desktop untuk HP */
        @media (max-width: 991.98px) {
            body.mode-desktop {
                min-width: 1100px !important;
                overflow-x: auto !important;
            }
        }
    </style>
</head>
<body>

    <div id="landing-shell-container">
        <!-- Mockup Status Bar untuk Phone Mode di Layar Lebar -->
        <div class="phone-notch-bar">
            <span><i class="fa-solid fa-signal me-1"></i> E-RW 5G</span>
            <span><i class="fa-solid fa-mobile-screen me-1 text-success"></i> Phone Mode</span>
            <span><i class="fa-solid fa-wifi me-1"></i> 100% <i class="fa-solid fa-battery-full ms-1 text-success"></i></span>
        </div>

        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg landing-nav sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
                    <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" style="width: 42px; height: 42px;">
                    <div>
                        <span class="fw-bold fs-5 text-dark d-block lh-1">E-RT RW BERSATU</span>
                        <small class="text-muted fw-semibold" style="font-size: 0.72rem;">RW 19 (RT 06 & RT 07) BEKASI JAYA</small>
                    </div>
                </a>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="#layanan">Layanan & Fitur</a></li>
                        <li class="nav-item"><a class="nav-link text-primary fw-bold" href="#panduan"><i class="fa-solid fa-book-open-reader me-1"></i> Panduan Penggunaan</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang RW 19</a></li>
                        <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                    </ul>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Desktop & Phone Mode Switcher di Navbar -->
                        <div class="view-mode-switcher me-1">
                            <button type="button" class="btn-mode btn-mode-desktop" onclick="setViewMode('desktop')" title="Mode Desktop (Layar Lebar)">
                                <i class="fa-solid fa-desktop"></i> <span class="d-none d-sm-inline">Desktop</span>
                            </button>
                            <button type="button" class="btn-mode btn-mode-phone" onclick="setViewMode('phone')" title="Mode Phone (Layar HP)">
                                <i class="fa-solid fa-mobile-screen-button"></i> <span class="d-none d-sm-inline">Phone</span>
                            </button>
                        </div>

                        @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-hero-primary py-2 px-3 small">
                            <i class="fa-solid fa-gauge-high"></i> Menuju Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-dark fw-bold rounded-pill px-4 py-2">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-hero-primary py-2 px-4 rounded-pill">
                            <i class="fa-solid fa-user-plus me-1"></i> Daftar Warga
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="beranda">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-badge">
                        <i class="fa-solid fa-shield-heart text-success"></i> Portal Informasi & Layanan Mandiri Warga
                    </div>
                    <h1 class="hero-title mb-3">
                        Pelayanan Warga <span class="text-gradient">Modern, Cepat & Cerdas</span> Berbasis IoT.
                    </h1>
                    <p class="hero-desc mb-4">
                        Platform resmi digitalisasi wilayah <strong>RW 19 (RT 06 & RT 07) Kelurahan Bekasi Jaya</strong>. Kelola administrasi kependudukan (KK & Surat Pengantar), kas lingkungan transparan, Bank Sampah, Posyandu Balita, dan inovasi <strong>Bank Digital Warga</strong> hanya dengan sentuhan smartphone atau tap e-KTP.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-5">
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
                        </a>
                        <a href="#panduan" class="btn-hero-secondary">
                            <i class="fa-solid fa-circle-question text-success"></i> Panduan Lengkap
                        </a>
                        <a href="{{ route('register') }}" class="btn-hero-secondary">
                            <i class="fa-solid fa-user-check text-primary"></i> Daftar Akun Warga
                        </a>
                    </div>

                    <!-- Mini Feature Badges -->
                    <div class="row g-3 pt-2">
                        <div class="col-auto d-flex align-items-center gap-2 text-muted small">
                            <i class="fa-solid fa-check-circle text-success fs-6"></i> Surat Pengantar 24 Jam
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2 text-muted small">
                            <i class="fa-solid fa-check-circle text-success fs-6"></i> Bayar Iuran QRIS & Bank Warga
                        </div>
                        <div class="col-auto d-flex align-items-center gap-2 text-muted small">
                            <i class="fa-solid fa-check-circle text-success fs-6"></i> Integrasi IoT Tap e-KTP
                        </div>
                    </div>
                </div>

                <!-- Mockup Banner Kanan -->
                <div class="col-lg-5">
                    <div class="hero-mockup-card">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-success rounded-circle p-1 d-inline-block"></span>
                                <span class="small fw-bold text-dark">Sistem RT RW Digital Aktif</span>
                            </div>
                            <span class="badge bg-light text-secondary border">RW 19 Bekasi Jaya</span>
                        </div>

                        <!-- Card Preview Bank Warga -->
                        <div class="p-3 rounded-4 mb-3" style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 100%); color: white;">
                            <div class="d-flex justify-content-between align-items-center small opacity-75 mb-2">
                                <span>BANK DIGITAL WARGA</span>
                                <i class="fa-solid fa-wifi text-success"></i>
                            </div>
                            <div class="font-monospace fs-5 fw-bold mb-1">BW-1906-880012</div>
                            <div class="d-flex justify-content-between align-items-end mt-3">
                                <div>
                                    <div style="font-size: 0.7rem;" class="opacity-75">SALDO KAS WARGA</div>
                                    <div class="fs-4 fw-bold text-warning">Rp 125.000</div>
                                </div>
                                <span class="badge bg-success bg-opacity-25 border border-success border-opacity-50 text-white rounded-pill px-2 py-1 small">
                                    <i class="fa-solid fa-check-double me-1"></i> Tap e-KTP Ready
                                </span>
                            </div>
                        </div>

                        <!-- 2 Card Kecil Notif -->
                        <div class="p-2 bg-light rounded-3 mb-2 d-flex align-items-center gap-2 border small">
                            <div class="bg-info bg-opacity-10 text-info p-2 rounded-circle">
                                <i class="fa-solid fa-recycle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Bank Sampah Masuk Saldo</div>
                                <div class="text-muted" style="font-size: 0.75rem;">+ Rp 30.000 (Setoran Botol Plastik)</div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success">Otomatis</span>
                        </div>

                        <div class="p-2 bg-light rounded-3 d-flex align-items-center gap-2 border small">
                            <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Iuran Bulanan RT 06/07</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Lunas via Saldo Bank Warga</div>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary">Tervalidasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Counter Section -->
    <section class="py-4 bg-light border-top border-bottom">
        <div class="container">
            <div class="row g-3 text-center">
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <div class="fs-2 fw-bold text-success mb-1">2 RT</div>
                        <div class="text-muted small fw-semibold">RT 06 & RT 07 Terhubung</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <div class="fs-2 fw-bold text-primary mb-1">{{ $totalWarga }}+ Warga</div>
                        <div class="text-muted small fw-semibold">Warga Terdata Digital</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <div class="fs-2 fw-bold text-dark mb-1">{{ $totalKK }}+ KK</div>
                        <div class="text-muted small fw-semibold">Kartu Keluarga Terverifikasi</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-box">
                        <div class="fs-2 fw-bold text-info mb-1">{{ number_format($totalSampahKg, 1) }} Kg</div>
                        <div class="text-muted small fw-semibold">Sampah Didaur Ulang</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan & Fitur Utama Section -->
    <section class="py-5" id="layanan">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-2">
                    Layanan Unggulan Lingkungan
                </span>
                <h2 class="fw-bold text-dark mb-3">Solusi Digital Lengkap untuk Warga & Pengurus</h2>
                <p class="text-muted">
                    Semua kebutuhan administrasi, keuangan kas, dan pemberdayaan masyarakat terintegrasi dalam satu sistem terpadu.
                </p>
            </div>

            <div class="row g-4">
                <!-- 1. Administrasi KK -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Kartu Keluarga (KK) Resmi</h5>
                        <p class="text-muted small mb-3">
                            Warga dapat mengecek susunan anggota keluarganya yang otomatis diurutkan secara hierarki Dukcapil (Kepala Keluarga selalu di atas) serta mencetak blangko KK resmi.
                        </p>
                        <span class="badge bg-light text-secondary border">Warga Mandiri</span>
                    </div>
                </div>

                <!-- 2. Surat Pengantar Online -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Surat Pengantar RT Online</h5>
                        <p class="text-muted small mb-3">
                            Ajukan surat pengantar pembuatan KTP, SKCK, domisili, atau surat kematian secara online dari rumah. Pengurus RT memverifikasi dan mencetak secara instan.
                        </p>
                        <span class="badge bg-light text-secondary border">Layanan 24 Jam</span>
                    </div>
                </div>

                <!-- 3. Bank Digital Warga & IoT -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Bank Digital Warga & IoT</h5>
                        <p class="text-muted small mb-3">
                            Dompet digital kas warga yang terhubung dengan akun warga dan tap e-KTP. Saldo dapat dipakai bayar iuran RT tanpa ribet, serta menampung uang hasil setor bank sampah.
                        </p>
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Fitur Pintar IoT</span>
                    </div>
                </div>

                <!-- 4. Kas RT & Laporan Bulanan -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="fa-solid fa-vault"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Kas RT & Laporan Bulanan</h5>
                        <p class="text-muted small mb-3">
                            Transparansi pembukuan kas masuk dan pengeluaran lingkungan. Laporan keuangan rekapitulasi tersaji secara bulanan rapi dan siap dicetak/diaudit warga.
                        </p>
                        <span class="badge bg-light text-secondary border">Transparan 100%</span>
                    </div>
                </div>

                <!-- 5. Bank Sampah RW 19 -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-recycle"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Bank Sampah Lingkungan</h5>
                        <p class="text-muted small mb-3">
                            Warga menyetor sampah anorganik terpilah (kardus, botol plastik). Nilai konversi rupiahnya secara otomatis langsung ditransfer masuk ke saldo Bank Warga!
                        </p>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Auto-Transfer Saldo</span>
                    </div>
                </div>

                <!-- 6. Posyandu Balita & KIA -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-danger bg-opacity-10 text-danger">
                            <i class="fa-solid fa-baby"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Posyandu Balita Melati</h5>
                        <p class="text-muted small mb-3">
                            Pencatatan tumbuh kembang anak (BB, TB, Lingkar Kepala) sesuai standar Kemenkes/WHO. Orang tua dapat langsung mencetak surat keterangan kesehatan balitanya.
                        </p>
                        <span class="badge bg-light text-secondary border">Standar WHO / KIA</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BAGIAN PANDUAN PENGGUNAAN APLIKASI (LENGKAP & INTERAKTIF) -->
    <section class="py-5 bg-light" id="panduan">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">
                    <i class="fa-solid fa-book me-1"></i> Dokumentasi & Panduan
                </span>
                <h2 class="fw-bold text-dark mb-3">Panduan Cara Memakai Aplikasi E-RT RW Bersatu</h2>
                <p class="text-muted">
                    Ikuti petunjuk langkah demi langkah di bawah ini untuk memanfaatkan seluruh fasilitas digital lingkungan secara maksimal.
                </p>

                <!-- Navigation Tabs Panduan -->
                <ul class="nav nav-pills nav-pills-custom justify-content-center mt-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-warga-tab" data-bs-toggle="pill" data-bs-target="#pills-warga" type="button" role="tab">
                            <i class="fa-solid fa-user me-1"></i> Panduan Warga
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-bank-tab" data-bs-toggle="pill" data-bs-target="#pills-bank" type="button" role="tab">
                            <i class="fa-solid fa-building-columns me-1"></i> Bank Warga & IoT
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-admin-tab" data-bs-toggle="pill" data-bs-target="#pills-admin" type="button" role="tab">
                            <i class="fa-solid fa-user-shield me-1"></i> Panduan Pengurus RT/RW
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-faq-tab" data-bs-toggle="pill" data-bs-target="#pills-faq" type="button" role="tab">
                            <i class="fa-solid fa-circle-question me-1"></i> Tanya Jawab (FAQ)
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="pills-tabContent">

                <!-- 1. PANDUAN WARGA -->
                <div class="tab-pane fade show active" id="pills-warga" role="tabpanel">
                    <div class="row g-4">
                        <!-- Step 1 -->
                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number">1</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Pendaftaran & Login Akun Warga</h5>
                                        <p class="text-muted small mb-2">
                                            Bagi warga yang belum memiliki akun, klik tombol <strong>Daftar Warga</strong> pada halaman login. Masukkan NIK dan No KK Anda yang terdaftar pada RT 06 atau RT 07. Sistem akan memverifikasi identitas Anda secara otomatis.
                                        </p>
                                        <div class="p-2 bg-light rounded small text-secondary">
                                            <i class="fa-solid fa-info-circle text-primary me-1"></i> Jika NIK belum terdaftar di database, hubungi Ketua RT setempat untuk didaftarkan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number">2</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Melihat & Cetak Kartu Keluarga (KK)</h5>
                                        <p class="text-muted small mb-2">
                                            Buka menu <strong>Kartu Keluarga Saya</strong> pada sidebar. Anda hanya dapat melihat data KK keluarga Anda sendiri (privasi terjamin). Posisi Kepala Keluarga akan selalu berada paling atas sesuai standar resmi Dukcapil. Klik tombol <strong>Cetak KK</strong> untuk print dokumen.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number">3</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Mengajukan Surat Pengantar RT</h5>
                                        <p class="text-muted small mb-2">
                                            Pilih menu <strong>Surat Pengantar RT</strong> &raquo; klik <strong>Ajukan Surat Baru</strong>. Pilih jenis surat (SKCK, KTP, Domisili, dll.) dan cantumkan keperluannya. Anda dapat memantau status persetujuan dari Ketua RT secara real-time.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number">4</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Pembayaran Tagihan Iuran Warga</h5>
                                        <p class="text-muted small mb-2">
                                            Pada menu <strong>Tagihan Iuran Warga</strong>, klik tombol <strong>Bayar</strong> pada baris tagihan yang belum lunas. Pilih metode pembayaran:
                                        </p>
                                        <ul class="small text-muted mb-0 ps-3">
                                            <li><strong>Bank Digital Warga:</strong> Saldo terpotong otomatis dan lunas seketika.</li>
                                            <li><strong>QRIS Digital:</strong> Pindai QRIS via GoPay, OVO, DANA, BCA Mobile.</li>
                                            <li><strong>Transfer Bank & Tunai:</strong> Bayar manual ke pengurus RT.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PANDUAN BANK WARGA & IOT -->
                <div class="tab-pane fade" id="pills-bank" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-info text-white">1</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Aktivasi Rekening Bank Digital Warga</h5>
                                        <p class="text-muted small mb-2">
                                            Buka menu <strong>Bank Digital Warga</strong>. Rekening Anda langsung aktif otomatis terhubung dengan NIK, KK, dan akun Anda. Nomor rekening unik format <code>BW-1906-XXXXX</code> tertera di kartu ATM digital Anda.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-info text-white">2</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Top-Up Saldo Mandiri</h5>
                                        <p class="text-muted small mb-2">
                                            Klik tombol <strong>Top-Up Saldo</strong> &raquo; pilih nominal instan (Rp 25.000, Rp 50.000, Rp 100.000) &raquo; pilih kanal Transfer Antar Bank (BCA, Mandiri, BRI, BNI) atau E-Wallet (GoPay, DANA, OVO). Saldo langsung bertambah instan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-info text-white">3</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Otomatis Terima Saldo dari Bank Sampah</h5>
                                        <p class="text-muted small mb-2">
                                            Saat Anda menyetorkan sampah daur ulang di Bank Sampah RT, Anda tidak perlu menunggu uang tunai. Sistem secara otomatis mengkreditkan nilai uang sampah tersebut langsung ke saldo rekening Bank Warga Anda!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-info text-white">4</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Tap e-KTP pada IoT Reader Pos RT</h5>
                                        <p class="text-muted small mb-2">
                                            Saat berada di Pos RT/RW, Anda cukup menempelkan fisik e-KTP Anda ke alat sensor RFID reader. Sistem IoT secara otomatis membaca saldo dan nomor rekening Anda untuk transaksi cepat.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. PANDUAN PENGURUS RT / RW -->
                <div class="tab-pane fade" id="pills-admin" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-dark text-white">1</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Pengelolaan Buku Kas RT & Keuangan</h5>
                                        <p class="text-muted small mb-2">
                                            Menu <strong>Buku Kas RT</strong> khusus dapat diakses oleh Pengurus RT / Admin. Anda dapat mencatat mutasi pemasukan/pengeluaran baru serta mengunggah bukti nota kuitansi.
                                        </p>
                                        <p class="text-muted small mb-0">
                                            Buka <strong>Laporan Keuangan</strong> untuk melihat rekap bulanan yang rapi dan mencetak dokumen pertanggungjawaban kas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-dark text-white">2</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Penerbitan Tagihan Iuran Lingkungan</h5>
                                        <p class="text-muted small mb-2">
                                            Pada menu <strong>Tagihan Iuran Warga</strong>, klik <strong>Buat Tagihan Baru</strong>. Anda dapat membuat tagihan sekaligus untuk seluruh warga RT 06, RT 07, maupun per individu. Tagihan akan otomatis muncul di akun warga masing-masing.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-dark text-white">3</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Pencatatan Posyandu Balita Melati</h5>
                                        <p class="text-muted small mb-2">
                                            Kader Posyandu memasukkan data balita (BB, TB, LK). Sistem secara otomatis mengkalkulasi status gizi balita sesuai indikator Kemenkes / WHO. Kader & orang tua dapat langsung mencetak surat hasil pemeriksaan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="step-number bg-dark text-white">4</div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Monitoring Bank Digital & Kas Sampah</h5>
                                        <p class="text-muted small mb-2">
                                            Admin dapat memantau perputaran saldo Bank Digital Warga, membantu warga lansia menyetor saldo tunai di pos RT, serta mengoperasikan simulator/reader IoT RFID.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. FAQ (TANYA JAWAB) -->
                <div class="tab-pane fade" id="pills-faq" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-circle-question text-success me-1"></i> Apakah data warga aman di aplikasi ini?
                                </h6>
                                <p class="text-muted small mb-0">
                                    Sangat aman. Setiap akun warga hanya memiliki akses ke Kartu Keluarga dan data tagihannya sendiri. Buku Kas RT dan data kependudukan sensitif hanya dapat diakses oleh Admin Pengurus RT/RW yang berwenang.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-circle-question text-success me-1"></i> Bagaimana jika saldo Bank Warga saya kurang saat bayar iuran?
                                </h6>
                                <p class="text-muted small mb-0">
                                    Sistem akan memberikan peringatan berapa kekurangan saldo Anda. Anda bisa langsung melakukan Top-Up via Transfer Bank/E-Wallet dalam beberapa detik, atau menyetor sampah daur ulang di Bank Sampah RT.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-circle-question text-success me-1"></i> Apa fungsi alat IoT di Pos RT?
                                </h6>
                                <p class="text-muted small mb-0">
                                    Alat IoT pembaca e-KTP memungkinkan warga melakukan verifikasi cepat kartu identitas, memvalidasi pinjaman koperasi tanpa antre, dan mengecek saldo rekening kas warga hanya dengan sekali tap kartu.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-custom p-4 bg-white h-100">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-circle-question text-success me-1"></i> Siapa yang harus saya hubungi jika ada kendala?
                                </h6>
                                <p class="text-muted small mb-0">
                                    Anda dapat langsung menghubungi Ketua RT 06 (Dimas), Ketua RT 07 (Bambang), atau Sekretariat RW 19 (Fabian) melalui nomor kontak pengurus yang tersedia di bawah.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Tentang Wilayah & Kepengurusan RW 19 -->
    <section class="py-5" id="tentang">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-bold mb-2">
                    Struktur Wilayah & Pengurus
                </span>
                <h2 class="fw-bold text-dark mb-2">Rukun Warga 19 Kelurahan Bekasi Jaya</h2>
                <p class="text-muted">Kecamatan Bekasi Timur, Kota Bekasi • Jawa Barat 17113</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Ketua RW 19 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card-custom p-4 text-center bg-white h-100 border">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-inline-block mb-3" style="width: 80px; height: 80px; line-height: 50px;">
                            <i class="fa-solid fa-user-tie fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-1">FABIAN</h5>
                        <p class="text-success fw-semibold small mb-2">Ketua RW 19</p>
                        <p class="text-muted small mb-0">Penanggung jawab koordinasi wilayah RT 06 & RT 07 Kelurahan Bekasi Jaya.</p>
                    </div>
                </div>

                <!-- Ketua RT 06 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card-custom p-4 text-center bg-white h-100 border">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-block mb-3" style="width: 80px; height: 80px; line-height: 50px;">
                            <i class="fa-solid fa-user-shield fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-1">DIMAS</h5>
                        <p class="text-primary fw-semibold small mb-2">Ketua RT 06 / RW 19</p>
                        <p class="text-muted small mb-0">Pelayanan administrasi, surat pengantar, dan ketertiban warga wilayah RT 06.</p>
                    </div>
                </div>

                <!-- Ketua RT 07 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card-custom p-4 text-center bg-white h-100 border">
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-inline-block mb-3" style="width: 80px; height: 80px; line-height: 50px;">
                            <i class="fa-solid fa-user-shield fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-1">BAMBANG</h5>
                        <p class="text-info fw-semibold small mb-2">Ketua RT 07 / RW 19</p>
                        <p class="text-muted small mb-0">Pelayanan administrasi, surat pengantar, dan ketertiban warga wilayah RT 07.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-5" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white;" id="kontak">
        <div class="container text-center py-4">
            <h2 class="fw-bold mb-3">Siap Menikmati Layanan Warga yang Mudah & Cepat?</h2>
            <p class="opacity-90 max-w-700 mx-auto mb-4 fs-5">
                Masuk ke akun Anda sekarang untuk mengecek KK, mengajukan surat, dan membayar iuran secara online.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-light text-success fw-bold rounded-pill px-5 py-3 fs-6 shadow">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang ke Akun Anda
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light fw-bold rounded-pill px-5 py-3 fs-6">
                    <i class="fa-solid fa-user-plus me-2"></i> Registrasi Akun Warga
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Resmi -->
    <footer class="footer-landing">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo" style="width: 36px; height: 36px;">
                        <h5 class="fw-bold text-white mb-0">E-RT RW BERSATU</h5>
                    </div>
                    <p class="small text-secondary mb-3">
                        Sistem Informasi Manajemen Pelayanan Warga RT 06 & RT 07, Rukun Warga 19, Kelurahan Bekasi Jaya, Kecamatan Bekasi Timur, Kota Bekasi, Jawa Barat.
                    </p>
                    <div class="small text-muted">
                        &copy; 2026 E-RT RW Digital Bersatu. Dikembangkan dengan Laravel Framework.
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="mb-3">Tautan Cepat</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#layanan">Layanan & Fitur</a></li>
                        <li><a href="#panduan">Panduan Penggunaan</a></li>
                        <li><a href="{{ route('login') }}">Halaman Login</a></li>
                        <li><a href="{{ route('register') }}">Pendaftaran Warga</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-12">
                    <h6 class="mb-3">Sekretariat RW 19</h6>
                    <p class="small text-secondary mb-2">
                        <i class="fa-solid fa-location-dot text-success me-2"></i>
                        Balai Warga RW 19, Jl. Melati / Jl. Mawar, Kelurahan Bekasi Jaya, Bekasi Timur 17113.
                    </p>
                    <p class="small text-secondary mb-1">
                        <i class="fa-solid fa-clock text-success me-2"></i>
                        Pelayanan Online: 24 Jam Nonstop
                    </p>
                    <p class="small text-secondary mb-0">
                        <i class="fa-solid fa-phone text-success me-2"></i>
                        Pos RT 06 (Dimas) • Pos RT 07 (Bambang)
                    </p>
                </div>
            </div>
        </div>
    </footer>

    </div>
    <!-- End of #landing-shell-container -->

    <!-- Floating Mode Switcher Button (Pojok Kanan Bawah) -->
    <div class="floating-mode-badge shadow" title="Ganti Mode Tampilan Halaman">
        <button type="button" class="btn-float btn-mode-desktop" id="btnFloatDesktop" onclick="setViewMode('desktop')" title="Beralih ke Tampilan Desktop">
            <i class="fa-solid fa-desktop me-1"></i> Desktop
        </button>
        <button type="button" class="btn-float btn-mode-phone" id="btnFloatPhone" onclick="setViewMode('phone')" title="Beralih ke Tampilan Phone / HP">
            <i class="fa-solid fa-mobile-screen-button me-1"></i> Phone
        </button>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

            // Toggle active state di seluruh tombol switch
            document.querySelectorAll('.btn-mode-desktop, #btnFloatDesktop').forEach(function(el) {
                el.classList.toggle('active', mode === 'desktop');
            });
            document.querySelectorAll('.btn-mode-phone, #btnFloatPhone').forEach(function(el) {
                el.classList.toggle('active', mode === 'phone');
            });

            // Sesuaikan meta viewport
            var metaVp = document.querySelector('meta[name="viewport"]');
            if (metaVp) {
                if (mode === 'desktop' && window.innerWidth <= 768) {
                    metaVp.setAttribute('content', 'width=1100, initial-scale=0.35, maximum-scale=2.0, user-scalable=yes');
                } else {
                    metaVp.setAttribute('content', 'width=device-width, initial-scale=1.0');
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
