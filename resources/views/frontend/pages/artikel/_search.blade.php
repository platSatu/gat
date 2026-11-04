{{-- Kolom cari + bar kategori (dipakai di halaman Artikel & hasil pencarian). --}}
<form class="search-pill" action="{{ route('articles.search') }}" method="GET" role="search">
    @include('frontend.partials.icon', ['name' => 'search'])
    <label class="sr-only" for="q">{{ __('Cari artikel') }}</label>
    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari artikel, topik atau pertanyaan..') }}">
</form>

@if ($withCategories ?? true)
    <div class="category-bar">
        <strong>{{ __('Kategori') }} :</strong>
        <nav aria-label="{{ __('Kategori') }}">
            <a href="{{ route('articles.categories') }}">{{ __('Semua kategori') }}</a>
            @foreach (['Mengenal Yesus', 'Gatsu News', 'Renungan', 'Pesan Gembala', 'Pengajaran', 'Kehidupan Kristen', 'Kisah Inspiratif', 'Komunitas'] as $category)
                <a href="{{ route('articles.category') }}">{{ __($category) }}</a>
            @endforeach
        </nav>
    </div>
@endif
