@extends('layouts.backend')

@section('title', 'Daftar User')
@section('breadcrumb')<li class="breadcrumb-item">Settings</li><li class="breadcrumb-item active">Daftar User</li>@endsection

@section('content')
    @include('backend.settings._layout', [
        'judul' => 'Daftar User',
        'keterangan' => 'Akun yang bisa login ke admin. Sama dengan website lama, jadi perubahan di sini juga berlaku di sana.',
        'aksi' => [['Buat User Baru', route('admin.settings.users.create'), 'mdi-account-plus']],
    ])

    <div class="set-stats">
        <div class="set-stat"><div class="set-stat__icon"><i class="mdi mdi-account-multiple"></i></div>
            <div><div class="set-stat__num">{{ $ringkasan['total'] }}</div><div class="set-stat__label">Total user</div></div></div>
        <div class="set-stat"><div class="set-stat__icon set-stat__icon--green"><i class="mdi mdi-account-star"></i></div>
            <div><div class="set-stat__num">{{ $ringkasan['superadmin'] }}</div><div class="set-stat__label">Superadmin</div></div></div>
        <div class="set-stat"><div class="set-stat__icon set-stat__icon--amber"><i class="mdi mdi-email-outline"></i></div>
            <div><div class="set-stat__num">{{ $ringkasan['belum_verifikasi'] }}</div><div class="set-stat__label">Belum verifikasi email</div></div></div>
        <div class="set-stat"><div class="set-stat__icon set-stat__icon--gray"><i class="mdi mdi-account-alert"></i></div>
            <div><div class="set-stat__num">{{ $ringkasan['tanpa_role'] }}</div><div class="set-stat__label">Tanpa role</div></div></div>
    </div>

    <div class="set-card">
        <div class="set-card__head">
            <h2>{{ $users->total() }} user</h2>
            <form method="GET" class="set-filter">
                <div class="set-search">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama atau email...">
                </div>
                <select name="role" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua role</option>
                    <option value="none" @selected(request('role') === 'none')>Tanpa role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) request('role') === (string) $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn set-btn set-btn--primary">Cari</button>
                @if (request()->filled('search') || request()->filled('role'))
                    <a href="{{ route('admin.settings.users.index') }}" class="btn set-btn set-btn--ghost">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="set-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Akses menu</th>
                        <th>Email</th>
                        <th>Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="set-user">
                                    <span class="set-avatar" data-avatar="{{ $user->name }}">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}</span>
                                    <div>
                                        <div class="set-user__name">{{ $user->name }} @if ($user->is(auth()->user()))<span class="set-chip set-chip--gray">Anda</span>@endif</div>
                                        <div class="set-user__sub">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($user->role)
                                    <span @class(['set-chip', 'set-chip--dark' => $user->isSuperadmin()])>{{ $user->role->name }}</span>
                                @else
                                    <span class="set-chip set-chip--gray">No role</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->isSuperadmin())
                                    <span class="set-chip set-chip--green">Semua menu</span>
                                @elseif ($user->jumlah_akses)
                                    <a href="{{ route('admin.settings.setting-menu.index', ['search' => $user->email]) }}" class="set-chip">{{ $user->jumlah_akses }} role aktif</a>
                                @else
                                    <a href="{{ route('admin.settings.setting-menu.create', ['user' => $user->id]) }}" class="set-chip set-chip--amber">Atur akses</a>
                                @endif
                            </td>
                            <td>
                                @if ($user->email_verified_at)
                                    <span class="set-chip set-chip--green"><i class="mdi mdi-check-circle"></i> Terverifikasi</span>
                                @else
                                    <span class="set-chip set-chip--amber"><i class="mdi mdi-clock"></i> Belum</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="set-actions">
                                @unless ($user->email_verified_at)
                                    <form method="POST" action="{{ route('admin.settings.users.verify', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="set-icon-btn set-icon-btn--success" title="Tandai email sudah diverifikasi"><i class="mdi mdi-email-open"></i></button>
                                    </form>
                                @endunless
                                <a href="{{ route('admin.settings.users.edit', $user) }}" class="set-icon-btn" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.settings.users.destroy', $user) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Akses menunya juga ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="set-icon-btn set-icon-btn--danger" title="Hapus" @disabled($user->is(auth()->user()))><i class="mdi mdi-delete"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="set-empty"><i class="mdi mdi-account-search"></i>Tidak ada user yang cocok.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="set-pagination">{{ $users->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
@endsection
