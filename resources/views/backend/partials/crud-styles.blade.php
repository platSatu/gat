{{-- Gaya kecil untuk halaman CRUD admin, meniru tampilan admin gbigatsu.id (judul besar, thumbnail, filter). --}}
@once
    @push('styles')
        <style>
            .admin-heading { font-size: 34px; font-weight: 700; margin: 0 0 16px; color: #212529; }
            .admin-table th { font-weight: 600; white-space: nowrap; }
            .admin-table td { vertical-align: middle; }
            .admin-thumb { width: 90px; height: 50px; object-fit: cover; border-radius: 4px; }
            .admin-preview { display: block; max-width: 320px; max-height: 200px; border-radius: 6px; }
            .admin-filter-select { max-width: 200px; }
        </style>
    @endpush
@endonce
