<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Pendiente'],
            ['name' => 'Remisionado'],
            ['name' => 'Rechazado'],
        ];
        $s_model = new Status();
        foreach ($statuses as $key => $status) {
            $s_model->save($status);
        }
    }
}
