<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Posyandu Balita - RW 19</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 13px; color: #000; }
        .kop-surat { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="text-center mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary fw-bold px-4">
            Cetak Rekapitulasi (Print / PDF)
        </button>
    </div>

    @php
        $filterRt = request('rt');
        $judulRt = $filterRt ? 'RUKUN TETANGGA ' . str_pad($filterRt, 2, '0', STR_PAD_LEFT) . ' / ' : '';
    @endphp

    <div class="kop-surat d-flex align-items-center justify-content-center gap-3">
        <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" style="width: 55px; height: 55px;">
        <div>
            <h4 class="fw-bold mb-0 text-uppercase">POS PELAYANAN TERPADU (POSYANDU) MELATI</h4>
            <h5 class="fw-bold mb-0">{{ $judulRt }}RUKUN WARGA 19</h5>
            <p class="mb-0 small text-muted">Kelurahan Bekasi Jaya, Kecamatan Bekasi Timur, Kota Bekasi</p>
        </div>
    </div>

    <div class="text-center mb-3">
        <h5 class="fw-bold text-decoration-underline mb-0">DATA REKAPITULASI PENIMBANGAN & GIZI BALITA</h5>
        <small class="text-muted">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</small>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Balita</th>
                <th>Wilayah</th>
                <th style="width: 60px;">L/P</th>
                <th style="width: 70px;">Usia</th>
                <th style="width: 80px;">BB (kg)</th>
                <th style="width: 80px;">TB (cm)</th>
                <th style="width: 80px;">LK (cm)</th>
                <th>Status Kesehatan / Gizi (Standar KIA)</th>
                <th>Orang Tua</th>
                <th>Tanggal Periksa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $row->nama_bayi }}</td>
                    <td class="text-center">RT {{ $row->warga->rt ?? '06' }}</td>
                    <td class="text-center">{{ $row->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                    <td class="text-center">{{ $row->usia_bulan }} Bln</td>
                    <td class="text-center fw-semibold text-primary">{{ $row->berat_badan }}</td>
                    <td class="text-center">{{ $row->tinggi_badan ?? '-' }}</td>
                    <td class="text-center">{{ $row->lingkar_kepala ?? '-' }}</td>
                    <td>{{ $row->status_kesehatan ?? 'Gizi Baik / Normal (Sehat)' }}</td>
                    <td>{{ $row->warga->nama ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row text-center mt-5">
        <div class="col-6">
            @if($filterRt == '07')
                <p class="mb-5">Mengetahui,<br>Ketua RT 07</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">BAMBANG</p>
                <small>Ketua RT 07 / RW 19</small>
            @elseif($filterRt == '06')
                <p class="mb-5">Mengetahui,<br>Ketua RT 06</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">DIMAS</p>
                <small>Ketua RT 06 / RW 19</small>
            @else
                <p class="mb-5">Mengetahui,<br>Ketua RW 19</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">FABIAN</p>
                <small>Ketua RW 19 Bekasi Jaya</small>
            @endif
        </div>
        <div class="col-6">
            <p class="mb-5">Kader Posyandu Melati RW 19</p>
            <br>
            <p class="fw-bold text-decoration-underline mb-0">BIDAN & KADER KESEHATAN</p>
            <small>Posyandu Terpadu RW 19</small>
        </div>
    </div>

</body>
</html>
