<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-RT RW Digital Bersatu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #059669 0%, #0284c7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            max-width: 440px;
            width: 100%;
            padding: 36px 32px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .brand-icon {
            width: 64px;
            height: 64px;
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
        .btn-login {
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
        .btn-login:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            transform: translateY(-1px);
            color: white;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" class="brand-icon">
            <h4 class="fw-bold text-dark mb-1">E-RT RW Bersatu</h4>
            <p class="text-muted small">Sistem Informasi Manajemen RW 19 (RT 06 & RT 07)</p>
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

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="login" id="loginInput" class="form-control border-start-0" placeholder="Masukkan username atau email" value="{{ old('login') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
            </button>

            <div class="text-center mb-3">
                <span class="text-muted small">Belum punya akun warga?</span>
                <a href="{{ route('register') }}" class="fw-bold text-success text-decoration-none small">Daftar Warga</a>
            </div>

            <div class="text-center pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="{{ route('landing') }}" class="text-decoration-none small text-muted">
                    <i class="fa-solid fa-arrow-left me-1"></i> Ke Beranda
                </a>
                <a href="{{ route('landing') }}#panduan" class="text-decoration-none small text-primary fw-semibold">
                    <i class="fa-solid fa-book-open me-1"></i> Panduan Aplikasi
                </a>
            </div>
        </form>
    </div>
</body>
</html>
