<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar - {{ $surat->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            color: #000;
            padding: 20px;
            font-size: 15px;
            line-height: 1.6;
        }
        .surat-container {
            background: #ffffff;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 60px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
            text-align: center;
        }
        .kop-surat h4 {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .kop-surat h5 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .kop-surat p {
            font-size: 0.85rem;
            margin-bottom: 0;
            font-style: italic;
        }
        .judul-surat {
            text-align: center;
            margin-bottom: 24px;
        }
        .judul-surat h5 {
            font-size: 1.15rem;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .tabel-biodata td {
            padding: 4px 6px;
            vertical-align: top;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .surat-container {
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

    <div class="text-center mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4 fw-bold">
            Cetak Surat Resmi (Print / Simpan PDF)
        </button>
    </div>

    <div class="surat-container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <h4>RUKUN TETANGGA 06 / RUKUN WARGA 19</h4>
            <h5>KELURAHAN BEKASI JAYA - KECAMATAN BEKASI TIMUR</h5>
            <h5>KOTA BEKASI - JAWA BARAT</h5>
            <p>Sekretariat: Jl. Melati Raya Blok C No. 12, RW 19, Bekasi Jaya, Kota Bekasi 17113</p>
        </div>

        <!-- Judul Surat -->
        <div class="judul-surat">
            <h5>{{ strtoupper($surat->jenis_surat) }}</h5>
            <span>Nomor: {{ sprintf('%03d', $surat->id) }}/SK-RT06/RW19/{{ date('m/Y') }}</span>
        </div>

        <p>Yang bertanda tangan di bawah ini Ketua Rukun Tetangga (RT) 06 Rukun Warga (RW) 19 Kelurahan Bekasi Jaya, Kecamatan Bekasi Timur, Kota Bekasi, dengan ini menerangkan bahwa:</p>

        <table class="table-borderless tabel-biodata w-100 my-3">
            <tr>
                <td style="width: 220px;">1. Nama Lengkap</td>
                <td style="width: 15px;">:</td>
                <td class="fw-bold">{{ strtoupper($surat->nama) }}</td>
            </tr>
            <tr>
                <td>2. NIK / No. KTP</td>
                <td>:</td>
                <td>{{ $surat->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td>3. Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $surat->ttl ?? 'Bekasi, 01-01-2000' }}</td>
            </tr>
            <tr>
                <td>4. Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $surat->user->kelamin ?? 'Laki-laki' }}</td>
            </tr>
            <tr>
                <td>5. Warga Negara / Agama</td>
                <td>:</td>
                <td>Indonesia / {{ $surat->user->agama ?? 'Islam' }}</td>
            </tr>
            <tr>
                <td>6. Pekerjaan</td>
                <td>:</td>
                <td>{{ $surat->user->pekerjaan ?? 'Wiraswasta' }}</td>
            </tr>
            <tr>
                <td>7. Alamat KTP / Domisili</td>
                <td>:</td>
                <td>{{ $surat->alamat ?? $surat->user->alamat ?? '-' }}</td>
            </tr>
        </table>

        <p class="mt-3">
            Bahwa orang tersebut di atas adalah benar warga yang berdomisili di lingkungan RT 06 / RW 19 Kelurahan Bekasi Jaya, Kecamatan Bekasi Timur, Kota Bekasi.
        </p>

        <p>
            Surat keterangan ini diberikan kepada yang bersangkutan untuk keperluan: <br>
            <strong class="d-block p-2 bg-light border mt-1 font-monospace">"{{ $surat->tujuan }}"</strong>
        </p>

        <p class="mt-3">
            Demikian surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.
        </p>

        <!-- Tanda Tangan -->
        <div class="row text-center mt-5 pt-3">
            <div class="col-6">
                <p class="mb-5">Mengetahui,<br>Ketua RW 19</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">H. AHMAD RIFAI, S.E.</p>
                <small>Ketua RW 19 Bekasi Jaya</small>
            </div>
            <div class="col-6">
                <p class="mb-1">Bekasi, {{ \Carbon\Carbon::parse($surat->tanggal_permohonan)->translatedFormat('d F Y') }}</p>
                <p class="mb-5">Ketua RT 06</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">REIVAL AL KHAFI</p>
                <small>Ketua RT 06 / RW 19</small>
            </div>
        </div>
    </div>

</body>
</html>
