<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="form-panel"><div class="panel-heading"><div><h2>Transaksi baru</h2><p class="muted">Transaksi terkait uang muka pesanan dibuat otomatis.</p></div><a class="text-link" href="<?= site_url('finance') ?>">← Kembali</a></div>
<?php if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach ?></div><?php endif ?>
<form method="post" action="<?= site_url('finance') ?>" class="form-grid"><?= csrf_field() ?>
    <label>Tanggal *<input type="date" name="entry_date" value="<?= esc(old('entry_date', date('Y-m-d'))) ?>" required></label>
    <label>Jenis transaksi *<select name="type"><option value="masuk" <?= old('type') === 'masuk' ? 'selected' : '' ?>>Pemasukan</option><option value="keluar" <?= old('type') === 'keluar' ? 'selected' : '' ?>>Pengeluaran</option></select></label>
    <label>Kategori *<input name="category" value="<?= esc(old('category')) ?>" placeholder="Contoh: Operasional, transport" required maxlength="80"></label>
    <label>Nominal (Rp) *<input type="number" min="1" step="1" name="amount" value="<?= esc(old('amount')) ?>" required></label>
    <label class="full-width">Keterangan *<input name="description" value="<?= esc(old('description')) ?>" required maxlength="255"></label>
    <label class="full-width">Tautkan pembayaran ke pesanan (opsional)<select name="booking_id"><option value="">Transaksi umum</option><?php foreach ($bookings as $booking): ?><?php if ($booking['status'] !== 'dibatalkan' && (float) $booking['paid_amount'] < (float) $booking['total_amount']): ?><option value="<?= (int) $booking['id'] ?>" <?= old('booking_id') == $booking['id'] ? 'selected' : '' ?>><?= esc($booking['booking_code'] . ' · ' . $booking['customer_name'] . ' · sisa Rp ' . number_format((float) $booking['total_amount'] - (float) $booking['paid_amount'], 0, ',', '.')) ?></option><?php endif ?><?php endforeach ?></select><small>Pembayaran tertaut wajib berupa pemasukan dan memperbarui jumlah yang telah dibayar.</small></label>
    <div class="form-actions"><button class="btn btn-primary">Simpan transaksi</button><a class="btn btn-plain" href="<?= site_url('finance') ?>">Batal</a></div>
</form></div>
