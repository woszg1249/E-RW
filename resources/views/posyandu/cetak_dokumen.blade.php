<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemeriksaan Posyandu - {{ $posyandu->nama_bayi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 24px;
            font-size: 13px;
        }
        .doc-container {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 44px;
            border: 2px solid #334155;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header-kop img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        .table-doc th, .table-doc td {
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            font-size: 13px;
        }
        .table-doc th {
            background-color: #f8fafc;
        }
        .status-badge-doc {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
        }
        .status-sehat {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .status-warning {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }
        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .doc-container {
                border: 1px solid #000;
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak / PDF (No Print) -->
    <div class="text-center mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-bold rounded-pill shadow-sm">
            <i class="fa-solid fa-print me-2"></i> Cetak Dokumen Hasil Pemeriksaan (Print / PDF)
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 py-2 rounded-pill ms-2">
            Tutup
        </button>
    </div>

    <div class="doc-container">
        <!-- Kop Surat Resmi Posyandu RW 19 -->
        <div class="header-kop d-flex align-items-center justify-content-center gap-3">
            <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo Posyandu RW 19">
            <div>
                <h4 class="fw-bold mb-0 text-uppercase tracking-wide">POS PELAYANAN TERPADU (POSYANDU) MELATI</h4>
                <h5 class="fw-bold mb-0 text-uppercase">RUKUN WARGA 19 (RT 06 & RT 07)</h5>
                <p class="mb-0 small text-secondary">
                    Kelurahan Bekasi Jaya, Kecamatan Bekasi Timur, Kota Bekasi • Jawa Barat 17113
                </p>
            </div>
        </div>

        <div class="text-center mb-4">
            <h5 class="fw-bold text-decoration-underline mb-1">SURAT KETERANGAN HASIL PEMERIKSAAN KESEHATAN BALITA</h5>
            <small class="text-muted">Nomor Registrasi: POS-{{ date('Ym', strtotime($posyandu->tanggal_pemeriksaan)) }}-{{ sprintf('%04d', $posyandu->id) }}</small>
        </div>

        <!-- Bagian 1: Data Identitas -->
        <h6 class="fw-bold text-dark border-bottom pb-1 mb-2">I. IDENTITAS BALITA & ORANG TUA</h6>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <table class="table-borderless w-100 small">
                    <tr>
                        <td style="width: 140px;" class="text-muted">Nama Lengkap Balita</td>
                        <td style="width: 10px;">:</td>
                        <td class="fw-bold text-uppercase">{{ $posyandu->nama_bayi }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Kelamin</td>
                        <td>:</td>
                        <td class="fw-semibold">{{ $posyandu->jenis_kelamin }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Usia Saat Pemeriksaan</td>
                        <td>:</td>
                        <td class="fw-semibold">{{ $posyandu->usia_bulan }} Bulan</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table-borderless w-100 small">
                    <tr>
                        <td style="width: 140px;" class="text-muted">Nama Orang Tua / Wali</td>
                        <td style="width: 10px;">:</td>
                        <td class="fw-bold">{{ $warga->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIK Orang Tua</td>
                        <td>:</td>
                        <td>{{ $warga->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Wilayah Domisili</td>
                        <td>:</td>
                        <td>RT {{ $rt }} / RW 19, Bekasi Jaya</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Bagian 2: Hasil Pengukuran Fisik -->
        <h6 class="fw-bold text-dark border-bottom pb-1 mb-2">II. HASIL PENGUKURAN FISIK & STANDAR KIA (KEMENKES RI / WHO)</h6>
        <table class="table table-doc w-100 mb-3 align-middle">
            <thead class="text-center small">
                <tr>
                    <th style="width: 180px;">Parameter Pengukuran</th>
                    <th style="width: 140px;">Hasil Pemeriksaan</th>
                    <th>Standar Normal Usia {{ $posyandu->usia_bulan }} Bln</th>
                    <th style="width: 150px;">Status Indikator</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-semibold">
                        <i class="fa-solid fa-weight-scale text-primary me-1"></i> Berat Badan (BB)
                    </td>
                    <td class="text-center fw-bold fs-6 text-primary">
                        {{ number_format($posyandu->berat_badan, 2) }} kg
                    </td>
                    <td class="text-center small">
                        @php
                            $stdBb = $evaluation['standard']['bb'] ?? $evaluation['std']['bb'] ?? null;
                            $statusBb = $evaluation['statusBb'] ?? $evaluation['status_bb'] ?? 'Normal';
                        @endphp
                        @if($stdBb)
                            {{ $stdBb[0] }} – {{ $stdBb[1] }} kg
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($statusBb === 'Normal')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small fw-semibold">Normal</span>
                        @elseif($statusBb === 'Berlebih' || $statusBb === 'Lebih')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1 small fw-semibold">Berat Berlebih</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 small fw-semibold">Berat Kurang</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <i class="fa-solid fa-ruler-vertical text-success me-1"></i> Panjang / Tinggi Badan (TB)
                    </td>
                    <td class="text-center fw-bold fs-6 text-dark">
                        {{ $posyandu->tinggi_badan ? number_format($posyandu->tinggi_badan, 2) . ' cm' : '-' }}
                    </td>
                    <td class="text-center small">
                        @php
                            $stdTb = $evaluation['standard']['tb'] ?? $evaluation['std']['tb'] ?? null;
                            $statusTb = $evaluation['statusTb'] ?? $evaluation['status_tb'] ?? 'Normal';
                        @endphp
                        @if($stdTb)
                            {{ $stdTb[0] }} – {{ $stdTb[1] }} cm
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!$posyandu->tinggi_badan)
                            <span class="text-muted small">-</span>
                        @elseif($statusTb === 'Normal')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small fw-semibold">Normal</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1 small fw-semibold">{{ $statusTb }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <i class="fa-solid fa-head-side-virus text-info me-1"></i> Lingkar Kepala (LK)
                    </td>
                    <td class="text-center fw-bold fs-6 text-dark">
                        {{ $posyandu->lingkar_kepala ? number_format($posyandu->lingkar_kepala, 2) . ' cm' : '-' }}
                    </td>
                    <td class="text-center small">
                        @php
                            $stdLk = $evaluation['standard']['lk'] ?? $evaluation['std']['lk'] ?? null;
                            $statusLk = $evaluation['statusLk'] ?? $evaluation['status_lk'] ?? 'Normal';
                        @endphp
                        @if($stdLk)
                            {{ $stdLk[0] }} – {{ $stdLk[1] }} cm
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!$posyandu->lingkar_kepala)
                            <span class="text-muted small">-</span>
                        @elseif($statusLk === 'Normal')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small fw-semibold">Normal</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1 small fw-semibold">{{ $statusLk }}</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Bagian 3: Kesimpulan & Catatan Gizi -->
        @php
            $isHealthy = str_contains(strtolower($posyandu->status_kesehatan), 'sehat') || str_contains(strtolower($posyandu->status_kesehatan), 'baik');
            $badgeClass = $isHealthy ? 'status-sehat' : 'status-warning';
        @endphp
        <div class="p-3 rounded-3 mb-3 {{ $badgeClass }}">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="small fw-bold text-uppercase">Diagnosis Status Kesehatan / Gizi Balita:</span>
                <span class="status-badge-doc {{ $badgeClass }}">
                    <i class="fa-solid fa-heart-pulse me-1"></i> {{ $posyandu->status_kesehatan }}
                </span>
            </div>
            <div class="small mt-2">
                <strong>Catatan Analisis:</strong> {{ $evaluation['detail'] ?? 'Pertumbuhan balita terpantau berada pada rentang batas kurva normal Buku KIA Kemenkes RI.' }}
            </div>
        </div>

        @if(!empty($posyandu->catatan))
            <div class="p-2 border rounded bg-light small mb-3">
                <strong>Pesan Kader Posyandu untuk Orang Tua:</strong> {{ $posyandu->catatan }}
            </div>
        @endif

        <div class="border rounded p-2 small bg-light text-muted mb-4">
            <strong>Petunjuk Pemantauan untuk Orang Tua:</strong>
            <ul class="mb-0 ps-3">
                <li>Pastikan asupan ASI / MPASI bergizi seimbang terus diberikan secara disiplin.</li>
                <li>Pantau garis pertumbuhan di Buku KIA (Kesehatan Ibu dan Anak) agar selalu berada pada pita hijau.</li>
                <li>Bawa kembali balita pada jadwal Posyandu Melati bulan berikutnya untuk penimbangan dan imunisasi berkala.</li>
            </ul>
        </div>

        <!-- Bagian 4: Pengesahan & Tanda Tangan -->
        <div class="row text-center pt-2">
            <div class="col-6">
                <p class="mb-1 text-muted small">Pemeriksa / Kader Posyandu Melati,</p>
                <br><br><br>
                <p class="fw-bold text-decoration-underline mb-0">{{ strtoupper($posyandu->pemeriksaan_oleh ?? 'KADER POSYANDU') }}</p>
                <small class="text-muted">Kader Kesehatan RW 19</small>
            </div>
            <div class="col-6">
                <p class="mb-1 text-muted small">Bekasi, {{ \Carbon\Carbon::parse($posyandu->tanggal_pemeriksaan)->translatedFormat('d F Y') }}</p>
                <p class="mb-1 text-muted small">Mengetahui, Ketua RT {{ $rt }} / RW 19</p>
                <br><br>
                <p class="fw-bold text-decoration-underline mb-0">{{ strtoupper($ketuaRt) }}</p>
                <small class="text-muted">Ketua RT {{ $rt }} / RW 19 Bekasi Jaya</small>
            </div>
        </div>
    </div>

</body>
</html>
