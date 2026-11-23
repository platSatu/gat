<?php

/*
 * Pengaturan website Gatsu (versi baru).
 */
return [

    // Alamat asal gambar & file upload lama (folder public website lama), mis. https://gbigatsu.id
    // Kosong = gambar dibaca dari folder public project ini sendiri.
    // Dipakai selama website baru (VPS) membaca database & gambar dari website lama (cPanel).
    'gambar_url' => rtrim((string) env('GAMBAR_URL', ''), '/'),

    // Sumber data gereja & jadwal ibadah (sama seperti website lama: HMMinistry).
    'hmministry' => [
        'cabang_url' => env('HMMINISTRY_CABANG_URL', 'https://hmministry.net/api/cabang_api.php'),
        'kebaktian_url' => env('HMMINISTRY_KEBAKTIAN_URL', 'https://hmministry.net/api/kebaktian_api.php'),
        // Website lama mematikan cek SSL. Di sini menyala; matikan (false) hanya kalau sertifikat sumbernya bermasalah.
        'verify_ssl' => (bool) env('HMMINISTRY_VERIFY_SSL', true),
        // Lama data disimpan sementara (detik), supaya tidak mengunduh ±3.000 data setiap ketikan.
        'cache_ttl' => (int) env('HMMINISTRY_CACHE_TTL', 1800),
    ],

];
