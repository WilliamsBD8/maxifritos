<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypesDocumentsIdentification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'code'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'status'    => ['type' => 'ENUM("active", "inactive")', 'default' => 'active'],
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('type_document_identifications');
    }

    public function down()
    {
		$this->forge->dropTable('type_document_identifications');
    }
}
