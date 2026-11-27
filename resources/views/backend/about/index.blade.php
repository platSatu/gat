@extends('layouts.backend')

@section('title', 'Tentang Kami')
@section('breadcrumb')<li class="breadcrumb-item active">Tentang Kami</li>@endsection

@section('content')
    {{-- Daftar blok halaman /tentang, urut seperti tampil di website. Tombol ▲▼ untuk mengubah urutan. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">Tentang Kami</h1>

            @unless ($siap)
                <div class="alert alert-warning">
                    Tabel <code>about_sections</code> belum ada di database. Jalankan
                    <code>php artisan migrate --path=database/migrations/2026_11_25_200000_create_about_sections_table.php</code>
                    atau impor <code>database/sql/about_sections.sql</code> lewat phpMyAdmin. Sampai saat itu halaman /tentang memakai isi lama.
                </div>
            @else
                <a href="{{ route('admin.about.create') }}" class="btn btn-primary mb-3">Tambah Blok</a>
                <a href="{{ route('about') }}" target="_blank" rel="noopener" class="btn btn-link mb-3">Lihat halaman Tentang</a>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 admin-table">
                        <thead>
                            <tr>
                                <th style="width: 90px">Urutan</th>
                                <th style="width: 130px">Background</th>
                                <th>Name &amp; Slug</th>
                                <th>Tombol ke</th>
                                <th>Status</th>
                                <th style="width: 150px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sections as $item)
                                <tr>
                                    <td class="text-nowrap">
                                        <span class="mr-1">{{ $loop->iteration }}</span>
                                        @foreach (['up' => '▲', 'down' => '▼'] as $arah => $ikon)
                                            <form method="POST" action="{{ route('admin.about.move', [$item, $arah]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-light btn-sm px-2" title="{{ $arah === 'up' ? 'Naikkan' : 'Turunkan' }}"
                                                    @disabled(($arah === 'up' && $loop->parent->first) || ($arah === 'down' && $loop->parent->last))>{{ $ikon }}</button>
                                            </form>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($item->background_url)
                                            <img src="{{ $item->background_url }}" alt="" class="admin-thumb" loading="lazy">
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->punya_en)<span class="badge badge-info mr-1" title="Sudah diterjemahkan ke {{ $item->punya_en }} bahasa">{{ $item->punya_en }} bhs</span>@endif
                                        <strong>{{ $item->name }}</strong><br>
                                        @if ($item->status)
                                            <a href="{{ route('about.show', $item) }}" target="_blank" rel="noopener" class="text-muted small">/tentang/{{ $item->slug }}</a>
                                        @else
                                            <span class="text-muted small">/tentang/{{ $item->slug }}</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $item->link ?: 'Halaman detail' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status ? 'badge-success' : 'badge-secondary' }}">{{ $item->status ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.about.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.about.destroy', $item) }}" class="d-inline"
                                            onsubmit="return confirm('Hapus blok ini dari halaman Tentang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada blok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endunless
        </div>
    </div>
@endsection

@include('backend.partials.crud-styles')
