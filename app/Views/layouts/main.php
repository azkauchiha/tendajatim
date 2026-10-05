<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> · TendaJatim</title>
    <link rel="stylesheet" href="<?= base_url('assets/app.css?v=3') ?>">
</head>
<body>
<?php if (session()->has('user_id')): ?>
<aside class="sidebar" id="app-sidebar">
    <?php $isStaff = session('role') === 'staff'; ?>
    <a class="brand" href="<?= site_url($isStaff ? 'my-attendance' : 'dashboard') ?>"><span class="brand-mark">T</span><span>Tenda<span class="brand-light">Jatim</span><small><?= $isStaff ? 'ABSENSI STAFF' : 'OPERASIONAL' ?></small></span></a>
    <button class="sidebar-close" type="button" aria-label="Tutup menu navigasi">×</button>
    <div class="nav-label">MENU UTAMA</div>
    <nav>
        <?php if ($isStaff): ?>
            <a href="<?= site_url('my-attendance') ?>" class="<?= str_starts_with(uri_string(), 'my-attendance') ? 'active' : '' ?>"><span>◷</span> Absensi saya</a>
        <?php else: ?>
            <a href="<?= site_url('dashboard') ?>" class="<?= uri_string() === 'dashboard' || uri_string() === '' ? 'active' : '' ?>"><span>▦</span> Ringkasan</a>
            <a href="<?= site_url('bookings') ?>" class="<?= str_starts_with(uri_string(), 'bookings') ? 'active' : '' ?>"><span>▣</span> Pesanan tenda</a>
            <a href="<?= site_url('finance') ?>" class="<?= str_starts_with(uri_string(), 'finance') ? 'active' : '' ?>"><span>◈</span> Buku kas</a>
            <a href="<?= site_url('employees') ?>" class="<?= str_starts_with(uri_string(), 'employees') ? 'active' : '' ?>"><span>♙</span> Data pegawai</a>
            <a href="<?= site_url('attendance') ?>" class="<?= str_starts_with(uri_string(), 'attendance') ? 'active' : '' ?>"><span>◷</span> Rekap absensi</a>
            <?php if (session('role') === 'super_admin'): ?><a href="<?= site_url('users') ?>" class="<?= str_starts_with(uri_string(), 'users') ? 'active' : '' ?>"><span>⚙</span> Pengguna</a><?php endif ?>
        <?php endif ?>
    </nav>
    <div class="sidebar-bottom"><div class="avatar"><?= esc(strtoupper(substr((string) session('name'), 0, 1))) ?></div><div class="identity"><strong><?= esc(session('name')) ?></strong><small><?= $isStaff ? 'Staff' : (session('role') === 'super_admin' ? 'Super Admin' : 'Admin') ?></small></div><form action="<?= site_url('logout') ?>" method="post"><?= csrf_field() ?><button class="logout" title="Keluar" aria-label="Keluar">↗</button></form></div>
</aside>
<button class="sidebar-backdrop" type="button" aria-label="Tutup menu navigasi" tabindex="-1"></button>
<?php endif ?>
<main class="<?= session()->has('user_id') ? 'main' : 'main main-auth' ?>">
    <?php if (session()->has('user_id')): ?>
    <header class="topbar"><div class="topbar-heading"><button class="sidebar-toggle" type="button" aria-expanded="true" aria-controls="app-sidebar" aria-label="Sembunyikan menu navigasi"><span></span><span></span><span></span></button><div><span class="eyebrow">TENDAJATIM / <?= esc(strtoupper($title)) ?></span><h1><?= esc($title) ?></h1></div></div><div class="today"><?= esc(date('d/m/Y')) ?></div></header>
    <?php endif ?>
    <section class="content">
        <?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-error"><?= esc($error) ?></div><?php endif ?>
        <?php if ($message = session()->getFlashdata('message')): ?><div class="alert alert-success"><?= esc($message) ?></div><?php endif ?>
        <?= $body ?>
    </section>
    <footer class="footer">TendaJatim <span>•</span> Sistem operasional & pembukuan</footer>
</main>
<script src="<?= base_url('assets/app.js?v=2') ?>" defer></script>
</body>
</html>
