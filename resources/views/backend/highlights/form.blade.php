@extends('layouts.backend')

@php
    $baru = ! $highlight->exists;
    $foto = collect(\App\Models\Highlight::KOLOM_FOTO)->mapWithKeys(fn ($k, $i) => [$k => 'Foto '.($i + 1)]);
    $hapusLama = (array) old('hapus', []);
@endphp

@section('title', $baru ? 'Create Highlight' : 'Edit Highlight')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.highlights.index') }}">Highlights</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Create' : 'Edit' }}</li>
@endsection

@section('content')
    {{--
        Form highlight: Title & Description (tab Indonesia | English), Video, Foto 1-8, Background, Date Post, Status.
        Foto 1 = gambar kartu di slider beranda & "Kegiatan Sebelumnya". Background = poster video / cadangan gambar.
    --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">{{ $baru ? 'Create Highlight' : 'Edit Highlight' }}</h1>

            <form method="POST" enctype="multipart/form-data"
                action="{{ $baru ? route('admin.highlights.store') : route('admin.highlights.update', $highlight) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless

                @include('backend.partials.lang-tabs', ['model' => $highlight, 'titleHelp' => 'Judul Indonesia juga menjadi alamat halaman, mis. "Everyone Asia 2024" → /highlights/everyone-asia-2024.'])

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="date_post">Date Post</label>
                        <input id="date_post" type="date" name="date_post" required
                            value="{{ old('date_post', $highlight->date_post?->format('Y-m-d')) }}"
                            @class(['form-control', 'is-invalid' => $errors->has('date_post')])>
                        @error('date_post')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="1" @selected((string) old('status', $highlight->status) === '1')>Active (tampil di website)</option>
                            <option value="0" @selected((string) old('status', $highlight->status) === '0')>Hidden</option>
                        </select>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3">Video</h5>
                <div class="form-group">
                    <input id="video" type="file" name="video" accept="video/mp4,video/webm,video/quicktime"
                        @class(['form-control', 'is-invalid' => $errors->has('video')])>
                    @error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">MP4/WEBM/MOV, maksimal 100 MB.@unless ($baru) Kosongkan kalau video tidak diganti.@endunless</small>
                    @if (! $baru && $highlight->video_url)
                        <video src="{{ $highlight->video_url }}" class="admin-preview mt-2" controls preload="metadata"></video>
                        <div class="custom-control custom-checkbox mt-1">
                            <input type="checkbox" class="custom-control-input" id="hapus-video" name="hapus[]" value="video" @checked(in_array('video', $hapusLama, true))>
                            <label class="custom-control-label text-danger" for="hapus-video">Hapus video</label>
                        </div>
                    @endif
                </div>

                <hr>
                <h5 class="mb-1">Foto</h5>
                <p class="text-muted mb-3">JPG, PNG atau WEBP, maksimal 5 MB per foto. <strong>Foto 1</strong> dipakai sebagai gambar kartu di beranda.@unless ($baru) Kosongkan kalau foto tidak diganti.@endunless</p>

                <div class="row">
                    @foreach ($foto->put('bghighlights', 'Background') as $kolom => $label)
                        <div class="col-md-6 col-xl-4 form-group">
                            <label for="{{ $kolom }}">{{ $label }}@if ($kolom === 'image1' && $baru) <span class="text-danger">*</span>@endif</label>
                            <input id="{{ $kolom }}" type="file" name="{{ $kolom }}" accept="image/jpeg,image/png,image/webp"
                                @if ($kolom === 'image1' && $baru) required @endif
                                @class(['form-control', 'is-invalid' => $errors->has($kolom)])>
                            @error($kolom)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if ($kolom === 'bghighlights')
                                <small class="form-text text-muted">Poster video & cadangan gambar kalau Foto 1 kosong.</small>
                            @endif

                            @php $url = $baru ? null : \App\Models\Article::urlPublik($highlight->{$kolom}); @endphp
                            @if ($url)
                                <img src="{{ $url }}" alt="{{ $label }}" class="admin-photo mt-2" loading="lazy">
                                @if ($kolom !== 'image1')
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="hapus-{{ $kolom }}" name="hapus[]" value="{{ $kolom }}" @checked(in_array($kolom, $hapusLama, true))>
                                        <label class="custom-control-label text-danger" for="hapus-{{ $kolom }}">Hapus {{ strtolower($label) }}</label>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">{{ $baru ? 'Create Highlight' : 'Update Highlight' }}</button>
                <a href="{{ route('admin.highlights.index') }}" class="btn btn-light">Batal</a>
                @if (! $baru && $highlight->status === 1)
                    <a href="{{ route('highlights.show', $highlight) }}" target="_blank" rel="noopener" class="btn btn-link">Lihat di website</a>
                @endif
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .admin-photo { display: block; width: 100%; max-height: 170px; object-fit: cover; border-radius: 6px; background: #eee; }
    </style>
@endpush

@push('scripts')
    {{-- Editor sama seperti form artikel (TinyMCE 4 di public/be/assets/plugins/tinymce). --}}
    <script src="{{ asset('be/assets/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: 'textarea.js-editor',
            height: 360,
            menubar: 'file edit insert view format table tools',
            plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table contextmenu directionality emoticons paste textcolor',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview media | forecolor backcolor',
            convert_urls: false
        });
    </script>
@endpush

@include('backend.partials.crud-styles')
