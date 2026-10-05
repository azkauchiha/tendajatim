<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTendaJatimTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'username' => ['type' => 'VARCHAR', 'constraint' => 60, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'VARCHAR', 'constraint' => 20],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'booking_code' => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'event_date' => ['type' => 'DATE'],
            'venue' => ['type' => 'VARCHAR', 'constraint' => 180],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'paid_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'baru'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('event_date');
        $this->forge->createTable('bookings');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'entry_date' => ['type' => 'DATE'],
            'type' => ['type' => 'VARCHAR', 'constraint' => 10],
            'category' => ['type' => 'VARCHAR', 'constraint' => 80],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '14,2'],
            'booking_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('entry_date');
        $this->forge->addKey('booking_id');
        $this->forge->createTable('finance_entries');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'position' => ['type' => 'VARCHAR', 'constraint' => 80],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('employees');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'employee_id' => ['type' => 'INT', 'unsigned' => true],
            'attendance_date' => ['type' => 'DATE'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20],
            'check_in' => ['type' => 'TIME', 'null' => true],
            'check_out' => ['type' => 'TIME', 'null' => true],
            'notes' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'recorded_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['employee_id', 'attendance_date']);
        $this->forge->addKey('attendance_date');
        $this->forge->createTable('attendance_records');
    }

    public function down()
    {
        $this->forge->dropTable('attendance_records', true);
        $this->forge->dropTable('employees', true);
        $this->forge->dropTable('finance_entries', true);
        $this->forge->dropTable('bookings', true);
        $this->forge->dropTable('users', true);
    }
}
