<?php

namespace App\Database\Seeds;

use App\Models\Menu;
use App\Models\Permission;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            ['option' => 'Clientes','url' => 'clientes','icon' => 'ri-user-2-line','position' => '1','type' => 'primario','references' => NULL,'status' => 'active','component' => 'controller','title' => 'Clientes','description' => NULL,'table' => 'customers'],
            ['option' => 'Vendedores','url' => 'sellers','icon' => 'ri-id-card-fill','position' => '3','type' => 'primario','references' => NULL,'status' => 'active','component' => 'table','title' => 'Vendedores','description' => NULL,'table' => 'users'],
            ['option' => 'Productos','url' => '','icon' => 'ri-product-hunt-line','position' => '2','type' => 'primario','references' => NULL,'status' => 'active','component' => 'table','title' => NULL,'description' => NULL,'table' => NULL],
            ['option' => 'Familia','url' => 'product_family','icon' => 'ri-stack-fill','position' => '1','type' => 'secundario','references' => '3','status' => 'active','component' => 'table','title' => 'Familia de productos','description' => NULL,'table' => 'groups_product'],
            ['option' => 'Productos','url' => 'productos','icon' => NULL,'position' => '2','type' => 'secundario','references' => '3','status' => 'active','component' => 'table','title' => 'Productos','description' => NULL,'table' => 'products'],
            ['option' => 'Documentos','url' => 'cotizaciones','icon' => 'ri-file-list-3-line','position' => '0','type' => 'primario','references' => NULL,'status' => 'active','component' => 'controller','title' => NULL,'description' => NULL,'table' => NULL],
            ['option' => 'Mi perfil','url' => 'perfil','icon' => 'ri-user-line','position' => '10','type' => 'primario','references' => NULL,'status' => 'active','component' => 'table','title' => NULL,'description' => NULL,'table' => 'users'],
            ['option' => 'Historial','url' => 'history','icon' => 'ri-file-history-line','position' => '10','type' => 'primario','references' => NULL,'status' => 'active','component' => 'table','title' => NULL,'description' => NULL,'table' => 'history_users'],
            ['option' => 'Usuarios','url' => 'users','icon' => 'ri-group-line','position' => '2','type' => 'primario','references' => NULL,'status' => 'active','component' => 'table','title' => NULL,'description' => NULL,'table' => 'users']
        ];
        $m_model = new Menu();
        foreach ($menus as $key => $menu) {
            $m_model->save($menu);
        }

        $p_model = new Permission();
        $permissions = [
            ['role_id' => '2','menu_id' => '6'],
            ['role_id' => '2','menu_id' => '1'],
            ['role_id' => '2','menu_id' => '3'],
            ['role_id' => '2','menu_id' => '5'],
            ['role_id' => '2','menu_id' => '4'],
            ['role_id' => '2','menu_id' => '9'],
            ['role_id' => '2','menu_id' => '2'],
            ['role_id' => '2','menu_id' => '7'],
            ['role_id' => '2','menu_id' => '8'],
            ['role_id' => '3','menu_id' => '6'],
            ['role_id' => '3','menu_id' => '1'],
            ['role_id' => '3','menu_id' => '7']
        ];
        foreach($permissions as $permission)
            $p_model->save($permission);
    }
}
