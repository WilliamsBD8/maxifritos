<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\TypeDocument;

class TypeDocumentsSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Cotización', 'code' => 'CO'],
            ['name' => 'Remisión', 'code' => 'RE'],
        ];
        $td_model = new TypeDocument();
        foreach ($types as $key => $type) {
            $td_model->save($type);
        }
    }
}
