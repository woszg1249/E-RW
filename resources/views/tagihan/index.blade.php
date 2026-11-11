@extends('layouts.app')

@section('title', 'Tagihan Iuran Warga')
@section('page_title', 'Tagihan Iuran Warga RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Stat Cards -->
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white border-start border-danger border-4">
            <span class="text-muted small fw-bold text-uppercase">Total Tunggakan Belum Lunas</span>
            <h3 class="fw-bold text-danger mb-0 mt-1">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
            <small class="text-muted">Total kewajiban iuran yang belum disetorkan</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom p-4 bg-white border-start border-success border-4">
            <span class="text-muted small fw-bold text-uppercase">Total Iuran Terkumpul (Lunas)</span>
            <h3 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h3>
            <small class="text-muted">Iuran yang telah terverifikasi dan masuk ke Kas RT/RW</small>
        </div>
    </div>

    <!-- Main Card -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Tagihan Iuran Lingkungan</h5>
                    <p class="text-muted small mb-0">Kelola dan bayar iuran bulanan kas RT 06 dan RT 07 secara terintegrasi</p>
                </div>

                @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-primary-custom rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalGenerateTagihan">
                        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Buat Tagihan Iuran
                    </button>
                @endif
            </div>

            <!-- Filter -->
            <form action="{{ route('tagihan.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-3">
                    <input type="text" name="bulan_tahun" class="form-control form-control-sm" placeholder="Periode (misal: November 2026)" value="{{ request('bulan_tahun') }}">
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
                        <option value="">Semua Status Pembayaran</option>
                        <option value="Belum Lunas" {{ request('status') === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="Lunas" {{ request('status') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter Data</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('tagihan.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Nama Warga</th>
                            <th>Wilayah RT</th>
                            <th>Periode Iuran</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tgl Update</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihanList as $index => $item)
                            @php
                                $rtWarga = $item->user->rt ?? '06';
                                $badgeRt = ($rtWarga == '07') ? 'bg-primary text-primary' : 'bg-success text-success';
                            @endphp
                            <tr>
                                <td class="small">{{ $tagihanList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->user->nama ?? '-' }}</div>
                                    <small class="text-muted">NIK: {{ $item->user->nik ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-opacity-10 {{ $badgeRt }} border rounded-pill px-2 py-1 small fw-bold">
                                        RT {{ $rtWarga }} / RW 19
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill small fw-semibold">
                                        {{ $item->bulan_tahun }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->status_bayar === 'Lunas')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-clock me-1"></i> Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $item->status_bayar === 'Lunas' && $item->tgl_update ? \Carbon\Carbon::parse($item->tgl_update)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($item->status_bayar === 'Belum Lunas')
                                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $item->id_tagihan }}">
                                            <i class="fa-solid fa-credit-card me-1"></i> Bayar
                                        </button>
                                    @else
                                        <span class="text-success small fw-semibold"><i class="fa-solid fa-check-double"></i> Terverifikasi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Tidak ada data tagihan yang sesuai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tagihanList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Tagihan (Admin) -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalGenerateTagihan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Tagihan Iuran Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tagihan.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bulan & Tahun Periode</label>
                        <input type="text" name="bulan_tahun" class="form-control" placeholder="Contoh: November 2026" value="{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tanggal Jatuh Tempo / Tagihan</label>
                        <input type="date" name="tanggal_tagihan" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nominal Iuran (Rp)</label>
                        <input type="number" name="jumlah" class="form-control" value="25000" min="1000" step="1000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Target Penerima Tagihan</label>
                        <select name="target" class="form-select" id="selectTargetTagihan" onchange="togglePilihWarga(this.value)">
                            <option value="semua">Semua Warga RW 19 (RT 06 & RT 07)</option>
                            <option value="rt06">Khusus Warga RT 06 (Ketua: Dimas)</option>
                            <option value="rt07">Khusus Warga RT 07 (Ketua: Bambang)</option>
                            <option value="individu">Satu Warga Tertentu</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="divPilihWarga">
                        <label class="form-label fw-semibold small">Pilih Warga</label>
                        <select name="id_user" class="form-select">
                            <option value="">-- Pilih Warga --</option>
                            @foreach($daftarWarga as $w)
                                <option value="{{ $w->id }}">{{ $w->nama }} (RT {{ $w->rt ?? '06' }}/RW 19)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Generate Tagihan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- MODAL BAYAR DILETAKKAN DI LUAR TABEL AGAR BEKERJA 100% TANPA KUNCI BACKDROP -->
@foreach($tagihanList as $item)
    @if($item->status_bayar === 'Belum Lunas')
        <div class="modal fade" id="modalBayar{{ $item->id_tagihan }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Konfirmasi Pembayaran Iuran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('tagihan.bayar', $item->id_tagihan) }}" method="POST">
                        @csrf
                        <div class="modal-body text-center">
                            <div class="p-3 bg-light rounded-3 mb-3 text-start">
                                <div class="row g-2 small">
                                    <div class="col-5 text-muted">Nama Warga:</div>
                                    <div class="col-7 fw-bold">{{ $item->user->nama ?? '-' }}</div>
                                    <div class="col-5 text-muted">Wilayah:</div>
                                    <div class="col-7 fw-bold">RT {{ $item->user->rt ?? '06' }} / RW 19</div>
                                    <div class="col-5 text-muted">Periode Iuran:</div>
                                    <div class="col-7 fw-bold">{{ $item->bulan_tahun }}</div>
                                    <div class="col-5 text-muted">Total Pembayaran:</div>
                                    <div class="col-7 fw-bold text-success fs-6">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <p class="small text-muted mb-2">Pindai QRIS di bawah ini melalui GoPay / OVO / Dana / BCA / Mandiri:</p>
                                <img src="{{ asset('img/logo_Qris.jpeg') }}" alt="QRIS Iuran RT" class="img-fluid rounded border" style="max-height: 200px;" onerror="this.src='https://placehold.co/220x220?text=QRIS+RW+19'">
                            </div>

                            <div class="mb-3 text-start">
                                @php
                                    $rekWargaItem = \App\Models\BankWargaRekening::where('id_user', $item->id_user)->first();
                                    $saldoWargaItem = $rekWargaItem ? (float) $rekWargaItem->saldo : 0;
                                    $cukupSaldo = $saldoWargaItem >= (float) $item->jumlah;
                                @endphp
                                <label class="form-label fw-semibold small">Metode Pembayaran</label>
                                <select name="metode" class="form-select form-select-sm" id="metodeBayar{{ $item->id_tagihan }}" onchange="handleMetodeChange(this.value, '{{ $item->id_tagihan }}', {{ $saldoWargaItem }}, {{ (float) $item->jumlah }})">
                                    <option value="Bank Digital Warga" {{ $cukupSaldo ? 'selected' : '' }}>
                                        🏦 Bank Digital Warga (Saldo: Rp {{ number_format($saldoWargaItem, 0, ',', '.') }})
                                    </option>
                                    <option value="QRIS Digital" {{ !$cukupSaldo ? 'selected' : '' }}>QRIS Digital (GoPay, OVO, ShopeePay, DANA)</option>
                                    <option value="Transfer Bank">Transfer Bank (BCA / Mandiri)</option>
                                    <option value="Tunai / Cash">Tunai ke Pengurus RT</option>
                                </select>
                                <div id="infoBankWarga{{ $item->id_tagihan }}" class="mt-2 p-2 rounded small {{ $cukupSaldo ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25' }}">
                                    @if($cukupSaldo)
                                        <i class="fa-solid fa-circle-check me-1"></i> Saldo Bank Warga mencukupi. Pembayaran akan memotong saldo secara otomatis & lunas seketika.
                                    @else
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Saldo Bank Warga Rp {{ number_format($saldoWargaItem, 0, ',', '.') }} (Kurang Rp {{ number_format($item->jumlah - $saldoWargaItem, 0, ',', '.') }}). Silakan Top Up atau setor bank sampah terlebih dahulu.
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="fa-solid fa-check me-1"></i> Konfirmasi Telah Dibayar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@section('scripts')
<script>
    function togglePilihWarga(val) {
        const div = document.getElementById('divPilihWarga');
        if (val === 'individu') {
            div.classList.remove('d-none');
        } else {
            div.classList.add('d-none');
        }
    }

    function handleMetodeChange(val, id, saldo, tagihan) {
        const info = document.getElementById('infoBankWarga' + id);
        if (!info) return;
        if (val === 'Bank Digital Warga') {
            info.classList.remove('d-none');
        } else {
            info.classList.add('d-none');
        }
    }
</script>
@endsection
