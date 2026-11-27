@extends('layouts.backend')

@section('title', 'Menu')
@section('breadcrumb')<li class="breadcrumb-item">Settings</li><li class="breadcrumb-item active">Menu</li>@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => 'Menu',
        'keterangan' => 'Nama halaman admin yang bisa diberikan ke user lewat Setting Menu. Dipakai bersama website lama.',
        'aksi' => [['Tambah Menu', route('admin.settings.menus.create'), 'mdi-plus']],
    ])

    @if ($belumAda->isNotEmpty())
        <div class="set-card" style="border-color: #f3c77a; background: #fffaf0;">
            <div class="set-card__body d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
                <div>
                    <strong><i class="mdi mdi-alert-outline"></i> Ada menu admin v2 yang belum terdaftar:</strong>
                    @foreach ($belumAda as $nama)<span class="set-chip set-chip--amber">{{ $nama }}</span>@endforeach
                    <div class="small text-muted">Selama belum terdaftar, menu itu hanya bisa dibuka superadmin.</div>
                </div>
                <form method="POST" action="{{ route('admin.settings.menus.sync') }}">
                    @csrf
                    <button type="submit" class="btn set-btn set-btn--primary"><i class="mdi mdi-sync"></i> Tambahkan menu yang belum ada</button>
                </form>
            </div>
        </div>
    @endif

    <div class="set-card">
        <div class="set-card__head"><h2>{{ $menus->count() }} menu</h2></div>
        <div class="table-responsive">
            <table class="set-table">
                <thead>
                    <tr>
                        <th style="width: 70px">ID</th>
                        <th>Nama menu</th>
                        <th>Dipakai di</th>
                        <th>User yang boleh</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menus as $menu)
                        <tr>
                            <td class="text-muted">#{{ $menu->id }}</td>
                            <td><strong>{{ $menu->name }}</strong></td>
                            <td>
                                @if ($menu->dipakaiV2())
                                    <span class="set-chip">Admin v2</span>
                                @endif
                                <span class="set-chip set-chip--gray">Website lama</span>
                            </td>
                            <td>{{ $jumlahUser[$menu->id] ?? 0 }} user</td>
                            <td class="set-actions">
                                <a href="{{ route('admin.settings.menus.edit', $menu) }}" class="set-icon-btn" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.settings.menus.destroy', $menu) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus menu {{ addslashes($menu->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="set-icon-btn set-icon-btn--danger" title="Hapus"><i class="mdi mdi-delete"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="set-empty"><i class="mdi mdi-format-list-bulleted"></i>Belum ada menu.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
