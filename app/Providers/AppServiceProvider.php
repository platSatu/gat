<?php

namespace App\Providers;

use App\Support\TerjemahanLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Teks __('...') dibaca dari tabel `terjemahan` (admin Settings → Terjemahan), bukan hanya lang/*.json.
        $this->app->extend('translation.loader', fn ($loader) => new TerjemahanLoader($loader));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
