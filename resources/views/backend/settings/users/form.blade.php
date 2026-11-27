@extends('layouts.backend')

@php $baru = ! $user->exists; @endphp

@section('title', $baru ? 'Buat User Baru' : 'Edit User')
@section('breadcrumb')
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.users.index') }}">Daftar User</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Buat' : 'Edit' }}</li>
@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => $baru ? 'Buat User Baru' : 'Edit User: '.$user->name,
        'keterangan' => 'Role menentukan jenis akun. Menu apa saja yang boleh dibuka diatur di Setting Menu.',
    ])

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" class="set-card set-form" action="{{ $baru ? route('admin.settings.users.store') : route('admin.settings.users.update', $user) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless

                <div class="set-card__head"><h2>Data akun</h2></div>
                <div class="set-card__body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="name">Nama <span class="text-danger">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required
                                @class(['form-control', 'is-invalid' => $errors->has('name')])>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required
                                @class(['form-control', 'is-invalid' => $errors->has('email')])>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="password">Password @if ($baru)<span class="text-danger">*</span>@endif</label>
                            <input id="password" type="password" name="password" autocomplete="new-password" @if ($baru) required @endif
                                @class(['form-control', 'is-invalid' => $errors->has('password')])>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Minimal 8 karakter.@unless ($baru) Kosongkan kalau tidak diganti.@endunless</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" @if ($baru) required @endif class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select id="role_id" name="role_id" @class(['form-control', 'is-invalid' => $errors->has('role_id')])>
                            <option value="">— Tanpa role —</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Role <strong>superadmin</strong> otomatis boleh membuka semua menu, termasuk Settings.</small>
                    </div>

                    @if ($baru)
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="verified" name="verified" value="1" @checked(old('verified', true))>
                            <label class="custom-control-label" for="verified">Tandai email sudah diverifikasi</label>
                        </div>
                    @endif

                    <div class="set-form-actions">
                        <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-content-save"></i> {{ $baru ? 'Simpan' : 'Update' }}</button>
                        <a href="{{ route('admin.settings.users.index') }}" class="btn set-btn set-btn--ghost">Batal</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="set-card">
                <div class="set-card__head"><h2>Akses menu</h2></div>
                <div class="set-card__body">
                    @if (! $baru && $user->isSuperadmin())
                        <p class="mb-0"><span class="set-chip set-chip--green">Semua menu</span> User ini superadmin.</p>
                    @elseif (! $baru)
                        @php $aktif = $user->settingMenus()->with('role')->get(); @endphp
                        @forelse ($aktif as $s)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="set-chip">{{ $s->role?->name ?? 'role #'.$s->role_id }}</span>
                                <span @class(['set-chip', 'set-chip--green' => $s->aktif(), 'set-chip--gray' => ! $s->aktif()])>{{ $s->status }}</span>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada akses menu.</p>
                        @endforelse
                        <a href="{{ route('admin.settings.setting-menu.create', ['user' => $user->id]) }}" class="btn set-btn set-btn--ghost btn-block mt-2"><i class="mdi mdi-key-plus"></i> Atur akses menu</a>
                    @else
                        <p class="text-muted mb-0">Setelah user dibuat, atur menu yang boleh dibuka di <strong>Setting Menu</strong>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
