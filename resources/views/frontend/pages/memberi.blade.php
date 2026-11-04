@extends('layouts.frontend')

@section('title', __('Memberi'))
@section('body_class', 'page-giving')

@section('content')
    {{-- Figma "Giving" (Indonesia). Versi Inggris: route('giving.en'). --}}
    <section class="page page--slate page--center">
        <div class="container contact-grid">
            <div>
                <h1>{{ __('Memberi') }}</h1>
                <p class="verse">Hendaklah masing-masing memberikan menurut kerelaan hatinya, jangan dengan sedih hati atau karena paksaan, sebab Allah mengasihi orang yang memberi dengan sukacita.<br>2 Kor 9:7</p>
                <p class="verse">Terima kasih telah memberi dengan sukacita kepada Tuhan!</p>
            </div>

            {{-- Rekening: BCA (desktop & mobile Figma) dan Permata (hanya di frame mobile Figma). --}}
            <div class="give-cards">
                <div class="card-cream give-card">
                    <span class="bank-name">BCA</span>
                    <span>BCA cabang Menara Ancol</span>
                    <span>Ac. 6350070778</span>
                    <span>A/n GBI JL GATOT SUBROTO</span>
                </div>
                <div class="card-cream give-card">
                    <span class="bank-pill">PERMATA</span>
                    <span>Bank Permata cabang Kelapa Gading</span>
                    <span>Ac. 0701129504</span>
                    <span>A/n GBI JL GATOT SUBROTO</span>
                </div>
            </div>
        </div>
    </section>
@endsection
