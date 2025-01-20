<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\TypeDocumentIdentifications as TD_Identifications;

class TypeDocumentIdentificationsSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Registro Civil', 'code' => 'RC'],
            ['name' => 'Tarjeta de Identidad', 'code' => 'TI'],
            ['name' => 'Cédula de Ciudadania', 'code' => 'CC'],
            ['name' => 'Tarjeta de Extranjería', 'code' => 'TE'],
            ['name' => 'Cédula de Extranjeria', 'code' => 'CE'],
            ['name' => 'NIT', 'code' => 'NIT'],
            ['name' => 'Pasaporte', 'code' => 'PAS'],
            ['name' => 'Documento de Identificacion Extranjero', 'code' => 'DIE'],
            ['name' => 'Nit de otro país', 'code' => 'NOP'],
            ['name' => 'NUIP *', 'code' => 'NUIP'],
            ['name' => 'PEP', 'code' => 'PEP']
        ];

        $tdi_model = new TD_Identifications();
        foreach ($types as $key => $type) {
            $tdi_model->save($type);
        }
    }
}
