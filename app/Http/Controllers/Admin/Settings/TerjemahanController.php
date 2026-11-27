<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bahasa;
use App\Models\Terjemahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Settings → Terjemahan: teks tampilan website (menu, tombol, judul bagian, footer, ...).
 * Di kode ditulis __('Teks Indonesia'); di sini diisi terjemahannya per bahasa, dan boleh juga
 * mengganti teks Indonesia-nya (kolom "Indonesia") tanpa mengubah kode.
 */
class TerjemahanController extends Controller
{
    private const PER_HALAMAN = 25;

    public function index(Request $request): View
    {
        $siap = Schema::hasTable('terjemahan') && Schema::hasTable('bahasa');
        $bahasa = Bahasa::daftar();
        $lain = Bahasa::lain();
        $cari = trim((string) $request->input('cari'));
        $kodeFilter = $lain->pluck('kode')->contains($request->input('bahasa')) ? $request->input('bahasa') : null;
        $status = in_array($request->input('status'), ['belum', 'sudah'], true) ? $request->input('status') : null;

        $daftar = $siap ? Terjemahan::query()
            ->when($cari !== '', fn ($q) => $q->where(fn ($w) => $w->where('kunci', 'like', "%{$cari}%")->orWhere('isi', 'like', "%{$cari}%")))
            ->when($status && $kodeFilter, function ($q) use ($status, $kodeFilter) {
                $jalur = '$."'.$kodeFilter.'"';
                $status === 'belum'
                    ? $q->whereRaw("JSON_EXTRACT(COALESCE(NULLIF(isi, ''), '{}'), ?) IS NULL", [$jalur])
                    : $q->whereRaw("JSON_EXTRACT(COALESCE(NULLIF(isi, ''), '{}'), ?) IS NOT NULL", [$jalur]);
            })
            ->orderBy('kunci')
            ->paginate(self::PER_HALAMAN)->withQueryString() : null;

        $ringkasan = [];
        if ($siap) {
            $total = Terjemahan::count();
            foreach ($lain as $b) {
                $terisi = count(Terjemahan::peta($b['kode']));
                $ringkasan[] = ['kode' => $b['kode'], 'nama' => $b['nama'], 'terisi' => $terisi, 'total' => $total];
            }
        }

        return view('backend.settings.terjemahan.index', compact('daftar', 'bahasa', 'lain', 'siap', 'ringkasan', 'cari', 'kodeFilter', 'status'));
    }

    /** Simpan semua isian di halaman yang sedang dibuka (input isi[id][kode]). */
    public function simpan(Request $request): RedirectResponse
    {
        $request->validate([
            'isi' => ['required', 'array'],
            'isi.*' => ['array'],
            'isi.*.*' => ['nullable', 'string', 'max:5000'],
        ]);

        $kodeBoleh = Bahasa::kodeAktif();
        $diubah = 0;

        DB::transaction(function () use ($request, $kodeBoleh, &$diubah) {
            foreach ((array) $request->input('isi') as $id => $perBahasa) {
                $t = Terjemahan::find($id);
                if (! $t) {
                    continue;
                }
                $isi = (array) ($t->isi ?? []);
                foreach ((array) $perBahasa as $kode => $teks) {
                    if (in_array($kode, $kodeBoleh, true)) {
                        $isi[$kode] = $teks;
                    }
                }
                $t->isi = $isi;
                if ($t->isDirty('isi')) {
                    $t->save();
                    $diubah++;
                }
            }
        });

        return back()->with('success', $diubah ? "$diubah teks berhasil disimpan." : 'Tidak ada perubahan.');
    }

    /** Tambah teks manual (biasanya cukup lewat tombol "Pindai teks"). */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['kunci' => ['required', 'string', 'max:2000']], [], ['kunci' => 'Teks Indonesia']);

        if (Terjemahan::where('hash', md5($data['kunci']))->exists()) {
            return back()->with('error', 'Teks itu sudah ada di daftar.');
        }

        Terjemahan::create(['kunci' => $data['kunci'], 'isi' => [], 'sumber' => 'ditambah manual']);

        return redirect()->route('admin.settings.terjemahan.index', ['cari' => $data['kunci']])->with('success', 'Teks ditambahkan. Isi terjemahannya di bawah.');
    }

    public function destroy(Terjemahan $terjemahan): RedirectResponse
    {
        $terjemahan->delete();

        return back()->with('success', 'Teks dihapus dari daftar (di website akan tampil teks aslinya).');
    }

    /** Cari teks __('...') di semua tampilan website dan daftarkan yang belum ada. */
    public function pindai(): RedirectResponse
    {
        $baru = Terjemahan::pindai();

        return redirect()->route('admin.settings.terjemahan.index', $baru ? ['bahasa' => Bahasa::lain()->first()['kode'] ?? null, 'status' => 'belum'] : [])
            ->with('success', $baru ? "$baru teks baru ditemukan dan ditambahkan. Silakan isi terjemahannya." : 'Tidak ada teks baru. Semua teks sudah terdaftar.');
    }
}
