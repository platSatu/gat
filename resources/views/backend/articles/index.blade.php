@extends('layouts.backend')

@section('title', 'Articles')
@section('breadcrumb')<li class="breadcrumb-item active">Articles</li>@endsection

@section('content')
    {{-- Meniru gbigatsu.id/admins/article: tombol buat, cari + filter kategori, tabel, paginasi. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">Articles</h1>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary mb-3">Create New Article</a>

            <form method="GET" action="{{ route('admin.articles.index') }}" class="input-group mb-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search articles...">
                <select name="category_id" class="custom-select admin-filter-select">
                    <option value="">All Categories</option>
                    @foreach ($kategori as $item)
                        <option value="{{ $item->id }}" @selected((string) request('category_id') === (string) $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0 admin-table">
                    <thead>
                        <tr>
                            <th style="width: 110px">Thumbnail</th>
                            <th>Judul &amp; Link Article</th>
                            <th>Category</th>
                            <th>Date Posted</th>
                            <th>Status</th>
                            <th style="width: 150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($artikel as $item)
                            <tr>
                                <td>
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="" class="admin-thumb" loading="lazy">
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status === 1 && $item->slug)
                                        <a href="{{ route('articles.show', $item) }}" target="_blank" rel="noopener" class="text-muted">{{ $item->title }}</a>
                                    @else
                                        <span class="text-muted">{{ $item->title }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->category?->name ?? '-' }}</td>
                                <td>{{ $item->date_post?->format('Y-m-d') }}</td>
                                <td>{{ $item->status === 1 ? 'Published' : 'Draft' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.articles.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $item) }}" class="d-inline"
                                        onsubmit="return confirm('Hapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada artikel.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $artikel->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
@endsection

@include('backend.partials.crud-styles')
