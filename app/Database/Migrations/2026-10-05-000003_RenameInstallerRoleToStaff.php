<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameInstallerRoleToStaff extends Migration
{
    public function up()
    {
        $this->db->table('users')
            ->where('role', 'installer')
            ->update(['role' => 'staff']);
    }

    public function down()
    {
        $this->db->table('users')
            ->where('role', 'staff')
            ->where('employee_id IS NOT NULL', null, false)
            ->update(['role' => 'installer']);
    }
}
