<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Invoices extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        			          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'customer_id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'seller_id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'user_id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'type_document_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'status_id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'resolution'                => ['type' => 'INT', 'constraint' => 11],
            'resolution_reference'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'address'  			            => ['type' => 'TEXT', 'null' => TRUE],
            'note'  			              => ['type' => 'TEXT', 'null' => TRUE],
            'invoice_amount'            => ['type' => 'DECIMAL(20,2)', 'default' => 0],
            'payable_amount'            => ['type' => 'DECIMAL(20,2)', 'default' => 0],
            'discount_amount'           => ['type' => 'DECIMAL(20,2)', 'default' => 0],
            'discount_percentage'       => ['type' => 'INT', 'default' => 0],
            'address_origin'  			    => ['type' => 'TEXT', 'null' => TRUE],
            'created_at'                => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'                => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('customer_id', 'customers', 'id');
		$this->forge->addForeignKey('seller_id', 'users', 'id');
		$this->forge->addForeignKey('type_document_id', 'type_documents', 'id');
		$this->forge->addForeignKey('status_id', 'status', 'id');
		$this->forge->addForeignKey('user_id', 'users', 'id');
		$this->forge->addForeignKey('resolution_reference', 'invoices', 'id');
		$this->forge->createTable('invoices');
    }

    public function down()
    {
		$this->forge->dropTable('invoices');
    }
}
