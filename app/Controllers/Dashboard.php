<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Dashboard extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session('role') === 'staff') {
            return redirect()->to('/my-attendance');
        }

        $db = $this->db();
        $month = date('Y-m');
        $today = date('Y-m-d');
        $attendanceRows = $db->table('employees e')
            ->select('e.name, e.position, a.status, a.check_in, a.check_out')
            ->join('attendance_records a', 'a.employee_id = e.id AND a.attendance_date = ' . $db->escape($today), 'left')
            ->where('e.is_active', 1)
            ->orderBy('e.name')
            ->get()
            ->getResultArray();
        $attendanceSummary = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'belum_dicatat' => 0,
        ];
        foreach ($attendanceRows as $attendanceRow) {
            $status = $attendanceRow['status'];
            if ($status === null) {
                $attendanceSummary['belum_dicatat']++;
            } else {
                $attendanceSummary[$status]++;
            }
        }

        return $this->render('dashboard/index', [
            'title' => 'Ringkasan',
            'bookingCount' => $db->table('bookings')->where('status !=', 'dibatalkan')->countAllResults(),
            'activeEmployees' => count($attendanceRows),
            'monthIncome' => $db->table('finance_entries')->selectSum('amount')->where('type', 'masuk')->where('entry_date >=', $month . '-01')->where('entry_date <=', date('Y-m-t'))->get()->getRowArray()['amount'] ?? 0,
            'monthExpense' => $db->table('finance_entries')->selectSum('amount')->where('type', 'keluar')->where('entry_date >=', $month . '-01')->where('entry_date <=', date('Y-m-t'))->get()->getRowArray()['amount'] ?? 0,
            'upcoming' => $db->table('bookings')->where('event_date >=', date('Y-m-d'))->where('status !=', 'dibatalkan')->orderBy('event_date', 'ASC')->limit(5)->get()->getResultArray(),
            'attendanceRows' => $attendanceRows,
            'attendanceSummary' => $attendanceSummary,
        ]);
    }
}
