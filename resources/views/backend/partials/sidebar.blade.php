{{--
    Menu kiri admin. Daftar menunya ada di config/admin-menu.php (bukan di sini).
    Yang tampil hanya menu yang boleh dibuka user ini (Settings → Setting Menu); superadmin melihat semua.
    Grup di $lipat tampil sebagai satu menu yang bisa dibuka-tutup (submenu), supaya sidebar tidak terlalu panjang.
--}}
@php
    $pengguna = auth()->user();
    // Nama grup di config/admin-menu.php => ikon menu induknya.
    $lipat = ['Settings' => 'mdi-settings'];

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
                    @if (isset($lipat[$judul]))
                        @php
                            $aktif = $items->contains(fn ($item) => request()->routeIs($item['active']));
                            // Kalau alamat halaman persis sama dengan salah satu link, template (be/assets/js/app.js)
                            // membuka submenunya sendiri; dibuka di sini hanya untuk halaman lain (mis. tambah/edit).
                            $tepat = $items->contains(fn ($item) => route($item['route']) === url()->current() && ! request()->getQueryString());
                            $buka = $aktif && ! $tepat;
                        @endphp
                        <li @class(['has_sub', 'nav-active' => $buka])>
                            <a href="javascript:void(0);" @class(['waves-effect', 'active' => $aktif])>
                                <i class="mdi {{ $lipat[$judul] }}"></i>
                                <span> {{ $judul }} </span>
                                <span class="float-right sidebar-sub-arrow"><i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="list-unstyled" @if ($buka) style="display: block;" @endif>
                                @foreach ($items as $item)
                                    <li @class(['active' => request()->routeIs($item['active'])])>
                                        <a href="{{ route($item['route']) }}" @class(['active' => request()->routeIs($item['active'])])>
                                            <i class="mdi {{ $item['icon'] }}"></i> {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="menu-title">{{ $judul }}</li>
                        @foreach ($items as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" class="waves-effect {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                                    <i class="mdi {{ $item['icon'] }}"></i>
                                    <span> {{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

{{-- Ditulis langsung (bukan @push) karena sidebar dimuat oleh layout setelah <head> selesai. --}}
<style>
    /* Submenu sidebar (Settings): isi sedikit menjorok, ikon kecil. */
    #sidebar-menu .has_sub > ul > li > a { display: flex; align-items: center; gap: 8px; padding-left: 52px; font-size: 13.5px; }
    #sidebar-menu .has_sub > ul > li > a i { font-size: 15px; opacity: .7; }
    #sidebar-menu .has_sub > ul > li > a.active { font-weight: 600; }
</style>
