<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="form-panel"><div class="panel-heading"><div><h2>Akun Admin baru</h2><p class="muted">Kata sandi minimal 12 karakter. Peran Admin tidak dapat membuka pengelolaan akun.</p></div><a class="text-link" href="<?= site_url('users') ?>">← Kembali</a></div>
<?php if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach ?></div><?php endif ?>
<form method="post" action="<?= site_url('users') ?>" class="form-grid"><?= csrf_field() ?>
    <label>Nama lengkap *<input name="name" value="<?= esc(old('name')) ?>" required maxlength="120"></label><label>Nama pengguna *<input name="username" value="<?= esc(old('username')) ?>" required minlength="4" maxlength="60"></label><label>Kata sandi *<input type="password" name="password" required minlength="12" maxlength="128"></label>
    <div class="form-actions"><button class="btn btn-primary">Buat akun Admin</button><a class="btn btn-plain" href="<?= site_url('users') ?>">Batal</a></div>
</form></div>
