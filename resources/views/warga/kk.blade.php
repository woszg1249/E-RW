@extends('layouts.app')

@section('title', 'Manajemen Kartu Keluarga')
@section('page_title', 'Data Kartu Keluarga (KK) RW 19 (RT 06 & RT 07)')

@section('content')
<div class="row g-4">
    <!-- Header -->
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

    <!-- Family Cards Grid -->
    @forelse($families as $fam)
        @php
            $isRt07 = ($fam['rt'] == '07');
            $badgeColor = $isRt07 ? 'bg-primary' : 'bg-success';
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
                            @foreach($fam['members'] as $m)
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
</div>
@endsection
