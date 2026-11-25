{{--
    Menu kiri admin. Tambah menu baru cukup di array $menu di bawah:
    ['title' => 'Judul grup'] atau ['label', 'icon', 'route', 'active' (pola nama route)].
--}}
@php
    $menu = [
        ['title' => 'Main Menu'],
        ['label' => 'Dashboard', 'icon' => 'mdi-airplay', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['title' => 'GBI Gatsu'],
        ['label' => 'Article', 'icon' => 'mdi-file-document', 'route' => 'admin.articles.index', 'active' => 'admin.articles.*'],
        ['label' => 'Category Article', 'icon' => 'mdi-tag-multiple', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*'],
        ['label' => 'Highlights', 'icon' => 'mdi-star-circle', 'route' => 'admin.highlights.index', 'active' => 'admin.highlights.*'],
    ];
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
                @foreach ($menu as $item)
                    @isset($item['title'])
                        <li class="menu-title">{{ $item['title'] }}</li>
                    @else
                        <li>
                            <a href="{{ route($item['route']) }}" class="waves-effect {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                                <i class="mdi {{ $item['icon'] }}"></i>
                                <span> {{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endisset
                @endforeach
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
