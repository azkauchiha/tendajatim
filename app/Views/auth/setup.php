<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Pengaturan awal · TendaJatim</title><link rel="stylesheet" href="<?= base_url('assets/app.css') ?>"></head>
<body><main class="setup-page"><div class="setup-head"><a class="brand" href="#"><span class="brand-mark">T</span><span>Tenda<span class="brand-light">Jatim</span><small>OPERASIONAL</small></span></a><p class="eyebrow">PENGATURAN PERTAMA</p><h1>Buat akun pengelola</h1><p class="muted">Buat akun Super Admin dan Admin. Kata sandi minimal 12 karakter. Halaman ini akan tertutup setelah kedua akun dibuat.</p></div>
<?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-error"><?= esc($error) ?></div><?php endif ?>
<?php $errors = session()->getFlashdata('errors') ?? []; if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach ?></div><?php endif ?>
<form action="<?= site_url('setup') ?>" method="post" class="setup-card"><?= csrf_field() ?>
    <div class="setup-columns">
        <div><h2>Super Admin</h2><p class="muted">Akses penuh, termasuk pengelolaan akun Admin.</p><label>Nama lengkap<input name="super_name" value="<?= esc(old('super_name')) ?>" required></label><label>Nama pengguna<input name="super_username" value="<?= esc(old('super_username')) ?>" minlength="4" required></label><label>Kata sandi<input type="password" name="super_password" minlength="12" required></label></div>
        <div><h2>Admin</h2><p class="muted">Akses pesanan, pembukuan, pegawai, dan absensi.</p><label>Nama lengkap<input name="admin_name" value="<?= esc(old('admin_name')) ?>" required></label><label>Nama pengguna<input name="admin_username" value="<?= esc(old('admin_username')) ?>" minlength="4" required></label><label>Kata sandi<input type="password" name="admin_password" minlength="12" required></label></div>
    </div><button class="btn btn-primary">Buat kedua akun <span>→</span></button>
</form></main></body></html>
