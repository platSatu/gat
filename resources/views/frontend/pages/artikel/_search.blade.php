{{-- Kolom cari + bar kategori (dipakai di halaman Artikel & hasil pencarian).
     Kategori dari database ($kategoriArtikel dikirim controller); kategori aktif = $kategori. --}}
<form class="search-pill" action="{{ route('articles.search') }}" method="GET" role="search">
    @include('frontend.partials.icon', ['name' => 'search'])
    <label class="sr-only" for="q">{{ __('Cari artikel') }}</label>
    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari artikel, topik atau pertanyaan..') }}">
</form>

@if (($withCategories ?? true) && isset($kategoriArtikel))
    <div class="category-bar">
        <strong>{{ __('Kategori') }} :</strong>
        <nav aria-label="{{ __('Kategori') }}">
            <a href="{{ route('articles.categories') }}">{{ __('Semua kategori') }}</a>
            @foreach ($kategoriArtikel as $item)
                <a href="{{ route('articles.category', $item) }}" @class(['is-active' => isset($kategori) && $kategori?->is($item)])
                    @if (isset($kategori) && $kategori?->is($item)) aria-current="page" @endif>{{ $item->name }}</a>
            @endforeach
        </nav>
    </div>
@endif
