@extends('layouts.app')

@section('title', 'Manajemen Bank Digital Warga')
@section('page_title', 'Manajemen Bank Digital Warga RW 19')

@section('content')
<div class="row g-4">
    <!-- Header Summary Stats -->
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1">Pusat Pengelolaan Bank Digital Warga RW 19</h5>
                <p class="text-muted small mb-0">Integrasi dompet digital kas warga untuk pembayaran iuran RT, integrasi bank sampah, dan tap IoT e-KTP.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAdminSimulasiIoT">
                    <i class="fa-solid fa-nfc-directional me-1"></i> Simulator Reader IoT
                </button>
                <button class="btn btn-primary-custom rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBukaRekeningBaru">
                    <i class="fa-solid fa-user-plus me-1"></i> Buka Rekening Warga
                </button>
            </div>
        </div>
    </div>

    <!-- 4 Kartu Metrik -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-4">
                    <i class="fa-solid fa-vault fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Total Simpanan Warga</span>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalSaldoSeluruhWarga, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4">
                    <i class="fa-solid fa-users-viewfinder fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Nasabah Terdaftar</span>
                    <h5 class="fw-bold mb-0 text-dark">{{ $totalNasabah }} Warga</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-4">
                    <i class="fa-solid fa-recycle fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Total Dana Bank Sampah</span>
                    <h5 class="fw-bold mb-0 text-info">Rp {{ number_format($totalPencairanSampah, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4">
                    <i class="fa-solid fa-chart-line fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small">Mutasi Hari Ini</span>
                    <h5 class="fw-bold mb-0 text-dark">{{ $totalTrxHariIni }} Transaksi</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Rekening Warga -->
    <div class="col-12">
        <div class="card-custom p-4">
            <!-- Filter & Pencarian -->
            <form action="{{ route('bank-warga.index') }}" method="GET" class="row g-2 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama warga / nomor rekening / NIK..." value="{{ request('search') }}">
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
                    <a href="{{ route('bank-warga.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>No</th>
                            <th>No. Rekening</th>
                            <th>Nama Nasabah / Warga</th>
                            <th>Wilayah RT</th>
                            <th>NIK & No. KK</th>
                            <th>Metode Daftar</th>
                            <th class="text-end">Saldo Saat Ini</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekeningList as $index => $r)
                            <tr>
                                <td class="small">{{ $rekeningList->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold text-dark font-monospace">{{ $r->nomor_rekening }}</span>
                                    @if($r->rfid_uid)
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-id-card text-success"></i> {{ $r->rfid_uid }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $r->user->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $r->user->no_hp ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        RT {{ $r->user->rt ?? '06' }} / RW 19
                                    </span>
                                </td>
                                <td class="small">
                                    <div>NIK: {{ $r->nik ?? ($r->user->nik ?? '-') }}</div>
                                    <div class="text-muted">KK: {{ $r->no_kk ?? ($r->user->no_kk ?? '-') }}</div>
                                </td>
                                <td>
                                    @if($r->metode_daftar === 'iot_tap_ktp')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-nfc-directional me-1"></i> Tap IoT e-KTP
                                        </span>
                                    @elseif($r->metode_daftar === 'otomatis_bank_sampah')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-recycle me-1"></i> Dari Bank Sampah
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-user-check me-1"></i> Akun Warga
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success fs-6">
                                    Rp {{ number_format($r->saldo, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($r->status === 'aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">{{ ucfirst($r->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" data-bs-toggle="modal" data-bs-target="#modalAdminTopUp{{ $r->id }}">
                                        <i class="fa-solid fa-plus me-1"></i> Setor / Top Up
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Belum ada data rekening Bank Digital Warga terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar (10 baris per tab) -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <div class="small text-muted">
                    Menampilkan <strong>{{ $rekeningList->firstItem() ?? 0 }}</strong> – <strong>{{ $rekeningList->lastItem() ?? 0 }}</strong> dari total <strong>{{ $rekeningList->total() }}</strong> rekening warga (10 baris per halaman).
                </div>
                <div>
                    {{ $rekeningList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buka Rekening Cepat untuk Warga -->
<div class="modal fade" id="modalBukaRekeningBaru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Aktivasi Rekening Bank Warga Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-warga.daftar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Pilih warga RW 19 yang ingin diaktifkan rekening Bank Digital Warganya. Nomor rekening dibuat otomatis.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Warga</label>
                        <select name="id_user" class="form-select" required>
                            <option value="">-- Pilih Warga (Belum Berrekening) --</option>
                            @foreach($daftarWargaBelumPunya as $wb)
                                <option value="{{ $wb->id }}">{{ $wb->nama }} (NIK: {{ $wb->nik ?? '-' }} • RT {{ $wb->rt ?? '06' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Buka Rekening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Admin Bantu Top Up untuk Warga -->
@foreach($rekeningList as $rekItem)
<div class="modal fade" id="modalAdminTopUp{{ $rekItem->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Setor Tunai / Top-Up Saldo Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-warga.topup') }}" method="POST">
                @csrf
                <input type="hidden" name="id_user" value="{{ $rekItem->id_user }}">
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="small text-muted">Nama Nasabah:</div>
                        <div class="fw-bold">{{ $rekItem->user->nama ?? '-' }} (RT {{ $rekItem->user->rt ?? '06' }})</div>
                        <div class="small text-muted mt-1">No Rekening: <span class="font-monospace fw-bold">{{ $rekItem->nomor_rekening }}</span></div>
                        <div class="small text-muted">Saldo Saat Ini: <span class="fw-bold text-success">Rp {{ number_format($rekItem->saldo, 0, ',', '.') }}</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nominal Setoran (Rp)</label>
                        <input type="number" name="jumlah" class="form-control" value="25000" min="5000" step="5000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Metode Setoran</label>
                        <select name="metode" class="form-select">
                            <option value="Setoran Tunai di Pos RT">Setor Tunai di Pos RT / Kantor RW</option>
                            <option value="Transfer Bank Pengurus">Transfer Bank ke Pengurus RT</option>
                            <option value="QRIS Pos RT">QRIS Pos RT</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Proses Setoran Saldo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Simulator Admin IoT Reader -->
<div class="modal fade" id="modalAdminSimulasiIoT" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Simulator IoT RFID / e-KTP Reader</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Simulasi scan tap e-KTP warga pada mesin reader fisik di Pos RW 19.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Pilih Warga untuk Simulasi Tap</label>
                    <select id="selectWargaSimulator" class="form-select" onchange="updateSimulatorInputs(this)">
                        <option value="">-- Pilih Warga --</option>
                        @foreach($rekeningList as $rSim)
                            <option value="{{ $rSim->id_user }}" data-nik="{{ $rSim->user->nik ?? '' }}" data-rfid="{{ $rSim->user->rfid_uid ?? 'UID-KTP-' . $rSim->id_user . '991' }}">
                                {{ $rSim->user->nama ?? '-' }} (RT {{ $rSim->user->rt ?? '06' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">UID RFID e-KTP</label>
                        <input type="text" id="simRfidUid" class="form-control form-control-sm font-monospace" placeholder="e.g. A3-4F-91-0C">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">NIK e-KTP</label>
                        <input type="text" id="simNik" class="form-control form-control-sm font-monospace" placeholder="e.g. 327501...">
                    </div>
                </div>

                <div id="adminIotResult" class="d-none alert alert-success small mb-0"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="jalankanAdminSimulasiTap()">
                    <i class="fa-solid fa-nfc-directional me-1"></i> Simulasikan Tap Alat IoT
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateSimulatorInputs(select) {
        const opt = select.options[select.selectedIndex];
        document.getElementById('simNik').value = opt.getAttribute('data-nik') || '';
        document.getElementById('simRfidUid').value = opt.getAttribute('data-rfid') || '';
    }

    function jalankanAdminSimulasiTap() {
        const rfid = document.getElementById('simRfidUid').value;
        const nik = document.getElementById('simNik').value;
        const resDiv = document.getElementById('adminIotResult');

        if (!rfid && !nik) {
            alert('Pilih warga atau masukkan NIK / UID kartu terlebih dahulu.');
            return;
        }

        fetch("{{ route('api.iot.bank_warga.tap') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                rfid_uid: rfid,
                nik: nik,
                device_id: 'IOT-READER-POS-RW19'
            })
        })
        .then(res => res.json())
        .then(data => {
            resDiv.classList.remove('d-none');
            if (data.status === 'success') {
                resDiv.className = 'alert alert-success small mb-0';
                resDiv.innerHTML = `<strong><i class="fa-solid fa-check-circle me-1"></i> ${data.message}</strong><br>
                Nama: <b>${data.data.nama}</b> (RT ${data.data.rt})<br>
                No Rek: <b>${data.data.nomor_rekening}</b><br>
                Saldo Terkini: <b class="text-success">${data.data.saldo_format}</b>`;
            } else {
                resDiv.className = 'alert alert-danger small mb-0';
                resDiv.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i> ${data.message}`;
            }
        })
        .catch(err => {
            resDiv.classList.remove('d-none');
            resDiv.className = 'alert alert-danger small mb-0';
            resDiv.innerHTML = 'Error: ' + err.message;
        });
    }
</script>
@endsection
