<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LineInvoices extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        			    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'product_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'invoice_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'quantity'  			    => ['type' => 'INT', 'null' => TRUE],
            'value'                     => ['type' => 'DECIMAL(20,2)', 'default' => 0],
            'discount_amount'           => ['type' => 'DECIMAL(20,2)', 'default' => 0],
            'discount_percentage'       => ['type' => 'INT', 'default' => 0],
            'created_at'                => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'                => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('product_id', 'products', 'id');
		$this->forge->addForeignKey('invoice_id', 'invoices', 'id');
		$this->forge->createTable('line_invoices');
    }

    public function down()
    {
		$this->forge->dropTable('line_invoices');
    }
}
