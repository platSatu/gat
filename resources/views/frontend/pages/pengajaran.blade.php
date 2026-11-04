@extends('layouts.frontend')

@section('title', __('Pengajaran'))

@section('content')
    {{-- Figma "Pengajaran 1": pilihan Artikel / KOM Everyone. --}}
    <section class="page page--slate page--center">
        <div class="container">
            <h1 class="sr-only">{{ __('Pengajaran') }}</h1>
            <div class="teach-grid">
                <div class="card-cream teach-card">
                    <h2>{{ __('ARTIKEL') }}</h2>
                    <p>{!! __('<strong>Bacaan</strong> yang membahas iman, pesan gembala, isu sehari-hari, dan bahan pembelajaran untuk menolong Anda memahami serta menerapkan firman Tuhan.') !!}</p>
                    <a href="{{ route('articles') }}" class="btn-pill btn-blue">{{ __('Mulai Membaca') }}</a>
                </div>
                <div class="card-cream teach-card">
                    <h2>KOM EVERYONE</h2>
                    <p>{!! __('<strong>Kelas Online</strong> dengan materi firman Tuhan yang tersusun secara bertahap untuk membangun dasar iman dan mendukung pertumbuhan rohani Anda.') !!}</p>
                    <a href="#" class="btn-pill btn-blue">{{ __('Mulai Belajar') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
