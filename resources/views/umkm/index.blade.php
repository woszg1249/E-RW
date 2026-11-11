@extends('layouts.app')

@section('title', 'Direktori UMKM Warga')
@section('page_title', 'Etalase & UMKM Warga RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">Etalase Usaha Mikro, Kecil, & Menengah (UMKM) Warga</h5>
                <p class="text-muted small mb-0">Dukung produk dan jasa tetangga sekitar lingkungan RW 19 (RT 06 & RT 07)</p>
            </div>

            <button class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahUmkm">
                <i class="fa-solid fa-store me-1"></i> Daftarkan Usaha Anda
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="col-12">
        <div class="card-custom p-3 bg-white">
            <form action="{{ route('umkm.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama usaha, produk, makanan, jasa..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="kategori" class="form-select form-select-sm">
                        <option value="">Semua Kategori Usaha</option>
                        <option value="Kuliner / Makanan" {{ request('kategori') == 'Kuliner / Makanan' ? 'selected' : '' }}>Kuliner / Makanan & Minuman</option>
                        <option value="Toko Kelontong / Sembako" {{ request('kategori') == 'Toko Kelontong / Sembako' ? 'selected' : '' }}>Toko Kelontong / Sembako</option>
                        <option value="Jasa & Servis" {{ request('kategori') == 'Jasa & Servis' ? 'selected' : '' }}>Jasa & Servis</option>
                        <option value="Fashion & Pakaian" {{ request('kategori') == 'Fashion & Pakaian' ? 'selected' : '' }}>Fashion & Pakaian</option>
                        <option value="Kerajinan Tangan" {{ request('kategori') == 'Kerajinan Tangan' ? 'selected' : '' }}>Kerajinan Tangan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- UMKM Grid Cards -->
    @forelse($umkmList as $u)
        <div class="col-md-6 col-lg-4">
            <div class="card-custom h-100 bg-white overflow-hidden d-flex flex-column">
                <div style="height: 180px; background-color: #f1f5f9; position: relative;">
                    @if($u->foto)
                        <img src="{{ asset('img/' . $u->foto) }}" alt="{{ $u->nama_usaha }}" class="w-100 h-100 object-fit-cover" onerror="this.src='https://placehold.co/600x400?text={{ urlencode($u->nama_usaha) }}'">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted fs-1 bg-light">
                            <i class="fa-solid fa-store"></i>
                        </div>
                    @endif
                    <span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-3 px-3 py-1 rounded-pill small">
                        {{ $u->kategori ?? 'Umum' }}
                    </span>
                </div>

                <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">{{ $u->nama_usaha }}</h5>
                        <p class="text-secondary small mb-3">{{ Str::limit($u->deskripsi, 90) }}</p>

                        <div class="small text-muted mb-1">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $u->alamat ?? '-' }}
                        </div>
                        <div class="small text-muted mb-3">
                            <i class="fa-regular fa-clock text-primary me-1"></i> Buka: {{ $u->jam_operasional ?? '-' }}
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3 d-flex align-items-center justify-content-between">
                        @php
                            $waClean = preg_replace('/[^0-9]/', '', $u->whatsapp ?? '');
                            if (str_starts_with($waClean, '0')) {
                                $waClean = '62' . substr($waClean, 1);
                            }
                        @endphp
                        @if($waClean)
                            <a href="https://wa.me/{{ $waClean }}?text=Halo%2C+saya+warga+RT+06+tertarik+dengan+produk+{{ urlencode($u->nama_usaha) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
                                <i class="fa-brands fa-whatsapp me-1"></i> Hubungi WA
                            </a>
                        @else
                            <button class="btn btn-sm btn-light border rounded-pill px-3 text-muted" disabled>
                                Hubungi WA
                            </button>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <div class="d-flex gap-1">
                                <form action="{{ route('umkm.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus profil UMKM ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card-custom p-5 text-center text-muted bg-white">
                <i class="fa-solid fa-store-slash fs-1 mb-2 text-secondary"></i>
                <h6>Belum ada usaha UMKM terdaftar yang sesuai kriteria.</h6>
            </div>
        </div>
    @endforelse

    <div class="col-12 mt-4">
        {{ $umkmList->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah UMKM -->
<div class="modal fade" id="modalTambahUmkm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Daftarkan Usaha / UMKM Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Usaha / Toko <span class="text-danger">*</span></label>
                            <input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Warung Nasi Bu Siti" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kategori Usaha</label>
                            <select name="kategori" class="form-select">
                                <option value="Kuliner / Makanan">Kuliner / Makanan & Minuman</option>
                                <option value="Toko Kelontong / Sembako">Toko Kelontong / Sembako</option>
                                <option value="Jasa & Servis">Jasa & Servis</option>
                                <option value="Fashion & Pakaian">Fashion & Pakaian</option>
                                <option value="Kerajinan Tangan">Kerajinan Tangan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nomor WhatsApp Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('whatsapp', auth()->user()->no_hp ?? '') }}" required>
                            <small class="text-success" style="font-size: 0.78rem;"><i class="fa-solid fa-circle-check me-1"></i> Otomatis dari nomor HP akun Anda</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Jam Operasional</label>
                            <input type="text" name="jam_operasional" class="form-control" placeholder="Contoh: 08.00 - 21.00 WIB" value="{{ old('jam_operasional', '08.00 - 21.00 WIB') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Alamat / Lokasi Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="alamat" class="form-control" placeholder="Contoh: Jl. Melati No. 5, RT 06" value="{{ old('alamat', auth()->user()->alamat ?? '') }}" required>
                            <small class="text-success" style="font-size: 0.78rem;"><i class="fa-solid fa-circle-check me-1"></i> Otomatis dari alamat domisili akun Anda</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Foto Produk / Toko (Opsional)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi Singkat Usaha</label>
                            <textarea name="deskripsi" rows="3" class="form-control" placeholder="Jelaskan menu andalan, keunggulan, atau produk yang dijual..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Daftarkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
