<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->has('user_id')) {
            return redirect()->to('/login/admin')->with('error', 'Silakan masuk untuk melanjutkan.');
        }

        $user = \Config\Database::connect()->table('users')
            ->select('id, role, is_active, employee_id')
            ->where('id', (int) session('user_id'))
            ->get()
            ->getRowArray();
        if ($user === null || (int) $user['is_active'] !== 1 || $user['role'] !== session('role')) {
            $role = (string) session('role');
            session()->destroy();

            return redirect()->to('/login/' . $this->loginSlug($role))->with('error', 'Sesi tidak berlaku. Silakan masuk kembali.');
        }
        if ($user['role'] === 'staff') {
            $employeeIsActive = \Config\Database::connect()->table('employees')
                ->where('id', (int) $user['employee_id'])
                ->where('is_active', 1)
                ->countAllResults();
            if ($employeeIsActive === 0 || (int) session('employee_id') !== (int) $user['employee_id']) {
                session()->destroy();

                return redirect()->to('/login/staff')->with('error', 'Akun Staff tidak aktif. Hubungi Admin.');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
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
