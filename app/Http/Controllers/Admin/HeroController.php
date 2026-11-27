<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Hero beranda: kartu sambutan (tabel hero_slides + terjemahan) dan video/gambar latar (tabel pengaturan).
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
