@extends('layouts.backend')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mt-0">Selamat datang, {{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-0">Ini halaman admin {{ config('app.name') }} versi 2.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
