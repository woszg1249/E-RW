<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - E-RT RW Digital Bersatu</title>

    <!-- Pre-load View Mode & Theme Script -->
    <script>
        (function() {
            var savedMode = localStorage.getItem('erw_view_mode') || 'desktop';
            document.documentElement.classList.add(savedMode === 'phone' ? 'mode-phone' : 'mode-desktop');

            var savedTheme = localStorage.getItem('erw_theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom Modern Styling -->
    <style>
        :root {
            --primary: #059669;
            --primary-hover: #047857;
            --primary-light: #ecfdf5;
            --secondary: #0284c7;
            --dark: #0f172a;
            --sidebar-width: 260px;
            --body-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #334155;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            max-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        #sidebar::-webkit-scrollbar {
            width: 5px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        #sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-brand {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f1f5f9;
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 10;
        }

        .sidebar-brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .sidebar-brand h6 {
            margin: 0;
            font-weight: 800;
            font-size: 1.05rem;
            color: #0f172a;
            line-height: 1.2;
        }

        .sidebar-brand span {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 500;
        }

        .sidebar-menu {
            padding: 16px 12px 100px 12px;
            flex: 1;
        }

        .menu-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 700;
            padding: 12px 14px 6px;
        }

        .nav-item-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 3px;
            transition: all 0.2s ease;
        }

        .nav-item-custom:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .nav-item-custom.active {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .nav-item-custom i {
            width: 20px;
            font-size: 1rem;
            text-align: center;
        }

        /* Main Content Wrapper */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        #content-wrapper.expanded {
            margin-left: 0;
        }

        /* Top Navbar */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            cursor: pointer;
        }

        .user-avatar-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
        }

        .badge-role {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .badge-role.admin {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .badge-role.warga {
            background-color: #dcfce7;
            color: #16a34a;
        }

        /* Page Content */
        .page-content {
            padding: 28px;
            flex: 1;
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 8px 18px;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            color: white;
            transform: translateY(-1px);
        }

        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show-mobile {
                margin-left: 0;
            }
            #content-wrapper {
                margin-left: 0;
            }
            .page-content {
                padding: 18px;
            }
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

        /* Bottom Navigation Bar untuk Phone Mode */
        #mobileBottomNav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            z-index: 1050;
            padding: 6px 12px;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
        }

        #mobileBottomNav .nav-mobile-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 6px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        #mobileBottomNav .nav-mobile-item.active,
        #mobileBottomNav .nav-mobile-item:hover {
            color: var(--primary);
        }

        #mobileBottomNav .nav-mobile-item i {
            font-size: 1.15rem;
            margin-bottom: 2px;
        }

        /* ===== ATURAN KETIKA MODE PHONE DIAKTIFKAN ===== */
        body.mode-phone {
            padding-bottom: 65px;
        }

        body.mode-phone #sidebar {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        body.mode-phone #sidebar.show-mobile {
            margin-left: 0 !important;
            display: flex !important;
        }

        body.mode-phone #content-wrapper {
            margin-left: 0 !important;
        }

        body.mode-phone .top-navbar {
            padding: 12px 18px;
        }

        body.mode-phone .page-content {
            padding: 16px 16px 70px 16px;
        }

        body.mode-phone #mobileBottomNav {
            display: flex !important;
            justify-content: space-around;
        }

        /* ===== ATURAN KETIKA MODE DESKTOP DIAKTIFKAN ===== */
        body.mode-desktop {
            background-color: var(--body-bg) !important;
            padding-bottom: 0;
        }

        body.mode-desktop #mobileBottomNav {
            display: none !important;
        }

        /* Jika diakses dari HP saat Mode Desktop dipilih: paksa canvas lebar desktop */
        @media (max-width: 991.98px) {
            body.mode-desktop {
                min-width: 1050px !important;
                overflow-x: auto !important;
            }
            body.mode-desktop #sidebar {
                margin-left: 0 !important;
                position: fixed !important;
                width: var(--sidebar-width) !important;
                display: flex !important;
            }
            body.mode-desktop #content-wrapper {
                margin-left: var(--sidebar-width) !important;
                min-width: calc(1050px - var(--sidebar-width)) !important;
            }
        }
        /* Dark Theme Variables & Styles */
        html.dark-theme,
        body.dark-theme {
            --body-bg: #0b1322;
            background-color: #0b1322 !important;
            color: #e2e8f0 !important;
        }

        body.dark-theme #sidebar {
            background-color: #151f32 !important;
            border-right-color: #1e293b !important;
        }

        body.dark-theme .sidebar-brand {
            background-color: #151f32 !important;
            border-bottom-color: #1e293b !important;
        }

        body.dark-theme .sidebar-brand h6 {
            color: #f8fafc !important;
        }

        body.dark-theme .sidebar-brand span {
            color: #94a3b8 !important;
        }

        body.dark-theme .nav-item-custom {
            color: #94a3b8 !important;
        }

        body.dark-theme .nav-item-custom:hover {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
        }

        body.dark-theme .nav-item-custom.active {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            color: #ffffff !important;
        }

        body.dark-theme .menu-header {
            color: #64748b !important;
        }

        body.dark-theme .top-navbar {
            background: #151f32 !important;
            border-bottom-color: #1e293b !important;
        }

        body.dark-theme .text-dark,
        body.dark-theme h1, body.dark-theme h2, body.dark-theme h3,
        body.dark-theme h4, body.dark-theme h5, body.dark-theme h6 {
            color: #f8fafc !important;
        }

        body.dark-theme .text-muted {
            color: #94a3b8 !important;
        }

        body.dark-theme .card,
        body.dark-theme .card-custom,
        body.dark-theme .card-stat,
        body.dark-theme .stat-card {
            background-color: #151f32 !important;
            border-color: #1e293b !important;
            color: #f8fafc !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
        }

        body.dark-theme .bg-white,
        body.dark-theme .bg-light {
            background-color: #151f32 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .border,
        body.dark-theme .border-bottom,
        body.dark-theme .border-top,
        body.dark-theme .border-start,
        body.dark-theme .border-end {
            border-color: #1e293b !important;
        }

        body.dark-theme .table {
            color: #e2e8f0 !important;
            border-color: #1e293b !important;
        }

        body.dark-theme .table th,
        body.dark-theme .table td {
            background-color: transparent !important;
            border-color: #1e293b !important;
            color: #e2e8f0 !important;
        }

        body.dark-theme .table thead th {
            background-color: #0f172a !important;
            color: #94a3b8 !important;
        }

        body.dark-theme .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        body.dark-theme .form-control,
        body.dark-theme .form-select,
        body.dark-theme .input-group-text {
            background-color: #0b1322 !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .form-control:focus,
        body.dark-theme .form-select:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.2) !important;
        }

        body.dark-theme .dropdown-menu,
        body.dark-theme .modal-content,
        body.dark-theme .offcanvas {
            background-color: #151f32 !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
        }

        body.dark-theme .dropdown-item {
            color: #cbd5e1 !important;
        }

        body.dark-theme .dropdown-item:hover {
            background-color: #1e293b !important;
            color: #ffffff !important;
        }

        body.dark-theme .user-pill {
            background: #0b1322 !important;
            border-color: #334155 !important;
        }

        body.dark-theme .mobile-bottom-nav {
            background: #151f32 !important;
            border-top-color: #1e293b !important;
        }

        body.dark-theme .mobile-nav-link {
            color: #94a3b8 !important;
        }

        body.dark-theme .mobile-nav-link.active {
            color: #10b981 !important;
        }

        /* Toggle Button Theme Style */
        .theme-toggle-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
            cursor: pointer;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .theme-toggle-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: scale(1.05);
        }

        body.dark-theme .theme-toggle-btn {
            background: #1e293b !important;
            color: #facc15 !important;
            border-color: #334155 !important;
        }

        body.dark-theme .theme-toggle-btn:hover {
            background: #334155 !important;
        }
    </style>
    @yield('styles')
</head>
<body>

        <!-- Sidebar -->
        <nav id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW Digital">
            <div>
                <h6>RT RW DIGITAL</h6>
                <span>RW 19 (RT 06 & RT 07)</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-header">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>

            <div class="menu-header">Administrasi & Warga</div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('warga.index') }}" class="nav-item-custom {{ request()->routeIs('warga.index') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Daftar Warga</span>
            </a>
            @endif
            <a href="{{ route('warga.kk') }}" class="nav-item-custom {{ request()->routeIs('warga.kk*') ? 'active' : '' }}">
                <i class="fa-solid fa-address-card"></i>
                <span>{{ auth()->user()->isAdmin() ? 'Kartu Keluarga (KK)' : 'Kartu Keluarga Saya' }}</span>
            </a>
            <a href="{{ route('surat.index') }}" class="nav-item-custom {{ request()->routeIs('surat.index') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope-open-text"></i>
                <span>Surat Pengantar RT</span>
            </a>

            <div class="menu-header">Keuangan & Iuran</div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('keuangan.index') }}" class="nav-item-custom {{ request()->routeIs('keuangan.index') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>Buku Kas RT</span>
            </a>
            @endif
            <a href="{{ route('tagihan.index') }}" class="nav-item-custom {{ request()->routeIs('tagihan.index') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Tagihan Iuran Warga</span>
            </a>
            <a href="{{ route('keuangan.laporan') }}" class="nav-item-custom {{ request()->routeIs('keuangan.laporan') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Laporan Keuangan</span>
            </a>

            <div class="menu-header">Pemberdayaan & Layanan</div>
            <a href="{{ route('kegiatan.index') }}" class="nav-item-custom {{ request()->routeIs('kegiatan.index') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Agenda & Kegiatan</span>
            </a>
            <a href="{{ route('umkm.index') }}" class="nav-item-custom {{ request()->routeIs('umkm.index') ? 'active' : '' }}">
                <i class="fa-solid fa-store"></i>
                <span>UMKM Warga</span>
            </a>
            <a href="{{ route('koperasi.index') }}" class="nav-item-custom {{ request()->routeIs('koperasi.index') ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <span>Koperasi & IoT RFID</span>
            </a>
            <a href="{{ route('bank-sampah.index') }}" class="nav-item-custom {{ request()->routeIs('bank-sampah.index') ? 'active' : '' }}">
                <i class="fa-solid fa-recycle"></i>
                <span>Bank Sampah</span>
            </a>
            <a href="{{ route('bank-warga.index') }}" class="nav-item-custom {{ request()->routeIs('bank-warga.index') ? 'active' : '' }}">
                <i class="fa-solid fa-building-columns"></i>
                <span>Bank Digital Warga</span>
            </a>
            <a href="{{ route('posyandu.index') }}" class="nav-item-custom {{ request()->routeIs('posyandu.index') ? 'active' : '' }}">
                <i class="fa-solid fa-baby"></i>
                <span>Posyandu Balita</span>
            </a>
            <a href="{{ route('aset.index') }}" class="nav-item-custom {{ request()->routeIs('aset.index') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventaris Aset RT</span>
            </a>

            <div class="menu-header">Pengaturan</div>
            <a href="{{ route('profil.index') }}" class="nav-item-custom {{ request()->routeIs('profil.index') ? 'active' : '' }}">
                <i class="fa-solid fa-user-gear"></i>
                <span>Profil Akun</span>
            </a>

            <div class="menu-header">Bantuan & Panduan</div>
            <a href="{{ route('panduan') }}" class="nav-item-custom {{ request()->routeIs('panduan') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open-reader"></i>
                <span>Panduan Aplikasi</span>
            </a>
            <a href="{{ route('landing') }}" class="nav-item-custom">
                <i class="fa-solid fa-house-chimney"></i>
                <span>Halaman Utama</span>
            </a>
        </div>
    </nav>

    <!-- Content Wrapper -->
    <div id="content-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border-0 shadow-none" id="sidebarToggle" type="button">
                    <i class="fa-solid fa-bars-staggered text-muted fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Sistem Informasi E-RT RW Digital')</h5>
                    <small class="text-muted">Kelurahan Bekasi Jaya, Bekasi Timur</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Theme Mode Toggle (Dark / Light) -->
                <button type="button" class="theme-toggle-btn me-1" id="themeToggleBtn" onclick="toggleTheme()" title="Ubah Mode Gelap / Terang">
                    <i class="fa-solid fa-moon theme-icon-dark"></i>
                    <i class="fa-solid fa-sun theme-icon-light d-none"></i>
                </button>

                <!-- Desktop & Phone Mode Switcher di Top Navbar -->
                <div class="view-mode-switcher me-1">
                    <button type="button" class="btn-mode btn-mode-desktop" onclick="setViewMode('desktop')" title="Beralih ke Mode Desktop (Layar Penuh Komputer)">
                        <i class="fa-solid fa-desktop"></i> <span class="d-none d-sm-inline">Desktop</span>
                    </button>
                    <button type="button" class="btn-mode btn-mode-phone" onclick="setViewMode('phone')" title="Beralih ke Mode Phone (Layar Smartphone)">
                        <i class="fa-solid fa-mobile-screen-button"></i> <span class="d-none d-sm-inline">Phone</span>
                    </button>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <div class="user-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('img/' . (auth()->user()->foto ?? 'profile.jpg')) }}" 
                             alt="{{ auth()->user()->nama }}" 
                             class="user-avatar-img"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&background=059669&color=fff'">
                        <div class="d-none d-sm-block text-start">
                            <div class="fw-bold text-dark lh-sm" style="font-size: 0.85rem;">{{ auth()->user()->nama }}</div>
                            <span class="badge-role {{ auth()->user()->isAdmin() ? 'admin' : 'warga' }}">
                                @if(auth()->user()->username === 'fabian')
                                    Ketua RW 19
                                @elseif(auth()->user()->username === 'dimas')
                                    Ketua RT 06
                                @elseif(auth()->user()->username === 'rt07')
                                    Ketua RT 07
                                @elseif(auth()->user()->isAdmin())
                                    Admin • RT {{ auth()->user()->rt ?? '07' }}
                                @else
                                    Warga • RT {{ auth()->user()->rt ?? '06' }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 mt-2 p-2">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <p class="mb-0 fw-bold">{{ auth()->user()->nama }}</p>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </div>
                        </li>
                        <li><a class="dropdown-item py-2" href="{{ route('profil.index') }}"><i class="fa-solid fa-user-pen me-2 text-primary"></i> Edit Profil</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('tagihan.index') }}"><i class="fa-solid fa-receipt me-2 text-info"></i> Tagihan Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Page Content -->
        <main class="page-content">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-info fs-5 me-2"></i>
                        <div>{{ session('info') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-white border-top py-3 px-4 text-center text-muted" style="font-size: 0.85rem;">
            &copy; {{ date('Y') }} <strong>E-RT RW Digital Bersatu</strong> • Dikembangkan dengan Laravel Framework &bull; Wilayah RW 19 (RT 06 & RT 07)
        </footer>
    </div>
    <!-- End of #content-wrapper -->

    <!-- Mobile Bottom Navigation Bar (Phone Mode) -->
    <nav id="mobileBottomNav">
        <a href="{{ route('dashboard') }}" class="nav-mobile-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('warga.kk') }}" class="nav-mobile-item {{ request()->routeIs('warga.kk*') ? 'active' : '' }}">
            <i class="fa-solid fa-address-card"></i>
            <span>KK Saya</span>
        </a>
        <a href="{{ route('tagihan.index') }}" class="nav-mobile-item {{ request()->routeIs('tagihan.index') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i>
            <span>Iuran</span>
        </a>
        <a href="{{ route('bank-warga.index') }}" class="nav-mobile-item {{ request()->routeIs('bank-warga.index') ? 'active' : '' }}">
            <i class="fa-solid fa-building-columns"></i>
            <span>Bank Warga</span>
        </a>
        <button type="button" class="nav-mobile-item border-0 bg-transparent" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMobileMenu">
            <i class="fa-solid fa-bars-staggered"></i>
            <span>Lainnya</span>
        </button>
    </nav>

    <!-- Offcanvas Mobile Drawer Menu (Phone Mode) -->
    <div class="offcanvas offcanvas-bottom rounded-top-4" tabindex="-1" id="offcanvasMobileMenu" style="max-height: 85vh; max-width: 480px; margin: 0 auto; left: 0; right: 0;">
        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo" style="width: 32px; height: 32px;">
                <div>
                    <h6 class="offcanvas-title fw-bold mb-0">Menu Layanan Warga</h6>
                    <small class="text-muted" style="font-size: 0.72rem;">RW 19 (RT 06 & RT 07) Bekasi Jaya</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            <div class="row g-2 mb-3">
                <div class="col-4">
                    <a href="{{ route('surat.index') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-envelope-open-text fs-4 text-primary mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Surat Pengantar</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('bank-sampah.index') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-recycle fs-4 text-success mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Bank Sampah</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('posyandu.index') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-baby fs-4 text-danger mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Posyandu Balita</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('koperasi.index') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-hand-holding-dollar fs-4 text-warning mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Koperasi & IoT</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('umkm.index') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-store fs-4 text-info mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">UMKM Warga</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('aset.index') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-boxes-stacked fs-4 text-secondary mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Inventaris Aset</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('kegiatan.index') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-calendar-check fs-4 text-primary mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Kegiatan</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('keuangan.laporan') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-file-invoice-dollar fs-4 text-success mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Laporan Kas</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('panduan') }}" class="p-3 bg-light rounded-3 text-center d-block text-decoration-none text-dark border">
                        <i class="fa-solid fa-book-open-reader fs-4 text-primary mb-1 d-block"></i>
                        <span class="small fw-semibold" style="font-size: 0.75rem;">Panduan App</span>
                    </a>
                </div>
            </div>

            <div class="border-top pt-2">
                <a href="{{ route('profil.index') }}" class="d-flex align-items-center gap-2 p-2 text-decoration-none text-dark rounded-3 hover-bg">
                    <i class="fa-solid fa-user-gear text-secondary"></i>
                    <span class="small fw-semibold">Pengaturan Profil Akun</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill mt-2 py-2 small fw-bold">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

            // Toggle active state di tombol switch top navbar
            document.querySelectorAll('.btn-mode-desktop').forEach(function(el) {
                el.classList.toggle('active', mode === 'desktop');
            });
            document.querySelectorAll('.btn-mode-phone').forEach(function(el) {
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
            // Inisialisasi Tema Tersimpan
            var currentTheme = localStorage.getItem('erw_theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            applyTheme(currentTheme);

            // Inisialisasi Mode Tampilan Tersimpan (Default: Desktop)
            var currentMode = localStorage.getItem('erw_view_mode') || 'desktop';
            applyViewMode(currentMode);

            // Sidebar Toggle Script
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content-wrapper');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    if (window.innerWidth <= 992) {
                        sidebar.classList.toggle('show-mobile');
                    } else {
                        sidebar.classList.toggle('collapsed');
                        content.classList.toggle('expanded');
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
