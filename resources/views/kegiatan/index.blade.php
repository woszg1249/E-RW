@extends('layouts.app')

@section('title', 'Agenda & Kegiatan Warga')
@section('page_title', 'Agenda & Kegiatan RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">Agenda Kegiatan Lingkungan RT & RW</h5>
                <p class="text-muted small mb-0">Informasi kerja bakti, rapat warga, ronda malam, dan acara sosial kemasyarakatan</p>
            </div>

            @if(auth()->user()->isAdmin())
                <button class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahKegiatan">
                    <i class="fa-solid fa-calendar-plus me-1"></i> Tambah Kegiatan Baru
                </button>
            @endif
        </div>
    </div>

    <!-- Kegiatan Cards Grid -->
    @forelse($kegiatan as $k)
        <div class="col-md-6 col-lg-4">
            <div class="card-custom h-100 bg-white overflow-hidden d-flex flex-column">
                <div class="p-3 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <span class="badge bg-white text-success px-2 py-1 rounded-pill small mb-2">
                        <i class="fa-solid fa-calendar-day me-1"></i> {{ \Carbon\Carbon::parse($k->tanggal_mulai)->format('d M Y') }}
                    </span>
                    <h5 class="fw-bold mb-0 text-white">{{ $k->judul }}</h5>
                </div>

                <div class="p-4 flex-grow-1 d-flex flex-column justify-content-between">
                    <div>
                        <div class="small text-muted mb-2">
                            <i class="fa-regular fa-clock text-primary me-1"></i>
                            <strong>Waktu:</strong> {{ $k->waktu ?? 'Sesuai Jadwal' }}
                        </div>
                        <div class="small text-muted mb-3">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            <strong>Lokasi:</strong> {{ $k->lokasi }}
                        </div>
                        <p class="text-secondary small mb-0">
                            {{ $k->deskripsi ?? 'Tidak ada deskripsi tambahan.' }}
                        </p>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div class="border-top pt-3 mt-4 d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditKegiatan{{ $k->id }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </button>
                            <form action="{{ route('kegiatan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Edit Kegiatan -->
        @if(auth()->user()->isAdmin())
        <div class="modal fade" id="modalEditKegiatan{{ $k->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Edit Agenda Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('kegiatan.update', $k->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Judul Kegiatan</label>
                                <input type="text" name="judul" class="form-control" value="{{ $k->judul }}" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ $k->tanggal_mulai }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Tanggal Selesai (Opsional)</label>
                                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ $k->tanggal_selesai }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Waktu / Jam</label>
                                <input type="text" name="waktu" class="form-control" value="{{ $k->waktu }}" placeholder="Contoh: 08:00 - Selesai WIB">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" value="{{ $k->lokasi }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Deskripsi</label>
                                <textarea name="deskripsi" rows="3" class="form-control">{{ $k->deskripsi }}</textarea>
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
        @endif

    @empty
        <div class="col-12">
            <div class="card-custom p-5 text-center text-muted bg-white">
                <i class="fa-solid fa-calendar-xmark fs-1 mb-2 text-secondary"></i>
                <h6>Belum ada agenda kegiatan yang terdaftar.</h6>
            </div>
        </div>
    @endforelse

    <div class="col-12 mt-4">
        {{ $kegiatan->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah Kegiatan (Admin) -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalTambahKegiatan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Publikasikan Kegiatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kegiatan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Judul Kegiatan</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Kerja Bakti Massal Lingkungan RT" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="tanggal_selesai" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Waktu</label>
                        <input type="text" name="waktu" class="form-control" placeholder="Contoh: 07.30 WIB - Selesai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lapangan Serbaguna RT 06" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi Kegiatan</label>
                        <textarea name="deskripsi" rows="3" class="form-control" placeholder="Penjelasan rincian kegiatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Terbitkan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
