<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
<meta name="description" content="@yield('meta_description', __('GBI Jl. Jend. Gatot Subroto — gereja lokal di bawah naungan Sinode Gereja Bethel Indonesia.'))">
<link rel="shortcut icon" href="{{ asset('images/logo/96.png') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,700;0,800;1,300;1,700&display=swap" rel="stylesheet">
<link href="{{ asset('css/frontend.css') }}?v={{ filemtime(public_path('css/frontend.css')) }}" rel="stylesheet">
@stack('styles')
