<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="form-panel"><div class="panel-heading"><div><h2>Catat kehadiran</h2><p class="muted">Satu catatan per pegawai per hari. Menyimpan ulang akan memperbarui catatan.</p></div><a class="text-link" href="<?= site_url('attendance') ?>">← Kembali</a></div>
<?php if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach ?></div><?php endif ?>
<?php if (! $employees): ?><div class="empty-state">Belum ada pegawai aktif. Tambahkan pegawai terlebih dahulu di <a href="<?= site_url('employees/new') ?>">Data Pegawai</a>.</div><?php else: ?>
<form method="post" action="<?= site_url('attendance') ?>" class="form-grid"><?= csrf_field() ?>
    <label>Pegawai *<select name="employee_id" required><?php foreach ($employees as $employee): ?><option value="<?= (int) $employee['id'] ?>" <?= old('employee_id') == $employee['id'] ? 'selected' : '' ?>><?= esc($employee['name'] . ' · ' . $employee['position']) ?></option><?php endforeach ?></select></label>
    <label>Tanggal *<input type="date" name="attendance_date" value="<?= esc(old('attendance_date', date('Y-m-d'))) ?>" required></label>
    <label>Status *<select name="status" required><?php foreach ($statuses as $status): ?><option value="<?= esc($status) ?>" <?= old('status', 'hadir') === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option><?php endforeach ?></select></label>
    <label>Jam masuk<input type="time" name="check_in" value="<?= esc(old('check_in')) ?>"></label><label>Jam pulang<input type="time" name="check_out" value="<?= esc(old('check_out')) ?>"></label>
    <label class="full-width">Catatan<input name="notes" value="<?= esc(old('notes')) ?>" maxlength="255"></label>
    <div class="form-actions"><button class="btn btn-primary">Simpan absensi</button><a class="btn btn-plain" href="<?= site_url('attendance') ?>">Batal</a></div>
</form><?php endif ?></div>
