@extends('layouts.backend')

@section('title', 'Setting Menu')
@section('breadcrumb')<li class="breadcrumb-item">Settings</li><li class="breadcrumb-item active">Setting Menu</li>@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => 'Setting Menu',
        'keterangan' => 'Menu apa saja yang boleh dibuka setiap user. Hanya baris dengan status active yang berlaku. Superadmin tidak perlu diatur.',
        'aksi' => [['Tambah Menu User', route('admin.settings.setting-menu.create'), 'mdi-key-plus']],
    ])

    <div class="set-card">
        <div class="set-card__head">
            <h2>{{ $settings->total() }} pengaturan</h2>
            <form method="GET" class="set-filter">
                <div class="set-search">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama atau email user...">
                </div>
                <select name="role" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) request('role') === (string) $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
                <button type="submit" class="btn set-btn set-btn--primary">Cari</button>
                @if (request()->filled('search') || request()->filled('role') || request()->filled('status'))
                    <a href="{{ route('admin.settings.setting-menu.index') }}" class="btn set-btn set-btn--ghost">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="set-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Menu yang boleh dibuka</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($settings as $s)
                        @php $menus = $menuPerBaris[$s->id] ?? collect(); @endphp
                        <tr>
                            <td>
                                <div class="set-user">
                                    <span class="set-avatar" data-avatar="{{ $s->user?->name }}">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($s->user?->name ?? '?', 0, 1)) }}</span>
                                    <div>
                                        <div class="set-user__name">{{ $s->user?->name ?? 'User #'.$s->user_id.' (sudah dihapus)' }}</div>
                                        <div class="set-user__sub">{{ $s->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="set-chip">{{ $s->role?->name ?? 'role #'.$s->role_id }}</span></td>
                            <td style="max-width: 420px;">
                                @foreach ($menus->take(4) as $nama)
                                    <span class="set-chip set-chip--gray">{{ \Illuminate\Support\Str::after($nama, 'Page-') }}</span>
                                @endforeach
                                @if ($menus->count() > 4)
                                    <span class="set-chip" title="{{ $menus->slice(4)->implode(', ') }}">+{{ $menus->count() - 4 }} lagi</span>
                                @endif
                                @if ($menus->isEmpty())<span class="text-muted small">Belum ada menu</span>@endif
                            </td>
                            <td>
                                <span @class(['set-chip', 'set-chip--green' => $s->aktif(), 'set-chip--red' => ! $s->aktif()])>
                                    <i class="mdi {{ $s->aktif() ? 'mdi-check-circle' : 'mdi-close-circle' }}"></i> {{ $s->status }}
                                </span>
                            </td>
                            <td class="set-actions">
                                <a href="{{ route('admin.settings.setting-menu.edit', $s) }}" class="set-icon-btn" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.settings.setting-menu.destroy', $s) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus akses menu {{ addslashes($s->user?->name ?? '') }} ({{ $s->role?->name }})?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="set-icon-btn set-icon-btn--danger" title="Hapus"><i class="mdi mdi-delete"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="set-empty"><i class="mdi mdi-key-remove"></i>Tidak ada pengaturan yang cocok.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($settings->hasPages())
            <div class="set-pagination">{{ $settings->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
@endsection
