@extends('layouts.backend')

@section('title', 'Highlights')
@section('breadcrumb')<li class="breadcrumb-item active">Highlights</li>@endsection

@section('content')
    {{-- Daftar highlight (slider beranda & /highlights/{slug}). Yang Active tampil di website, terbaru dulu. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">Highlights</h1>
            <a href="{{ route('admin.highlights.create') }}" class="btn btn-primary mb-3">Create New Highlight</a>

            <form method="GET" action="{{ route('admin.highlights.index') }}" class="input-group mb-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search highlights...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0 admin-table">
                    <thead>
                        <tr>
                            <th style="width: 110px">Thumbnail</th>
                            <th>Judul &amp; Link</th>
                            <th>Video</th>
                            <th>Date Post</th>
                            <th>Status</th>
                            <th style="width: 150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($highlights as $item)
                            <tr>
                                <td>
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="" class="admin-thumb" loading="lazy">
                                    @endif
                                </td>
                                <td>
                                    @if ($item->punya_en)<span class="badge badge-info mr-1" title="Sudah ada versi English">EN</span>@endif
                                    @if ($item->status === 1)
                                        <a href="{{ route('highlights.show', $item) }}" target="_blank" rel="noopener" class="text-muted">{{ $item->title }}</a>
                                    @else
                                        <span class="text-muted">{{ $item->title }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->video ? 'Ada' : '-' }}</td>
                                <td>{{ $item->date_post?->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge {{ $item->status === 1 ? 'badge-success' : 'badge-secondary' }}">{{ $item->status === 1 ? 'Active' : 'Hidden' }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.highlights.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.highlights.destroy', $item) }}" class="d-inline"
                                        onsubmit="return confirm('Hapus highlight ini beserta file yang diunggah lewat admin?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada highlight.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $highlights->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
@endsection

@include('backend.partials.crud-styles')
