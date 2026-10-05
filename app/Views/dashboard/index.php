<div class="welcome-row"><div><p class="muted">Ringkasan kegiatan usaha hari ini.</p></div><a class="btn btn-primary" href="<?= site_url('bookings/new') ?>">＋ Pesanan baru</a></div>
<div class="stats-grid">
    <article class="stat-card"><span class="stat-icon violet">▣</span><span class="stat-label">PESANAN AKTIF</span><strong><?= number_format((int) $bookingCount) ?></strong><small>Pesanan belum dibatalkan</small></article>
    <article class="stat-card"><span class="stat-icon green">↗</span><span class="stat-label">PEMASUKAN BULAN INI</span><strong>Rp <?= number_format((float) $monthIncome, 0, ',', '.') ?></strong><small>Kas masuk tercatat</small></article>
    <article class="stat-card"><span class="stat-icon orange">↙</span><span class="stat-label">PENGELUARAN BULAN INI</span><strong>Rp <?= number_format((float) $monthExpense, 0, ',', '.') ?></strong><small>Kas keluar tercatat</small></article>
    <article class="stat-card"><a class="stat-card-link" href="<?= site_url('attendance') ?>"><span class="stat-icon blue">♙</span><span class="stat-label">HADIR HARI INI</span><strong><?= number_format($attendanceSummary['hadir'] + $attendanceSummary['terlambat']) ?></strong><small>Dari <?= number_format((int) $activeEmployees) ?> pegawai aktif · Lihat absensi →</small></a></article>
</div>
<div class="dashboard-panels">
    <div class="panel">
        <div class="panel-heading"><div><h2>Absensi hari ini</h2><p class="muted"><?= esc(date('d/m/Y')) ?> · <?= number_format((int) $activeEmployees) ?> pegawai aktif</p></div><a class="text-link" href="<?= site_url('attendance') ?>">Rekap bulanan →</a></div>
        <div class="attendance-summary">
            <div><strong class="positive"><?= number_format($attendanceSummary['hadir'] + $attendanceSummary['terlambat']) ?></strong><span>Hadir</span></div>
            <div><strong><?= number_format($attendanceSummary['izin']) ?></strong><span>Izin</span></div>
            <div><strong><?= number_format($attendanceSummary['sakit']) ?></strong><span>Sakit</span></div>
            <div><strong class="negative"><?= number_format($attendanceSummary['alpa']) ?></strong><span>Alpa</span></div>
            <div><strong class="attendance-unrecorded"><?= number_format($attendanceSummary['belum_dicatat']) ?></strong><span>Belum dicatat</span></div>
        </div>
        <?php if ($attendanceRows): ?>
            <div class="attendance-roster">
                <?php foreach (array_slice($attendanceRows, 0, 6) as $employee): ?>
                    <div class="attendance-person">
                        <span class="avatar avatar-small"><?= esc(strtoupper(substr($employee['name'], 0, 1))) ?></span>
                        <span class="attendance-person-name"><strong><?= esc($employee['name']) ?></strong><small><?= esc($employee['position']) ?></small></span>
                        <?php if ($employee['status'] === null): ?><span class="badge badge-unrecorded">Belum dicatat</span>
                        <?php else: ?><span class="badge badge-<?= esc($employee['status']) ?>"><?= esc(ucfirst($employee['status'])) ?><?= $employee['check_in'] ? ' · ' . esc(substr($employee['check_in'], 0, 5)) : '' ?></span><?php endif ?>
                    </div>
                <?php endforeach ?>
                <?php if ($activeEmployees > 6): ?><p class="roster-more">+ <?= number_format($activeEmployees - 6) ?> pegawai lainnya</p><?php endif ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Belum ada pegawai aktif. Tambahkan pegawai agar absensi dapat dikelola.</div>
        <?php endif ?>
        <div class="attendance-actions"><a class="btn btn-primary" href="<?= site_url('attendance/new') ?>">＋ Catat absensi</a><a class="btn btn-plain" href="<?= site_url('attendance') ?>">Buka aplikasi absensi</a></div>
    </div>
    <div class="panel">
        <div class="panel-heading"><div><h2>Jadwal pesanan mendatang</h2><p class="muted">Acara yang akan datang</p></div><a class="text-link" href="<?= site_url('bookings') ?>">Lihat semua →</a></div>
        <?php if ($upcoming): ?><div class="table-wrap"><table><thead><tr><th>KODE</th><th>PELANGGAN</th><th>TANGGAL ACARA</th><th>LOKASI</th><th>STATUS</th></tr></thead><tbody><?php foreach ($upcoming as $item): ?><tr><td class="strong"><?= esc($item['booking_code']) ?></td><td><?= esc($item['customer_name']) ?></td><td><?= esc(date('d/m/Y', strtotime($item['event_date']))) ?></td><td><?= esc($item['venue']) ?></td><td><span class="badge badge-<?= esc($item['status']) ?>"><?= esc(ucfirst($item['status'])) ?></span></td></tr><?php endforeach ?></tbody></table></div><?php else: ?><div class="empty-state">Belum ada pesanan mendatang. Tambahkan pesanan baru untuk mulai mengelola jadwal.</div><?php endif ?>
    </div>
</div>
