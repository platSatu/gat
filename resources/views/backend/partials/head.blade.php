<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex, nofollow">
<title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }} Admin</title>

<link rel="shortcut icon" href="{{ asset('images/logo/96.png') }}">
<link href="{{ asset('be/assets/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('be/assets/css/icons.css') }}" rel="stylesheet">
<link href="{{ asset('be/assets/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('be/assets/plugins/alertify/css/alertify.css') }}" rel="stylesheet">
@stack('styles')
