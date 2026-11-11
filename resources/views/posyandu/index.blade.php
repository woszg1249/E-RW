@extends('layouts.app')

@section('title', 'Layanan Posyandu Balita')
@section('page_title', 'Layanan Posyandu Balita RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">Pencatatan Tumbuh Kembang Balita (Posyandu RW 19)</h5>
                <p class="text-muted small mb-0">Pemantauan otomatis berat, panjang, dan lingkar kepala balita berdasarkan standar resmi Buku KIA (Kemenkes / WHO)</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTabelStandar">
                    <i class="fa-solid fa-table-list me-1"></i> Standar Normal Bayi (1-12 Bln)
                </button>
                <a href="{{ route('posyandu.cetak', ['rt' => request('rt')]) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-print me-1"></i> Cetak Rekap Posyandu
                </a>
                <button class="btn btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahPosyandu">
                    <i class="fa-solid fa-plus me-1"></i> Catat Pemeriksaan
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <!-- Filter & Search -->
            <form action="{{ route('posyandu.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama balita / status gizi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="rt" class="form-select form-select-sm">
                        <option value="">Semua Wilayah RT (RW 19)</option>
                        <option value="06" {{ request('rt') == '06' ? 'selected' : '' }}>RT 06 (Ketua: Dimas)</option>
                        <option value="07" {{ request('rt') == '07' ? 'selected' : '' }}>RT 07 (Ketua: Bambang)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter Data</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('posyandu.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Nama Balita</th>
                            <th>Wilayah</th>
                            <th>L/P</th>
                            <th>Usia</th>
                            <th>Berat (BB)</th>
                            <th>Panjang (TB)</th>
                            <th>Lingkar Kepala</th>
                            <th>Status Kesehatan (Otomatis)</th>
                            <th>Tanggal Periksa</th>
                            <th class="text-center" style="min-width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posyanduList as $index => $p)
                            <tr>
                                <td class="small">{{ $posyanduList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->nama_bayi }}</div>
                                    <small class="text-muted">Orang Tua: {{ $p->warga->nama ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        RT {{ $p->warga->rt ?? '06' }} / RW 19
                                    </span>
                                </td>
                                <td class="small">{{ $p->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                                <td class="fw-semibold">{{ $p->usia_bulan }} Bulan</td>
                                <td class="fw-bold text-primary">{{ $p->berat_badan }} kg</td>
                                <td>{{ $p->tinggi_badan ? $p->tinggi_badan . ' cm' : '-' }}</td>
                                <td>{{ $p->lingkar_kepala ? $p->lingkar_kepala . ' cm' : '-' }}</td>
                                <td>
                                    @php
                                        $st = strtolower($p->status_kesehatan ?? '');
                                        if (str_contains($st, 'baik') || str_contains($st, 'sehat') || str_contains($st, 'normal')) {
                                            $badge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                            $icon = 'fa-circle-check';
                                        } elseif (str_contains($st, 'kurang') || str_contains($st, 'stunting')) {
                                            $badge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                            $icon = 'fa-triangle-exclamation';
                                        } else {
                                            $badge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                            $icon = 'fa-circle-info';
                                        }
                                    @endphp
                                    <span class="badge {{ $badge }} rounded-pill px-3 py-1">
                                        <i class="fa-solid {{ $icon }} me-1"></i> {{ $p->status_kesehatan ?? 'Gizi Baik / Normal (Sehat)' }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($p->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                                <td class="text-center text-nowrap">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('posyandu.cetak.dokumen', $p->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 small" title="Cetak Surat Hasil Pemeriksaan Balita">
                                            <i class="fa-solid fa-print me-1"></i> Cetak Hasil
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <form action="{{ route('posyandu.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus catatan pemeriksaan posyandu ini?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 ms-1" title="Hapus Data">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">Belum ada data pemeriksaan posyandu tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bottom Pagination Bar: 1 Halaman / Tab 10 Baris -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <div class="small text-muted">
                    Menampilkan <strong>{{ $posyanduList->firstItem() ?? 0 }}</strong> – <strong>{{ $posyanduList->lastItem() ?? 0 }}</strong> dari total <strong>{{ $posyanduList->total() }}</strong> data pemeriksaan balita (<strong>10 baris per halaman</strong>).
                </div>
                <div>
                    {{ $posyanduList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Catatan Posyandu -->
<div class="modal fade" id="modalTambahPosyandu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">Catat Pemeriksaan Posyandu</h5>
                    <p class="text-muted small mb-0">Status kesehatan otomatis dianalisis sesuai standar Kemenkes RI / WHO</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.store') }}" method="POST" id="formPosyandu">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small">Nama Balita / Bayi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_bayi" class="form-control" placeholder="Contoh: Ananda Rizky" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small">Orang Tua (Warga)</label>
                            <select name="id_warga" class="form-select" id="posyandu_id_warga">
                                <option value="">-- Pilih Orang Tua / Kepala Keluarga --</option>
                                @foreach($wargaList as $w)
                                    <option value="{{ $w->id }}" data-rt="{{ $w->rt }}">{{ $w->nama }} (RT {{ $w->rt ?? '06' }}/RW 19)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="posyandu_gender" class="form-select" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Usia (Bulan 1–12) <span class="text-danger">*</span></label>
                            <input type="number" name="usia_bulan" id="posyandu_usia" class="form-control" placeholder="Contoh: 3" min="1" max="60" value="1" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Berat Badan (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_badan" id="posyandu_bb" class="form-control" placeholder="Misal: 4.5" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Panjang / Tinggi (cm)</label>
                            <input type="number" step="0.01" name="tinggi_badan" id="posyandu_tb" class="form-control" placeholder="Misal: 55.0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Lingkar Kepala (cm)</label>
                            <input type="number" step="0.01" name="lingkar_kepala" id="posyandu_lk" class="form-control" placeholder="Misal: 38.0">
                        </div>
                    </div>

                    <!-- BOX HASIL ANALISIS KESEHATAN OTOMATIS -->
                    <div class="card border-0 rounded-3 mb-3 p-3" id="boxKesehatanOtomatis" style="background: #f0fdf4; border: 1px solid #bbf7d0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold text-dark small">
                                <i class="fa-solid fa-wand-magic-sparkles text-success me-1"></i> Analisis Status Kesehatan Bayi (Otomatis)
                            </div>
                            <span class="badge bg-success rounded-pill px-3 py-1" id="badgeStatusKesehatan">Gizi Baik / Normal (Sehat)</span>
                        </div>
                        <div class="small text-muted" id="teksStandarAcuan">
                            Standar Normal Usia 1 Bulan (Laki-laki): BB: 3,4 – 5,8 kg | TB: 50,8 – 58,6 cm | LK: 35,1 – 39,5 cm
                        </div>
                        <div class="mt-2 small fw-semibold" id="detailKondisi">
                            <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Pengukuran dalam batas kurva normal Buku KIA.</span>
                        </div>
                    </div>

                    <!-- Input Hidden Status Kesehatan yang otomatis diisi -->
                    <input type="hidden" name="status_kesehatan" id="inputStatusKesehatan" value="Gizi Baik / Normal (Sehat)">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Pemeriksa (Kader / Bidan)</label>
                            <input type="text" name="pemeriksaan_oleh" class="form-control" value="{{ auth()->user()->nama }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Kader / Vitamin / Imunisasi</label>
                        <textarea name="catatan" rows="2" class="form-control" placeholder="Contoh: Diberi Vitamin A dan imunisasi Polio"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tabel Standar KIA (Kemenkes) 1 - 12 Bulan -->
<div class="modal fade" id="modalTabelStandar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-book-medical text-primary me-2"></i>Tabel Ukuran Normal Bayi (1–12 Bulan) Buku KIA Kemenkes / WHO</h5>
                    <p class="text-muted small mb-0">Panduan acuan batas rentang lingkar kepala, berat badan, dan panjang badan normal</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="nav nav-pills mb-3 gap-2" id="pills-tab-standar" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill px-4" id="tab-boy" data-bs-toggle="pill" data-bs-target="#standar-boy" type="button">
                            <i class="fa-solid fa-child me-1 text-primary"></i> Bayi Laki-Laki
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4" id="tab-girl" data-bs-toggle="pill" data-bs-target="#standar-girl" type="button">
                            <i class="fa-solid fa-child-dress me-1 text-danger"></i> Bayi Perempuan
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContentStandar">
                    <!-- Tab Laki-Laki -->
                    <div class="tab-pane fade show active" id="standar-boy" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center mb-0">
                                <thead class="table-primary text-dark small fw-bold">
                                    <tr>
                                        <th>Usia (Bulan)</th>
                                        <th>Lingkar Kepala (cm)</th>
                                        <th>Berat Badan (kg)</th>
                                        <th>Panjang Badan (cm)</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @foreach($standards['Laki-laki'] as $bln => $std)
                                        <tr>
                                            <td class="fw-bold">{{ $bln }} Bulan</td>
                                            <td>{{ str_replace('.', ',', $std['lk'][0]) }} – {{ str_replace('.', ',', $std['lk'][1]) }}</td>
                                            <td class="text-primary fw-semibold">{{ str_replace('.', ',', $std['bb'][0]) }} – {{ str_replace('.', ',', $std['bb'][1]) }}</td>
                                            <td>{{ str_replace('.', ',', $std['tb'][0]) }} – {{ str_replace('.', ',', $std['tb'][1]) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Perempuan -->
                    <div class="tab-pane fade" id="standar-girl" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center mb-0">
                                <thead class="table-danger text-dark small fw-bold">
                                    <tr>
                                        <th>Usia (Bulan)</th>
                                        <th>Lingkar Kepala (cm)</th>
                                        <th>Berat Badan (kg)</th>
                                        <th>Panjang Badan (cm)</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @foreach($standards['Perempuan'] as $bln => $std)
                                        <tr>
                                            <td class="fw-bold">{{ $bln }} Bulan</td>
                                            <td>{{ str_replace('.', ',', $std['lk'][0]) }} – {{ str_replace('.', ',', $std['lk'][1]) }}</td>
                                            <td class="text-danger fw-semibold">{{ str_replace('.', ',', $std['bb'][0]) }} – {{ str_replace('.', ',', $std['bb'][1]) }}</td>
                                            <td>{{ str_replace('.', ',', $std['tb'][0]) }} – {{ str_replace('.', ',', $std['tb'][1]) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light border mt-3 small mb-0">
                    <strong>Catatan Penting Pemantauan:</strong>
                    <ul class="mb-0 ps-3">
                        <li><strong>Konsistensi Kurva:</strong> Angka di atas adalah rentang normal. Pastikan ukuran bayi naik secara konsisten mengikuti garis kurva di Buku KIA setiap bulannya.</li>
                        <li><strong>Berat Kepala:</strong> Lingkar kepala diukur untuk memantau perkembangan volume otak.</li>
                        <li><strong>Pemeriksaan Rutin:</strong> Konsultasikan ke dokter anak / puskesmas apabila grafik pertumbuhan mendatar, menurun, atau keluar dari rentang normal.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data Standar Normal Resmi dari User
    const STANDARDS = {
        'Laki-laki': {
            1:  { lk: [35.1, 39.5], bb: [3.4, 5.8],  tb: [50.8, 58.6] },
            2:  { lk: [36.8, 41.5], bb: [4.3, 7.1],  tb: [54.4, 62.4] },
            3:  { lk: [38.1, 42.9], bb: [5.0, 8.0],  tb: [57.3, 65.5] },
            4:  { lk: [39.2, 44.0], bb: [5.6, 8.7],  tb: [59.7, 68.0] },
            5:  { lk: [40.1, 45.0], bb: [6.0, 9.3],  tb: [61.7, 70.1] },
            6:  { lk: [40.9, 45.8], bb: [6.4, 9.8],  tb: [63.3, 71.9] },
            7:  { lk: [41.5, 46.6], bb: [6.7, 10.3], tb: [64.8, 73.5] },
            8:  { lk: [42.0, 47.2], bb: [6.9, 10.7], tb: [66.2, 75.0] },
            9:  { lk: [42.5, 47.7], bb: [7.1, 11.0], tb: [67.5, 76.5] },
            10: { lk: [42.9, 48.2], bb: [7.4, 11.4], tb: [68.7, 77.9] },
            11: { lk: [43.2, 48.6], bb: [7.6, 11.7], tb: [69.9, 79.2] },
            12: { lk: [43.5, 49.0], bb: [7.7, 12.0], tb: [71.0, 80.5] }
        },
        'Perempuan': {
            1:  { lk: [34.2, 38.9], bb: [3.2, 5.5],  tb: [49.8, 57.6] },
            2:  { lk: [35.8, 40.7], bb: [3.9, 6.6],  tb: [53.0, 61.1] },
            3:  { lk: [37.1, 42.0], bb: [4.5, 7.5],  tb: [55.6, 64.0] },
            4:  { lk: [38.1, 43.1], bb: [5.0, 8.2],  tb: [57.8, 66.4] },
            5:  { lk: [38.9, 44.0], bb: [5.4, 8.8],  tb: [59.6, 68.5] },
            6:  { lk: [39.6, 44.8], bb: [5.7, 9.3],  tb: [61.2, 70.3] },
            7:  { lk: [40.2, 45.5], bb: [6.0, 9.8],  tb: [62.7, 71.9] },
            8:  { lk: [40.7, 46.0], bb: [6.3, 10.2], tb: [64.0, 73.5] },
            9:  { lk: [41.2, 46.5], bb: [6.5, 10.5], tb: [65.3, 75.0] },
            10: { lk: [41.5, 46.9], bb: [6.7, 10.9], tb: [66.5, 76.4] },
            11: { lk: [41.9, 47.3], bb: [6.9, 11.2], tb: [67.7, 77.8] },
            12: { lk: [42.2, 47.6], bb: [7.0, 11.5], tb: [68.9, 79.2] }
        }
    };

    const genderSelect = document.getElementById('posyandu_gender');
    const usiaInput = document.getElementById('posyandu_usia');
    const bbInput = document.getElementById('posyandu_bb');
    const tbInput = document.getElementById('posyandu_tb');
    const lkInput = document.getElementById('posyandu_lk');

    const badgeStatus = document.getElementById('badgeStatusKesehatan');
    const teksStandar = document.getElementById('teksStandarAcuan');
    const detailKondisi = document.getElementById('detailKondisi');
    const inputStatus = document.getElementById('inputStatusKesehatan');
    const boxKesehatan = document.getElementById('boxKesehatanOtomatis');

    function hitungStatusOtomatis() {
        const gender = genderSelect.value || 'Laki-laki';
        let usia = parseInt(usiaInput.value) || 1;
        const bb = parseFloat(bbInput.value);
        const tb = parseFloat(tbInput.value);
        const lk = parseFloat(lkInput.value);

        // Batasi usia untuk referensi tabel 1-12 bulan
        const usiaRef = Math.max(1, Math.min(12, usia));
        const std = STANDARDS[gender][usiaRef];

        if (!std) return;

        teksStandar.innerHTML = `Standar Normal Usia ${usiaRef} Bln (${gender}): ` +
            `BB: <strong>${std.bb[0]} – ${std.bb[1]} kg</strong> | ` +
            `TB: <strong>${std.tb[0]} – ${std.tb[1]} cm</strong> | ` +
            `LK: <strong>${std.lk[0]} – ${std.lk[1]} cm</strong>`;

        if (isNaN(bb) || bb <= 0) {
            badgeStatus.className = 'badge bg-secondary rounded-pill px-3 py-1';
            badgeStatus.textContent = 'Menunggu Input Berat Badan...';
            detailKondisi.innerHTML = '<span class="text-muted">Masukkan angka berat badan balita untuk melihat analisis otomatis.</span>';
            boxKesehatan.style.background = '#f8fafc';
            boxKesehatan.style.borderColor = '#e2e8f0';
            inputStatus.value = 'Gizi Baik / Normal (Sehat)';
            return;
        }

        let issues = [];
        let statusBb = 'Normal';
        let statusTb = 'Normal';
        let statusLk = 'Normal';

        // Cek BB
        if (bb < std.bb[0]) {
            statusBb = 'Kurang';
            issues.push(`BB di bawah normal (${bb} kg < ${std.bb[0]} kg)`);
        } else if (bb > std.bb[1]) {
            statusBb = 'Berlebih';
            issues.push(`BB di atas normal (${bb} kg > ${std.bb[1]} kg)`);
        }

        // Cek TB
        if (!isNaN(tb) && tb > 0) {
            if (tb < std.tb[0]) {
                statusTb = 'Pendek';
                issues.push(`Panjang badan kurang (${tb} cm < ${std.tb[0]} cm)`);
            } else if (tb > std.tb[1]) {
                statusTb = 'Tinggi';
            }
        }

        // Cek LK
        if (!isNaN(lk) && lk > 0) {
            if (lk < std.lk[0]) {
                statusLk = 'Kecil';
                issues.push(`Lingkar kepala kecil (${lk} cm < ${std.lk[0]} cm)`);
            } else if (lk > std.lk[1]) {
                statusLk = 'Besar';
                issues.push(`Lingkar kepala besar (${lk} cm > ${std.lk[1]} cm)`);
            }
        }

        let statusText = '';
        let badgeColor = '';
        let bgColor = '';
        let borderColor = '';

        if (issues.length === 0) {
            statusText = 'Gizi Baik / Normal (Sehat)';
            badgeColor = 'bg-success';
            bgColor = '#f0fdf4';
            borderColor = '#bbf7d0';
            detailKondisi.innerHTML = '<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Sempurna! Berat, tinggi, dan lingkar kepala bayi berada di rentang normal Buku KIA.</span>';
        } else if (statusBb === 'Kurang' && statusTb === 'Pendek') {
            statusText = 'Gizi Kurang & Risiko Stunting';
            badgeColor = 'bg-danger';
            bgColor = '#fef2f2';
            borderColor = '#fecaca';
            detailKondisi.innerHTML = `<span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Perhatian Khusus: ${issues.join(', ')}. Disarankan rujukan gizi / konsultasi KIA.</span>`;
        } else if (statusBb === 'Kurang') {
            statusText = 'Gizi Kurang (BB Rendah)';
            badgeColor = 'bg-warning text-dark';
            bgColor = '#fffbeb';
            borderColor = '#fde68a';
            detailKondisi.innerHTML = `<span class="text-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i> Catatan: ${issues.join(', ')}. Pantau asupan ASI/MPASI.</span>`;
        } else if (statusTb === 'Pendek') {
            statusText = 'Panjang Kurang (Pantau TB)';
            badgeColor = 'bg-warning text-dark';
            bgColor = '#fffbeb';
            borderColor = '#fde68a';
            detailKondisi.innerHTML = `<span class="text-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i> Catatan: ${issues.join(', ')}. Pantau kurva pertumbuhan tinggi badan.</span>`;
        } else if (statusBb === 'Berlebih') {
            statusText = 'Berat Berlebih (Risiko Overweight)';
            badgeColor = 'bg-info text-dark';
            bgColor = '#f0f9ff';
            borderColor = '#bae6fd';
            detailKondisi.innerHTML = `<span class="text-info text-dark"><i class="fa-solid fa-circle-info me-1"></i> Catatan: ${issues.join(', ')}.</span>`;
        } else {
            statusText = 'Perlu Pantau Pertumbuhan';
            badgeColor = 'bg-warning text-dark';
            bgColor = '#fffbeb';
            borderColor = '#fde68a';
            detailKondisi.innerHTML = `<span class="text-warning text-dark"><i class="fa-solid fa-circle-info me-1"></i> Catatan: ${issues.join(', ')}.</span>`;
        }

        badgeStatus.className = `badge ${badgeColor} rounded-pill px-3 py-1`;
        badgeStatus.textContent = statusText;
        boxKesehatan.style.background = bgColor;
        boxKesehatan.style.borderColor = borderColor;
        inputStatus.value = statusText;
    }

    [genderSelect, usiaInput, bbInput, tbInput, lkInput].forEach(el => {
        el.addEventListener('input', hitungStatusOtomatis);
        el.addEventListener('change', hitungStatusOtomatis);
    });

    hitungStatusOtomatis();
});
</script>
@endsection
