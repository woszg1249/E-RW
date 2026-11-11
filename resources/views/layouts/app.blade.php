<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - E-RT RW Digital Bersatu</title>

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

            <div class="d-flex align-items-center gap-3">
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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content-wrapper');
            const toggleBtn = document.getElementById('sidebarToggle');

            toggleBtn.addEventListener('click', function () {
                if (window.innerWidth <= 992) {
                    sidebar.classList.toggle('show-mobile');
                } else {
                    sidebar.classList.toggle('collapsed');
                    content.classList.toggle('expanded');
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
