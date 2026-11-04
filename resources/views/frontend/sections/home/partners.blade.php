{{-- Pelayanan dan mitra (logo). Ganti/tambah logo di $partners (file di public/images/partners). --}}
@php
    $partners = [
        'gbi' => 'Gereja Bethel Indonesia',
        'e21' => 'Empowered21',
        'everyone' => 'Everyone Asia',
        'sttb' => 'STT Bethel The Way',
        'hmm' => 'Healing Movement Ministry',
        'jdn' => 'Jaringan Doa Nasional',
        'sicc' => 'Sentul International Convention Center',
        'gba' => 'Graha Bina Asuh',
    ];
@endphp

<section class="home-partners">
    <div class="container">
        <h2>{{ __('PELAYANAN DAN MITRA KAMI') }}</h2>
        <ul class="home-partners__list">
            @foreach ($partners as $file => $name)
                <li><img src="{{ asset('images/partners/'.$file.'.png') }}" alt="{{ $name }}" loading="lazy"></li>
            @endforeach
        </ul>
    </div>
</section>
