{{--
    Menu layar penuh (Figma "Menu"). Tambah/ubah menu cukup di array $items.
    Animasi buka/tutup ada di frontend.css (bagian Header & Menu); --i = urutan untuk jeda bergantian.
    'match' = pola URL untuk menandai menu aktif.
--}}
@php
    $items = [
        ['label' => __('Beranda'), 'url' => route('home'), 'match' => '/'],
        ['label' => __('Tentang Gatsu'), 'url' => route('about'), 'match' => 'tentang*'],
        ['label' => __('Pelayanan'), 'url' => route('ministry'), 'match' => 'pelayanan*'],
        ['label' => __('Kegiatan'), 'url' => route('events'), 'match' => 'kegiatan*'],
        ['label' => __('Pengajaran'), 'url' => route('teaching'), 'match' => ['pengajaran*', 'artikel*']],
        ['label' => __('Komunitas'), 'url' => route('community'), 'match' => 'komunitas*'],
        ['label' => __('Kontak'), 'url' => route('contact'), 'match' => ['kontak*', 'memberi', 'give']],
    ];
    $socials = [
        'Facebook' => 'https://www.facebook.com/',
        'Instagram' => 'https://www.instagram.com/',
        'Youtube' => 'https://www.youtube.com/',
    ];
@endphp

<div class="site-menu" id="site-menu" data-menu aria-hidden="true" role="dialog" aria-modal="true" aria-label="{{ __('Menu') }}">
    <span class="site-menu__line site-menu__line--v" aria-hidden="true"></span>
    <span class="site-menu__line site-menu__line--h" aria-hidden="true"></span>

    <nav class="site-menu__nav" aria-label="{{ __('Menu utama') }}">
        <ul>
            @foreach ($items as $item)
                <li>
                    <a href="{{ $item['url'] }}" style="--i: {{ $loop->index }}" @class(['is-active' => request()->is($item['match'])])
                        @if (request()->is($item['match'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="site-menu__side">
        <button type="button" class="site-menu__close" data-menu-close>
            {{ __('Close') }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>

        <div class="site-menu__social">
            @foreach ($socials as $name => $link)
                <span><a href="{{ $link }}" target="_blank" rel="noopener" style="--i: {{ $loop->index }}">{{ $name }}</a></span>
            @endforeach
        </div>
    </div>
</div>
