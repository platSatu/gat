@extends('layouts.backend')

@php $baru = ! $category->exists; @endphp

@section('title', $baru ? 'Create Category' : 'Edit Category')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Category Article</a></li>
    <li class="breadcrumb-item active">{{ $baru ? 'Create' : 'Edit' }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h1 class="admin-heading">{{ $baru ? 'Create Category' : 'Edit Category' }}</h1>

            <form method="POST" action="{{ $baru ? route('admin.categories.store') : route('admin.categories.update', $category) }}">
                @csrf
                @unless ($baru) @method('PUT') @endunless

                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required maxlength="100"
                        @class(['form-control', 'is-invalid' => $errors->has('name')])>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ $baru ? 'Create Category' : 'Update Category' }}</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Batal</a>
            </form>
        </div>
    </div>
@endsection

@include('backend.partials.crud-styles')
