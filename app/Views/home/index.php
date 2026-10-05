<div class="public-home">
    <a class="brand auth-brand" href="<?= site_url('/') ?>"><span class="brand-mark">T</span><span>Tenda<span class="brand-light">Jatim</span><small>OPERASIONAL</small></span></a>
    <p class="eyebrow">SISTEM OPERASIONAL TENDAJATIM</p>
    <div class="gallery" aria-label="Galeri Tenda Jatim" data-slideshow>
        <div class="gallery-slides" aria-live="polite">
            <img class="gallery-slide is-active" src="<?= base_url('assets/gallery/tendajt1.png') ?>" alt="Tenda Jatim - foto 1" fetchpriority="high">
            <img class="gallery-slide" src="<?= base_url('assets/gallery/tendajt2.jpeg') ?>" alt="Tenda Jatim - foto 2" loading="lazy" hidden>
            <img class="gallery-slide" src="<?= base_url('assets/gallery/tendajt3.jpeg') ?>" alt="Tenda Jatim - foto 3" loading="lazy" hidden>
            <img class="gallery-slide" src="<?= base_url('assets/gallery/tendajt4.jpeg') ?>" alt="Tenda Jatim - foto 4" loading="lazy" hidden>
            <img class="gallery-slide" src="<?= base_url('assets/gallery/tendajt5.jpeg') ?>" alt="Tenda Jatim - foto 5" loading="lazy" hidden>
            <img class="gallery-slide" src="<?= base_url('assets/gallery/tendajt6.jpeg') ?>" alt="Tenda Jatim - foto 6" loading="lazy" hidden>
        </div>
        <button class="gallery-control gallery-previous" type="button" aria-label="Foto sebelumnya">‹</button>
        <button class="gallery-control gallery-next" type="button" aria-label="Foto berikutnya">›</button>
        <div class="gallery-indicators" aria-label="Pilih foto">
            <?php for ($index = 0; $index < 6; $index++): ?>
                <button class="gallery-indicator<?= $index === 0 ? ' is-active' : '' ?>" type="button" aria-label="Tampilkan foto <?= $index + 1 ?>" aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"></button>
            <?php endfor ?>
        </div>
    </div>
    <h1>Kelola operasional dan absensi dalam satu aplikasi.</h1>
    <p class="muted">Akses pemesanan, pembukuan, data pegawai, dan pencatatan kehadiran sesuai akun Anda.</p>
    <?php if ($hasUsers): ?>
        <div class="public-home-actions">
            <a class="btn btn-primary" href="<?= site_url('login/admin') ?>">Masuk Admin <span>→</span></a>
            <a class="btn btn-plain" href="<?= site_url('login/super-admin') ?>">Masuk Super Admin</a>
            <a class="text-link" href="<?= site_url('login/staff') ?>">Masuk Staff untuk absensi</a>
        </div>
    <?php else: ?>
        <div class="public-home-actions">
            <a class="btn btn-primary" href="<?= site_url('setup') ?>">Pengaturan awal <span>→</span></a>
            <p class="muted">Buat akun Super Admin dan Admin untuk mulai menggunakan aplikasi.</p>
        </div>
    <?php endif ?>
</div>
