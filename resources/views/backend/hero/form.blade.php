@extends('layouts.backend')

@php
    $baru = ! $slide->exists;
    // Isian per bahasa (tab Indonesia + bahasa lain dari Settings → Bahasa): [kolom, label, jenis, keterangan, wajib]
    $teks = [
        ['label', 'Label atas', 'text', 'Tulisan kecil di atas judul, mis. "Welcome to".', false],
        ['title', 'Judul', 'text', 'Tulisan besar, mis. "GBI Jl. Jend. Gatot Subroto".', true],
        ['sub_label', 'Label bawah', 'text', 'Opsional, mis. "Penggembalaan".', false],
        ['sub_title', 'Teks bawah', 'text', 'Opsional, mis. nama gembala.', false],
        ['button_text', 'Tulisan tombol', 'text', 'Kosong = "Temukan Kisah Kami".', false],
    ];
@endphp

@section('title', $baru ? 'Tambah Kartu Hero' : 'Edit Kartu Hero')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.hero.index') }}">Hero Beranda</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Tambah' : 'Edit' }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">{{ $baru ? 'Tambah Kartu Hero' : 'Edit Kartu Hero' }}</h1>

            <form method="POST" action="{{ $baru ? route('admin.hero.store') : route('admin.hero.update', $slide) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless

                @include('backend.partials.lang-tabs', ['model' => $slide, 'kolom' => $teks])

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="link">Link tombol <small class="text-muted">(opsional)</small></label>
                        <input id="link" type="text" name="link" value="{{ old('link', $slide->link) }}" maxlength="255"
                            @class(['form-control', 'is-invalid' => $errors->has('link')]) placeholder="mis. tentang/sejarah atau https://...">
                        @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Kosong = halaman Tentang Kami.</small>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="sort_order">Urutan</label>
                        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $slide->sort_order) }}" required
                            @class(['form-control', 'is-invalid' => $errors->has('sort_order')])>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="1" @selected((string) old('status', (int) $slide->status) === '1')>Active</option>
                            <option value="0" @selected((string) old('status', (int) $slide->status) === '0')>Inactive</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ $baru ? 'Simpan' : 'Update' }}</button>
                <a href="{{ route('admin.hero.index') }}" class="btn btn-light">Batal</a>
            </form>
        </div>
    </div>
@endsection

@include('backend.partials.crud-styles')
