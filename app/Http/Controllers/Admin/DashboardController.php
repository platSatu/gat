<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\StatistikDashboard;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Beranda admin (/dashboard/admin): ringkasan konten, grafik pengunjung, halaman & artikel terpopuler. */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('backend.dashboard', [
            'stat' => StatistikDashboard::ambil($request->boolean('segarkan')),
        ]);
    }
}
