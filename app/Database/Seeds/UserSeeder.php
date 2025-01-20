<?php namespace App\Database\Seeds;

use App\Models\User;
use App\Models\Password;

class UserSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $data = [
            [
                'name'              => 'Super Administrador',
                'email'             => 'iplanet@iplanetcolombia.com',
                'username'          => 'root',
                'status'            => 'active',
                'photo'             => '',
                'role_id'           => 1
            ],
            [
                'name'              => 'Administrador',
                'email'             => 'administrador@gmail.com',
                'username'          => 'admin',
                'status'            => 'active',
                'photo'             => '',
                'role_id'           => 2
            ],
            [
                'name'              => 'Cotizador',
                'email'             => 'cotizador@gmail.com',
                'username'          => 'cotizador',
                'status'            => 'active',
                'photo'             => '',
                'role_id'           => 3
            ]
        ];
        foreach ($data as $key => $user) {
            $u_model = new User();
            $u_model->save($user);
    
            $user_id = $u_model->insertID();
            $p_model = new Password();
            $p_model->save([
                'user_id'   => $user_id,
                'password'  => password_hash('123456789', PASSWORD_DEFAULT)
            ]);
        }
        
    }
}