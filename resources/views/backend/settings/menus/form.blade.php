@extends('layouts.backend')

@php $baru = ! $menu->exists; @endphp

@section('title', $baru ? 'Tambah Menu' : 'Edit Menu')
@section('breadcrumb')
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.menus.index') }}">Menu</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Tambah' : 'Edit' }}</li>
@endsection

@section('content')
    @include('backend.settings._layout', ['judul' => $baru ? 'Tambah Menu' : 'Edit Menu: '.$menu->name])

    <div class="row">
        <div class="col-lg-6">
            <form method="POST" class="set-card set-form" action="{{ $baru ? route('admin.settings.menus.store') : route('admin.settings.menus.update', $menu) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless
                <div class="set-card__body">
                    <div class="form-group">
                        <label for="name">Nama menu <span class="text-danger">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $menu->name) }}" maxlength="255" required autofocus
                            @class(['form-control', 'is-invalid' => $errors->has('name')]) placeholder="mis. Page-Warta">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Ikuti pola yang sudah ada: <code>Page-Nama-Halaman</code>. Untuk menu admin v2, nama ini harus sama dengan isian <code>'menu'</code> di <code>config/admin-menu.php</code>.</small>
                    </div>
                    <div class="set-form-actions">
                        <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-content-save"></i> {{ $baru ? 'Simpan' : 'Update' }}</button>
                        <a href="{{ route('admin.settings.menus.index') }}" class="btn set-btn set-btn--ghost">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
