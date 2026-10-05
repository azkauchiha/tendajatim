<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="form-panel"><div class="panel-heading"><div><h2><?= $booking ? 'Informasi pesanan' : 'Detail pesanan baru' ?></h2><p class="muted">Isian bertanda * wajib dilengkapi.</p></div><a class="text-link" href="<?= site_url('bookings') ?>">← Kembali</a></div>
<?php if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach ?></div><?php endif ?>
<form method="post" action="<?= $booking ? site_url('bookings/' . $booking['id']) : site_url('bookings') ?>" class="form-grid"><?= csrf_field() ?>
    <label>Nama pelanggan *<input name="customer_name" value="<?= esc(old('customer_name', $booking['customer_name'] ?? '')) ?>" required maxlength="120"></label>
    <label>Nomor telepon<input name="phone" value="<?= esc(old('phone', $booking['phone'] ?? '')) ?>" maxlength="30"></label>
    <label>Tanggal acara *<input type="date" name="event_date" value="<?= esc(old('event_date', $booking['event_date'] ?? date('Y-m-d'))) ?>" required></label>
    <label>Lokasi acara *<input name="venue" value="<?= esc(old('venue', $booking['venue'] ?? '')) ?>" required maxlength="180"></label>
    <label>Total pesanan (Rp) *<input type="number" min="0" step="1000" name="total_amount" value="<?= esc(old('total_amount', $booking['total_amount'] ?? '')) ?>" required></label>
    <?php if ($booking): ?><label>Uang diterima<input value="Rp <?= number_format((float) $booking['paid_amount'], 0, ',', '.') ?>" disabled><small>Pembayaran dicatat di buku kas agar saldo tetap akurat.</small></label><?php else: ?><label>Uang muka diterima (Rp)<input type="number" min="0" step="1000" name="paid_amount" value="<?= esc(old('paid_amount', '0')) ?>" required><small>Uang muka otomatis menjadi kas masuk.</small></label><?php endif ?>
    <label>Status<select name="status"><?php foreach ($statuses as $status): ?><option value="<?= esc($status) ?>" <?= old('status', $booking['status'] ?? 'baru') === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option><?php endforeach ?></select></label>
    <label class="full-width">Catatan<textarea name="notes" rows="3"><?= esc(old('notes', $booking['notes'] ?? '')) ?></textarea></label>
    <div class="form-actions"><button class="btn btn-primary">Simpan pesanan</button><a class="btn btn-plain" href="<?= site_url('bookings') ?>">Batal</a></div>
</form></div>
