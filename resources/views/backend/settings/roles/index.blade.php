@extends('layouts.backend')

@section('title', 'Roles')
@section('breadcrumb')<li class="breadcrumb-item">Settings</li><li class="breadcrumb-item active">Roles</li>@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => 'Roles',
        'keterangan' => 'Jenis akun, mis. admin_media atau admin_events. Role superadmin selalu boleh membuka semua menu.',
        'aksi' => [['Tambah Role', route('admin.settings.roles.create'), 'mdi-plus']],
    ])

    <div class="set-card">
        <div class="set-card__head"><h2>{{ $roles->count() }} role</h2></div>
        <div class="table-responsive">
            <table class="set-table">
                <thead>
                    <tr>
                        <th style="width: 70px">ID</th>
                        <th>Nama role</th>
                        <th>User</th>
                        <th>Dipakai di Setting Menu</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="text-muted">#{{ $role->id }}</td>
                            <td>
                                <span @class(['set-chip', 'set-chip--dark' => $role->isSuperadmin()])>
                                    @if ($role->isSuperadmin())<i class="mdi mdi-lock"></i>@endif {{ $role->name }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.settings.users.index', ['role' => $role->id]) }}" class="text-muted">{{ $role->users_count }} user</a>
                            </td>
                            <td>{{ $role->jumlah_setting }} baris</td>
                            <td class="set-actions">
                                @if ($role->isSuperadmin())
                                    <span class="text-muted small">Dikunci</span>
                                @else
                                    <a href="{{ route('admin.settings.roles.edit', $role) }}" class="set-icon-btn" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.settings.roles.destroy', $role) }}" class="d-inline"
                                        onsubmit="return confirm('Hapus role {{ $role->name }}? {{ $role->users_count }} user akan jadi tanpa role, dan Setting Menu dengan role ini ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="set-icon-btn set-icon-btn--danger" title="Hapus"><i class="mdi mdi-delete"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="set-empty"><i class="mdi mdi-shield-outline"></i>Belum ada role.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
