@extends('layouts.frontend')

@section('title', __('Galeri Kegiatan'))
@section('header_theme', 'dark')

@section('content')
    {{-- Figma "Desktop - 4": grid foto + "Tampilkan Lebih Banyak". --}}
    @php $photos = ['kegiatan-1', 'kegiatan-2', 'kegiatan-3', 'kegiatan-4', 'kegiatan-2', 'kegiatan-1']; @endphp

    <section class="page page--cream">
        <div class="container">
            <h1 class="sr-only">{{ __('Galeri Kegiatan') }}</h1>
            <div class="gallery-grid">
                @for ($i = 0; $i < 24; $i++)
                    <a href="{{ route('events.photo') }}"><img src="{{ asset('images/pages/'.$photos[$i % 6].'.jpg') }}" alt="" loading="lazy"></a>
                @endfor
            </div>
            <div class="gallery-more">
                <a href="#" class="btn-pill btn-blue">{{ __('Tampilkan Lebih Banyak') }}</a>
            </div>
        </div>
    </section>
@endsection
