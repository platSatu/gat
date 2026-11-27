@extends('layouts.backend')

@php $baru = ! $partner->exists; @endphp

@section('title', $baru ? 'Tambah Mitra' : 'Edit Mitra')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.partners.index') }}">Mitra</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Tambah' : 'Edit' }}</li>
@endsection

@section('content')
    {{-- Form mitra: name, logo, description, website (opsional), status. --}}
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">{{ $baru ? 'Tambah Mitra' : 'Edit Mitra' }}</h1>

            <form method="POST" enctype="multipart/form-data"
                action="{{ $baru ? route('admin.partners.store') : route('admin.partners.update', $partner) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless

                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name', $partner->name) }}" maxlength="255" required
                        @class(['form-control', 'is-invalid' => $errors->has('name')])>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">Nama mitra, juga dipakai sebagai teks alternatif logo.</small>
                </div>

                <div class="form-group">
                    <label for="logo">Logo @if ($baru)<span class="text-danger">*</span>@endif</label>
                    <input id="logo" type="file" name="logo" accept="image/png,image/jpeg,image/webp" @if ($baru) required @endif
                        @class(['form-control', 'is-invalid' => $errors->has('logo')])>
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">PNG (latar transparan paling bagus), JPG atau WEBP, maks 2 MB.@unless ($baru) Kosongkan kalau tidak diganti.@endunless</small>
                    @if ($partner->logo_url)
                        <img src="{{ $partner->logo_url }}" alt="" class="admin-logo-preview mt-2">
                    @endif
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" maxlength="2000"
                        @class(['form-control', 'is-invalid' => $errors->has('description')])>{{ old('description', $partner->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">Opsional. Catatan untuk admin (tidak tampil di beranda, beranda hanya menampilkan logo).</small>
                </div>

                <div class="row">
                    <div class="col-md-8 form-group">
                        <label for="website">Website <small class="text-muted">(opsional)</small></label>
                        <input id="website" type="text" name="website" value="{{ old('website', $partner->website) }}" maxlength="255"
                            @class(['form-control', 'is-invalid' => $errors->has('website')]) placeholder="mis. https://gbi.or.id">
                        @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Diisi = logo di beranda bisa diklik dan pindah ke website ini. Kosong = logo tidak bisa diklik.</small>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="1" @selected((string) old('status', (int) $partner->status) === '1')>Active</option>
                            <option value="0" @selected((string) old('status', (int) $partner->status) === '0')>Inactive</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ $baru ? 'Simpan' : 'Update' }}</button>
                <a href="{{ route('admin.partners.index') }}" class="btn btn-light">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .admin-logo-preview { display: block; max-width: 220px; max-height: 90px; object-fit: contain; padding: 8px; border: 1px solid #dee2e6; border-radius: 6px; background: #fff; }
    </style>
@endpush

@include('backend.partials.crud-styles')
