@extends('layouts.backend')

@section('title', 'Category Article')
@section('breadcrumb')<li class="breadcrumb-item active">Category Article</li>@endsection

@section('content')
    {{-- Meniru gbigatsu.id/admins/category-gallery: tombol buat, cari, tabel Name / Slug / Actions. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">Category</h1>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Create New Category</a>

            <form method="GET" action="{{ route('admin.categories.index') }}" class="input-group mb-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search categories...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0 admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            @if ($adaSlug)<th>Slug</th>@endif
                            <th>Jumlah Artikel</th>
                            <th style="width: 150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategori as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                @if ($adaSlug)<td>{{ $item->slug }}</td>@endif
                                <td>{{ $item->articles_count }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.categories.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $item) }}" class="d-inline"
                                        onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $kategori->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
@endsection

@include('backend.partials.crud-styles')
