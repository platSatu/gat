@extends('layouts.frontend')

@section('title', __('Kegiatan'))
@section('header_theme', 'dark')

@section('content')
    {{-- Figma "Desktop - 3": kegiatan terkini (besar) + mendatang & sebelumnya. Isi sementara di $events. --}}
    @php
        $events = [
            'current' => ['tag' => __('Kegiatan Terkini'), 'title' => 'Planetshakers Live in Indonesia 2026', 'date' => 'Friday, July 3, 2026', 'button' => __('Daftar Sekarang'), 'image' => 'images/pages/kegiatan-3.jpg'],
            'next' => ['tag' => __('Kegiatan Mendatang'), 'title' => 'Planetshakers Live in Indonesia 2027', 'date' => 'Friday, July 2, 2027', 'button' => __('Lihat Selengkapnya'), 'image' => 'images/pages/kegiatan-1.jpg'],
            'past' => ['tag' => __('Kegiatan Sebelumnya'), 'title' => 'EveryOneAsia 2024', 'date' => 'Friday - Sunday, 4-6 July 2024', 'button' => __('Lihat Dokumentasi'), 'image' => 'images/pages/kegiatan-2.jpg', 'url' => route('events.gallery')],
        ];
    @endphp

    <section class="page page--cream">
        <div class="container">
            <h1 class="sr-only">{{ __('Kegiatan') }}</h1>
            <div class="events-grid">
                @include('frontend.pages.kegiatan._tile', ['event' => $events['current']])
                <div class="events-grid__side">
                    @include('frontend.pages.kegiatan._tile', ['event' => $events['next']])
                    @include('frontend.pages.kegiatan._tile', ['event' => $events['past']])
                </div>
            </div>
        </div>
    </section>
@endsection
