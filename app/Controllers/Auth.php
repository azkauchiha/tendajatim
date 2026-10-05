<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function home(): string|RedirectResponse
    {
        if (session()->has('user_id')) {
            return redirect()->to($this->homeForRole((string) session('role')));
        }

        return $this->render('home/index', [
            'title' => 'Selamat datang',
            'hasUsers' => $this->db()->table('users')->countAllResults() > 0,
        ]);
    }

    public function setup()
    {
        helper(['form', 'url']);
        if ($this->db()->table('users')->countAllResults() > 0) {
            return redirect()->to('/login/admin');
        }

        return view('auth/setup', ['title' => 'Pengaturan akun awal']);
    }

    public function createInitialUsers(): RedirectResponse
    {
        if ($this->db()->table('users')->countAllResults() > 0) {
            return redirect()->to('/login/admin')->with('error', 'Pengaturan awal sudah pernah dilakukan.');
        }

        $rules = [
            'super_name' => 'required|min_length[3]|max_length[120]',
            'super_username' => 'required|alpha_numeric|min_length[4]|max_length[60]',
            'super_password' => 'required|min_length[12]|max_length[128]',
            'admin_name' => 'required|min_length[3]|max_length[120]',
            'admin_username' => 'required|alpha_numeric|min_length[4]|max_length[60]',
            'admin_password' => 'required|min_length[12]|max_length[128]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = $this->db();
        $superUsername = trim((string) $this->request->getPost('super_username'));
        $adminUsername = trim((string) $this->request->getPost('admin_username'));
        if ($superUsername === $adminUsername) {
            return redirect()->back()->withInput()->with('error', 'Nama pengguna Super Admin dan Admin harus berbeda.');
        }
        $db->transStart();
        foreach (['super' => 'super_admin', 'admin' => 'admin'] as $prefix => $role) {
            $db->table('users')->insert([
                'name' => trim((string) $this->request->getPost($prefix . '_name')),
                'username' => $prefix === 'super' ? $superUsername : $adminUsername,
                'password_hash' => password_hash((string) $this->request->getPost($prefix . '_password'), PASSWORD_DEFAULT),
                'role' => $role,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Akun gagal dibuat. Pastikan nama pengguna berbeda.');
        }

        return redirect()->to('/login/super-admin')->with('message', 'Akun berhasil dibuat. Silakan masuk sebagai Super Admin.');
    }

    public function login(string $role)
    {
        helper(['form', 'url']);
        if ($role === 'installer') {
            return redirect()->to('/login/staff');
        }

        $expectedRole = $this->roleFromSlug($role);
        if ($expectedRole === null) {
            return redirect()->to('/login/admin');
        }
        if (session()->has('user_id')) {
            return redirect()->to($this->homeForRole((string) session('role')));
        }

        return $this->render('auth/login', ['title' => 'Masuk', 'role' => $expectedRole, 'slug' => $role]);
    }

    public function attempt(string $role): RedirectResponse
    {
        $expectedRole = $this->roleFromSlug($role);
        if ($expectedRole === null) {
            return redirect()->to('/login/admin');
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $user = $this->db()->table('users')->where('username', $username)->where('role', $expectedRole)->where('is_active', 1)->get()->getRowArray();

        if ($user === null || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Nama pengguna atau kata sandi tidak sesuai.');
        }

        session()->regenerate();
        $employeeId = (int) ($user['employee_id'] ?? 0);
        if ($expectedRole === 'staff') {
            $employee = $this->db()->table('employees')
                ->where('id', $employeeId)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            if ($employee === null) {
                return redirect()->back()->with('error', 'Akun Staff belum terhubung ke pegawai aktif.');
            }
        }
        session()->set([
            'user_id' => (int) $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'role' => $user['role'],
            'employee_id' => $employeeId,
        ]);

        return redirect()->to($this->homeForRole($expectedRole));
    }

    public function logout(): RedirectResponse
    {
        $role = (string) session('role');
        session()->destroy();

        return redirect()->to('/login/' . $this->loginSlug($role))->with('message', 'Anda berhasil keluar.');
    }

    private function roleFromSlug(string $slug): ?string
    {
        return match ($slug) {
            'admin' => 'admin',
            'super-admin' => 'super_admin',
            'staff', 'installer' => 'staff',
            default => null,
        };
    }

    private function homeForRole(string $role): string
    {
        return $role === 'staff' ? '/my-attendance' : '/dashboard';
    }

    private function loginSlug(string $role): string
    {
        return match ($role) {
            'super_admin' => 'super-admin',
            'staff', 'installer' => 'staff',
            default => 'admin',
        };
    }

}
