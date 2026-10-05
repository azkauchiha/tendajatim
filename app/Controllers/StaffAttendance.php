<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class StaffAttendance extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $db = $this->db();
        $employeeId = (int) session('employee_id');
        $today = date('Y-m-d');
        $employee = $db->table('employees')->where('id', $employeeId)->where('is_active', 1)->get()->getRowArray();
        if ($employee === null) {
            session()->destroy();

            return redirect()->to('/login/staff')->with('error', 'Akun Staff tidak terhubung ke pegawai aktif.');
        }

        $todayAttendance = $db->table('attendance_records')
            ->where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->get()
            ->getRowArray();
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $monthRecords = $db->table('attendance_records')
            ->where('employee_id', $employeeId)
            ->where('attendance_date >=', $monthStart)
            ->where('attendance_date <=', $monthEnd)
            ->orderBy('attendance_date', 'DESC')
            ->get()
            ->getResultArray();

        return $this->render('attendance/staff', [
            'title' => 'Absensi saya',
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
            'monthRecords' => $monthRecords,
            'monthSummary' => [
                'hadir' => count(array_filter($monthRecords, static fn (array $row): bool => in_array($row['status'], ['hadir', 'terlambat'], true))),
                'izin' => count(array_filter($monthRecords, static fn (array $row): bool => $row['status'] === 'izin')),
                'sakit' => count(array_filter($monthRecords, static fn (array $row): bool => $row['status'] === 'sakit')),
                'alpa' => count(array_filter($monthRecords, static fn (array $row): bool => $row['status'] === 'alpa')),
            ],
        ]);
    }

    public function checkIn(): RedirectResponse
    {
        $db = $this->db();
        $employeeId = (int) session('employee_id');
        $today = date('Y-m-d');
        $attendance = $db->table('attendance_records')
            ->where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->get()
            ->getRowArray();

        if ($attendance !== null && $attendance['check_in'] !== null) {
            return redirect()->to('/my-attendance')->with('error', 'Absensi masuk hari ini sudah tercatat.');
        }
        if ($attendance !== null && ! in_array($attendance['status'], ['hadir', 'terlambat'], true)) {
            return redirect()->to('/my-attendance')->with('error', 'Status absensi hari ini sudah ditetapkan Admin. Hubungi Admin untuk perubahan.');
        }

        $now = date('H:i:s');
        if ($attendance === null) {
            $db->table('attendance_records')->insert([
                'employee_id' => $employeeId,
                'attendance_date' => $today,
                'status' => 'hadir',
                'check_in' => $now,
                'recorded_by' => session('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('attendance_records')
                ->where('id', $attendance['id'])
                ->update([
                    'check_in' => $now,
                    'recorded_by' => session('user_id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        return redirect()->to('/my-attendance')->with('message', 'Jam masuk tercatat pukul ' . substr($now, 0, 5) . '.');
    }

    public function checkOut(): RedirectResponse
    {
        $db = $this->db();
        $attendance = $db->table('attendance_records')
            ->where('employee_id', (int) session('employee_id'))
            ->where('attendance_date', date('Y-m-d'))
            ->get()
            ->getRowArray();

        if ($attendance === null || $attendance['check_in'] === null) {
            return redirect()->to('/my-attendance')->with('error', 'Catat jam masuk terlebih dahulu.');
        }
        if ($attendance['check_out'] !== null) {
            return redirect()->to('/my-attendance')->with('error', 'Absensi pulang hari ini sudah tercatat.');
        }

        $now = date('H:i:s');
        $db->table('attendance_records')->where('id', $attendance['id'])->update([
            'check_out' => $now,
            'recorded_by' => session('user_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/my-attendance')->with('message', 'Jam pulang tercatat pukul ' . substr($now, 0, 5) . '.');
    }
}
