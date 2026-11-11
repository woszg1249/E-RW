@extends('layouts.app')

@section('title', 'Layanan Surat Pengantar')
@section('page_title', 'Layanan Surat Keterangan / Pengantar RT')

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
            <span class="text-muted small fw-bold text-uppercase">Surat Telah Disetujui</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ $totalDisetujui }} Berkas</h3>
        </div>
    </div>

    <!-- Main Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Pengajuan Surat Keterangan Warga</h5>
                    <p class="text-muted small mb-0">Ajukan surat pengantar resmi RT/RW secara daring tanpa perlu datang manual</p>
                </div>

                <button class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAjukanSurat">
                    <i class="fa-solid fa-file-circle-plus me-1"></i> Ajukan Surat Baru
                </button>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Pemohon</th>
                            <th>Jenis Surat</th>
                            <th>Keperluan / Tujuan</th>
                            <th>Tanggal Diajukan</th>
                            <th>Status Berkas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratList as $index => $s)
                            <tr>
                                <td class="small">{{ $suratList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $s->nama }}</div>
                                    <small class="text-muted">NIK: {{ $s->nik ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1 fw-semibold">
                                        {{ $s->jenis_surat }}
                                    </span>
                                </td>
                                <td class="small" style="max-width: 250px;">{{ $s->tujuan }}</td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($s->tanggal_permohonan)->format('d M Y') }}</td>
                                <td>
                                    @if($s->status === 'Disetujui')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-check me-1"></i> Disetujui
                                        </span>
                                    @elseif($s->status === 'Ditolak')
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
                                        @if($s->status === 'Disetujui')
                                            <a href="{{ route('surat.cetak', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Cetak Surat">
                                                <i class="fa-solid fa-print me-1"></i> Cetak
                                            </a>
                                        @endif

                                        @if(auth()->user()->isAdmin())
                                            <button class="btn btn-sm btn-light border rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalStatusSurat{{ $s->id }}" title="Ubah Status">
                                                <i class="fa-solid fa-sliders text-secondary"></i> Verifikasi
                                            </button>

                                            <!-- Modal Ubah Status (Admin) -->
                                            <div class="modal fade" id="modalStatusSurat{{ $s->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 rounded-4 shadow">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold">Verifikasi Surat: {{ $s->nama }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('surat.status', $s->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body text-start">
                                                                <div class="p-3 bg-light rounded-3 mb-3 small">
                                                                    <div><strong>Jenis Surat:</strong> {{ $s->jenis_surat }}</div>
                                                                    <div><strong>Keperluan:</strong> {{ $s->tujuan }}</div>
                                                                    <div><strong>Tanggal Pengajuan:</strong> {{ $s->tanggal_permohonan }}</div>
                                                                </div>

                                                                <label class="form-label fw-semibold small">Pilih Keputusan Persetujuan</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="Disetujui" {{ $s->status === 'Disetujui' ? 'selected' : '' }}>Disetujui (Surat Siap Dicetak)</option>
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada permohonan surat yang diajukan.</td>
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
                        <label class="form-label fw-semibold small">Jenis Surat Permohonan</label>
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
                        <label class="form-label fw-semibold small">Keperluan / Tujuan Pembuatan Surat</label>
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
@endsection
