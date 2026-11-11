@extends('layouts.app')

@section('title', 'Buku Panduan Aplikasi')
@section('page_title', 'Buku Panduan Penggunaan E-RT RW Digital')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card-custom p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold mb-1"><i class="fa-solid fa-book-open-reader text-success me-2"></i>Buku Panduan Resmi E-RT RW Bersatu</h4>
                <p class="text-muted small mb-0">Petunjuk praktis cara menggunakan seluruh fitur kependudukan, keuangan, dan layanan warga RW 19.</p>
            </div>
            <div>
                <a href="{{ route('landing') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-globe me-1"></i> Buka Landing Page
                </a>
            </div>
        </div>
    </div>

    <!-- Content Panduan Interaktif -->
    <div class="col-12">
        @include('panduan_content')
    </div>
</div>
@endsection
