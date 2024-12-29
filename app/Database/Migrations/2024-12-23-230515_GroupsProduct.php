<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GroupsProduct extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'constraint' => 100, 'null' => TRUE],
            'code'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'status'      => ['type' => 'ENUM("active", "inactive")', 'default' => 'active'],
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('groups_product');
    }

    public function down()
    {
		$this->forge->dropTable('groups_product');
    }
}
