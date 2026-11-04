@extends('layouts.frontend')

@section('title', 'Gatsu News')

@section('content')
    {{-- Figma "Gatsu News": daftar artikel dalam satu kategori. --}}
    @php $images = ['images/about/berakar.jpg', 'images/pages/sejarah.jpg', 'images/about/mengenal.jpg', 'images/about/restorasi.jpg']; @endphp

    <section class="page page--slate">
        <div class="container">
            <h1 class="page-title">Gatsu News</h1>
            <div class="news-list">
                @foreach ($images as $image)
                    <a href="{{ route('articles.show') }}" class="strip-card">
                        <div class="strip-card__img" style="background-image: url('{{ asset($image) }}')"></div>
                        <div class="strip-card__body">
                            <h3>{{ __('Judul Artikel') }}</h3>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
