<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! in_array(session('role'), $arguments ?? [], true)) {
            $home = session('role') === 'staff' ? '/my-attendance' : '/dashboard';

            return redirect()->to($home)->with('error', 'Anda tidak memiliki izin untuk membuka halaman tersebut.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
