@extends('layouts.frontend')

@section('title', 'Community of Love (COOL)')
@section('header_theme', 'dark')

@section('content')
    {{-- Figma "Desktop - 7": COOL. --}}
    <section class="page page--cream">
        <div class="container">
            <div class="cool-grid">
                <div class="cool-grid__left">
                    <div class="card-slate cool-intro">
                        <h1>COMMUNITY OF LOVE (COOL)</h1>
                        <p>{{ __('COOL — Community of Love, komunitas rohani dari GBI Jl. Jend. Gatot Subroto yang menolong setiap orang untuk dikenal, dikuatkan, dan bertumbuh bersama dalam kasih Tuhan.') }}</p>
                    </div>
                    <div class="cool-links">
                        <a href="#" class="card-slate cool-link">{{ __('Saya ingin bertumbuh!') }} @include('frontend.partials.icon', ['name' => 'arrow-circle'])</a>
                        <a href="#" class="card-slate cool-link">{{ __('Apa itu COOL?') }} @include('frontend.partials.icon', ['name' => 'arrow-circle'])</a>
                    </div>
                </div>
                <img class="cool-photo" src="{{ asset('images/pages/kegiatan-2.jpg') }}" alt="">
            </div>
        </div>
    </section>
@endsection
