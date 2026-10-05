<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Users extends BaseController
{
    public function index(): string
    {
        return $this->render('users/index', [
            'title' => 'Pengguna aplikasi',
            'users' => $this->db()->table('users')->orderBy('role')->orderBy('name')->get()->getResultArray(),
        ]);
    }

    public function new(): string
    {
        return $this->render('users/form', ['title' => 'Tambah admin']);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate([
            'name' => 'required|min_length[3]|max_length[120]',
            'username' => 'required|alpha_numeric|min_length[4]|max_length[60]',
            'password' => 'required|min_length[12]|max_length[128]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $username = trim((string) $this->request->getPost('username'));
        if ($this->db()->table('users')->where('username', $username)->countAllResults() > 0) {
            return redirect()->back()->withInput()->with('error', 'Nama pengguna sudah dipakai.');
        }
        $this->db()->table('users')->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'username' => $username,
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'admin',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/users')->with('message', 'Akun Admin berhasil dibuat.');
    }

    public function delete(int $id): RedirectResponse
    {
        $user = $this->db()->table('users')->where('id', $id)->get()->getRowArray();
        if ($user === null || $user['role'] !== 'admin' || (int) $user['id'] === (int) session('user_id')) {
            return redirect()->to('/users')->with('error', 'Akun ini tidak dapat dihapus.');
        }
        $this->db()->table('users')->where('id', $id)->delete();

        return redirect()->to('/users')->with('message', 'Akun Admin berhasil dihapus.');
    }
}
