@extends('layouts.app')

@section('title', 'Layanan Surat Pengantar')
@section('page_title', 'Layanan Surat Keterangan / Pengantar RT (RW 19)')

@section('content')
<div class="row g-4">
    <!-- Stat Cards -->
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Pengajuan Menunggu Persetujuan</span>
            <h3 class="fw-bold text-warning mb-0 mt-1">{{ $totalMenunggu }} Berkas</h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white">
            <span class="text-muted small fw-bold text-uppercase">Surat Telah Disetujui (Siap Cetak)</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ $totalDisetujui }} Berkas</h3>
        </div>
    </div>

    <!-- Main Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Pengajuan Surat Keterangan Warga</h5>
                    <p class="text-muted small mb-0">Ajukan surat pengantar resmi RT/RW secara daring tanpa antre manual</p>
                </div>

                <button class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAjukanSurat">
                    <i class="fa-solid fa-file-circle-plus me-1"></i> Ajukan Surat Baru
                </button>
            </div>

            <!-- Filter & Search -->
            <form action="{{ route('surat.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari pemohon, NIK, jenis surat..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="rt" class="form-select form-select-sm">
                        <option value="">Semua Wilayah RT (RW 19)</option>
                        <option value="06" {{ request('rt') == '06' ? 'selected' : '' }}>RT 06 (Ketua: Dimas)</option>
                        <option value="07" {{ request('rt') == '07' ? 'selected' : '' }}>RT 07 (Ketua: Bambang)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status Berkas</option>
                        <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui / Selesai</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter Surat</button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Pemohon & NIK</th>
                            <th>Wilayah RT</th>
                            <th>Jenis Surat</th>
                            <th>Keperluan / Tujuan</th>
                            <th>Tanggal Diajukan</th>
                            <th>Status Berkas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratList as $index => $s)
                            @php
                                $isApproved = in_array($s->status, ['Disetujui', 'ACC']);
                                $isRejected = ($s->status === 'Ditolak');
                                $rtDisplay = str_contains($s->nama_rt ?? '', '07') || ($s->user->rt ?? '') == '07' ? 'RT 07' : 'RT 06';
                            @endphp
                            <tr>
                                <td class="small">{{ $suratList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $s->nama }}</div>
                                    <small class="text-muted">NIK: {{ $s->nik ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $rtDisplay == 'RT 07' ? 'bg-primary' : 'bg-success' }} bg-opacity-10 {{ $rtDisplay == 'RT 07' ? 'text-primary' : 'text-success' }} border rounded-pill px-2 py-1 small fw-semibold">
                                        {{ $rtDisplay }} / RW 19
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1 fw-semibold">
                                        {{ $s->jenis_surat }}
                                    </span>
                                </td>
                                <td class="small" style="max-width: 230px;">{{ $s->tujuan }}</td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($s->tanggal_permohonan)->format('d M Y') }}</td>
                                <td>
                                    @if($isApproved)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-check me-1"></i> Disetujui
                                        </span>
                                    @elseif($isRejected)
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-xmark me-1"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-clock me-1"></i> Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @if($isApproved)
                                            <a href="{{ route('surat.cetak', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Cetak Surat Resmi">
                                                <i class="fa-solid fa-print me-1"></i> Cetak
                                            </a>
                                        @endif

                                        @if(auth()->user()->isAdmin())
                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalStatusSurat{{ $s->id }}" title="Ubah Status Verifikasi">
                                                <i class="fa-solid fa-sliders text-secondary"></i> Verifikasi
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada permohonan surat yang diajukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $suratList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajukan Surat Baru -->
<div class="modal fade" id="modalAjukanSurat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Pengajuan Surat Keterangan RT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('surat.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Wilayah RT Domisili Pemohon <span class="text-danger">*</span></label>
                        <select name="nama_rt" class="form-select" required>
                            <option value="RT 06" {{ (auth()->user()->rt ?? '06') == '06' ? 'selected' : '' }}>RT 06 (Ketua RT: Dimas)</option>
                            <option value="RT 07" {{ (auth()->user()->rt ?? '06') == '07' ? 'selected' : '' }}>RT 07 (Ketua RT: Bambang)</option>
                        </select>
                        <small class="text-muted">Surat akan diteruskan dan ditandatangani oleh Ketua RT wilayah yang dipilih.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jenis Surat Permohonan <span class="text-danger">*</span></label>
                        <select name="jenis_surat" class="form-select" required>
                            <option value="Surat Pengantar RT/RW">Surat Pengantar RT/RW</option>
                            <option value="Surat Keterangan Domisili">Surat Keterangan Domisili</option>
                            <option value="Surat Keterangan Usaha (SKU)">Surat Keterangan Usaha (SKU)</option>
                            <option value="Surat Keterangan Belum Menikah">Surat Keterangan Belum Menikah</option>
                            <option value="Surat Keterangan Tidak Mampu (SKTM)">Surat Keterangan Tidak Mampu (SKTM)</option>
                            <option value="Surat Keterangan Kematian">Surat Keterangan Kematian</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keperluan / Tujuan Pembuatan Surat <span class="text-danger">*</span></label>
                        <textarea name="tujuan" rows="3" class="form-control" placeholder="Contoh: Pengurusan KTP baru di Kelurahan / Pembukaan rekening bank" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan / Detail Tambahan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Catatan tambahan bila diperlukan">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VERIFIKASI SURAT (ADMIN) DILETAKKAN DI LUAR TABEL AGAR BEKERJA 100% -->
@if(auth()->user()->isAdmin())
    @foreach($suratList as $s)
        <div class="modal fade" id="modalStatusSurat{{ $s->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Verifikasi Surat #{{ $s->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('surat.status', $s->id) }}" method="POST">
                        @csrf
                        <div class="modal-body text-start">
                            <div class="p-3 bg-light rounded-3 mb-3 small">
                                <div><strong>Nama Pemohon:</strong> {{ $s->nama }} (NIK: {{ $s->nik ?? '-' }})</div>
                                <div><strong>Wilayah:</strong> {{ $s->nama_rt ?? 'RT 06' }} / {{ $s->nama_rw ?? 'RW 19' }}</div>
                                <div><strong>Jenis Surat:</strong> {{ $s->jenis_surat }}</div>
                                <div><strong>Keperluan:</strong> {{ $s->tujuan }}</div>
                                <div><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($s->tanggal_permohonan)->format('d F Y') }}</div>
                            </div>

                            <label class="form-label fw-semibold small">Pilih Keputusan Persetujuan</label>
                            <select name="status" class="form-select mb-2">
                                <option value="Disetujui" {{ in_array($s->status, ['Disetujui', 'ACC']) ? 'selected' : '' }}>Disetujui (Surat Resmi Siap Dicetak)</option>
                                <option value="Menunggu" {{ $s->status === 'Menunggu' ? 'selected' : '' }}>Menunggu Verifikasi Berkas</option>
                                <option value="Ditolak" {{ $s->status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Keputusan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
