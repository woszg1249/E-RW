@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Manajemen Kartu Keluarga' : 'Kartu Keluarga Saya')
@section('page_title', auth()->user()->isAdmin() ? 'Data Kartu Keluarga (KK) RW 19 (RT 06 & RT 07)' : 'Kartu Keluarga Saya')

@section('content')
<div class="row g-4">
    @if(auth()->user()->isAdmin())
        <!-- Header Admin -->
        <div class="col-12">
            <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Kartu Keluarga Warga RW 19</h5>
                    <p class="text-muted small mb-0">Kelola susunan keluarga, kepala keluarga, dan cetak Kartu Keluarga resmi wilayah RT 06 dan RT 07</p>
                </div>

                <form action="{{ route('warga.kk') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" style="min-width: 180px;" placeholder="Cari No KK / Alamat..." value="{{ request('search') }}">
                    <select name="rt" class="form-select form-select-sm" style="min-width: 150px;">
                        <option value="">Semua Wilayah RT</option>
                        <option value="06" {{ request('rt') == '06' ? 'selected' : '' }}>RT 06 (Dimas)</option>
                        <option value="07" {{ request('rt') == '07' ? 'selected' : '' }}>RT 07 (Bambang)</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
                    <a href="{{ route('warga.kk') }}" class="btn btn-sm btn-outline-secondary px-2">Reset</a>
                </form>
            </div>
        </div>

        <!-- Family Cards Grid untuk Admin -->
        @forelse($families as $fam)
            @php
                $isRt07 = ($fam['rt'] == '07');
                $badgeColor = $isRt07 ? 'bg-primary' : 'bg-success';
                $famMembers = collect($fam['members'])->sortBy(function($m) {
                    $status = strtolower(trim($m->status_keluarga ?? ''));
                    $p = ['kepala keluarga' => 1, 'suami' => 2, 'istri' => 3, 'anak' => 4, 'menantu' => 5, 'cucu' => 6, 'orangtua' => 7, 'mertua' => 8, 'famili lain' => 9];
                    return $p[$status] ?? 99;
                })->values();
            @endphp
            <div class="col-lg-6">
                <div class="card-custom p-4 h-100 bg-white">
                    <div class="d-flex align-items-start justify-content-between mb-3 border-bottom pb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-light text-primary border rounded-pill px-3 py-1 small fw-semibold font-monospace">
                                    NO KK: {{ $fam['no_kk'] }}
                                </span>
                                <span class="badge {{ $badgeColor }} bg-opacity-10 {{ $isRt07 ? 'text-primary' : 'text-success' }} border rounded-pill px-2 py-1 small fw-bold">
                                    RT {{ $fam['rt'] ?? '06' }} / RW 19
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">{{ $fam['kepala']->nama ?? 'Keluarga' }}</h5>
                            <small class="text-muted"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $fam['alamat'] ?? '-' }}</small>
                        </div>
                        <a href="{{ route('warga.kk.cetak', $fam['no_kk']) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fa-solid fa-print me-1"></i> Cetak KK
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <thead class="text-muted small">
                                <tr>
                                    <th>Nama Anggota</th>
                                    <th>NIK</th>
                                    <th>Hubungan</th>
                                    <th>L/P</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($famMembers as $m)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $m->nama }}</div>
                                            <small class="text-muted">{{ $m->pekerjaan ?? '-' }}</small>
                                        </td>
                                        <td class="small font-monospace">{{ $m->nik ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $m->status_keluarga === 'Kepala Keluarga' ? 'bg-primary' : 'bg-light text-dark border' }} rounded-pill px-2 py-1 small">
                                                {{ $m->status_keluarga ?? 'Anggota' }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $m->kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-custom p-5 text-center text-muted bg-white">
                    <i class="fa-solid fa-address-card fs-1 mb-2 text-secondary"></i>
                    <h6>Belum ada data Kartu Keluarga untuk wilayah ini.</h6>
                </div>
            </div>
        @endforelse

        <div class="col-12 mt-4">
            {{ $kkList->links('pagination::bootstrap-5') }}
        </div>

    @else
        <!-- Tampilan Khusus Warga: Hanya Kartu Keluarganya Sendiri -->
        <div class="col-12">
            <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="fa-solid fa-house-chimney-user text-success me-2"></i>Kartu Keluarga Saya
                    </h5>
                    <p class="text-muted small mb-0">
                        Berikut adalah susunan data anggota keluarga Anda yang terdaftar resmi di sistem RT {{ auth()->user()->rt ?? '06' }} / RW 19.
                    </p>
                </div>
                @if(!empty(auth()->user()->no_kk) && count($families) > 0)
                    <a href="{{ route('warga.kk.cetak', auth()->user()->no_kk) }}" target="_blank" class="btn btn-primary-custom px-4 py-2">
                        <i class="fa-solid fa-print me-2"></i>Cetak Kartu Keluarga Resmi
                    </a>
                @endif
            </div>
        </div>

        @if(empty(auth()->user()->no_kk) || count($families) === 0)
            <div class="col-12">
                <div class="card-custom p-5 text-center bg-white">
                    <i class="fa-solid fa-triangle-exclamation text-warning fs-1 mb-3"></i>
                    <h5 class="fw-bold text-dark">Data Kartu Keluarga Belum Terhubung</h5>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                        Nomor Kartu Keluarga (KK) belum tercatat atau belum terhubung dengan akun Anda. Silakan hubungi Ketua RT {{ auth()->user()->rt ?? '06' }} (<strong>{{ \App\Models\User::getKetuaRt(auth()->user()->rt ?? '06') }}</strong>) untuk melengkapi verifikasi Kartu Keluarga Anda.
                    </p>
                    <a href="{{ route('profil.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-user-pen me-2"></i>Periksa Profil Akun
                    </a>
                </div>
            </div>
        @else
            @foreach($families as $fam)
                @php
                    $isRt07 = ($fam['rt'] == '07');
                    $badgeColor = $isRt07 ? 'bg-primary' : 'bg-success';
                    $famMembers = collect($fam['members'])->sortBy(function($m) {
                        $status = strtolower(trim($m->status_keluarga ?? ''));
                        $p = ['kepala keluarga' => 1, 'suami' => 2, 'istri' => 3, 'anak' => 4, 'menantu' => 5, 'cucu' => 6, 'orangtua' => 7, 'mertua' => 8, 'famili lain' => 9];
                        return $p[$status] ?? 99;
                    })->values();
                @endphp
                <div class="col-12">
                    <div class="card-custom p-4 bg-white border-top border-4 {{ $isRt07 ? 'border-primary' : 'border-success' }}">
                        <div class="row align-items-center mb-4 pb-3 border-bottom g-3">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1 small fw-semibold font-monospace">
                                        NO KK: {{ $fam['no_kk'] }}
                                    </span>
                                    <span class="badge {{ $badgeColor }} bg-opacity-10 {{ $isRt07 ? 'text-primary' : 'text-success' }} border rounded-pill px-3 py-1 small fw-bold">
                                        RT {{ $fam['rt'] ?? '06' }} / RW 19
                                    </span>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Keluarga {{ $fam['kepala']->nama ?? auth()->user()->nama }}</h4>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                    Alamat: {{ $fam['alamat'] ?? '-' }}, Kel. Bekasi Jaya, Kec. Bekasi Timur
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="{{ route('warga.kk.cetak', $fam['no_kk']) }}" target="_blank" class="btn btn-outline-primary px-3 py-2">
                                    <i class="fa-solid fa-print me-1"></i> Cetak Format Blanko KK
                                </a>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-3">Daftar Anggota Keluarga ({{ count($fam['members']) }} Orang):</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="ps-3">No</th>
                                        <th>Nama Lengkap</th>
                                        <th>NIK</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Hubungan Keluarga</th>
                                        <th>Pekerjaan</th>
                                        <th class="pe-3">Status Akun</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($famMembers as $idx => $m)
                                        <tr class="{{ $m->id == auth()->id() ? 'table-success bg-opacity-10 fw-semibold' : '' }}">
                                            <td class="ps-3">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ $m->nama }}
                                                    @if($m->id == auth()->id())
                                                        <span class="badge bg-success ms-1 small">Anda</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $m->email ?? '-' }}</small>
                                            </td>
                                            <td class="font-monospace small">{{ $m->nik ?? '-' }}</td>
                                            <td>{{ $m->kelamin ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $m->status_keluarga === 'Kepala Keluarga' ? 'bg-primary' : 'bg-light text-dark border' }} rounded-pill px-3 py-1">
                                                    {{ $m->status_keluarga ?? 'Anggota' }}
                                                </span>
                                            </td>
                                            <td class="small">{{ $m->pekerjaan ?? '-' }}</td>
                                            <td class="pe-3">
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small">
                                                    Terverifikasi
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
</div>
@endsection
