@extends('layouts.app')

@section('title', 'Data Kependudukan Warga')
@section('page_title', 'Data Warga RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Stat Cards -->
    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Total Penduduk RW 19</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalWarga) }} Jiwa</h3>
            <small class="text-muted">Total warga di lingkungan RW 19</small>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 bg-white border-start border-success border-4">
            <span class="text-muted small fw-bold text-uppercase">Wilayah RT 06</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($totalWargaRt06) }} Jiwa</h3>
            <small class="text-muted">Ketua RT: Dimas</small>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 bg-white border-start border-primary border-4">
            <span class="text-muted small fw-bold text-uppercase">Wilayah RT 07</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format($totalWargaRt07) }} Jiwa</h3>
            <small class="text-muted">Ketua RT: Bambang</small>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 bg-white border-start border-warning border-4">
            <span class="text-muted small fw-bold text-uppercase">Kepala Keluarga (KK)</span>
            <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($totalKK) }} KK</h3>
            <small class="text-muted">Terbagi di RT 06 & RT 07</small>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Daftar Induk Kependudukan Warga</h5>
            <small class="text-muted">Kelola data administrasi kependudukan warga RT 06 dan RT 07</small>
        </div>
        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
            <a href="{{ route('warga.kk') }}" class="btn btn-outline-primary rounded-pill px-3 flex-grow-1 flex-md-grow-0">
                <i class="fa-solid fa-address-card me-1"></i> Kelola Kartu Keluarga
            </a>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-primary-custom rounded-pill px-3 flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#modalTambahWarga">
                    <i class="fa-solid fa-user-plus me-1"></i> Tambah Warga
                </button>
            @endif
        </div>
    </div>

    <!-- Main Table -->
    <div class="col-12">
        <div class="card-custom p-4">
            <!-- Filter & Search -->
            <form action="{{ route('warga.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, NIK, No KK, atau alamat..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="rt" class="form-select form-select-sm">
                        <option value="">Semua Wilayah RT (RW 19)</option>
                        <option value="06" {{ request('rt') == '06' ? 'selected' : '' }}>RT 06 (Ketua: Dimas)</option>
                        <option value="07" {{ request('rt') == '07' ? 'selected' : '' }}>RT 07 (Ketua: Bambang)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Semua Peran</option>
                        <option value="Warga" {{ request('role') == 'Warga' ? 'selected' : '' }}>Warga</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin / Pengurus</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter Data</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('warga.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIK / No KK</th>
                            <th>L/P</th>
                            <th>Alamat & Wilayah RT</th>
                            <th>No HP</th>
                            <th>Status Keluarga</th>
                            <th>Role</th>
                            @if(auth()->user()->isAdmin())
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wargaList as $index => $w)
                            @php
                                $badgeRt = ($w->rt == '07') ? 'bg-primary text-primary' : 'bg-success text-success';
                            @endphp
                            <tr>
                                <td class="small">{{ $wargaList->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('img/' . ($w->foto ?? 'profile.jpg')) }}" 
                                             alt="{{ $w->nama }}" 
                                             class="rounded-circle border" 
                                             style="width: 34px; height: 34px; object-fit: cover;"
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($w->nama) }}&background=059669&color=fff'">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $w->nama }}</div>
                                            <small class="text-muted">{{ '@' . $w->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">
                                    <div><strong>NIK:</strong> {{ $w->nik ?? '-' }}</div>
                                    <div class="text-muted"><strong>KK:</strong> {{ $w->no_kk ?? '-' }}</div>
                                </td>
                                <td class="small">{{ $w->kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                                <td class="small">
                                    <div class="text-truncate" style="max-width: 220px;" title="{{ $w->alamat }}">{{ $w->alamat ?? '-' }}</div>
                                    <span class="badge bg-opacity-10 {{ $badgeRt }} border rounded-pill px-2 py-1 fw-bold">
                                        RT {{ $w->rt ?? '06' }} / RW {{ $w->rw ?? '19' }}
                                    </span>
                                </td>
                                <td class="small">{{ $w->no_hp ?? '-' }}</td>
                                <td class="small">{{ $w->status_keluarga ?? 'Warga' }}</td>
                                <td>
                                    <span class="badge-role {{ $w->isAdmin() ? 'admin' : 'warga' }}">
                                        {{ $w->role }}
                                    </span>
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-2" data-bs-toggle="modal" data-bs-target="#modalEditWarga{{ $w->id }}" title="Edit Warga">
                                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                                            </button>
                                            @if($w->id !== auth()->id())
                                                <form action="{{ route('warga.destroy', $w->id) }}" method="POST" onsubmit="return confirm('Hapus data warga ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light border py-1 px-2 rounded-2 text-danger" title="Hapus">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada data warga terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $wargaList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Warga -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalTambahWarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Data Warga Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warga.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap sesuai KTP" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="username_warga" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="nama@gmail.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Password Akun <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="nik" class="form-control" placeholder="16 digit NIK">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">No Kartu Keluarga (KK)</label>
                            <input type="text" name="no_kk" class="form-control" placeholder="16 digit No KK">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="kelamin" class="form-select" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Wilayah RT <span class="text-danger">*</span></label>
                            <select name="rt" class="form-select" required>
                                <option value="06">RT 06 (Ketua: Dimas)</option>
                                <option value="07">RT 07 (Ketua: Bambang)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Wilayah RW</label>
                            <input type="text" name="rw" class="form-control" value="19" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status Dalam Keluarga</label>
                            <select name="status_keluarga" class="form-select">
                                <option value="Kepala Keluarga">Kepala Keluarga</option>
                                <option value="Istri">Istri</option>
                                <option value="Anak">Anak</option>
                                <option value="Famili Lain">Famili Lain</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Peran Akun (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="Warga">Warga</option>
                                <option value="Admin">Admin / Pengurus</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">No HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" placeholder="Karyawan Swasta / Wiraswasta">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" class="form-control" placeholder="Jl. Melati Blok X No. Y"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Warga</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT WARGA DILETAKKAN DI LUAR TABEL AGAR BEKERJA 100% -->
@foreach($wargaList as $w)
    <div class="modal fade" id="modalEditWarga{{ $w->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Data Warga: {{ $w->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('warga.update', $w->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="{{ $w->nama }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $w->username }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $w->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Password Baru (Kosongkan jika tetap)</label>
                                <input type="password" name="password" class="form-control" placeholder="Ketik jika ingin ganti">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">NIK</label>
                                <input type="text" name="nik" class="form-control" value="{{ $w->nik }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">No Kartu Keluarga (KK)</label>
                                <input type="text" name="no_kk" class="form-control" value="{{ $w->no_kk }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Jenis Kelamin</label>
                                <select name="kelamin" class="form-select">
                                    <option value="Laki-laki" {{ $w->kelamin === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ $w->kelamin === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Wilayah RT</label>
                                <select name="rt" class="form-select">
                                    <option value="06" {{ $w->rt === '06' ? 'selected' : '' }}>RT 06 (Ketua: Dimas)</option>
                                    <option value="07" {{ $w->rt === '07' ? 'selected' : '' }}>RT 07 (Ketua: Bambang)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Wilayah RW</label>
                                <input type="text" name="rw" class="form-control" value="{{ $w->rw ?? '19' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status Keluarga</label>
                                <select name="status_keluarga" class="form-select">
                                    <option value="Kepala Keluarga" {{ $w->status_keluarga === 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                                    <option value="Istri" {{ $w->status_keluarga === 'Istri' ? 'selected' : '' }}>Istri</option>
                                    <option value="Anak" {{ $w->status_keluarga === 'Anak' ? 'selected' : '' }}>Anak</option>
                                    <option value="Famili Lain" {{ $w->status_keluarga === 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Peran (Role)</label>
                                <select name="role" class="form-select">
                                    <option value="Warga" {{ $w->role === 'Warga' ? 'selected' : '' }}>Warga</option>
                                    <option value="Admin" {{ $w->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">No HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ $w->no_hp }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control" value="{{ $w->pekerjaan }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Alamat Lengkap</label>
                                <textarea name="alamat" rows="2" class="form-control">{{ $w->alamat }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endif
@endsection
