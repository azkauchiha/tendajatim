<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstallerUserAccounts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'employee_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'role'],
        ]);
        $this->forge->addUniqueKey('employee_id', 'users_employee_id_unique');
        $this->forge->processIndexes('users');
    }

    public function down()
    {
        $this->forge->dropKey('users', 'users_employee_id_unique');
        $this->forge->dropColumn('users', 'employee_id');
    }
}
