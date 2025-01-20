<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\TypesCustomers;

class TypesCustomersSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Cliente']
        ];
        $tc_model = new TypesCustomers();
        foreach ($types as $key => $type) {
            $tc_model->save($type);
        }
    }
}
