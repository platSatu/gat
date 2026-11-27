{{--
    Pelayanan dan mitra (logo). Isi dari tabel `partners` (admin: Mitra), hanya yang Active.
    Di beranda hanya logo yang tampil. Website diisi = logo bisa diklik dan pindah ke website mitra (tab yang sama).
    Kalau tabel belum dibuat, dipakai daftar lama di bawah ini (file di public/images/partners).
--}}
@php
    $partners = \App\Models\Partner::untukBeranda()?->map(fn ($p) => [
        'name' => $p->name, 'logo' => $p->logo_url, 'description' => $p->description, 'website' => $p->website,
    ])->all() ?? collect([
        'gbi' => 'Gereja Bethel Indonesia',
        'e21' => 'Empowered21',
        'everyone' => 'Everyone Asia',
        'sttb' => 'STT Bethel The Way',
        'hmm' => 'Healing Movement Ministry',
        'jdn' => 'Jaringan Doa Nasional',
        'sicc' => 'Sentul International Convention Center',
        'gba' => 'Graha Bina Asuh',
    ])->map(fn ($name, $file) => ['name' => $name, 'logo' => asset('images/partners/'.$file.'.png'), 'description' => null, 'website' => null])->values()->all();
@endphp

@if (count($partners))
    <section class="home-partners" id="mitra">
        <div class="container">
            <h2>{{ __('PELAYANAN DAN MITRA KAMI') }}</h2>
            <ul class="home-partners__list">
                @foreach ($partners as $partner)
                    <li>
                        @if ($partner['website'])
                            <a href="{{ $partner['website'] }}"><img src="{{ $partner['logo'] }}" alt="{{ $partner['name'] }}" loading="lazy"></a>
                        @else
                            <img src="{{ $partner['logo'] }}" alt="{{ $partner['name'] }}" loading="lazy">
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
