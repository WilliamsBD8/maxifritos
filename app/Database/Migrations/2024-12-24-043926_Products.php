<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Products extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'group_product_id'  => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'code_item'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'code'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'description'       => ['type' => 'TEXT', 'constraint' => 100, 'null' => TRUE],
            'value'             => ['type' => 'DECIMAL(20,2)', 'default' => '0'],
            'status'            => ['type' => 'ENUM("active", "inactive")', 'default' => 'active'],
            'created_at'        => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'        => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('group_product_id', 'groups_product', 'id');
		$this->forge->createTable('products');
    }

    public function down()
    {
		$this->forge->dropTable('products');
    }
}
