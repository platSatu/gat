<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bahasa;
use App\Models\Terjemahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Settings → Bahasa (tabel `bahasa`). Bahasa Indonesia = utama, dikunci.
 * Bahasa aktif muncul di tombol bahasa website dan sebagai tab di semua form konten admin.
 */
class BahasaController extends Controller
{
    public function index(): View
    {
        $siap = Schema::hasTable('bahasa');
        $bahasa = $siap ? Bahasa::query()->orderByDesc('utama')->orderBy('urutan')->orderBy('id')->get() : collect();

        // Berapa teks tampilan yang sudah diterjemahkan ke tiap bahasa.
        $terisi = [];
        $totalTeks = 0;
        if ($siap && Schema::hasTable('terjemahan')) {
            $totalTeks = Terjemahan::count();
            foreach ($bahasa as $b) {
                $terisi[$b->kode] = count(Terjemahan::peta($b->kode));
            }
        }

        return view('backend.settings.bahasa.index', compact('bahasa', 'siap', 'terisi', 'totalTeks'));
    }

    public function create(): View
    {
        return view('backend.settings.bahasa.form', ['bahasa' => new Bahasa([
            'aktif' => true,
            'urutan' => (int) Bahasa::max('urutan') + 1,
        ])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $bahasa = Bahasa::create($this->validasi($request, new Bahasa) + ['utama' => false]);

        return redirect()->route('admin.settings.bahasa.index')
            ->with('success', "Bahasa {$bahasa->nama} ditambahkan. Isi terjemahannya di Settings → Terjemahan dan di tiap konten.");
    }

    public function edit(Bahasa $bahasa): View
    {
        return view('backend.settings.bahasa.form', compact('bahasa'));
    }

    public function update(Request $request, Bahasa $bahasa): RedirectResponse
    {
        $data = $this->validasi($request, $bahasa);

        if ($bahasa->utama) {
            // Bahasa utama: kode & status tidak bisa diubah, hanya nama tampilan.
            $data = ['nama' => $data['nama']];
        }

        $bahasa->update($data);

        return redirect()->route('admin.settings.bahasa.index')->with('success', "Bahasa {$bahasa->nama} berhasil diperbarui.");
    }

    public function destroy(Bahasa $bahasa): RedirectResponse
    {
        if ($bahasa->utama) {
            return back()->with('error', 'Bahasa utama tidak bisa dihapus.');
        }

        // Isi terjemahannya tidak ikut dihapus (bisa dipakai lagi kalau bahasa ditambahkan kembali). Matikan saja kalau ragu.
        $bahasa->delete();

        return redirect()->route('admin.settings.bahasa.index')->with('success', "Bahasa {$bahasa->nama} dihapus dari daftar.");
    }

    private function validasi(Request $request, Bahasa $bahasa): array
    {
        $request->merge(['kode' => strtolower(trim((string) $request->input('kode')))]);

        $data = $request->validate([
            'kode' => [$bahasa->utama ? 'nullable' : 'required', 'string', 'max:5', 'regex:/^[a-z]{2,3}(-[a-z0-9]{2})?$/', Rule::unique('bahasa', 'kode')->ignore($bahasa->id)],
            'nama' => ['required', 'string', 'max:60'],
            'urutan' => ['required', 'integer', 'min:0'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'kode.regex' => 'Kode bahasa 2–3 huruf kecil, mis. en, zh, ja, atau zh-tw.',
        ], ['kode' => 'Kode', 'nama' => 'Nama', 'urutan' => 'Urutan']);

        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }
}
