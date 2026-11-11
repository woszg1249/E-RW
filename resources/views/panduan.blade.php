<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Panduan Penggunaan - E-RT RW Bersatu RW 19</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo_ert_rw.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .list-group-item.active {
            background-color: #059669;
            border-color: #059669;
        }
    </style>
</head>
<body class="py-5">
    <div class="container" style="max-width: 1050px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('landing') }}" class="btn btn-outline-dark rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Beranda
            </a>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-success fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-success fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Akun
                    </a>
                @endauth
            </div>
        </div>

        <div class="card-custom p-4 mb-4 text-center">
            <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo" style="width: 50px; height: 50px;" class="mb-2">
            <h3 class="fw-bold mb-1">Panduan Penggunaan Aplikasi E-RT RW Bersatu</h3>
            <p class="text-muted small mb-0">Petunjuk praktis operasional sistem pelayanan kependudukan & keuangan RW 19 (RT 06 & RT 07)</p>
        </div>

        @include('panduan_content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
