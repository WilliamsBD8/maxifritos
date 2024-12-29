<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\TypeDocumentIdentifications as TD_Identifications;

class TypeDocumentIdentificationsSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Registro Civil'],
            ['name' => 'Tarjeta de Identidad'],
            ['name' => 'Cédula de Ciudadania'],
            ['name' => 'Tarjeta de Extranjería'],
            ['name' => 'Cédula de Extranjeria'],
            ['name' => 'NIT'],
            ['name' => 'Pasaporte'],
            ['name' => 'Documento de Identificacion Extranjero'],
            ['name' => 'Nit de otro país'],
            ['name' => 'NUIP *'],
            ['name' => 'PEP']
        ];

        $tdi_model = new TD_Identifications();
        foreach ($types as $key => $type) {
            $tdi_model->save($type);
        }
    }
}
