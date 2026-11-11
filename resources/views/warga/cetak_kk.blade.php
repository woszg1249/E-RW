<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Keluarga - {{ $no_kk }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f5f9;
            color: #000;
            padding: 20px;
        }
        .kk-container {
            background: #ffffff;
            max-width: 1050px;
            margin: 0 auto;
            padding: 30px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #334155;
        }
        .header-title {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-title h3 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }
        .header-title h4 {
            font-size: 1.1rem;
            font-weight: 700;
        }
        .kk-info {
            font-size: 0.82rem;
            margin-bottom: 18px;
        }
        .table-kk {
            font-size: 0.78rem;
            border-color: #000;
            margin-bottom: 20px;
        }
        .table-kk th, .table-kk td {
            border: 1px solid #000 !important;
            padding: 5px 6px;
        }
        .table-kk th {
            background-color: #f8fafc;
            text-align: center;
            vertical-align: middle;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .kk-container {
                box-shadow: none;
                border: 1px solid #000;
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
            Cetak Kartu Keluarga (Print / PDF)
        </button>
    </div>

    <div class="kk-container">
        @php
            // Pastikan urutan anggota keluarga selalu mendahulukan Kepala Keluarga (No. 1)
            $hierarchyMap = [
                'kepala keluarga' => 1,
                'suami' => 2,
                'istri' => 3,
                'anak' => 4,
                'menantu' => 5,
                'cucu' => 6,
                'orangtua' => 7,
                'mertua' => 8,
                'famili lain' => 9,
            ];
            $members = $members->sortBy(function($m) use ($hierarchyMap) {
                $status = strtolower(trim($m->status_keluarga ?? ''));
                return $hierarchyMap[$status] ?? 99;
            })->values();
        @endphp
        <div class="header-title">
            <h3>KARTU KELUARGA</h3>
            <h4>No. {{ $no_kk }}</h4>
        </div>

        <div class="kk-info">
            <div class="row">
                <div class="col-7">
                    <table class="table-borderless w-100">
                        <tr>
                            <td style="width: 170px;">Nama Kepala Keluarga</td>
                            <td style="width: 15px;">:</td>
                            <td class="fw-bold">{{ strtoupper($kepala->nama ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td>{{ $kepala->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>RT / RW</td>
                            <td>:</td>
                            <td>{{ $kepala->rt ?? '06' }} / {{ $kepala->rw ?? '19' }}</td>
                        </tr>
                        <tr>
                            <td>Kode Pos</td>
                            <td>:</td>
                            <td>17113</td>
                        </tr>
                    </table>
                </div>
                <div class="col-5">
                    <table class="table-borderless w-100">
                        <tr>
                            <td style="width: 140px;">Desa / Kelurahan</td>
                            <td style="width: 15px;">:</td>
                            <td>Bekasi Jaya</td>
                        </tr>
                        <tr>
                            <td>Kecamatan</td>
                            <td>:</td>
                            <td>Bekasi Timur</td>
                        </tr>
                        <tr>
                            <td>Kabupaten / Kota</td>
                            <td>:</td>
                            <td>Kota Bekasi</td>
                        </tr>
                        <tr>
                            <td>Provinsi</td>
                            <td>:</td>
                            <td>Jawa Barat</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel 1 Data Pokok -->
        <table class="table table-kk w-100">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Lengkap</th>
                    <th style="width: 130px;">NIK</th>
                    <th style="width: 70px;">Jenis Kelamin</th>
                    <th>Tempat Lahir</th>
                    <th style="width: 85px;">Tanggal Lahir</th>
                    <th>Agama</th>
                    <th>Pendidikan</th>
                    <th>Jenis Pekerjaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $idx => $m)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="fw-bold">{{ strtoupper($m->nama) }}</td>
                        <td class="text-center">{{ $m->nik ?? '-' }}</td>
                        <td class="text-center">{{ $m->kelamin === 'Laki-laki' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</td>
                        <td>{{ strtoupper($m->tempat_lahir ?? 'BEKASI') }}</td>
                        <td class="text-center">{{ $m->tanggal_lahir ? $m->tanggal_lahir->format('d-m-Y') : '01-01-2000' }}</td>
                        <td class="text-center">{{ strtoupper($m->agama ?? 'ISLAM') }}</td>
                        <td>{{ strtoupper($m->pendidikan ?? 'SLTA / SEDERAJAT') }}</td>
                        <td>{{ strtoupper($m->pekerjaan ?? 'BELUM / TIDAK BEKERJA') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tabel 2 Status Hubungan -->
        <table class="table table-kk w-100">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Status Perkawinan</th>
                    <th>Status Hubungan Dalam Keluarga</th>
                    <th>Kewarganegaraan</th>
                    <th>No. Paspor</th>
                    <th>No. KITAS / KITAP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $idx => $m)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ strtoupper($m->status_perkawinan ?? 'KAWIN') }}</td>
                        <td class="text-center fw-bold">{{ strtoupper($m->status_keluarga ?? 'ANGGOTA') }}</td>
                        <td class="text-center">WNI</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        @php
            $isRt07 = ($kepala->rt == '07');
            $namaRt = $isRt07 ? 'BAMBANG' : 'DIMAS';
            $labelRt = $isRt07 ? '07' : '06';
        @endphp
        <div class="row text-center mt-4" style="font-size: 0.85rem;">
            <div class="col-4">
                <p class="mb-5">KEPALA KELUARGA</p>
                <p class="fw-bold text-decoration-underline mb-0">{{ strtoupper($kepala->nama ?? '-') }}</p>
                <small>Tanda Tangan / Cap Jempol</small>
            </div>
            <div class="col-4">
                <p class="mb-2">Dikeluarkan Tanggal: {{ date('d-m-Y') }}</p>
                <p class="mb-5">KETUA RT {{ $labelRt }} / RW 19</p>
                <p class="fw-bold text-decoration-underline mb-0">{{ $namaRt }}</p>
                <small>Ketua RT {{ $labelRt }} / RW 19</small>
            </div>
            <div class="col-4">
                <p class="mb-5">KEPALA DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</p>
                <p class="fw-bold text-decoration-underline mb-0">DRS. H. SURYADI, M.SI</p>
                <small>NIP. 19680812 199403 1 005</small>
            </div>
        </div>
    </div>

</body>
</html>
