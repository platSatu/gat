@extends('layouts.backend')

@php $baru = ! $bahasa->exists; @endphp

@section('title', $baru ? 'Tambah Bahasa' : 'Edit Bahasa')
@section('breadcrumb')
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.bahasa.index') }}">Bahasa</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Tambah' : 'Edit' }}</li>
@endsection

@section('content')
    @include('backend.settings._layout', ['judul' => $baru ? 'Tambah Bahasa' : 'Edit Bahasa: '.$bahasa->nama])

    <div class="row">
        <div class="col-lg-6">
            <form method="POST" class="set-card set-form" action="{{ $baru ? route('admin.settings.bahasa.store') : route('admin.settings.bahasa.update', $bahasa) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless
                <div class="set-card__body">
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label for="kode">Kode @if (! $bahasa->utama)<span class="text-danger">*</span>@endif</label>
                            <input id="kode" type="text" name="kode" value="{{ old('kode', $bahasa->kode) }}" maxlength="5" placeholder="mis. zh"
                                @disabled($bahasa->utama) @if (! $bahasa->utama) required @endif
                                @class(['form-control', 'is-invalid' => $errors->has('kode')])>
                            @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-8 form-group">
                            <label for="nama">Nama <span class="text-danger">*</span></label>
                            <input id="nama" type="text" name="nama" value="{{ old('nama', $bahasa->nama) }}" maxlength="60" required placeholder="mis. 中文 (Mandarin)"
                                @class(['form-control', 'is-invalid' => $errors->has('nama')])>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="form-text text-muted mt-n2 mb-3">
                        Kode bahasa standar 2 huruf (ISO 639-1): <code>en</code> English, <code>zh</code> Mandarin, <code>ja</code> Jepang, <code>ko</code> Korea, <code>nl</code> Belanda.
                        @unless ($baru) Kode tidak sebaiknya diganti setelah ada isinya.@endunless
                    </small>

                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label for="urutan">Urutan</label>
                            <input id="urutan" type="number" min="0" name="urutan" value="{{ old('urutan', $bahasa->urutan) }}" required class="form-control">
                        </div>
                        <div class="col-sm-8 form-group">
                            <label>Status</label>
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="hidden" name="aktif" value="0">
                                <input type="checkbox" class="custom-control-input" id="aktif" name="aktif" value="1" @checked(old('aktif', $bahasa->aktif)) @disabled($bahasa->utama)>
                                <label class="custom-control-label" for="aktif">Aktif (tampil di website)</label>
                            </div>
                        </div>
                    </div>

                    <div class="set-form-actions">
                        <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-content-save"></i> {{ $baru ? 'Simpan' : 'Update' }}</button>
                        <a href="{{ route('admin.settings.bahasa.index') }}" class="btn set-btn set-btn--ghost">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
