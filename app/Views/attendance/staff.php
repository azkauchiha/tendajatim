<div class="staff-welcome">
    <p class="eyebrow">ABSENSI HARIAN</p>
    <h2>Halo, <?= esc($employee['name']) ?></h2>
    <p class="muted"><?= esc($employee['position']) ?> · <?= esc(date('d/m/Y')) ?></p>
</div>

<section class="panel staff-today">
    <div class="panel-heading"><div><h2>Absensi hari ini</h2><p class="muted">Catat jam masuk dan pulang Anda sendiri.</p></div><span class="badge <?= $todayAttendance ? 'badge-' . esc($todayAttendance['status']) : 'badge-unrecorded' ?>"><?= $todayAttendance ? esc(ucfirst($todayAttendance['status'])) : 'Belum absen' ?></span></div>
    <div class="clock-grid">
        <div class="clock-card"><span>JAM MASUK</span><strong><?= esc(($todayAttendance['check_in'] ?? null) ? substr($todayAttendance['check_in'], 0, 5) : '—:—') ?></strong><?php if (! $todayAttendance || ! $todayAttendance['check_in']): ?><small>Belum tercatat</small><?php else: ?><small>Sudah tercatat hari ini</small><?php endif ?></div>
        <div class="clock-card"><span>JAM PULANG</span><strong><?= esc(($todayAttendance['check_out'] ?? null) ? substr($todayAttendance['check_out'], 0, 5) : '—:—') ?></strong><?php if (! $todayAttendance || ! $todayAttendance['check_out']): ?><small>Belum tercatat</small><?php else: ?><small>Sudah tercatat hari ini</small><?php endif ?></div>
    </div>
    <div class="clock-actions">
        <?php if (! $todayAttendance || ! $todayAttendance['check_in']): ?>
            <?php if (! $todayAttendance || in_array($todayAttendance['status'], ['hadir', 'terlambat'], true)): ?>
                <form method="post" action="<?= site_url('my-attendance/check-in') ?>"><?= csrf_field() ?><button class="btn btn-primary btn-clock">◷ &nbsp; Absen masuk</button></form>
            <?php else: ?><p class="muted">Status hari ini sudah ditetapkan Admin. Hubungi Admin jika perlu diperbarui.</p><?php endif ?>
        <?php elseif (! $todayAttendance['check_out']): ?>
            <form method="post" action="<?= site_url('my-attendance/check-out') ?>"><?= csrf_field() ?><button class="btn btn-checkout btn-clock">↗ &nbsp; Absen pulang</button></form>
        <?php else: ?><div class="attendance-complete">✓ Absensi hari ini lengkap. Sampai jumpa!</div><?php endif ?>
    </div>
</section>

<div class="summary-grid summary-four staff-month-summary">
    <div class="summary-card"><span>Hadir bulan ini</span><strong class="positive"><?= number_format($monthSummary['hadir']) ?></strong></div>
    <div class="summary-card"><span>Izin</span><strong><?= number_format($monthSummary['izin']) ?></strong></div>
    <div class="summary-card"><span>Sakit</span><strong><?= number_format($monthSummary['sakit']) ?></strong></div>
    <div class="summary-card"><span>Alpa</span><strong class="negative"><?= number_format($monthSummary['alpa']) ?></strong></div>
</div>

<section class="panel">
    <div class="panel-heading"><div><h2>Riwayat absensi saya</h2><p class="muted">Catatan kehadiran bulan <?= esc(date('m/Y')) ?>.</p></div></div>
    <?php if ($monthRecords): ?>
        <div class="table-wrap"><table><thead><tr><th>TANGGAL</th><th>STATUS</th><th>JAM MASUK</th><th>JAM PULANG</th></tr></thead><tbody>
            <?php foreach ($monthRecords as $record): ?><tr><td><?= esc(date('d/m/Y', strtotime($record['attendance_date']))) ?></td><td><span class="badge badge-<?= esc($record['status']) ?>"><?= esc(ucfirst($record['status'])) ?></span></td><td><?= esc($record['check_in'] ? substr($record['check_in'], 0, 5) : '—') ?></td><td><?= esc($record['check_out'] ? substr($record['check_out'], 0, 5) : '—') ?></td></tr><?php endforeach ?>
        </tbody></table></div>
    <?php else: ?><div class="empty-state">Belum ada catatan absensi untuk bulan ini.</div><?php endif ?>
</section>
