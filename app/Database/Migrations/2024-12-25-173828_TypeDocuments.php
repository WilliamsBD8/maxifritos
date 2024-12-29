<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeDocuments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'code'      => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('type_documents');
    }

    public function down()
    {
		$this->forge->dropTable('type_documents');
    }
}
