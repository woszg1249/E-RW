@extends('layouts.app')

@section('title', 'Laporan, Aspirasi & Pengaduan Warga')
@section('page_title', 'Laporan, Aspirasi & Pengaduan Warga')

@section('content')
<div class="row g-4">
    <!-- Header Banner -->
    <div class="col-12">
        <div class="card-custom p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="fa-solid fa-bullhorn text-danger me-2"></i>Layanan Aspirasi &amp; Pengaduan Warga RW 19</h5>
                <p class="text-muted small mb-0">Sampaikan keluhan, laporan lingkungan, atau usulan kegiatan langsung ke pengurus RT / RW.</p>
            </div>
            <button type="button" class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalBuatPengaduan">
                <i class="fa-solid fa-plus me-1"></i> Buat Laporan / Aspirasi
            </button>
        </div>
    </div>

    <!-- Statistik -->
    <div class="col-6 col-md-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-muted">Total Laporan</div>
            <div class="fs-4 fw-bold text-dark">{{ $totalLaporan }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-muted">Menunggu</div>
            <div class="fs-4 fw-bold text-warning">{{ $totalMenunggu }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-muted">Diproses</div>
            <div class="fs-4 fw-bold text-primary">{{ $totalDiproses }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom p-3 h-100">
            <div class="small text-muted">Selesai</div>
            <div class="fs-4 fw-bold text-success">{{ $totalSelesai }}</div>
        </div>
    </div>

    <!-- Filter & Daftar Laporan -->
    <div class="col-12">
        <div class="card-custom p-4">
            <form action="{{ route('pengaduan.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Cari judul, isi, atau kategori...">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="Menunggu" {{ request('status') === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Diproses" {{ request('status') === 'Diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            @if(auth()->user()->isAdmin())
                                <th>Pelapor</th>
                            @endif
                            <th>Kategori &amp; Judul</th>
                            <th>Status</th>
                            <th>Tanggapan Pengurus</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengaduanList as $p)
                            @php
                                $badgeClass = match($p->status) {
                                    'Selesai' => 'bg-success bg-opacity-10 text-success border-success',
                                    'Diproses' => 'bg-primary bg-opacity-10 text-primary border-primary',
                                    'Ditolak' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                    default => 'bg-warning bg-opacity-10 text-warning border-warning',
                                };
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                @if(auth()->user()->isAdmin())
                                    <td>
                                        <div class="fw-bold text-dark">{{ $p->user->nama ?? '-' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">RT {{ $p->user->rt ?? '06' }}/RW {{ $p->user->rw ?? '19' }}</div>
                                    </td>
                                @endif
                                <td style="white-space: normal; min-width: 220px;">
                                    <span class="badge bg-light text-dark border mb-1">{{ $p->kategori }}</span>
                                    <div class="fw-bold text-dark">{{ $p->judul }}</div>
                                    <div class="text-muted">{{ \Illuminate\Support\Str::limit($p->isi_laporan, 100) }}</div>
                                    @if($p->lokasi)
                                        <div class="text-secondary mt-1" style="font-size: 0.75rem;"><i class="fa-solid fa-location-dot me-1"></i>{{ $p->lokasi }}</div>
                                    @endif
                                    @if($p->foto)
                                        <div class="mt-1">
                                            <a href="{{ asset('img/pengaduan/' . $p->foto) }}" target="_blank" class="text-primary text-decoration-none" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-image me-1"></i>Lihat Foto Lampiran
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge border {{ $badgeClass }} px-2 py-1">{{ $p->status }}</span>
                                </td>
                                <td style="white-space: normal; min-width: 180px;">
                                    @if($p->tanggapan_admin)
                                        <div class="text-dark">{{ $p->tanggapan_admin }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Oleh: {{ $p->ditanggapi_oleh ?? 'Pengurus' }}</div>
                                    @else
                                        <span class="text-muted fst-italic">Belum ada tanggapan</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(auth()->user()->isAdmin())
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1 me-1 btn-tanggapi"
                                                data-id="{{ $p->id }}"
                                                data-judul="{{ $p->judul }}"
                                                data-status="{{ $p->status }}"
                                                data-tanggapan="{{ $p->tanggapan_admin }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalTanggapi">
                                            <i class="fa-solid fa-reply"></i> Tanggapi
                                        </button>
                                    @endif
                                    @if(auth()->user()->isAdmin() || $p->id_user === auth()->id())
                                        <form action="{{ route('pengaduan.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus laporan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="text-center text-muted py-4">
                                    Belum ada laporan atau aspirasi warga.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pengaduanList->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat Pengaduan / Aspirasi -->
<div class="modal fade" id="modalBuatPengaduan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-bullhorn text-danger me-2"></i>Buat Laporan / Aspirasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="Infrastruktur & Jalan">Infrastruktur &amp; Jalan</option>
                            <option value="Kebersihan & Sampah">Kebersihan &amp; Sampah</option>
                            <option value="Keamanan & Ketertiban">Keamanan &amp; Ketertiban</option>
                            <option value="Layanan Administrasi">Layanan Administrasi</option>
                            <option value="Aspirasi / Usulan">Aspirasi / Usulan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Judul Laporan / Aspirasi</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Lampu jalan RT 06 mati" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lokasi (Opsional)</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gang Melati RT 06">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Detail Laporan / Aspirasi</label>
                        <textarea name="isi_laporan" rows="3" class="form-control" placeholder="Jelaskan kronologi atau usulan Anda..." required></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Foto Bukti (Opsional, Maks 2MB)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<!-- Modal Tanggapi Admin -->
<div class="modal fade" id="modalTanggapi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="formTanggapi" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tanggapi Laporan Warga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Judul Laporan</label>
                        <input type="text" id="tanggapiJudul" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status Penanganan</label>
                        <select name="status" id="tanggapiStatus" class="form-select" required>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Tanggapan Pengurus RT / RW</label>
                        <textarea name="tanggapan_admin" id="tanggapiIsi" rows="3" class="form-control" placeholder="Tulis tindak lanjut atau tanggapan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Tanggapan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.querySelectorAll('.btn-tanggapi').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('tanggapiJudul').value = this.dataset.judul;
        document.getElementById('tanggapiStatus').value = this.dataset.status;
        document.getElementById('tanggapiIsi').value = this.dataset.tanggapan || '';
        document.getElementById('formTanggapi').action = '{{ url("/pengaduan") }}/' + this.dataset.id + '/status';
    });
});
</script>
@endsection
