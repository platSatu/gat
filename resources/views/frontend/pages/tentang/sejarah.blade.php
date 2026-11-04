@extends('layouts.frontend')

@section('title', __('Sejarah GBI Gatsu'))

@section('content')
    {{-- Figma "Desktop - 8" / "Sejarah GATSU" --}}
    <section class="page page--slate pb-0">
        <div class="container">
            <h1 class="page-title page-title--upper text-center">{{ __('Sejarah GBI Gatsu') }}</h1>
            <img class="history-pill" src="{{ asset('images/pages/sejarah.jpg') }}" alt="">
            <p class="history-text">Lorem ipsum dolor sit amet, est fugiat nostrud dolore exercitation. Ullamco id adipiscing cillum ea sint cillum officia. Proident non adipiscing commodo sed magna anim sint minim est in voluptate voluptate.</p>
        </div>
        <img class="history-band" src="{{ asset('images/about/berakar.jpg') }}" alt="" loading="lazy">
        <div class="container history-foot">
            <p class="history-text mt-0">Dipiscing quis excepteur voluptate sed aliqua magna dolor proident dolore culpa dolore eiusmod. In anim commodo quis adipiscing velit irure officia ullamco.</p>
            <a href="#" class="btn-pill btn-sm text-ink fw-semibold">{{ __('Tonton Selengkapnya') }}</a>
        </div>
    </section>
@endsection
