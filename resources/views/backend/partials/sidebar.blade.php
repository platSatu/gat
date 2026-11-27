{{--
    Menu kiri admin. Daftar menunya ada di config/admin-menu.php (bukan di sini).
    Yang tampil hanya menu yang boleh dibuka user ini (Settings → Setting Menu); superadmin melihat semua.
--}}
@php
    $pengguna = auth()->user();
    $grupMenu = collect(config('admin-menu', []))
        ->map(fn ($items) => collect($items)->filter(fn ($item) => ! empty($item['superadmin'])
            ? $pengguna->isSuperadmin()
            : $pengguna->bolehMenu($item['menu'] ?? null)))
        ->filter(fn ($items) => $items->isNotEmpty());
@endphp

<div class="left side-menu">
    <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
        <i class="ion-close"></i>
    </button>

    <div class="topbar-left">
        <div class="text-center">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <img src="{{ asset('images/logo/Logo-GBIGATSU.svg') }}" alt="{{ config('app.name') }}" width="200">
            </a>
        </div>
    </div>

    <div class="sidebar-inner niceScrollleft">
        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">Main Menu</li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="waves-effect {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="mdi mdi-airplay"></i>
                        <span> Dashboard</span>
                    </a>
                </li>

                @foreach ($grupMenu as $judul => $items)
                    <li class="menu-title">{{ $judul }}</li>
                    @foreach ($items as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="waves-effect {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                                <i class="mdi {{ $item['icon'] }}"></i>
                                <span> {{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
