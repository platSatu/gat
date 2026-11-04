{{-- Kerangka halaman admin (template Zoter dari public/be). Isi tiap bagian ada di resources/views/backend/partials. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('backend.partials.head')
</head>
<body class="fixed-left">
    <div id="wrapper">
        @include('backend.partials.sidebar')

        <div class="content-page">
            <div class="content">
                @include('backend.partials.topbar')

                <div class="page-content-wrapper">
                    <div class="container-fluid">
                        @include('backend.partials.page-title')
                        @include('backend.partials.alerts')

                        @yield('content')
                    </div>
                </div>
            </div>

            @include('backend.partials.footer')
        </div>
    </div>

    @include('backend.partials.scripts')
</body>
</html>
