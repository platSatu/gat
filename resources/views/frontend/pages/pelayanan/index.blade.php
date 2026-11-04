@extends('layouts.frontend')

@section('title', __('Pelayanan'))

@section('content')
    {{-- Figma "Pelayanan & Ministry" (hero) --}}
    <section class="ministry-hero">
        <img src="{{ asset('images/pages/pelayanan.jpg') }}" alt="">
        <div>
            <h1>{{ __('PELAYANAN GBI GATSU') }}</h1>
            <div class="ministry-hero__card">
                <p>{{ __('Melalui berbagai pelayanan, GBI Jl. Jend. Gatot Subroto hadir untuk melayani, membangun kehidupan, dan menghadirkan kasih Tuhan bagi jemaat dan masyarakat. Temukan pelayanan kami dan lihat bagaimana anda dapat terhubung, terlibat, atau memperoleh dukungan yang dibutuhkan.') }}</p>
                <a href="{{ route('ministry.show') }}" class="btn-pill btn-sm">{{ __('Jelajahi Pelayanan Kami') }}</a>
            </div>
        </div>
    </section>
@endsection
