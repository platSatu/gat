@extends('layouts.backend')

@php $baru = ! $article->exists; @endphp

@section('title', $baru ? 'Create Article' : 'Edit Article')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Articles</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Create' : 'Edit' }}</li>
@endsection

@section('content')
    {{-- Meniru gbigatsu.id/admins/article/create & edit: Category, Title, Description (TinyMCE), Image, Date Post, Status. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">{{ $baru ? 'Create Article' : 'Edit Article' }}</h1>

            <form method="POST" enctype="multipart/form-data"
                action="{{ $baru ? route('admin.articles.store') : route('admin.articles.update', $article) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required @class(['form-control', 'is-invalid' => $errors->has('category_id')])>
                        <option value="">Select Category</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}" @selected((string) old('category_id', $article->category_id) === (string) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $article->title) }}" required maxlength="255"
                        @class(['form-control', 'is-invalid' => $errors->has('title')])>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="18" class="form-control">{{ old('description', $article->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp"
                        @class(['form-control', 'is-invalid' => $errors->has('image')])>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">JPG, PNG atau WEBP, maksimal 4 MB.@unless ($baru) Kosongkan kalau gambar tidak diganti.@endunless</small>
                    @if (! $baru && $article->image_url)
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="admin-preview mt-2">
                    @endif
                </div>

                <div class="form-group">
                    <label for="date_post">Date Post</label>
                    <input id="date_post" type="date" name="date_post" required
                        value="{{ old('date_post', $article->date_post?->format('Y-m-d')) }}"
                        @class(['form-control', 'is-invalid' => $errors->has('date_post')])>
                    @error('date_post')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="0" @selected((string) old('status', $article->status) === '0')>Draft</option>
                        <option value="1" @selected((string) old('status', $article->status) === '1')>Published</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">{{ $baru ? 'Create Article' : 'Update Article' }}</button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-light">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Editor sama seperti admin lama (TinyMCE 4, sudah ada di public/be/assets/plugins/tinymce). --}}
    <script src="{{ asset('be/assets/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: '#description',
            height: 420,
            menubar: 'file edit insert view format table tools',
            plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table contextmenu directionality emoticons paste textcolor',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons',
            convert_urls: false
        });
    </script>
@endpush

@include('backend.partials.crud-styles')
