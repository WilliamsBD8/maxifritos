<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistoryUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'user_id'   => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'attempts'  => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'created_at'=> ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'=> ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('user_id', 'users', 'id');
		$this->forge->createTable('history_users');
    }

    public function down()
    {
		$this->forge->dropTable('history_users');
    }
}
