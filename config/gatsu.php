<?php

/*
 * Pengaturan website Gatsu (versi baru).
 */
return [

    // Alamat asal gambar & file upload lama (folder public website lama), mis. https://gbigatsu.id
    // Kosong = gambar dibaca dari folder public project ini sendiri.
    // Dipakai selama website baru (VPS) membaca database & gambar dari website lama (cPanel).
    'gambar_url' => rtrim((string) env('GAMBAR_URL', ''), '/'),

];
