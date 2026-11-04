{{--
    Loading screen (warna & tulisan sesuai Figma "Loading Screen").
    Animasi: tulisan diam, lalu bergeser ke kiri; setelah itu lingkaran slate mengecil ke tengah dan membuka halaman.
    Hanya muncul sekali per sesi; skrip kecil di bawah langsung menghapusnya untuk kunjungan berikutnya
    supaya tidak sempat berkedip. Sisa animasinya di public/js/frontend.js.
--}}
<div class="site-loader" data-loader aria-hidden="true">
    <span class="site-loader__circle"></span>
    <span class="site-loader__mask"><span class="site-loader__text">GBI Gatsu</span></span>
</div>
<script>
    try { if (sessionStorage.getItem('gatsu-loaded') === '1') document.querySelector('[data-loader]').remove(); } catch (e) {}
</script>
