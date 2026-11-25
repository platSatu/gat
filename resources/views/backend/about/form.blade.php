@extends('layouts.backend')

@php
    $baru = ! $section->exists;
    $en = $section->terjemahan('en');
    $siapEn = \App\Models\AboutSection::terjemahanSiap();
    $enError = collect(['en.name', 'en.subtitle', 'en.excerpt', 'en.description', 'en.button_text'])->contains(fn ($k) => $errors->has($k));
    // Isian per bahasa: [kolom, label, jenis, keterangan]
    $teks = [
        ['name', 'Name', 'text', 'Judul blok (huruf besar otomatis di website).'],
        ['subtitle', 'Subtitle', 'text', 'Opsional. Sub-judul kecil di bawah judul, mis. "Berawal dari sebuah panggilan".'],
        ['excerpt', 'Teks singkat', 'textarea', 'Tampil di halaman Tentang (di atas tombol).'],
        ['description', 'Isi lengkap', 'editor', 'Isi halaman detail /tentang/{slug}. Kosong = halaman detail memakai teks singkat.'],
        ['button_text', 'Tulisan tombol', 'text', 'Kosong = "Lihat Selengkapnya".'],
    ];
@endphp

@section('title', $baru ? 'Tambah Blok Tentang Kami' : 'Edit Blok Tentang Kami')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.about.index') }}">Tentang Kami</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Tambah' : 'Edit' }}</li>
@endsection

@section('content')
    {{-- Form blok Tentang Kami: teks (tab Indonesia | English), slug, background + warnai biru, link tombol, urutan, status. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">{{ $baru ? 'Tambah Blok Tentang Kami' : 'Edit Blok Tentang Kami' }}</h1>

            <form method="POST" enctype="multipart/form-data"
                action="{{ $baru ? route('admin.about.store') : route('admin.about.update', $section) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless

                <ul class="nav nav-tabs lang-tabs" role="tablist">
                    <li class="nav-item"><a @class(['nav-link', 'active' => ! $enError]) data-toggle="tab" href="#tab-id" role="tab">Indonesia <small class="text-muted">(utama)</small></a></li>
                    <li class="nav-item">
                        <a @class(['nav-link', 'active' => $enError]) data-toggle="tab" href="#tab-en" role="tab">English
                            @if ($en->exists)<span class="badge badge-info ml-1">EN</span>@endif
                        </a>
                    </li>
                </ul>
                <div class="tab-content lang-tab-content">
                    @foreach (['id' => 'tab-id', 'en' => 'tab-en'] as $lang => $tab)
                        <div @class(['tab-pane', 'fade', 'show active' => ($lang === 'en') === $enError]) id="{{ $tab }}" role="tabpanel">
                            @if ($lang === 'en' && ! $siapEn)
                                <div class="alert alert-warning mb-0">Tabel terjemahan belum ada di database.</div>
                            @else
                            @if ($lang === 'en')
                                <p class="text-muted">Boleh dikosongkan. Bagian yang kosong tampil dalam Bahasa Indonesia saat pengunjung memilih English.</p>
                            @endif
                            @foreach ($teks as [$kolom, $label, $jenis, $bantuan])
                                @php
                                    $nama = $lang === 'en' ? "en[$kolom]" : $kolom;
                                    $kunci = $lang === 'en' ? "en.$kolom" : $kolom;
                                    $id = $lang === 'en' ? "{$kolom}_en" : $kolom;
                                    $nilai = old($kunci, $lang === 'en' ? $en->{$kolom} : $section->{$kolom});
                                @endphp
                                <div @class(['form-group', 'mb-0' => $loop->last])>
                                    <label for="{{ $id }}">{{ $label }}@if ($lang === 'en') (English)@endif @if ($kolom === 'name' && $lang === 'id')<span class="text-danger">*</span>@endif</label>
                                    @if ($jenis === 'text')
                                        <input id="{{ $id }}" type="text" name="{{ $nama }}" value="{{ $nilai }}" maxlength="255"
                                            @if ($kolom === 'name' && $lang === 'id') required @endif
                                            @class(['form-control', 'is-invalid' => $errors->has($kunci)])>
                                    @else
                                        <textarea id="{{ $id }}" name="{{ $nama }}" rows="{{ $jenis === 'editor' ? 12 : 4 }}"
                                            @class(['form-control', 'js-editor' => $jenis === 'editor', 'is-invalid' => $errors->has($kunci)])>{{ $nilai }}</textarea>
                                    @endif
                                    @error($kunci)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    @if ($lang === 'id')<small class="form-text text-muted">{{ $bantuan }}</small>@endif
                                </div>
                            @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="slug">Slug</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">/tentang/</span></div>
                            <input id="slug" type="text" name="slug" value="{{ old('slug', $section->slug) }}" maxlength="255"
                                @class(['form-control', 'is-invalid' => $errors->has('slug')]) placeholder="otomatis dari Name">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="form-text text-muted">Kosongkan untuk dibuat otomatis dari Name. Mengubah slug akan mengubah alamat halaman.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="link">Link tombol <small class="text-muted">(opsional)</small></label>
                        <input id="link" type="text" name="link" value="{{ old('link', $section->link) }}" maxlength="255"
                            @class(['form-control', 'is-invalid' => $errors->has('link')]) placeholder="mis. tentang/sejarah atau https://...">
                        @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Kosong = tombol membuka halaman detail /tentang/{slug}.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="background">Background @if ($baru)<span class="text-danger">*</span>@endif</label>
                        <input id="background" type="file" name="background" accept="image/jpeg,image/png,image/webp" @if ($baru) required @endif
                            @class(['form-control', 'is-invalid' => $errors->has('background')])>
                        @error('background')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">JPG, PNG atau WEBP, maks 5 MB. Foto lebar 16:9 paling pas.@unless ($baru) Kosongkan kalau tidak diganti.@endunless</small>
                        @if ($section->background_url)
                            <img src="{{ $section->background_url }}" alt="" class="admin-photo mt-2">
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="tint" value="0">
                                <input type="checkbox" class="custom-control-input" id="tint" name="tint" value="1" @checked(old('tint', $section->tint))>
                                <label class="custom-control-label" for="tint">Warnai biru otomatis</label>
                            </div>
                            <small class="form-text text-muted">Centang untuk foto biasa. Hilangkan centang kalau gambarnya sudah bernuansa biru dari desain.</small>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="sort_order">Urutan</label>
                                <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}" required
                                    @class(['form-control', 'is-invalid' => $errors->has('sort_order')])>
                                <small class="form-text text-muted">Kecil = lebih atas.</small>
                            </div>
                            <div class="col-6 form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="1" @selected((string) old('status', (int) $section->status) === '1')>Active</option>
                                    <option value="0" @selected((string) old('status', (int) $section->status) === '0')>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ $baru ? 'Simpan' : 'Update' }}</button>
                <a href="{{ route('admin.about.index') }}" class="btn btn-light">Batal</a>
                @if (! $baru && $section->status)
                    <a href="{{ route('about.show', $section) }}" target="_blank" rel="noopener" class="btn btn-link">Lihat di website</a>
                @endif
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .lang-tabs { margin-bottom: 0; }
        .lang-tab-content { padding: 20px; margin-bottom: 20px; border: 1px solid #dee2e6; border-top: 0; border-radius: 0 0 6px 6px; }
        .admin-photo { display: block; width: 100%; max-width: 420px; aspect-ratio: 16 / 9; object-fit: cover; border-radius: 6px; background: #eee; }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('be/assets/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: 'textarea.js-editor',
            height: 340,
            menubar: 'file edit insert view format table tools',
            plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table contextmenu directionality emoticons paste textcolor',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview media | forecolor backcolor',
            convert_urls: false
        });
    </script>
@endpush

@include('backend.partials.crud-styles')
