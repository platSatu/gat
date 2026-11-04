{{-- Halaman "Under Maintenance" (php artisan down). Dibuat mandiri (tanpa Vite / database) supaya selalu bisa tampil. --}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Sedang Dalam Perbaikan · GBI Gatot Subroto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --biru: #1e3c72; --biru-muda: #2a5298; --teks: #4b5563; --latar: #f5f7fb; }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif; color: var(--teks); background: var(--latar);
            display: flex; align-items: center; justify-content: center; padding: 24px;
            background-image: radial-gradient(circle at 15% 20%, rgba(42, 82, 152, .10), transparent 45%),
                              radial-gradient(circle at 85% 80%, rgba(30, 60, 114, .10), transparent 45%);
        }
        .kartu { width: 100%; max-width: 560px; background: #fff; border-radius: 24px; padding: 44px 36px; text-align: center;
            box-shadow: 0 20px 60px rgba(30, 60, 114, .12); }
        .logo { width: 190px; max-width: 70%; height: auto; margin-bottom: 28px; }
        .ikon { width: 64px; height: 64px; margin: 0 auto 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--biru), var(--biru-muda)); }
        .ikon svg { width: 30px; height: 30px; fill: #fff; animation: putar 6s linear infinite; }
        @keyframes putar { to { transform: rotate(360deg); } }
        h1 { color: var(--biru); font-size: 1.6rem; line-height: 1.3; margin: 0 0 10px; }
        p { margin: 0 0 8px; line-height: 1.7; }
        .en { font-size: .9rem; color: #8a93a3; margin-top: 18px; }
        .tautan { display: inline-block; margin-top: 24px; padding: 11px 22px; border-radius: 12px; font-weight: 600; text-decoration: none;
            color: #fff; background: linear-gradient(135deg, var(--biru), var(--biru-muda)); }
        .tautan:hover { filter: brightness(1.08); }
        @media (max-width: 480px) { .kartu { padding: 34px 22px; } h1 { font-size: 1.35rem; } }
        @media (prefers-reduced-motion: reduce) { .ikon svg { animation: none; } }
    </style>
</head>
<body>
    <main class="kartu">
        <img class="logo" src="{{ asset('images/logo/Logo-GBIGATSU.svg') }}" alt="GBI Gatot Subroto">
        <div class="ikon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M19.4 13a7.5 7.5 0 0 0 0-2l2.1-1.6-2-3.5-2.5 1a7.4 7.4 0 0 0-1.7-1L15 3.3h-4l-.4 2.6a7.4 7.4 0 0 0-1.7 1l-2.5-1-2 3.5L6.6 11a7.5 7.5 0 0 0 0 2l-2.1 1.6 2 3.5 2.5-1c.5.4 1.1.7 1.7 1l.4 2.6h4l.4-2.6c.6-.3 1.2-.6 1.7-1l2.5 1 2-3.5L19.4 13ZM13 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7Z" transform="translate(-1 0)"/></svg>
        </div>
        <h1>Website Sedang Dalam Perbaikan</h1>
        <p>Kami sedang menyiapkan tampilan baru website GBI Gatot Subroto agar lebih nyaman untuk Anda.</p>
        <p>Silakan kembali beberapa saat lagi. Terima kasih atas kesabaran Anda. Tuhan Yesus memberkati!</p>
        <a class="tautan" href="https://gbigatsu.id">Kunjungi gbigatsu.id</a>
        <p class="en">Our website is currently under maintenance. Please check back soon.</p>
    </main>
</body>
</html>
