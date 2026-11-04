{{-- Judul halaman + breadcrumb. Isi lewat @section('title') dan @section('breadcrumb') di setiap halaman. --}}
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @yield('breadcrumb')
                </ol>
            </div>
            <h4 class="page-title">@yield('title', 'Dashboard')</h4>
        </div>
    </div>
</div>
