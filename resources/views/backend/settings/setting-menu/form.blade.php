@extends('layouts.backend')

@php
    $baru = ! $setting->exists;
    $labelHak = ['list' => 'Lihat', 'add' => 'Tambah', 'edit' => 'Edit', 'delete' => 'Hapus'];
    $v2 = \App\Models\Menu::namaDipakaiV2()->map(fn ($n) => mb_strtolower($n))->all();
    // Centang awal: dari input sebelumnya (kalau gagal validasi) atau dari database.
    $centang = function (int $menuId, string $h) use ($hak) {
        $lama = old('akses');
        return $lama !== null ? ! empty($lama[$menuId][$h]) : ! empty($hak[$menuId][$h]);
    };
@endphp

@section('title', $baru ? 'Tambah Menu User' : 'Edit Setting Menu')
@section('breadcrumb')
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.setting-menu.index') }}">Setting Menu</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Tambah' : 'Edit' }}</li>
@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => $baru ? 'Tambah Menu User' : 'Edit Setting Menu: '.($setting->user?->name ?? ''),
        'keterangan' => 'Pilih user dan role, lalu centang menu yang boleh dibuka beserta haknya.',
    ])

    <form method="POST" class="set-form" action="{{ $baru ? route('admin.settings.setting-menu.store') : route('admin.settings.setting-menu.update', $setting) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="row">
            <div class="col-lg-4">
                <div class="set-card">
                    <div class="set-card__head"><h2>User &amp; role</h2></div>
                    <div class="set-card__body">
                        <div class="form-group">
                            <label for="user_id">User <span class="text-danger">*</span></label>
                            @if ($baru)
                                <select id="user_id" name="user_id" required @class(['form-control', 'is-invalid' => $errors->has('user_id')])>
                                    <option value="">— Pilih user —</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" data-role="{{ $u->role_id }}" @selected((string) old('user_id', $setting->user_id) === (string) $u->id)>
                                            {{ $u->name }} — {{ $u->email }}{{ $u->role ? ' ('.$u->role->name.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @else
                                <input type="text" class="form-control" value="{{ $setting->user?->name }} — {{ $setting->user?->email }}" disabled>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="role_id">Role <span class="text-danger">*</span></label>
                            <select id="role_id" name="role_id" required @class(['form-control', 'is-invalid' => $errors->has('role_id')])>
                                <option value="">— Pilih role —</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected((string) old('role_id', $setting->role_id) === (string) $role->id)>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if ($baru)<small class="form-text text-muted">Otomatis mengikuti role user, boleh diganti.</small>@endif
                        </div>

                        <div class="form-group mb-0">
                            <label>Status</label>
                            <div class="set-status">
                                @foreach (['active' => ['Active', 'mdi-check-circle'], 'inactive' => ['Inactive', 'mdi-close-circle']] as $nilai => [$label, $icon])
                                    <label class="set-status__opt">
                                        <input type="radio" name="status" value="{{ $nilai }}" @checked(old('status', $setting->status) === $nilai)>
                                        <span><i class="mdi {{ $icon }}"></i> {{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">Inactive = akses ini dimatikan sementara tanpa menghapus centangnya.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="set-card">
                    <div class="set-card__head">
                        <h2>Menu &amp; hak akses</h2>
                        <div>
                            <button type="button" class="btn set-btn set-btn--ghost btn-sm" data-pilih="semua">Centang semua</button>
                            <button type="button" class="btn set-btn set-btn--ghost btn-sm" data-pilih="kosong">Kosongkan</button>
                        </div>
                    </div>
                    @error('akses')<div class="alert alert-danger m-3 mb-0">{{ $message }}</div>@enderror
                    <div class="table-responsive">
                        <table class="set-table set-matrix">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    @foreach ($labelHak as $h => $label)
                                        <th class="text-center"><a href="#" data-kolom="{{ $h }}" title="Centang/kosongkan satu kolom">{{ $label }}</a></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($menus as $menu)
                                    <tr data-baris>
                                        <td>
                                            <strong>{{ \Illuminate\Support\Str::after($menu->name, 'Page-') }}</strong>
                                            <div class="small text-muted">
                                                {{ $menu->name }}
                                                @if (in_array(mb_strtolower(trim($menu->name)), $v2, true))<span class="set-chip" style="font-size: 10px;">Admin v2</span>@endif
                                            </div>
                                        </td>
                                        @foreach ($labelHak as $h => $label)
                                            <td class="text-center">
                                                <label class="set-check">
                                                    <input type="checkbox" name="akses[{{ $menu->id }}][{{ $h }}]" value="1" data-hak="{{ $h }}" @checked($centang($menu->id, $h))>
                                                    <span></span>
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="set-card__body small text-muted">
                        <strong>Lihat</strong> wajib dicentang agar menu tampil dan bisa dibuka. Mencentang Tambah/Edit/Hapus otomatis mencentang Lihat.
                    </div>
                </div>

                <div class="set-form-actions mb-4">
                    <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-content-save"></i> {{ $baru ? 'Simpan' : 'Update' }}</button>
                    <a href="{{ route('admin.settings.setting-menu.index') }}" class="btn set-btn set-btn--ghost">Batal</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        .set-status { display: flex; gap: 8px; }
        .set-status__opt { flex: 1; margin: 0; cursor: pointer; }
        .set-status__opt input { position: absolute; opacity: 0; }
        .set-status__opt span { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; border: 1px solid var(--set-line); border-radius: 10px; color: var(--set-muted); font-weight: 600; transition: all .15s; }
        .set-status__opt input:checked + span { border-color: var(--set-primary); background: var(--set-primary-soft); color: var(--set-primary); }
        .set-status__opt input[value="inactive"]:checked + span { border-color: #c8382b; background: #fdeceb; color: #c8382b; }

        .set-matrix th a { color: inherit; }
        .set-matrix th a:hover { color: var(--set-primary); text-decoration: none; }
        .set-matrix td { padding-top: 10px; padding-bottom: 10px; }
        .set-matrix tr.is-on { background: #f7f8ff; }

        .set-check { position: relative; display: inline-block; width: 22px; height: 22px; margin: 0; cursor: pointer; }
        .set-check input { position: absolute; opacity: 0; }
        .set-check span { position: absolute; inset: 0; border: 2px solid #cfd3e0; border-radius: 6px; background: #fff; transition: all .15s; }
        .set-check input:checked + span { border-color: var(--set-primary); background: var(--set-primary); }
        .set-check input:checked + span::after { content: ""; position: absolute; left: 6px; top: 2px; width: 6px; height: 11px; border: solid #fff; border-width: 0 2px 2px 0; transform: rotate(45deg); }
        .set-check input:focus-visible + span { box-shadow: 0 0 0 3px rgba(43, 58, 140, .2); }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var baris = document.querySelectorAll('[data-baris]');

            function rapikan(tr) {
                var list = tr.querySelector('[data-hak="list"]');
                var lain = tr.querySelectorAll('[data-hak]:not([data-hak="list"])');
                tr.classList.toggle('is-on', list.checked);
                return { list: list, lain: lain };
            }

            baris.forEach(function (tr) {
                var c = rapikan(tr);
                // Data lama bisa punya Tambah/Edit/Hapus tanpa Lihat (artinya tetap tidak bisa membuka menu) -> tampilkan kosong.
                if (! c.list.checked) { c.lain.forEach(function (cb) { cb.checked = false; }); }
                // Centang tambah/edit/hapus -> lihat ikut tercentang. Lihat dikosongkan -> semua dikosongkan.
                c.lain.forEach(function (cb) {
                    cb.addEventListener('change', function () { if (cb.checked) { c.list.checked = true; } rapikan(tr); });
                });
                c.list.addEventListener('change', function () {
                    if (! c.list.checked) { c.lain.forEach(function (cb) { cb.checked = false; }); }
                    rapikan(tr);
                });
            });

            document.querySelectorAll('[data-pilih]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var nilai = btn.getAttribute('data-pilih') === 'semua';
                    document.querySelectorAll('[data-hak]').forEach(function (cb) { cb.checked = nilai; });
                    baris.forEach(rapikan);
                });
            });

            document.querySelectorAll('[data-kolom]').forEach(function (a) {
                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    var h = a.getAttribute('data-kolom');
                    var kolom = document.querySelectorAll('[data-hak="' + h + '"]');
                    var nilai = Array.prototype.some.call(kolom, function (cb) { return ! cb.checked; });
                    kolom.forEach(function (cb) { cb.checked = nilai; });
                    if (nilai && h !== 'list') { document.querySelectorAll('[data-hak="list"]').forEach(function (cb) { cb.checked = true; }); }
                    if (! nilai && h === 'list') { document.querySelectorAll('[data-hak]').forEach(function (cb) { cb.checked = false; }); }
                    baris.forEach(rapikan);
                });
            });

            // Tambah baru: role otomatis mengikuti role user yang dipilih.
            var user = document.getElementById('user_id'), role = document.getElementById('role_id');
            if (user && role) {
                user.addEventListener('change', function () {
                    var r = user.options[user.selectedIndex].getAttribute('data-role');
                    if (r) { role.value = r; }
                });
            }
        })();
    </script>
@endpush
