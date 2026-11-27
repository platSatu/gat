@extends('layouts.backend')

@php $baru = ! $role->exists; @endphp

@section('title', $baru ? 'Tambah Role' : 'Edit Role')
@section('breadcrumb')
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Tambah' : 'Edit' }}</li>
@endsection

@section('content')
    @include('backend.settings._layout', ['judul' => $baru ? 'Tambah Role' : 'Edit Role: '.$role->name])

    <div class="row">
        <div class="col-lg-6">
            <form method="POST" class="set-card set-form" action="{{ $baru ? route('admin.settings.roles.store') : route('admin.settings.roles.update', $role) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless
                <div class="set-card__body">
                    <div class="form-group">
                        <label for="name">Nama role <span class="text-danger">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" maxlength="255" required autofocus
                            @class(['form-control', 'is-invalid' => $errors->has('name')]) placeholder="mis. admin_warta">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Huruf kecil dan garis bawah, tanpa spasi (seperti role yang sudah ada).</small>
                    </div>
                    <div class="set-form-actions">
                        <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-content-save"></i> {{ $baru ? 'Simpan' : 'Update' }}</button>
                        <a href="{{ route('admin.settings.roles.index') }}" class="btn set-btn set-btn--ghost">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
