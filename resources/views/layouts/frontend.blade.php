{{-- Kerangka website publik. Bagian-bagiannya di resources/views/frontend/partials, isi halaman di frontend/sections. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('frontend.partials.head')
</head>
<body class="@yield('body_class')">
    @include('frontend.partials.tag-body')
    @include('frontend.partials.loader')
    @include('frontend.partials.header')
    @include('frontend.partials.menu')

    <main id="main">
        @yield('content')
    </main>

    @include('frontend.partials.footer')
    @include('frontend.partials.scripts')
</body>
</html>
