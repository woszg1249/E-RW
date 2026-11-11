@extends('layouts.app')

@section('title', 'Laporan Keuangan Kas RT')
@section('page_title', 'Laporan Rekapitulasi Kas RT')

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
        }
    }
</style>
@endsection

@section('content')
<div class="row g-4">
    <!-- Filter Bar (No Print) -->
    <div class="col-12 no-print">
        <div class="card-custom p-3 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <form action="{{ route('keuangan.laporan') }}" method="GET" class="d-flex align-items-center gap-2">
                <select name="bulan" class="form-select form-select-sm" style="width: 140px;">
                    @for($m = 1; $m <= 12; $m++)
                        @php $mStr = sprintf('%02d', $m); @endphp
                        <option value="{{ $mStr }}" {{ $bulan == $mStr ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="tahun" class="form-select form-select-sm" style="width: 100px;">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-sm btn-dark px-3">Tampilkan</button>
            </form>

            <button onclick="window.print()" class="btn btn-sm btn-primary-custom rounded-pill px-4">
                <i class="fa-solid fa-print me-1"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Printable Report Card -->
    <div class="col-12">
        <div class="card-custom p-4 p-md-5 bg-white">
            <!-- Kop Surat Resmi RT/RW -->
            <div class="d-flex align-items-center justify-content-center gap-4 pb-3 mb-4 border-bottom border-2 border-dark text-center">
                <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RT RW" style="width: 70px; height: 70px;">
                <div>
                    <h4 class="fw-bold mb-0 text-uppercase tracking-wide">RUKUN TETANGGA 06 / RUKUN WARGA 19</h4>
                    <h5 class="fw-bold mb-1 text-uppercase">KELURAHAN BEKASI JAYA - KECAMATAN BEKASI TIMUR</h5>
                    <p class="mb-0 small text-secondary">Sekretariat: Jl. Melati Raya No. 12, RW 19, Bekasi Jaya, Kota Bekasi • Telp: 0812-3456-7890</p>
                </div>
            </div>

            <div class="text-center mb-4">
                <h5 class="fw-bold text-decoration-underline mb-1">LAPORAN ARUS KAS KEUANGAN BULANAN</h5>
                <span class="text-muted small">Periode: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}</span>
            </div>

            <!-- Summary Table Header -->
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="p-3 border rounded text-center">
                        <span class="small text-muted d-block">Pemasukan Periode Ini</span>
                        <strong class="text-success fs-6">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 border rounded text-center">
                        <span class="small text-muted d-block">Pengeluaran Periode Ini</span>
                        <strong class="text-danger fs-6">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 border rounded text-center">
                        <span class="small text-muted d-block">Total Saldo Kas RT</span>
                        <strong class="text-primary fs-6">Rp {{ number_format($saldoTotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center small">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 110px;">Tanggal</th>
                            <th>Uraian Transaksi</th>
                            <th>Pihak / Warga</th>
                            <th style="width: 130px;">Pemasukan (Rp)</th>
                            <th style="width: 130px;">Pengeluaran (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $i => $item)
                            <tr>
                                <td class="text-center small">{{ $i + 1 }}</td>
                                <td class="text-center small">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="small text-muted">{{ $item->nama_warga ?? '-' }}</td>
                                <td class="text-end text-success fw-semibold">
                                    {{ $item->jenis === 'masuk' ? number_format($item->jumlah, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end text-danger fw-semibold">
                                    {{ $item->jenis === 'keluar' ? number_format($item->jumlah, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada transaksi pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">TOTAL PERIODE INI:</td>
                            <td class="text-end text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">SURPLUS / DEFISIT PERIODE:</td>
                            <td colspan="2" class="text-end {{ $saldoPeriode >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($saldoPeriode, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Signatures Section -->
            <div class="row text-center mt-5 pt-3">
                <div class="col-6">
                    <p class="mb-1 text-muted small">Mengetahui,</p>
                    <p class="fw-bold mb-5">Ketua RT 06</p>
                    <br>
                    <p class="fw-bold text-decoration-underline mb-0">Reival al khafi</p>
                    <small class="text-muted">NIP/NIK: 3275012304950001</small>
                </div>
                <div class="col-6">
                    <p class="mb-1 text-muted small">Bekasi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p class="fw-bold mb-5">Bendahara RT 06</p>
                    <br>
                    <p class="fw-bold text-decoration-underline mb-0">Agus Kusuma</p>
                    <small class="text-muted">NIP/NIK: 3275012304950002</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
