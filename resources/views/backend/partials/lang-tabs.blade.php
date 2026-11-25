{{--
    Tab Indonesia | English untuk judul & isi (form admin Artikel dan Highlights).
    Indonesia = kolom di tabel utama (wajib). English = tabel terjemahan (boleh kosong -> website memakai versi Indonesia).
    Parameter: $model (Article/Highlight), $titleHelp (opsional, keterangan di bawah judul Indonesia).
--}}
@php
    $en = $model->terjemahan('en');
    $siap = $model::terjemahanSiap();
    $enError = $errors->has('en.title') || $errors->has('en.description');
@endphp

<ul class="nav nav-tabs lang-tabs" role="tablist">
    <li class="nav-item">
        <a @class(['nav-link', 'active' => ! $enError]) data-toggle="tab" href="#tab-id" role="tab">Indonesia <small class="text-muted">(utama)</small></a>
    </li>
    <li class="nav-item">
        <a @class(['nav-link', 'active' => $enError]) data-toggle="tab" href="#tab-en" role="tab">
            English
            @if (filled($en->title) || filled($en->description))<span class="badge badge-info ml-1">EN</span>@endif
        </a>
    </li>
</ul>

<div class="tab-content lang-tab-content">
    <div @class(['tab-pane', 'fade', 'show active' => ! $enError]) id="tab-id" role="tabpanel">
        <div class="form-group">
            <label for="title">Title</label>
            <input id="title" type="text" name="title" value="{{ old('title', $model->title) }}" required maxlength="255"
                @class(['form-control', 'is-invalid' => $errors->has('title')])>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @isset($titleHelp)<small class="form-text text-muted">{{ $titleHelp }}</small>@endisset
        </div>

        <div class="form-group mb-0">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="14" class="form-control">{{ old('description', $model->description) }}</textarea>
        </div>
    </div>

    <div @class(['tab-pane', 'fade', 'show active' => $enError]) id="tab-en" role="tabpanel">
        @if ($siap)
            <p class="text-muted">Boleh dikosongkan. Bagian yang kosong akan tampil dalam Bahasa Indonesia saat pengunjung memilih English.</p>
            <div class="form-group">
                <label for="en_title">Title (English)</label>
                <input id="en_title" type="text" name="en[title]" value="{{ old('en.title', $en->title) }}" maxlength="255"
                    @class(['form-control', 'is-invalid' => $errors->has('en.title')])>
                @error('en.title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group mb-0">
                <label for="description_en">Description (English)</label>
                <textarea id="description_en" name="en[description]" rows="14" class="form-control">{{ old('en.description', $en->description) }}</textarea>
            </div>
        @else
            <div class="alert alert-warning mb-0">
                Tabel terjemahan belum ada di database. Jalankan
                <code>php artisan migrate --path=database/migrations/2026_11_25_100000_create_translation_tables.php</code>
                atau impor <code>database/sql/translation_tables.sql</code> lewat phpMyAdmin.
            </div>
        @endif
    </div>
</div>

@once
    @push('styles')
        <style>
            .lang-tabs { margin-bottom: 0; }
            .lang-tab-content { padding: 20px; margin-bottom: 20px; border: 1px solid #dee2e6; border-top: 0; border-radius: 0 0 6px 6px; }
        </style>
    @endpush
@endonce
