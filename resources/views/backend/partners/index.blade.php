@extends('layouts.backend')

@section('title', 'Mitra')
@section('breadcrumb')<li class="breadcrumb-item active">Mitra</li>@endsection

@section('content')
    {{-- Daftar logo "Pelayanan dan Mitra Kami" di beranda. Yang Active tampil di website. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">Pelayanan &amp; Mitra</h1>

            @unless ($siap)
                <div class="alert alert-warning">
                    Tabel <code>partners</code> belum ada di database. Jalankan
                    <code>php artisan migrate --path=database/migrations/2026_11_27_100000_create_partners_table.php</code>
                    atau impor <code>database/sql/partners.sql</code> lewat phpMyAdmin. Sampai saat itu beranda memakai daftar logo lama.
                </div>
            @else
                <a href="{{ route('admin.partners.create') }}" class="btn btn-primary mb-3">Tambah Mitra</a>
                <a href="{{ route('home') }}#mitra" target="_blank" rel="noopener" class="btn btn-link mb-3">Lihat di beranda</a>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 admin-table">
                        <thead>
                            <tr>
                                <th style="width: 140px">Logo</th>
                                <th>Name</th>
                                <th>Website</th>
                                <th>Status</th>
                                <th style="width: 150px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($partners as $item)
                                <tr>
                                    <td>
                                        @if ($item->logo_url)
                                            <img src="{{ $item->logo_url }}" alt="" class="admin-logo" loading="lazy">
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if (filled($item->description))
                                            <br><span class="text-muted small">{{ \Illuminate\Support\Str::limit($item->description, 90) }}</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if ($item->website)
                                            <a href="{{ $item->website }}" target="_blank" rel="noopener">{{ $item->website }}</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->status ? 'badge-success' : 'badge-secondary' }}">{{ $item->status ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.partners.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.partners.destroy', $item) }}" class="d-inline"
                                            onsubmit="return confirm('Hapus mitra ini dari beranda?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada mitra.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endunless
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .admin-logo { display: block; width: 120px; height: 50px; object-fit: contain; }
    </style>
@endpush

@include('backend.partials.crud-styles')
