@extends('layouts.frontend')

@section('title', $section->tr('name'))
@section('meta_description', \App\Models\Article::teksPolos($section->tr('excerpt') ?: $section->tr('description'), 155))

@section('content')
    {{-- Detail blok Tentang Kami (/tentang/{slug}): foto lebar seperti di halaman Tentang, lalu isi lengkap. Data: AboutController@show. --}}
    <section @class(['about-block', 'about-block--tinted' => ! $section->tint])>
        @if ($section->background_url)
            <img class="about-block__img" src="{{ $section->background_url }}" alt="">
        @endif
        <div class="about-block__content">
            <h1 class="about-detail__title">{{ $section->tr('name') }}</h1>
            @if (filled($section->tr('subtitle')))<h3>{{ $section->tr('subtitle') }}</h3>@endif
        </div>
    </section>

    <section class="page page--slate">
        <div class="container">
            <article class="article-body">
                <div class="strip-card__body article-content about-detail__body">
                    {!! $section->isi !!}
                </div>
            </article>

            @if (filled($section->link))
                <p class="text-center about-detail__more">
                    <a href="{{ $section->url }}" class="btn-pill btn-blue">{{ $section->tombol() }}</a>
                </p>
            @endif

            @if ($lainnya->isNotEmpty())
                <div class="about-detail__others">
                    <h2>{{ __('Tentang Gatsu') }}</h2>
                    <div class="about-detail__grid">
                        @foreach ($lainnya as $item)
                            <a href="{{ route('about.show', $item) }}" @class(['about-mini', 'about-mini--tinted' => ! $item->tint])>
                                @if ($item->background_url)<img src="{{ $item->background_url }}" alt="" loading="lazy">@endif
                                <span>{{ $item->tr('name') }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
