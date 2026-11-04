{{-- Halaman login admin (/auth/login), memakai CSS template yang sama dengan admin. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('backend.partials.head')
</head>
<body>
    <div class="wrapper-page">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ asset('images/logo/gatsu_text_black.png') }}" height="60" alt="{{ config('app.name') }}">
                </div>

                <div class="px-3 pb-3">
                    <form method="POST" action="{{ route('login') }}" class="form-horizontal m-t-20" novalidate>
                        @csrf

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember" value="1">
                                <label class="custom-control-label" for="remember">Ingat saya</label>
                            </div>
                        </div>

                        <div class="form-group text-center m-t-20">
                            <button type="submit" class="btn btn-danger btn-block waves-effect waves-light">Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
