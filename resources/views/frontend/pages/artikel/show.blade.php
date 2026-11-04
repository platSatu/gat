@extends('layouts.frontend')

@section('title', __('Judul Artikel'))

@section('content')
    {{-- Figma "Gatsu News" (detail): judul, gambar + isi, "Baca juga". --}}
    <section class="page page--slate">
        <div class="container">
            <h1 class="page-title">{{ __('Judul Artikel') }}</h1>

            <article class="article-body">
                <div class="strip-card__img" style="background-image: url('{{ asset('images/pages/sejarah.jpg') }}')"></div>
                <div class="strip-card__body">
                    <h2>What is Lorem Ipsum?</h2>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                    <h2>Where does it come from?</h2>
                    <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.</p>
                </div>
            </article>

            <div class="related">
                <h2>{{ __('Baca juga:') }}</h2>
                @foreach (['images/about/berakar.jpg', 'images/about/restorasi.jpg'] as $image)
                    <a href="{{ route('articles.show') }}" class="related-card">
                        <img src="{{ asset($image) }}" alt="" loading="lazy">
                        <div>
                            <h3>{{ __('Judul Artikel') }}</h3>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                        </div>
                    </a>
                @endforeach
                <a href="{{ route('articles.category') }}" class="btn-pill btn-blue btn-sm">{{ __('Lihat Lebih Banyak Artikel') }}</a>
            </div>
        </div>
    </section>
@endsection
