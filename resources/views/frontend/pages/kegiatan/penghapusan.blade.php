@extends('layouts.frontend')

@section('title', __('Ajukan Penghapusan Foto'))
@section('header_theme', 'dark')

@section('content')
    {{-- Figma "Desktop - 6". Form belum tersambung ke database (tahap tampilan). --}}
    <section class="page page--cream page--center">
        <div class="container">
            <div class="card-slate request-card">
                <h1 class="tag-pill">{{ __('Ajukan Penghapusan Foto') }}</h1>
                <p>{{ __('Menemukan foto diri Anda dan memiliki pertanyaan atau permintaan terkait foto tersebut? Hubungi kami melalui form berikut:') }}</p>

                <form class="request-form" action="#" method="POST" onsubmit="return false">
                    @csrf
                    <label>{{ __('Nama') }} : <input type="text" name="name" required></label>
                    <label>{{ __('Email') }} : <input type="email" name="email" required></label>
                    <label>{{ __('Email atau Nomor Kontak') }} : <input type="text" name="contact"></label>
                    <label>{{ __('Link atau Screenshot Foto') }} : <input type="text" name="photo"></label>
                    <label>{{ __('Alasan Permintaan') }} : <textarea name="reason" rows="2"></textarea></label>
                    <button type="submit" class="btn-pill btn-sm">{{ __('Kirim') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection
