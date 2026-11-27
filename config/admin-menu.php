<?php

/*
 * Daftar menu admin v2 (sidebar + hak akses).
 *
 * Setiap item:
 *   'label'      => tulisan di sidebar
 *   'icon'       => ikon Material Design (mdi-...)
 *   'route'      => nama route yang dibuka saat menu diklik
 *   'active'     => pola nama route milik menu ini (dipakai untuk sidebar aktif DAN cek akses)
 *   'menu'       => nama menu di tabel `menus` (sama dengan website lama), mis. "Page-Article".
 *                   User yang bukan superadmin hanya bisa membuka menu ini kalau dicentang di Settings → Setting Menu.
 *   'superadmin' => true = hanya superadmin (dipakai untuk menu Settings).
 *
 * MENAMBAH MENU BARU:
 *   1. Buat route + halaman/CRUD-nya seperti biasa.
 *   2. Tambah satu item di bawah (isi 'menu' dengan nama baru, mis. "Page-Warta").
 *   3. Buka admin Settings → Menu, klik "Tambahkan menu yang belum ada" (nama menu otomatis masuk tabel `menus`),
 *      lalu centang menu itu di Settings → Setting Menu untuk user yang boleh memakainya.
 */
return [

    'GBI Gatsu' => [
        ['label' => 'Article', 'icon' => 'mdi-file-document', 'route' => 'admin.articles.index', 'active' => 'admin.articles.*', 'menu' => 'Page-Article'],
        ['label' => 'Category Article', 'icon' => 'mdi-tag-multiple', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'menu' => 'Page-Article'],
        ['label' => 'Highlights', 'icon' => 'mdi-star-circle', 'route' => 'admin.highlights.index', 'active' => 'admin.highlights.*', 'menu' => 'Page-Highlights'],
        ['label' => 'Tentang Kami', 'icon' => 'mdi-information-outline', 'route' => 'admin.about.index', 'active' => 'admin.about.*', 'menu' => 'Page-Tentang-Kami'],
        ['label' => 'Mitra', 'icon' => 'mdi-account-multiple', 'route' => 'admin.partners.index', 'active' => 'admin.partners.*', 'menu' => 'Page-Mitra'],
    ],

    'Settings' => [
        ['label' => 'Daftar User', 'icon' => 'mdi-account-box', 'route' => 'admin.settings.users.index', 'active' => 'admin.settings.users.*', 'superadmin' => true],
        ['label' => 'Roles', 'icon' => 'mdi-security', 'route' => 'admin.settings.roles.index', 'active' => 'admin.settings.roles.*', 'superadmin' => true],
        ['label' => 'Menu', 'icon' => 'mdi-format-list-bulleted', 'route' => 'admin.settings.menus.index', 'active' => 'admin.settings.menus.*', 'superadmin' => true],
        ['label' => 'Setting Menu', 'icon' => 'mdi-key-variant', 'route' => 'admin.settings.setting-menu.index', 'active' => 'admin.settings.setting-menu.*', 'superadmin' => true],
    ],

];
