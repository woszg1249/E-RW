<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Warga - E-RT RW Digital Bersatu</title>
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
            padding: 30px 15px;
        }
        .register-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            max-width: 680px;
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
    </style>
</head>
<body>

    <div class="register-card">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark mb-1">Pendaftaran Akun Warga Baru</h4>
            <p class="text-muted small">Lengkapi formulir di bawah ini untuk terdaftar dalam sistem E-RT RW Digital</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 p-3 small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Ahmad Fauzi" value="{{ old('nama') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" value="{{ old('nik') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nomor Kartu Keluarga (KK)</label>
                    <input type="text" name="no_kk" class="form-control" placeholder="16 digit No KK" value="{{ old('no_kk') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="kelamin" class="form-select" required>
                        <option value="Laki-laki" {{ old('kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
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

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('no_hp') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">RT</label>
                    <input type="text" name="rt" class="form-control" value="{{ old('rt', '06') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">RW</label>
                    <input type="text" name="rw" class="form-control" value="{{ old('rw', '19') }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold small text-secondary">Alamat Domisili Lengkap</label>
                    <textarea name="alamat" rows="2" class="form-control" placeholder="Nama jalan, nomor rumah, gang, dll">{{ old('alamat') }}</textarea>
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

</body>
</html>
