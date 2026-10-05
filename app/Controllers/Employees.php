<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Employees extends BaseController
{
    public function index(): string
    {
        $employees = $this->db()->table('employees e')
            ->select('e.*, u.username AS staff_username')
            ->join('users u', "u.employee_id = e.id AND u.role = 'staff'", 'left')
            ->orderBy('e.is_active', 'DESC')
            ->orderBy('e.name')
            ->get()
            ->getResultArray();

        return $this->render('employees/index', [
            'title' => 'Data pegawai',
            'employees' => $employees,
        ]);
    }

    public function new(): string
    {
        return $this->render('employees/form', ['title' => 'Tambah pegawai', 'employee' => null]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate([
            'name' => 'required|min_length[2]|max_length[120]',
            'phone' => 'permit_empty|max_length[30]',
            'position' => 'required|in_list[staff,admin]',
            'staff_username' => 'permit_empty|alpha_numeric|min_length[4]|max_length[60]',
            'staff_password' => 'permit_empty|min_length[12]|max_length[128]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $username = trim((string) $this->request->getPost('staff_username'));
        $password = (string) $this->request->getPost('staff_password');
        if (($username === '') !== ($password === '')) {
            return redirect()->back()->withInput()->with('error', 'Isi nama pengguna dan kata sandi Staff sekaligus, atau kosongkan keduanya.');
        }
        $db = $this->db();
        if ($username !== '' && $db->table('users')->where('username', $username)->countAllResults() > 0) {
            return redirect()->back()->withInput()->with('error', 'Nama pengguna Staff sudah dipakai.');
        }
        $db->transStart();
        $db->table('employees')->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'position' => trim((string) $this->request->getPost('position')),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($username !== '') {
            $db->table('users')->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'staff',
                'employee_id' => $db->insertID(),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Pegawai atau akun Staff gagal dibuat.');
        }

        return redirect()->to('/employees')->with('message', $username === '' ? 'Pegawai berhasil ditambahkan.' : 'Pegawai dan akun Staff berhasil dibuat.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        $employee = $this->db()->table('employees')->where('id', $id)->get()->getRowArray();
        if ($employee === null) {
            return redirect()->to('/employees')->with('error', 'Pegawai tidak ditemukan.');
        }

        $staffAccount = $this->db()->table('users')->where('employee_id', $id)->where('role', 'staff')->get()->getRowArray();

        return $this->render('employees/form', ['title' => 'Ubah pegawai', 'employee' => $employee, 'staffAccount' => $staffAccount]);
    }

    public function update(int $id): RedirectResponse
    {
        if (! $this->validate([
            'name' => 'required|min_length[2]|max_length[120]',
            'phone' => 'permit_empty|max_length[30]',
            'position' => 'required|in_list[staff,admin]',
            'is_active' => 'required|in_list[0,1]',
            'staff_username' => 'permit_empty|alpha_numeric|min_length[4]|max_length[60]',
            'staff_password' => 'permit_empty|min_length[12]|max_length[128]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $db = $this->db();
        $employee = $db->table('employees')->where('id', $id)->get()->getRowArray();
        if ($employee === null) {
            return redirect()->to('/employees')->with('error', 'Pegawai tidak ditemukan.');
        }
        $staffAccount = $db->table('users')->where('employee_id', $id)->where('role', 'staff')->get()->getRowArray();
        $username = trim((string) $this->request->getPost('staff_username'));
        $password = (string) $this->request->getPost('staff_password');
        if ($staffAccount !== null && $username === '') {
            $username = $staffAccount['username'];
        }
        if ($staffAccount === null && (($username === '') !== ($password === ''))) {
            return redirect()->back()->withInput()->with('error', 'Isi nama pengguna dan kata sandi Staff sekaligus, atau kosongkan keduanya.');
        }
        if ($staffAccount !== null && $username === '' && $password !== '') {
            return redirect()->back()->withInput()->with('error', 'Nama pengguna Staff harus diisi.');
        }
        if ($username !== '') {
            $usernameQuery = $db->table('users')->where('username', $username);
            if ($staffAccount !== null) {
                $usernameQuery->where('id !=', $staffAccount['id']);
            }
            if ($usernameQuery->countAllResults() > 0) {
                return redirect()->back()->withInput()->with('error', 'Nama pengguna Staff sudah dipakai.');
            }
        }

        $name = trim((string) $this->request->getPost('name'));
        $isActive = (int) $this->request->getPost('is_active');
        $db->transStart();
        $db->table('employees')->where('id', $id)->update([
            'name' => $name,
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'position' => trim((string) $this->request->getPost('position')),
            'is_active' => $isActive,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if ($staffAccount !== null) {
            $accountValues = [
                'name' => $name,
                'username' => $username,
                'is_active' => $isActive,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($password !== '') {
                $accountValues['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $db->table('users')->where('id', $staffAccount['id'])->update($accountValues);
        } elseif ($username !== '') {
            $db->table('users')->insert([
                'name' => $name,
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'staff',
                'employee_id' => $id,
                'is_active' => $isActive,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Perubahan data pegawai atau akun Staff gagal disimpan.');
        }

        return redirect()->to('/employees')->with('message', $username === '' ? 'Data pegawai berhasil diperbarui.' : 'Data pegawai dan akun Staff berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $db = $this->db();
        $db->transStart();
        $db->table('employees')->where('id', $id)->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
        $db->table('users')->where('employee_id', $id)->where('role', 'staff')->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
        $db->transComplete();

        return redirect()->to('/employees')->with('message', 'Pegawai dan akun Staff dinonaktifkan; riwayat absensi tetap tersimpan.');
    }
}
