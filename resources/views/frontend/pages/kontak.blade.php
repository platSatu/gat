@extends('layouts.frontend')

@section('title', __('Kontak'))

@section('content')
    {{-- Figma "Contact". Data kontak sementara di sini; nanti dari pengaturan admin. --}}
    @php
        $phone = '(021) 87950639';
        $email = 'otp@hmministry.com';
        $address = 'Sentul City, Jl. Akses Convention Center No.1, Kab. Bogor, Jawa Barat, Indonesia';
    @endphp

    <section class="page page--slate page--center">
        <div class="container contact-grid">
            <div>
                <h1>{{ __('Mari Terhubung!') }}</h1>
                <p>{{ __('Punya pertanyaan atau ingin terhubung dengan pelayanan kami? Klik ikon untuk langsung menghubungi kami, atau salin informasi kontak yang tersedia.') }}</p>
            </div>

            <div class="card-cream contact-card">
                <a href="tel:+622187950639">@include('frontend.partials.icon', ['name' => 'phone']) <span>{{ $phone }} (09.00 - 16.00) No Call</span></a>
                <a href="mailto:{{ $email }}">@include('frontend.partials.icon', ['name' => 'mail']) <span>{{ $email }}</span></a>
                <a href="https://maps.google.com/?q={{ urlencode($address) }}" target="_blank" rel="noopener">@include('frontend.partials.icon', ['name' => 'pin']) <span>{{ $address }}</span></a>
                <a href="{{ route('giving') }}">@include('frontend.partials.icon', ['name' => 'give']) <span>{{ __('Ambil bagian dalam mendukung pelayanan kami. Lihat Cara Memberi') }}</span></a>
            </div>
        </div>
    </section>
@endsection
