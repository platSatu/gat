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

    // Statistik pengunjung di dashboard admin (tabel visitor_histories, dipakai bersama website lama).
    'statistik' => [
        // Hanya kunjungan ke domain ini (dan www.) yang dihitung. Tabel yang sama juga berisi domain lain (mis. hmministry.id).
        'domain' => env('STATISTIK_DOMAIN', 'gbigatsu.id'),
        // Hasil hitungan disimpan sementara (menit) supaya dashboard tetap cepat walau tabelnya jutaan baris.
        'cache_menit' => (int) env('STATISTIK_CACHE_MENIT', 10),
        // Zona waktu kolom visited_at (website lama menyimpan dalam WIB).
        'zona_waktu' => env('STATISTIK_ZONA_WAKTU', 'Asia/Jakarta'),
        // Catat kunjungan halaman publik website ini ke visitor_histories (seperti website lama).
        'catat_pengunjung' => (bool) env('CATAT_PENGUNJUNG', true),
    ],

];
