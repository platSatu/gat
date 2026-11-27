@extends('layouts.backend')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mt-0">Selamat datang, {{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-0">Ini halaman admin {{ config('app.name') }} versi 2.</p>
                    @unless (auth()->user()->punyaAksesAdmin())
                        <div class="alert alert-warning mt-3 mb-0">
                            Akun Anda belum punya akses ke menu admin mana pun. Minta superadmin mengaturnya di Settings → Setting Menu.
                        </div>
                    @endunless
                </div>
            </div>
        </div>
    </div>
@endsection
