@extends('layouts.frontend')

@section('title', 'Community of Love (COOL)')
@section('header_theme', 'dark')

@section('content')
    {{-- Figma "Desktop - 7" (revisi): COOL, satu layar penuh. Kiri: kotak teks + 2 tautan; kanan: foto. Foto sementara dari Figma. --}}
    <section class="cool-page">
        <div class="cool-page__grid">
            <div class="cool-page__left">
                <div class="cool-page__intro">
                    <h1>{{ __('Butuh komunitas untuk bertumbuh?') }}</h1>
                    <p>{{ __('Community of Love (COOL) jawabannya!') }}</p>
                    <p>{{ __('Kita semua membutuhkan orang lain untuk berbagi cerita dan saling menguatkan. COOL adalah komunitas rohani GBI Jl. Jend. Gatot Subroto. Di sini, kita bisa saling mendengarkan, memberi dan menerima dukungan, serta bertumbuh bersama dalam kasih Tuhan.') }}</p>
                    <p>{{ __('Mari hidup dalam Kesatuan hati, Tumbuh bersama, dan Memenangkan jiwa (KTM).') }}</p>
                </div>
                <div class="cool-page__links">
                    <a href="#" class="cool-page__link"><span>{{ __('Saya ingin bertumbuh!') }}</span> @include('frontend.partials.icon', ['name' => 'arrow-circle'])</a>
                    <a href="#" class="cool-page__link"><span>{{ __('Apa itu COOL?') }}</span> @include('frontend.partials.icon', ['name' => 'arrow-circle'])</a>
                </div>
            </div>
            <figure class="cool-page__photo">
                <img src="{{ asset('images/pages/cool.jpg') }}" alt="Community of Love (COOL) GBI Gatsu">
            </figure>
        </div>
    </section>
@endsection
