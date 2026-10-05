<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Attendance extends BaseController
{
    private const STATUSES = ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'];

    public function index(): string
    {
        $month = $this->request->getGet('month');
        if (! is_string($month) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = date('Y-m');
        }
        $rows = $this->db()->table('attendance_records a')
            ->select('a.*, e.name AS employee_name, e.position')
            ->join('employees e', 'e.id = a.employee_id')
            ->where('a.attendance_date >=', $month . '-01')
            ->where('a.attendance_date <=', date('Y-m-t', strtotime($month . '-01')))
            ->orderBy('a.attendance_date', 'DESC')
            ->orderBy('e.name')
            ->get()->getResultArray();

        return $this->render('attendance/index', [
            'title' => 'Rekap absensi',
            'month' => $month,
            'records' => $rows,
            'summary' => [
                'hadir' => count(array_filter($rows, static fn (array $row): bool => in_array($row['status'], ['hadir', 'terlambat'], true))),
                'izin' => count(array_filter($rows, static fn (array $row): bool => $row['status'] === 'izin')),
                'sakit' => count(array_filter($rows, static fn (array $row): bool => $row['status'] === 'sakit')),
                'alpa' => count(array_filter($rows, static fn (array $row): bool => $row['status'] === 'alpa')),
            ],
        ]);
    }

    public function new(): string
    {
        return $this->render('attendance/form', [
            'title' => 'Catat absensi',
            'employees' => $this->db()->table('employees')->where('is_active', 1)->orderBy('name')->get()->getResultArray(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function save(): RedirectResponse
    {
        if (! $this->validate([
            'employee_id' => 'required|is_natural_no_zero',
            'attendance_date' => 'required|valid_date[Y-m-d]',
            'status' => 'required|in_list[hadir,terlambat,izin,sakit,alpa]',
            'check_in' => 'permit_empty|valid_date[H:i]',
            'check_out' => 'permit_empty|valid_date[H:i]',
            'notes' => 'permit_empty|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $employeeId = (int) $this->request->getPost('employee_id');
        $db = $this->db();
        if ($db->table('employees')->where('id', $employeeId)->where('is_active', 1)->countAllResults() === 0) {
            return redirect()->back()->withInput()->with('error', 'Pegawai aktif tidak ditemukan.');
        }
        $attendanceDate = (string) $this->request->getPost('attendance_date');
        $existing = $db->table('attendance_records')->select('id')->where('employee_id', $employeeId)->where('attendance_date', $attendanceDate)->get()->getRowArray();
        $values = [
            'employee_id' => $employeeId,
            'attendance_date' => $attendanceDate,
            'status' => $this->request->getPost('status'),
            'check_in' => $this->request->getPost('check_in') ?: null,
            'check_out' => $this->request->getPost('check_out') ?: null,
            'notes' => trim((string) $this->request->getPost('notes')) ?: null,
            'recorded_by' => session('user_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($existing === null) {
            $values['created_at'] = date('Y-m-d H:i:s');
            $db->table('attendance_records')->insert($values);
        } else {
            $db->table('attendance_records')->where('id', $existing['id'])->update($values);
        }

        return redirect()->to('/attendance?month=' . substr($attendanceDate, 0, 7))->with('message', 'Absensi berhasil disimpan.');
    }

    public function delete(int $id): RedirectResponse
    {
        $this->db()->table('attendance_records')->where('id', $id)->delete();

        return redirect()->to('/attendance')->with('message', 'Catatan absensi dihapus.');
    }
}
