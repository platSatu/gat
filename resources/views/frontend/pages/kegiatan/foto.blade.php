@extends('layouts.frontend')

@section('title', __('Foto Kegiatan'))
@section('header_theme', 'dark')

@section('content')
    {{-- Figma "Desktop - 5": satu foto + panah, tombol penghapusan foto, keterangan + unduh. --}}
    <section class="page page--cream">
        <div class="container">
            <div class="photo-view">
                <a href="#" class="round-btn round-btn--solid" aria-label="{{ __('Sebelumnya') }}">@include('frontend.partials.icon', ['name' => 'arrow-left'])</a>
                <img class="photo-view__img" src="{{ asset('images/pages/kegiatan-4.jpg') }}" alt="">
                <a href="#" class="round-btn round-btn--solid" aria-label="{{ __('Berikutnya') }}">@include('frontend.partials.icon', ['name' => 'arrow-right'])</a>
            </div>

            <div class="photo-view__meta">
                <a href="{{ route('events.photo-request') }}" class="btn-pill btn-sm">{{ __('Ajukan Penghapusan Foto') }}</a>
                <div class="photo-note">
                    <p>{{ __('Galeri ini berisi dokumentasi resmi kegiatan GBI Jl. Jend. Gatot Subroto yang telah dikurasi untuk keperluan informasi, publikasi, dan arsip pelayanan.') }}</p>
                    <a href="{{ asset('images/pages/kegiatan-4.jpg') }}" download aria-label="{{ __('Unduh foto') }}">@include('frontend.partials.icon', ['name' => 'download'])</a>
                </div>
            </div>
        </div>
    </section>
@endsection
