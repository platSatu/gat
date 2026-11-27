<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bahasa;
use App\Models\Pengaturan;
use App\Support\Situs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Settings → Situs & SEO: judul & deskripsi website (per bahasa), gambar share, favicon,
 * Google Tag Manager / Analytics / Search Console, Bing, Meta Pixel, link media sosial, noindex, kode tambahan.
 * Disimpan di tabel `pengaturan`; dibaca halaman publik lewat App\Support\Situs.
 */
class SitusController extends Controller
{
    private const FOLDER = 'uploads/situs';

    /** Pengaturan teks biasa (selain yang per bahasa & file). */
    private const TEKS = [
        'situs_nama', 'seo_kata_kunci', 'twitter_akun',
        'gtm_id', 'ga4_id', 'gsc_kode', 'bing_kode', 'meta_pixel_id',
        'sosial_facebook', 'sosial_instagram', 'sosial_youtube', 'sosial_tiktok', 'sosial_whatsapp',
        'kode_head',
    ];

    public function index(): View
    {
        $siap = Schema::hasTable('pengaturan');
        $bahasa = Bahasa::daftar();

        $nilai = [];
        if ($siap) {
            foreach ([...self::TEKS, 'og_gambar', 'favicon', 'noindex'] as $kunci) {
                $nilai[$kunci] = Pengaturan::ambil($kunci);
            }
            foreach ($bahasa as $b) {
                $nilai['seo_judul_'.$b['kode']] = Pengaturan::ambil('seo_judul_'.$b['kode']);
                $nilai['seo_deskripsi_'.$b['kode']] = Pengaturan::ambil('seo_deskripsi_'.$b['kode']);
            }
        }

        return view('backend.settings.situs.index', [
            'siap' => $siap,
            'bahasa' => $bahasa,
            'nilai' => $nilai,
            'domainUtama' => (string) config('gatsu.statistik.domain', 'gbigatsu.id'),
            'diDomainUtama' => Situs::domainUtama(),
        ]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        abort_unless(Schema::hasTable('pengaturan'), 503, 'Tabel pengaturan belum dibuat. Jalankan migration hero dulu.');

        // Rapikan isian sebelum dicek: ID diubah ke huruf besar, kode verifikasi boleh ditempel lengkap (<meta ... content="...">).
        $request->merge([
            'gtm_id' => strtoupper(trim((string) $request->input('gtm_id'))),
            'ga4_id' => strtoupper(trim((string) $request->input('ga4_id'))),
            'gsc_kode' => $this->ambilContent($request->input('gsc_kode')),
            'bing_kode' => $this->ambilContent($request->input('bing_kode')),
            'twitter_akun' => ltrim(trim((string) $request->input('twitter_akun')), '@'),
        ]);

        $aturan = [
            'situs_nama' => ['nullable', 'string', 'max:100'],
            'seo_kata_kunci' => ['nullable', 'string', 'max:255'],
            'twitter_akun' => ['nullable', 'regex:/^[A-Za-z0-9_]{1,30}$/'],
            'og_gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,jpg,jpeg,webp', 'max:1024'],
            'gtm_id' => ['nullable', 'regex:/^GTM-[A-Z0-9]{4,12}$/'],
            'ga4_id' => ['nullable', 'regex:/^G-[A-Z0-9]{4,15}$/'],
            'gsc_kode' => ['nullable', 'regex:/^[A-Za-z0-9_\-]{10,100}$/'],
            'bing_kode' => ['nullable', 'regex:/^[A-Za-z0-9_\-]{10,100}$/'],
            'meta_pixel_id' => ['nullable', 'regex:/^\d{8,20}$/'],
            'sosial_facebook' => ['nullable', 'url:http,https', 'max:255'],
            'sosial_instagram' => ['nullable', 'url:http,https', 'max:255'],
            'sosial_youtube' => ['nullable', 'url:http,https', 'max:255'],
            'sosial_tiktok' => ['nullable', 'url:http,https', 'max:255'],
            'sosial_whatsapp' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/\S+|\+?[\d\s\-]{8,20})$/'],
            'noindex' => ['nullable', 'boolean'],
            'kode_head' => ['nullable', 'string', 'max:10000'],
        ];
        foreach (Bahasa::daftar() as $b) {
            $aturan['seo_judul_'.$b['kode']] = ['nullable', 'string', 'max:120'];
            $aturan['seo_deskripsi_'.$b['kode']] = ['nullable', 'string', 'max:320'];
        }

        $request->validate($aturan, [
            'gtm_id.regex' => 'ID Google Tag Manager berbentuk GTM-XXXXXXX.',
            'ga4_id.regex' => 'Measurement ID Google Analytics berbentuk G-XXXXXXXXXX.',
            'gsc_kode.regex' => 'Kode verifikasi Search Console tidak dikenali. Tempel kode atau tag <meta> lengkapnya.',
            'bing_kode.regex' => 'Kode verifikasi Bing tidak dikenali. Tempel kode atau tag <meta> lengkapnya.',
            'meta_pixel_id.regex' => 'Pixel ID berupa angka saja (8–20 digit).',
            'twitter_akun.regex' => 'Nama akun X/Twitter hanya huruf, angka, dan garis bawah.',
            'sosial_whatsapp.regex' => 'Isi nomor WhatsApp (mis. 0812xxxx atau +62812xxxx) atau link https://wa.me/...',
        ], [
            'situs_nama' => 'Nama website', 'og_gambar' => 'Gambar share', 'favicon' => 'Favicon',
            'sosial_facebook' => 'Facebook', 'sosial_instagram' => 'Instagram', 'sosial_youtube' => 'YouTube', 'sosial_tiktok' => 'TikTok',
        ]);

        $data = [];
        foreach (self::TEKS as $kunci) {
            $data[$kunci] = $request->input($kunci);
        }
        foreach (Bahasa::daftar() as $b) {
            $data['seo_judul_'.$b['kode']] = $request->input('seo_judul_'.$b['kode']);
            $data['seo_deskripsi_'.$b['kode']] = $request->input('seo_deskripsi_'.$b['kode']);
        }
        $data['noindex'] = $request->boolean('noindex') ? '1' : null;

        foreach (['og_gambar', 'favicon'] as $kunci) {
            $lama = Pengaturan::ambil($kunci);
            if ($request->hasFile($kunci)) {
                $file = $request->file($kunci);
                $nama = Str::slug($kunci).'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(4)).'.'.strtolower($file->getClientOriginalExtension());
                $file->move(public_path(self::FOLDER), $nama);
                $data[$kunci] = self::FOLDER.'/'.$nama;
                $this->hapusFileLokal($lama);
            } elseif ($request->boolean('hapus_'.$kunci)) {
                $data[$kunci] = null;
                $this->hapusFileLokal($lama);
            }
        }

        Pengaturan::simpanBanyak($data);

        return redirect()->route('admin.settings.situs.index')->with('success', 'Pengaturan situs & SEO berhasil disimpan.');
    }

    /** "<meta name=... content="abc">" → "abc"; kode biasa dikembalikan apa adanya. */
    private function ambilContent(?string $isi): ?string
    {
        $isi = trim((string) $isi);

        return preg_match('/content\s*=\s*["\']([^"\']+)["\']/i', $isi, $m) ? trim($m[1]) : ($isi ?: null);
    }

    /** Hapus file hanya kalau ada di public/uploads/situs. */
    private function hapusFileLokal(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        $file = realpath(public_path(ltrim($path, '/'))) ?: '';
        $folder = realpath(public_path(self::FOLDER)) ?: public_path(self::FOLDER);
        if ($file !== '' && str_starts_with($file, $folder.DIRECTORY_SEPARATOR) && File::isFile($file)) {
            File::delete($file);
        }
    }
}
