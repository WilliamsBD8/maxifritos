<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Customers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        			            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'type_customer_id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'type_document_identification_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'user_id'                           => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'name'      			            => ['type' => 'VARCHAR', 'constraint' => 45],
            'email'     			            => ['type' => 'VARCHAR', 'constraint' => 100],
            'identification_number'             => ['type' => 'VARCHAR', 'constraint' => 45],
            'phone'  			                => ['type' => 'VARCHAR', 'constraint' => 45],
            'address'  			                => ['type' => 'VARCHAR', 'constraint' => 100],
            'status'    			        => ['type' => 'ENUM("active", "inactive")', 'default' => 'active'],
            'created_at'        => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'        => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('type_customer_id', 'type_customers', 'id');
		$this->forge->addForeignKey('type_document_identification_id', 'type_document_identifications', 'id');
		$this->forge->addForeignKey('user_id', 'users', 'id');
		$this->forge->createTable('customers');
    }

    public function down()
    {
		$this->forge->dropTable('customers');
    }
}
