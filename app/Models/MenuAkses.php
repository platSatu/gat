<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hak akses satu menu untuk satu user + role (tabel `cekrole`, sama dengan website lama).
 * Kolom list / add / edit / delete berisi 1 (boleh) atau 0 (tidak).
 */
class MenuAkses extends Model
{
    /** Jenis hak, sesuai nama kolom di tabel cekrole. */
    public const HAK = ['list', 'add', 'edit', 'delete'];

    protected $table = 'cekrole';

    protected $fillable = ['user_id', 'role_id', 'menu_id', 'list', 'add', 'edit', 'delete'];

    protected $casts = [
        'list' => 'integer',
        'add' => 'integer',
        'edit' => 'integer',
        'delete' => 'integer',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /** ['list' => bool, 'add' => bool, 'edit' => bool, 'delete' => bool] */
    public function hak(): array
    {
        return collect(self::HAK)->mapWithKeys(fn ($h) => [$h => (int) $this->{$h} === 1])->all();
    }
}
