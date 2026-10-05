<div class="auth-wrap">
    <div class="auth-card">
        <a class="brand auth-brand" href="<?= site_url('/') ?>"><span class="brand-mark">T</span><span>Tenda<span class="brand-light">Jatim</span><small>OPERASIONAL</small></span></a>
        <p class="eyebrow">AKSES AMAN</p>
        <h1>Selamat datang</h1>
        <p class="muted">Masuk sebagai <?= $role === 'super_admin' ? 'Super Admin' : ($role === 'staff' ? 'Staff' : 'Admin') ?><?= $role === 'staff' ? ' untuk mencatat absensi.' : ' untuk mengelola operasional.' ?></p>
        <form action="<?= site_url('login/' . $slug) ?>" method="post" class="form-stack">
            <?= csrf_field() ?>
            <label>Nama pengguna<input name="username" value="<?= esc(old('username')) ?>" autocomplete="username" required autofocus></label>
            <label>Kata sandi<input name="password" type="password" autocomplete="current-password" required></label>
            <button class="btn btn-primary btn-full">Masuk sebagai <?= $role === 'super_admin' ? 'Super Admin' : ($role === 'staff' ? 'Staff' : 'Admin') ?> <span>→</span></button>
        </form>
        <div class="auth-switch">
            <?php if ($slug === 'admin'): ?>Masuk sebagai <a href="<?= site_url('login/super-admin') ?>">Super Admin</a> · <a href="<?= site_url('login/staff') ?>">Staff</a>
            <?php elseif ($slug === 'super-admin'): ?>Masuk sebagai <a href="<?= site_url('login/admin') ?>">Admin</a> · <a href="<?= site_url('login/staff') ?>">Staff</a>
            <?php else: ?>Masuk sebagai <a href="<?= site_url('login/admin') ?>">Admin</a><?php endif ?>
        </div>
        <p class="secure-note">Akun awal belum dibuat? <a href="<?= site_url('setup') ?>">Mulai pengaturan</a></p>
    </div>
</div>
