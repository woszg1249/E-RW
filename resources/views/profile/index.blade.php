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
