@extends('layouts.frontend')

@section('body_class', 'page-home')

@section('content')
    {{-- Setiap bagian beranda ada di file sendiri: resources/views/frontend/sections/home/ --}}
    @include('frontend.sections.home.hero')
    @include('frontend.sections.home.calling')
    @include('frontend.sections.home.nearest')
    @include('frontend.sections.home.events')
    @include('frontend.sections.home.partners')
@endsection
