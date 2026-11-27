<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahasa;
use App\Models\HeroSlide;
use App\Models\Pengaturan;
use App\Support\Situs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Hero beranda: kartu sambutan (tabel hero_slides + terjemahan), video/gambar latar, dan layar pembuka
 * (loading screen) — dua yang terakhir disimpan di tabel pengaturan.
 * File latar baru disimpan di public/uploads/hero/; yang dihapus otomatis hanya file di folder itu
 * (file bawaan public/videos/hero.mp4 & public/images/home/hero.jpg tidak disentuh).
 */
class HeroController extends Controller
{
    private const FOLDER = 'uploads/hero';

    public function index(): View
    {
        $siap = Schema::hasTable('hero_slides');

        return view('backend.hero.index', [
            'siap' => $siap,
            'slides' => $siap ? HeroSlide::query()->urut()->withCount('translations as punya_en')->get() : collect(),
            'video' => $siap ? Pengaturan::ambil('hero_video') : null,
            'gambar' => $siap ? Pengaturan::ambil('hero_gambar') : null,
            'pembuka' => $this->isianPembuka($siap),
        ]);
    }

    public function create(): View
    {
        $this->pastikanTabel();

        return view('backend.hero.form', ['slide' => new HeroSlide([
            'status' => true,
            'sort_order' => (int) HeroSlide::max('sort_order') + 1,
        ])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->pastikanTabel();
        $slide = HeroSlide::create($this->validasi($request));
        $slide->simpanSemuaTerjemahan((array) $request->input('tr', []));

        return redirect()->route('admin.hero.index')->with('success', 'Kartu hero berhasil dibuat.');
    }

    public function edit(HeroSlide $hero): View
    {
        return view('backend.hero.form', ['slide' => $hero]);
    }

    public function update(Request $request, HeroSlide $hero): RedirectResponse
    {
        $hero->update($this->validasi($request));
        $hero->simpanSemuaTerjemahan((array) $request->input('tr', []));

        return redirect()->route('admin.hero.index')->with('success', 'Kartu hero berhasil diperbarui.');
    }

    public function destroy(HeroSlide $hero): RedirectResponse
    {
        $hero->delete();

        return redirect()->route('admin.hero.index')->with('success', 'Kartu hero berhasil dihapus.');
    }

    /** Geser urutan satu langkah (tukar dengan kartu di sebelahnya). */
    public function move(HeroSlide $hero, string $arah): RedirectResponse
    {
        $urutan = HeroSlide::query()->urut()->get(['id', 'sort_order'])->values();
        $i = $urutan->search(fn ($s) => $s->id === $hero->id);
        $j = $arah === 'up' ? $i - 1 : $i + 1;

        if ($i !== false && isset($urutan[$j])) {
            $ids = $urutan->pluck('id')->all();
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $n => $id) {
                HeroSlide::whereKey($id)->update(['sort_order' => $n + 1]);
            }
        }

        return redirect()->route('admin.hero.index');
    }

    /** Simpan video / gambar latar hero. */
    public function latar(Request $request): RedirectResponse
    {
        $this->pastikanTabel();

        $request->validate([
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm', 'max:102400'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hapus_video' => ['nullable', 'boolean'],
            'hapus_gambar' => ['nullable', 'boolean'],
        ], [
            'video.max' => 'Video maksimal 100 MB. Perkecil dulu (mis. 1080p, ±20 detik).',
        ], ['video' => 'Video', 'gambar' => 'Gambar']);

        foreach (['video' => 'hero_video', 'gambar' => 'hero_gambar'] as $input => $kunci) {
            $lama = Pengaturan::ambil($kunci);

            if ($request->hasFile($input)) {
                $file = $request->file($input);
                $nama = 'hero-'.$input.'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(4)).'.'.strtolower($file->getClientOriginalExtension());
                $file->move(public_path(self::FOLDER), $nama);
                Pengaturan::simpan($kunci, self::FOLDER.'/'.$nama);
                $this->hapusFileLokal($lama);
            } elseif ($request->boolean('hapus_'.$input)) {
                Pengaturan::simpan($kunci, null);
                $this->hapusFileLokal($lama);
            }
        }

        return redirect()->route('admin.hero.index')->with('success', 'Latar hero berhasil disimpan.');
    }

    /** Simpan pengaturan layar pembuka (loading screen). Isian kosong = nilai bawaan seperti semula. */
    public function pembuka(Request $request): RedirectResponse
    {
        $this->pastikanTabel();

        $aturan = [
            'pembuka_aktif' => ['nullable', 'boolean'],
            'pembuka_warna_latar' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'pembuka_warna_teks' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'pembuka_diam' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'pembuka_muncul' => ['required', 'in:'.implode(',', array_keys(Situs::PEMBUKA_MUNCUL))],
            'pembuka_logo' => ['nullable', 'image', 'mimes:png,webp,jpg,jpeg', 'max:2048'],
            'hapus_pembuka_logo' => ['nullable', 'boolean'],
        ];
        foreach (Bahasa::daftar() as $b) {
            $aturan['pembuka_teks_'.$b['kode']] = ['nullable', 'string', 'max:40'];
        }

        $request->validate($aturan, [
            'pembuka_warna_latar.regex' => 'Warna latar harus kode warna seperti #466883.',
            'pembuka_warna_teks.regex' => 'Warna teks harus kode warna seperti #ece3d4.',
        ], ['pembuka_diam' => 'Lama diam', 'pembuka_logo' => 'Logo']);

        // Warna yang sama dengan bawaan tidak disimpan, supaya tetap ikut warna tema kalau tema diubah.
        $warna = fn ($k, $bawaan) => strtolower((string) $request->input($k)) === $bawaan ? null : $request->input($k);
        $diam = $request->input('pembuka_diam');

        $data = [
            'pembuka_aktif' => $request->boolean('pembuka_aktif') ? null : '0',
            'pembuka_warna_latar' => $warna('pembuka_warna_latar', Situs::PEMBUKA_BAWAAN['latar']),
            'pembuka_warna_teks' => $warna('pembuka_warna_teks', Situs::PEMBUKA_BAWAAN['warna_teks']),
            'pembuka_diam' => is_numeric($diam) && (float) $diam !== (float) Situs::PEMBUKA_BAWAAN['diam'] ? (string) (float) $diam : null,
            'pembuka_muncul' => $request->input('pembuka_muncul') === Situs::PEMBUKA_BAWAAN['muncul'] ? null : $request->input('pembuka_muncul'),
        ];
        foreach (Bahasa::daftar() as $b) {
            $teks = trim((string) $request->input('pembuka_teks_'.$b['kode']));
            $data['pembuka_teks_'.$b['kode']] = $teks === Situs::PEMBUKA_BAWAAN['teks'] ? null : $teks;
        }

        $lama = Pengaturan::ambil('pembuka_logo');
        if ($request->hasFile('pembuka_logo')) {
            $file = $request->file('pembuka_logo');
            $nama = 'pembuka-logo-'.now()->format('YmdHis').'-'.Str::lower(Str::random(4)).'.'.strtolower($file->getClientOriginalExtension());
            $file->move(public_path(self::FOLDER), $nama);
            $data['pembuka_logo'] = self::FOLDER.'/'.$nama;
            $this->hapusFileLokal($lama);
        } elseif ($request->boolean('hapus_pembuka_logo')) {
            $data['pembuka_logo'] = null;
            $this->hapusFileLokal($lama);
        }

        Pengaturan::simpanBanyak($data);

        return redirect()->route('admin.hero.index')->with('success', 'Layar pembuka berhasil disimpan.');
    }

    /** Nilai untuk form layar pembuka (kosong diisi bawaan supaya terlihat apa yang sedang dipakai). */
    private function isianPembuka(bool $siap): array
    {
        $b = Situs::PEMBUKA_BAWAAN;
        $hasil = [
            'aktif' => ! $siap || Pengaturan::ambil('pembuka_aktif') !== '0',
            'latar' => ($siap ? Pengaturan::ambil('pembuka_warna_latar') : null) ?: $b['latar'],
            'warna_teks' => ($siap ? Pengaturan::ambil('pembuka_warna_teks') : null) ?: $b['warna_teks'],
            'diam' => ($siap ? Pengaturan::ambil('pembuka_diam') : null) ?: $b['diam'],
            'muncul' => ($siap ? Pengaturan::ambil('pembuka_muncul') : null) ?: $b['muncul'],
            'logo' => $siap ? Pengaturan::ambil('pembuka_logo') : null,
            'teks' => [],
        ];
        foreach (Bahasa::daftar() as $bhs) {
            $hasil['teks'][$bhs['kode']] = $siap ? Pengaturan::ambil('pembuka_teks_'.$bhs['kode']) : null;
        }

        return $hasil;
    }

    private function pastikanTabel(): void
    {
        abort_unless(Schema::hasTable('hero_slides'), 503, 'Tabel hero belum dibuat. Jalankan migration-nya dulu.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'sub_label' => ['nullable', 'string', 'max:255'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:0,1'],
            ...HeroSlide::aturanTerjemahan(['label' => 255, 'title' => 255, 'sub_label' => 255, 'sub_title' => 255, 'button_text' => 255]),
        ], [], ['title' => 'Judul', 'sort_order' => 'Urutan', 'tr.*.title' => 'Judul (terjemahan)']);

        unset($data['tr']);

        return $data;
    }

    /** Hapus file hanya kalau ada di public/uploads/hero. */
    private function hapusFileLokal(?string $path): void
    {
        if (blank($path) || preg_match('#^https?://#i', $path)) {
            return;
        }

        $file = realpath(public_path(ltrim($path, '/'))) ?: '';
        $folder = realpath(public_path(self::FOLDER)) ?: public_path(self::FOLDER);
        if ($file !== '' && str_starts_with($file, $folder.DIRECTORY_SEPARATOR) && File::isFile($file)) {
            File::delete($file);
        }
    }
}
