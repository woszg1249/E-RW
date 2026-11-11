@extends('layouts.app')

@section('title', 'Laporan Keuangan Bulanan Kas RT')
@section('page_title', 'Laporan Keuangan Bulanan Kas RT/RW')

@section('styles')
<style>
    @media print {
        #sidebar, .top-navbar, .no-print, footer {
            display: none !important;
        }
        #content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .page-content {
            padding: 0 !important;
        }
        .card-custom {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        body {
            background-color: #ffffff !important;
        }
    }
</style>
@endsection

@section('content')
<div class="row g-4">
    <!-- Filter Bar Bulanan (No Print) -->
    <div class="col-12 no-print">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="fa-solid fa-calendar-days text-primary me-2"></i>Laporan Keuangan Bulanan
                </h5>
                <p class="text-muted small mb-0">Pilih periode bulan dan tahun pembukuan kas RT 06 & RT 07 (RW 19)</p>
            </div>

            <form action="{{ route('keuangan.laporan') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                <select name="bulan" class="form-select form-select-sm" style="min-width: 140px;">
                    @for($m = 1; $m <= 12; $m++)
                        @php $mStr = sprintf('%02d', $m); @endphp
                        <option value="{{ $mStr }}" {{ $bulan == $mStr ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                <select name="tahun" class="form-select form-select-sm" style="min-width: 100px;">
                    @for($y = date('Y') + 1; $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <select name="rt" class="form-select form-select-sm" style="min-width: 150px;">
                    <option value="">Semua Wilayah</option>
                    <option value="06" {{ $rt == '06' ? 'selected' : '' }}>Khusus RT 06</option>
                    <option value="07" {{ $rt == '07' ? 'selected' : '' }}>Khusus RT 07</option>
                </select>

                <button type="submit" class="btn btn-sm btn-dark px-3">
                    <i class="fa-solid fa-filter me-1"></i> Tampilkan
                </button>

                <button type="button" onclick="window.print()" class="btn btn-sm btn-primary-custom rounded-pill px-3">
                    <i class="fa-solid fa-print me-1"></i> Cetak PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Printable Report Card -->
    <div class="col-12">
        <div class="card-custom p-4 p-md-5 bg-white">
            <!-- Kop Surat Resmi RT/RW -->
            <div class="d-flex align-items-center justify-content-center gap-4 pb-3 mb-4 border-bottom border-2 border-dark text-center">
                <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" style="width: 70px; height: 70px;">
                <div>
                    <h4 class="fw-bold mb-0 text-uppercase tracking-wide">RUKUN WARGA 19 (RT 06 & RT 07)</h4>
                    <h5 class="fw-bold mb-1 text-uppercase">KELURAHAN BEKASI JAYA - KECAMATAN BEKASI TIMUR</h5>
                    <p class="mb-0 small text-secondary">Sekretariat: Jl. Melati Raya No. 12, RW 19, Bekasi Jaya, Kota Bekasi • Telp: 0812-3456-7890</p>
                </div>
            </div>

            <div class="text-center mb-4">
                <h5 class="fw-bold text-decoration-underline mb-1">LAPORAN PERTANGGUNGJAWABAN KAS BULANAN</h5>
                <span class="badge bg-light text-dark border px-3 py-1 fs-6 fw-semibold">
                    Periode: {{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->translatedFormat('F Y') }}
                    @if($rt)
                        (Khusus RT {{ $rt }})
                    @else
                        (Wilayah RT 06 & RT 07)
                    @endif
                </span>
            </div>

            <!-- 4 Ringkasan Posisi Kas Bulanan -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="p-3 border rounded text-center bg-light">
                        <span class="small text-muted d-block fw-semibold">Saldo Awal Bulan</span>
                        <strong class="text-secondary fs-6">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 border rounded text-center bg-light">
                        <span class="small text-muted d-block fw-semibold">Pemasukan Bulan Ini</span>
                        <strong class="text-success fs-6">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 border rounded text-center bg-light">
                        <span class="small text-muted d-block fw-semibold">Pengeluaran Bulan Ini</span>
                        <strong class="text-danger fs-6">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 border rounded text-center bg-light">
                        <span class="small text-muted d-block fw-semibold">Saldo Akhir Bulan</span>
                        <strong class="text-primary fs-6">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light text-center small">
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th style="width: 105px;">Tanggal</th>
                            <th>Uraian Transaksi</th>
                            <th style="width: 170px;">Pihak / Warga</th>
                            <th style="width: 130px;">Pemasukan (Rp)</th>
                            <th style="width: 130px;">Pengeluaran (Rp)</th>
                            <th style="width: 140px;">Saldo Kas (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris Saldo Awal -->
                        <tr class="table-secondary fw-semibold">
                            <td class="text-center small">-</td>
                            <td class="text-center small">01/{{ $bulan }}/{{ $tahun }}</td>
                            <td colspan="2"><em>Saldo Awal Kas (Akumulasi Bulan Sebelumnya)</em></td>
                            <td class="text-end text-muted">-</td>
                            <td class="text-end text-muted">-</td>
                            <td class="text-end text-primary fw-bold">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                        </tr>

                        @php $currentRunningSaldo = $saldoAwal; @endphp

                        @forelse($dataLaporan as $i => $item)
                            @php
                                if ($item->jenis === 'masuk') {
                                    $currentRunningSaldo += $item->jumlah;
                                } else {
                                    $currentRunningSaldo -= $item->jumlah;
                                }
                            @endphp
                            <tr>
                                <td class="text-center small">{{ $i + 1 }}</td>
                                <td class="text-center small">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->keterangan }}</div>
                                </td>
                                <td class="small text-muted">
                                    <i class="fa-solid fa-user me-1 text-secondary"></i>
                                    {{ $item->nama_warga ?: '-' }}
                                </td>
                                <td class="text-end text-success fw-semibold">
                                    {{ $item->jenis === 'masuk' ? number_format($item->jumlah, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end text-danger fw-semibold">
                                    {{ $item->jenis === 'keluar' ? number_format($item->jumlah, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    {{ number_format($currentRunningSaldo, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-receipt fs-3 d-block mb-2 text-secondary"></i>
                                    Tidak ada catatan mutasi kas pada bulan {{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->translatedFormat('F Y') }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">TOTAL MUTASI BULAN INI:</td>
                            <td class="text-end text-success">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                            <td class="text-end text-muted">-</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">SURPLUS / (DEFISIT) BULAN INI:</td>
                            <td colspan="3" class="text-end {{ $surplusDefisit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $surplusDefisit >= 0 ? '+' : '' }} Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="table-primary border-dark">
                            <td colspan="4" class="text-end fs-6">SALDO AKHIR KAS PER {{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->endOfMonth()->translatedFormat('d F Y') }}:</td>
                            <td colspan="3" class="text-end fs-6 text-primary">
                                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Signatures Section -->
            <div class="row text-center mt-5 pt-3">
                <div class="col-4">
                    <p class="mb-1 text-muted small">Mengetahui,</p>
                    <p class="fw-bold mb-5">Ketua RW 19</p>
                    <br>
                    <p class="fw-bold text-decoration-underline mb-0">FABIAN</p>
                    <small class="text-muted">Ketua RW 19 Bekasi Jaya</small>
                </div>
                <div class="col-4">
                    <p class="mb-1 text-muted small">Menyetujui,</p>
                    <p class="fw-bold mb-5">Ketua RT 06</p>
                    <br>
                    <p class="fw-bold text-decoration-underline mb-0">DIMAS</p>
                    <small class="text-muted">Ketua RT 06 / RW 19</small>
                </div>
                <div class="col-4">
                    <p class="mb-1 text-muted small">Bekasi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p class="fw-bold mb-5">Ketua RT 07</p>
                    <br>
                    <p class="fw-bold text-decoration-underline mb-0">BAMBANG</p>
                    <small class="text-muted">Ketua RT 07 / RW 19</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
