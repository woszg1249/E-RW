@extends('layouts.app')

@section('title', 'Bank Digital Warga')
@section('page_title', 'Bank Digital Warga RW 19 (RT 06 & RT 07)')

@section('styles')
<style>
    .atm-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #064e3b 100%);
        color: #ffffff;
        border-radius: 20px;
        padding: 24px 28px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
        min-height: 220px;
    }
    .atm-card::after {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
        top: -60px;
        right: -60px;
        border-radius: 50%;
        pointer-events: none;
    }
    .atm-chip {
        width: 44px;
        height: 32px;
        background: linear-gradient(135deg, #fbbf24, #d97706);
        border-radius: 6px;
        display: inline-block;
        box-shadow: inset 0 0 4px rgba(0,0,0,0.3);
    }
    .rek-number {
        font-family: 'Courier New', Courier, monospace;
        letter-spacing: 2px;
        font-size: 1.25rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4);
    }
</style>
@endsection

@section('content')
<div class="row g-4">
    <!-- Header & Kartu ATM Digital -->
    <div class="col-lg-5 col-12">
        <div class="atm-card d-flex flex-column justify-content-between h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('img/logo_ert_rw.svg') }}" alt="Logo RW 19" style="width: 38px; height: 38px;">
                    <div>
                        <div class="fw-bold small tracking-wide">BANK DIGITAL WARGA</div>
                        <div class="small opacity-75" style="font-size: 0.75rem;">RW 19 • BEKASI JAYA</div>
                    </div>
                </div>
                <span class="badge bg-success bg-opacity-25 border border-success border-opacity-50 text-white rounded-pill px-3 py-1 small">
                    <i class="fa-solid fa-wifi me-1"></i> IoT e-KTP Aktif
                </span>
            </div>

            <div class="my-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="atm-chip"></div>
                    <i class="fa-solid fa-nfc-directional fs-4 opacity-75"></i>
                </div>
                <div class="rek-number mb-1">{{ $rekening->nomor_rekening }}</div>
                <div class="small opacity-75 text-uppercase">{{ $rekening->user->nama ?? auth()->user()->nama }}</div>
            </div>

            <div class="d-flex justify-content-between align-items-end pt-2 border-top border-white border-opacity-10">
                <div>
                    <div class="small opacity-75" style="font-size: 0.75rem;">SALDO TERSEDIA</div>
                    <div class="fs-4 fw-bold text-warning">Rp {{ number_format($rekening->saldo, 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                    <div class="small opacity-75" style="font-size: 0.75rem;">WILAYAH</div>
                    <div class="small fw-semibold">RT {{ $rekening->user->rt ?? '06' }} / RW 19</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action & Ringkasan Keuangan -->
    <div class="col-lg-7 col-12">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Layanan Keuangan Digital Warga</h5>
                        <p class="text-muted small mb-0">Saldo digunakan langsung untuk bayar tagihan iuran, hasil bank sampah otomatis masuk.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary-custom rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTopUp">
                            <i class="fa-solid fa-plus me-1"></i> Top-Up Saldo
                        </button>
                        <button class="btn btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalSimulasiIoT">
                            <i class="fa-solid fa-id-card me-1"></i> Tap e-KTP IoT
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 col-12">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                    <i class="fa-solid fa-arrow-down-left fs-5"></i>
                                </div>
                                <div>
                                    <span class="text-muted small">Total Dana Masuk</span>
                                    <h6 class="fw-bold mb-0 text-success">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Top Up & Setoran Sampah</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-12">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                                    <i class="fa-solid fa-arrow-up-right fs-5"></i>
                                </div>
                                <div>
                                    <span class="text-muted small">Total Pengeluaran</span>
                                    <h6 class="fw-bold mb-0 text-danger">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Pembayaran Iuran RT</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-12">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                                    <i class="fa-solid fa-recycle fs-5"></i>
                                </div>
                                <div>
                                    <span class="text-muted small">Dari Bank Sampah</span>
                                    <h6 class="fw-bold mb-0 text-info">+ Rp {{ number_format($totalDariSampah, 0, ',', '.') }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Otomatis transfer masuk</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-12">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                    <i class="fa-solid fa-receipt fs-5"></i>
                                </div>
                                <div>
                                    <span class="text-muted small">Iuran Terbayar</span>
                                    <h6 class="fw-bold mb-0 text-primary">Rp {{ number_format($totalBayarIuran, 0, ',', '.') }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;"><a href="{{ route('tagihan.index') }}" class="text-decoration-none">Cek Tagihan Lainnya &raquo;</a></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 p-2 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 d-flex align-items-center justify-content-between small text-success">
                <span>
                    <i class="fa-solid fa-circle-check me-1"></i> Rekening Anda terhubung dengan NIK <strong>{{ $rekening->user->nik ?? '-' }}</strong> & No KK <strong>{{ $rekening->user->no_kk ?? '-' }}</strong>.
                </span>
                <a href="{{ route('tagihan.index') }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold text-white">
                    Bayar Iuran Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi / Mutasi Buku Tabungan -->
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Mutasi Buku Tabungan Bank Warga</h5>
                    <p class="text-muted small mb-0">Catatan transaksi lengkap otomatis saldo masuk dan keluar.</p>
                </div>

                <!-- Filter -->
                <form action="{{ route('bank-warga.index') }}" method="GET" class="d-flex gap-2">
                    <select name="jenis" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Jenis (Masuk / Keluar)</option>
                        <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Dana Masuk (Kredit)</option>
                        <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Dana Keluar (Debit)</option>
                    </select>
                    <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <option value="bank_sampah" {{ request('kategori') == 'bank_sampah' ? 'selected' : '' }}>Hasil Bank Sampah</option>
                        <option value="bayar_iuran" {{ request('kategori') == 'bayar_iuran' ? 'selected' : '' }}>Pembayaran Iuran RT</option>
                        <option value="topup_bank" {{ request('kategori') == 'topup_bank' ? 'selected' : '' }}>Top Up Transfer Bank</option>
                        <option value="topup_ewallet" {{ request('kategori') == 'topup_ewallet' ? 'selected' : '' }}>Top Up E-Wallet / QRIS</option>
                    </select>
                    @if(request()->hasAny(['jenis', 'kategori']))
                        <a href="{{ route('bank-warga.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>Tanggal & Waktu</th>
                            <th>Kode Transaksi</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Keterangan / Uraian</th>
                            <th>Metode / Kanal</th>
                            <th class="text-end">Nominal (Rp)</th>
                            <th class="text-end">Saldo Akhir (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiList as $idx => $t)
                            <tr>
                                <td class="small">{{ $transaksiList->firstItem() + $idx }}</td>
                                <td class="small text-muted">
                                    {{ \Carbon\Carbon::parse($t->created_at)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td>
                                    <code class="small text-dark">{{ $t->kode_transaksi }}</code>
                                </td>
                                <td>
                                    @if($t->jenis === 'masuk')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-arrow-down-left me-1"></i> Masuk (CR)
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-arrow-up-right me-1"></i> Keluar (DB)
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($t->kategori === 'bank_sampah')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-recycle me-1"></i> Bank Sampah
                                        </span>
                                    @elseif($t->kategori === 'bayar_iuran')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-receipt me-1"></i> Bayar Iuran
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-wallet me-1"></i> Top-Up
                                        </span>
                                    @endif
                                </td>
                                <td class="small fw-semibold">{{ $t->keterangan }}</td>
                                <td class="small text-muted">{{ $t->metode_detail ?? '-' }}</td>
                                <td class="text-end fw-bold {{ $t->jenis === 'masuk' ? 'text-success' : 'text-danger' }}">
                                    {{ $t->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-semibold small text-dark">
                                    Rp {{ number_format($t->saldo_sesudah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-receipt fs-3 mb-2 d-block opacity-50"></i>
                                    Belum ada transaksi di rekening Bank Digital Warga Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar (10 baris per halaman) -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <div class="small text-muted">
                    Menampilkan <strong>{{ $transaksiList->firstItem() ?? 0 }}</strong> – <strong>{{ $transaksiList->lastItem() ?? 0 }}</strong> dari total <strong>{{ $transaksiList->total() }}</strong> mutasi rekening (10 baris per halaman).
                </div>
                <div>
                    {{ $transaksiList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Top Up Saldo Mandiri -->
<div class="modal fade" id="modalTopUp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">Top-Up Saldo Bank Warga</h5>
                    <p class="text-muted small mb-0">Isi saldo rekening untuk persiapan pembayaran iuran RT.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-warga.topup') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="small text-muted">Rekening Penerima:</div>
                        <div class="fw-bold">{{ $rekening->nomor_rekening }} ({{ $rekening->user->nama ?? auth()->user()->nama }})</div>
                        <div class="small text-muted mt-1">Saldo Saat Ini: <span class="fw-bold text-success">Rp {{ number_format($rekening->saldo, 0, ',', '.') }}</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Nominal Top-Up</label>
                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 btn-sm rounded-pill py-2" onclick="setNominalTopUp(25000)">Rp 25.000</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 btn-sm rounded-pill py-2" onclick="setNominalTopUp(50000)">Rp 50.000</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 btn-sm rounded-pill py-2" onclick="setNominalTopUp(100000)">Rp 100.000</button>
                            </div>
                        </div>
                        <input type="number" name="jumlah" id="inputJumlahTopUp" class="form-control" placeholder="Atau ketik nominal lainnya (Min. 5.000)..." min="5000" step="1000" value="50000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Kanal Pembayaran / Sumber Dana</label>
                        <select name="metode" class="form-select">
                            <optgroup label="Transfer Antar Bank (Virtual Account)">
                                <option value="Transfer Bank BCA">Bank Central Asia (BCA Virtual Account)</option>
                                <option value="Transfer Bank Mandiri">Bank Mandiri (Mandiri Bill Payment)</option>
                                <option value="Transfer Bank BRI">Bank BRI (BRIVA)</option>
                                <option value="Transfer Bank BNI">Bank BNI (BNI Virtual Account)</option>
                            </optgroup>
                            <optgroup label="Dompet Digital / E-Wallet Instant">
                                <option value="QRIS GoPay">QRIS (GoPay Instant)</option>
                                <option value="DANA Dompet Digital">DANA</option>
                                <option value="OVO Payment">OVO</option>
                                <option value="ShopeePay">ShopeePay</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="alert alert-info py-2 small mb-0">
                        <i class="fa-solid fa-info-circle me-1"></i> Simulasi pembayaran instan terverifikasi otomatis. Saldo Bank Warga Anda akan langsung bertambah.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Konfirmasi Top-Up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Simulasi Tap e-KTP IoT Reader -->
<div class="modal fade" id="modalSimulasiIoT" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                        <i class="fa-solid fa-nfc-directional fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Simulasi Tap e-KTP (IoT Reader)</h5>
                        <p class="text-muted small mb-0">Uji coba simulasi alat RFID IoT Reader di Pos RW/RT</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <div class="mb-3">
                        <i class="fa-solid fa-id-card-clip fs-1 text-primary animate__animated animate__pulse animate__infinite"></i>
                    </div>
                    <h6 class="fw-bold">Alat IoT RFID Reader Siap Menerima Tap</h6>
                    <p class="text-muted small px-3">
                        Setiap warga yang menempelkan e-KTP atau kartu RFID terdaftar ke mesin IoT pos RT, sistem otomatis memverifikasi saldo dan rekening bank warga.
                    </p>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="row g-2 small">
                        <div class="col-4 text-muted">ID Perangkat:</div>
                        <div class="col-8 fw-semibold"><code>IOT-BW-01 (Pos RW 19)</code></div>
                        <div class="col-4 text-muted">Kartu Anda:</div>
                        <div class="col-8 fw-semibold">{{ auth()->user()->rfid_uid ?? 'UID-KTP-' . auth()->id() . '882' }}</div>
                        <div class="col-4 text-muted">NIK Warga:</div>
                        <div class="col-8 fw-semibold">{{ auth()->user()->nik ?? '-' }}</div>
                        <div class="col-4 text-muted">No Rekening:</div>
                        <div class="col-8 fw-bold text-success">{{ $rekening->nomor_rekening }}</div>
                    </div>
                </div>

                <div id="iotTapResult" class="d-none alert alert-success small mb-0">
                    <!-- Dynamic Result -->
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnSimulasiTap" onclick="simulasikanTapIoT('{{ auth()->user()->rfid_uid ?? 'UID-KTP-' . auth()->id() . '882' }}', '{{ auth()->user()->nik }}')">
                    <i class="fa-solid fa-fingerprint me-1"></i> Tap Kartu Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setNominalTopUp(val) {
        document.getElementById('inputJumlahTopUp').value = val;
    }

    function simulasikanTapIoT(rfid, nik) {
        const btn = document.getElementById('btnSimulasiTap');
        const resDiv = document.getElementById('iotTapResult');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Membaca Kartu...';

        fetch("{{ route('api.iot.bank_warga.tap') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                rfid_uid: rfid,
                nik: nik,
                device_id: 'IOT-BW-01-SIMULATOR'
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-fingerprint me-1"></i> Tap Lagi';
            resDiv.classList.remove('d-none');
            if (data.status === 'success') {
                resDiv.className = 'alert alert-success small mb-0';
                resDiv.innerHTML = `<strong><i class="fa-solid fa-check-circle me-1"></i> ${data.message}</strong><br>
                Nama: <b>${data.data.nama}</b><br>
                No Rek: <b>${data.data.nomor_rekening}</b><br>
                Saldo Terkini: <b class="text-success">${data.data.saldo_format}</b> (Status: ${data.data.status_rekening})`;
            } else {
                resDiv.className = 'alert alert-danger small mb-0';
                resDiv.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i> ${data.message}`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-fingerprint me-1"></i> Tap Kartu Sekarang';
            resDiv.classList.remove('d-none');
            resDiv.className = 'alert alert-danger small mb-0';
            resDiv.innerHTML = 'Gagal menghubungi IoT Reader simulator: ' + err.message;
        });
    }
</script>
@endsection
