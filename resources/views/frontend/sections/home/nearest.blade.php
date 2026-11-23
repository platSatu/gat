{{-- Teks berjalan "DOA – PUJIAN – PENYEMBAHAN" + Gereja Terdekat (peta). --}}
@php $words = __('DOA – PUJIAN – PENYEMBAHAN') . ' – '; @endphp

<div class="home-marquee" aria-hidden="true">
    <div class="home-marquee__track">
        {{-- Isi diulang dua kali supaya animasinya menyambung tanpa jeda. --}}
        @for ($i = 0; $i < 12; $i++)
            <span>{{ $words }}</span>
        @endfor
    </div>
</div>

<section class="home-nearest">
    <div class="container home-nearest__inner">
        <div>
            <p class="home-nearest__eyebrow">{{ __('Terhubung dengan') }}</p>
            <h2>{{ __('GEREJA TERDEKAT ANDA') }}</h2>
            <p>{{ __('Temukan gereja di bawah pembinaan GBI Jl. Jend. Gatot Subroto yang berada di kota atau wilayah anda') }}</p>
        </div>
        <div class="home-nearest__map">
            <img src="{{ asset('images/home/world-map.png') }}" alt="" width="1080" height="650" loading="lazy">
            {{-- Cari gereja: tombol "Cari" berubah jadi kolom ketik, hasil muncul di dropdown (frontend.js, bagian "Cari gereja"). --}}
            <div class="church-search" data-church-search data-url="{{ route('gereja.cari') }}"
                data-msg-hint="{{ __('Mau ibadah di mana? Ketik nama kota, provinsi, atau negara.') }}"
                data-msg-loading="{{ __('Sedang mencari gereja…') }}"
                data-msg-found="{{ __('Ada :count gereja untuk “:q”') }}"
                data-msg-more="{{ __('Menampilkan :shown dari :count. Ketik lebih spesifik untuk mempersempit.') }}"
                data-msg-empty="{{ __('Belum ketemu gereja untuk “:q”. Coba nama kota terdekat atau provinsinya.') }}"
                data-msg-error="{{ __('Maaf, jadwal ibadah sedang tidak bisa dimuat. Coba lagi sebentar lagi ya.') }}"
                data-msg-noschedule="{{ __('Jadwal belum tersedia') }}"
                data-msg-call="{{ __('Telepon') }}">
                <button type="button" class="btn-pill btn-blue church-search__toggle" data-cs-open aria-expanded="false" aria-controls="church-search-panel">{{ __('Cari') }}</button>

                <div class="church-search__panel" id="church-search-panel" data-cs-panel hidden>
                    <div class="church-search__field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                        <label class="sr-only" for="church-search-input">{{ __('Cari gereja terdekat') }}</label>
                        <input id="church-search-input" type="search" data-cs-input autocomplete="off" spellcheck="false"
                            role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="church-search-list"
                            placeholder="{{ __('Kota, provinsi, atau nama gereja…') }}">
                        <button type="button" class="church-search__close" data-cs-close aria-label="{{ __('Tutup pencarian') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                        </button>
                    </div>

                    <div class="church-search__dropdown" data-cs-dropdown>
                        <p class="church-search__status" data-cs-status aria-live="polite"></p>
                        <div class="church-search__chips" data-cs-chips>
                            @foreach (['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Bali', 'Singapore'] as $saran)
                                <button type="button" data-cs-suggest>{{ $saran }}</button>
                            @endforeach
                        </div>
                        <ul class="church-search__list" id="church-search-list" role="listbox" data-cs-list></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
