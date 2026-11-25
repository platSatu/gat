<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Blok halaman "Tentang Kami" (/tentang) + terjemahannya.
 * Hanya MEMBUAT tabel baru; tabel lain tidak disentuh. Tabel yang sudah ada dilewati (aman dijalankan ulang).
 * Jalankan HANYA file ini:
 *   php artisan migrate --path=database/migrations/2026_11_25_200000_create_about_sections_table.php
 * Atau manual lewat phpMyAdmin: database/sql/about_sections.sql (isinya sama).
 *
 * Saat tabel baru dibuat, 4 blok yang sebelumnya tertulis di kode ikut diisi (ID + EN dari lang/en.json).
 */
return new class extends Migration
{
    public function up(): void
    {
        $baru = ! Schema::hasTable('about_sections');

        if ($baru) {
            Schema::create('about_sections', function (Blueprint $table) {
                $table->id();
                $table->string('name');                       // judul blok
                $table->string('slug')->unique();             // /tentang/{slug}
                $table->string('subtitle')->nullable();       // sub-judul kecil (opsional)
                $table->text('excerpt')->nullable();          // teks singkat di halaman Tentang
                $table->longText('description')->nullable();  // isi lengkap halaman detail
                $table->string('background')->nullable();     // path gambar di public/, mis. uploads/about/x.jpg
                $table->boolean('tint')->default(true);       // 1 = gambar diwarnai biru oleh website
                $table->string('button_text')->nullable();    // tulisan tombol (kosong = "Lihat Selengkapnya")
                $table->string('link')->nullable();           // tujuan tombol selain halaman detail (opsional)
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true);     // 1 = active, 0 = inactive
                $table->timestamps();
                $table->index(['status', 'sort_order']);
            });
        }

        if (! Schema::hasTable('about_section_translations')) {
            Schema::create('about_section_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('about_section_id')->constrained('about_sections')->cascadeOnDelete();
                $table->string('locale', 5);
                $table->string('name')->nullable();
                $table->string('subtitle')->nullable();
                $table->text('excerpt')->nullable();
                $table->longText('description')->nullable();
                $table->string('button_text')->nullable();
                $table->timestamps();
                $table->unique(['about_section_id', 'locale']);
            });
        }

        if ($baru) {
            $this->isiAwal();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('about_section_translations');
        Schema::dropIfExists('about_sections');
    }

    /** 4 blok yang sebelumnya ada di resources/views/frontend/pages/tentang/index.blade.php. */
    private function isiAwal(): void
    {
        $en = is_file(lang_path('en.json')) ? (json_decode(file_get_contents(lang_path('en.json')), true) ?: []) : [];
        $now = now();

        $blok = [
            ['Mengenal GBI Gatsu', 'mengenal-gbi-gatsu', 'Berawal dari sebuah panggilan', 'images/about/mengenal-figma.jpg', false, 'Ikuti Perjalanan GBI Gatsu', 'tentang/sejarah',
                'GBI Jl. Jend. Gatot Subroto, atau yang lebih dikenal sebagai GBI Gatsu, adalah gereja lokal di bawah naungan Sinode Gereja Bethel Indonesia. Berdiri sejak 4 September 1988 di Jakarta, GBI Gatsu terus bertumbuh menjadi sebuah keluarga besar gereja apostolik yang terdiri dari rayon, cabang khusus, dan gereja-gereja cabang di bawah pembinaannya.'],
            ['Dipimpin dalam Ketaatan kepada Tuhan', 'dipimpin-dalam-ketaatan-kepada-tuhan', null, 'images/about/dipimpin-figma.png', false, 'Lihat Selengkapnya', 'tentang/sejarah',
                'GBI Gatsu digembalakan oleh Pdt. Dr. Ir. Niko Njotorahardjo sebagai Gembala Jemaat, Gembala Pembina, sekaligus Bapa Rohani. Perjalanan pelayanannya dimulai dari panggilan Tuhan untuk melayani secara penuh waktu dan membawa umat-Nya masuk ke dalam hadirat Tuhan melalui doa, pujian, dan penyembahan.'],
            ['Menghidupi Panggilan Restorasi Pondok Daud', 'menghidupi-panggilan-restorasi-pondok-daud', null, 'images/about/restorasi-figma.png', false, 'Pahami Panggilan yang Kami Hidupi', 'tentang/dna-kami',
                'Sejak awal, GBI Gatsu menerima panggilan khusus untuk merestorasi Pondok Daud. Panggilan ini diwujudkan dengan membangun prajurit-prajurit Tuhan yang gagah perkasa, yang memiliki gaya hidup doa, pujian, dan penyembahan dalam unity siang dan malam, serta melakukan kehendak Bapa pada zaman ini.'],
            ['Berakar dalam Firman, Melangkah dalam Iman', 'berakar-dalam-firman-melangkah-dalam-iman', null, 'images/about/berakar.jpg', true, 'Kenali Dasar Iman Kami', 'tentang/dna-kami',
                'Sebagai bagian dari Gereja Bethel Indonesia, GBI Gatsu berpegang pada Alkitab sebagai dasar iman dan kehidupan. Setiap pengajaran, pelayanan, dan langkah gereja diarahkan untuk membawa umat semakin mengenal Kristus, hidup dalam kuasa Roh Kudus, bertumbuh dalam kebenaran firman Tuhan, dan mengerjakan Amanat Agung.'],
        ];

        foreach ($blok as $i => [$name, $slug, $subtitle, $background, $tint, $button, $link, $excerpt]) {
            $id = DB::table('about_sections')->insertGetId([
                'name' => $name, 'slug' => $slug, 'subtitle' => $subtitle, 'excerpt' => $excerpt, 'description' => null,
                'background' => $background, 'tint' => $tint, 'button_text' => $button, 'link' => $link,
                'sort_order' => $i + 1, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);

            DB::table('about_section_translations')->insert([
                'about_section_id' => $id, 'locale' => 'en',
                'name' => $en[$name] ?? null,
                'subtitle' => $subtitle ? ($en[$subtitle] ?? null) : null,
                'excerpt' => $en[$excerpt] ?? null,
                'description' => null,
                'button_text' => $en[$button] ?? null,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
};
