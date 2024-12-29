<?php


namespace App\Controllers;


use App\Traits\Grocery;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Password;
use App\Models\GroupProduct;
use CodeIgniter\Exceptions\PageNotFoundException;

class TableController extends BaseController
{
    use Grocery;

    private $crud;

    public function __construct()
    {
        $this->crud = $this->_getGroceryCrudEnterprise();
        // $this->crud->setSkin('bootstrap-v3');
        $this->crud->setLanguage('Spanish');
    }

    public function index($data)
    {
        $menu = new Menu();
        $component = $menu->where(['url' => $data, 'component' => 'table'])->get()->getResult();



        if($component) {
            $this->crud->setTable($component[0]->table);
            switch ($component[0]->url) {
                case 'users':
                    $this->crud->where(['role_id > ?' => 1]);
                    $this->crud->unsetDelete();
                    $this->crud->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');
                    $this->crud->setRelation('role_id', 'roles', 'name', ['id > ?' => 1]);
                    $this->crud->displayAs([
                        'name'  => 'Nombre',
                        'photo' => 'Foto',
                        'username'  => 'Usuario',
                        'status'    => 'Estado',
                        'role_id'   => 'Rol'
                    ]);
                    $this->crud->unsetEditFields(['role_id', 'usuario']);
                    $this->crud->uniqueFields(['email', 'username']);
                    $this->crud->setActionButton('Avatar', 'fa fa-lock', function ($row) {
                        return base_url(['table', 'users', $row->id]);
                    }, false);
                    break;
                case 'menus':
                    $this->crud->setTexteditor(['description']);
                    break;
                case 'clientes':
                    $this->crud->displayAs([
                        'type_document_identification_id'   => 'Tipo de documento',
                        'name'                              => 'Nombre',
                        'identification_number'             => 'Número de identificacón',
                        'phone'                             => 'Télefono',
                        'address'                           => 'Dirección',
                        'status'                            => 'Estado',
                        'created_at'                        => 'Fecha de creación'
                    ]);
                    $this->crud->where(['type_customer_id' => 1]);
                    $this->crud->setRelation('type_document_identification_id', 'type_document_identifications', 'name');
                    $this->crud->setTexteditor(['address']);
                    $unsetData = ['type_customer_id', 'user_id', 'updated_at'];
                    $this->crud->unsetColumns($unsetData);
                    $unsetData[] = 'created_at';
                    $this->crud->unsetEditFields($unsetData);
                    $unsetData[] = 'status';
                    $this->crud->unsetAddFields($unsetData);
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $stateParameters->data['type_customer_id'] = 1;
                        $stateParameters->data['created_at'] = date('Y-m-d H:i:s');
                        $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                        return $stateParameters;
                    });
                    $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                        $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                        return $stateParameters;
                    });
                    break;
                case 'sellers':
                    $this->crud->displayAs([
                        'type_document_identification_id'   => 'Tipo de documento',
                        'name'                              => 'Nombre',
                        'identification_number'             => 'Número de identificacón',
                        'phone'                             => 'Télefono',
                        'address'                           => 'Dirección',
                        'status'                            => 'Estado',
                        'created_at'                        => 'Fecha de creación'
                    ]);
                    $this->crud->where(['type_customer_id' => 2]);
                    $this->crud->setRelation('type_document_identification_id', 'type_document_identifications', 'name');
                    $this->crud->setTexteditor(['address']);
                    $unsetData = ['type_customer_id', 'user_id', 'updated_at'];
                    $this->crud->unsetColumns($unsetData);
                    $unsetData[] = 'created_at';
                    $this->crud->unsetEditFields($unsetData);
                    $unsetData[] = 'status';
                    $this->crud->unsetAddFields($unsetData);
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $stateParameters->data['type_customer_id'] = 2;
                        $stateParameters->data['created_at'] = date('Y-m-d H:i:s');
                        $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                        return $stateParameters;
                    });
                    $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                        $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                        return $stateParameters;
                    });
                    break;
                case 'product_family':
                    $this->crud->displayAs([
                        'name'          => 'Nombre',
                        'description'   => 'Descripción',
                        'status'        => 'Estado',
                        'code'          => 'Código'
                    ]);
                    $this->crud->uniqueFields(['code']);
                    $this->crud->setTexteditor(['description']);
                    $this->crud->unsetDelete();
                    $this->crud->requiredFields(['code']);
                    break;
                case 'productos':
                    $this->crud->displayAs([
                        'group_product_id'  => 'Familia',
                        'code'              => 'Código',
                        'code_item'         => 'Código producto',
                        'name'              => 'Nombre',
                        'description'       => 'Descripción',
                        'value'             => 'Valor',
                        'status'            => 'Estado',
                        'created_at'        => 'Fecha de creación',
                        'updated_at'        => 'Fecha de actualización'
                    ]);
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $gp_model = new GroupProduct();
                        $group = $gp_model->find($stateParameters->data['group_product_id']);
                        $value = str_replace(",", "", $stateParameters->data['value']);
                        $code = "$group->code{$stateParameters->data['code_item']}";
                        $p_model = new Product();
                        $product = $p_model->where(['code' => $code])->first();
                        if(!empty($product)){
                            $errorMessage = new \GroceryCrud\Core\Error\ErrorMessage();
                            return $errorMessage->setMessage("Ya existe un producto con el codigo <b>{$stateParameters->data['code_item']}</b> para la familia <b>{$group->name} - {$group->code}</b>");
                        }
                        $stateParameters->data['value'] = $value;
                        $stateParameters->data['code'] = $code;
                        $stateParameters->data['created_at'] = date('Y-m-d H:i:s');
                        $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                        
                        return $stateParameters;
                    });
                    $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                        $gp_model = new GroupProduct();
                        $group = $gp_model->find($stateParameters->data['group_product_id']);
                        $value = str_replace(",", "", $stateParameters->data['value']);
                        $code = "$group->code{$stateParameters->data['code_item']}";
                        $p_model = new Product();
                        $product = $p_model->where(['id != ' => $stateParameters->primaryKeyValue, 'code' => $code])->first();
                        if(!empty($product)){
                            $errorMessage = new \GroceryCrud\Core\Error\ErrorMessage();
                            return $errorMessage->setMessage("Ya existe un producto con el codigo <b>{$stateParameters->data['code_item']}</b> para la familia <b>{$group->name} - {$group->code}</b>");
                        }
                        $stateParameters->data['code'] = $code;
                        $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                        $stateParameters->data['value'] = $value;
                        return $stateParameters;
                    });
                    
                    $this->crud->setRelation('group_product_id', 'groups_product', '{name} - {code}');
                    $this->crud->setTexteditor(['description']);
                    $unsetData = ['code', 'created_at', 'updated_at'];
                    $this->crud->unsetAddFields($unsetData);
                    $this->crud->unsetEditFields($unsetData);
                    $this->crud->callbackAddField('value', function($fieldType, $fieldName){
                        return '<input class="form-control" name="' . $fieldName . '" onkeyup="updateFormattedValue(this)" type="text" value="" placeholder="0.00" required="true">';
                    });
                    $this->crud->callbackEditField('value', function($fieldValue, $primaryKeyValue, $rowData){
                        return '<input class="form-control" name="value" onkeyup="updateFormattedValue(this)" type="text" value="'.number_format($fieldValue, '2', '.', ',').'" placeholder="0.00" required="true">';
                    });
                    $this->crud->requiredFields(['code_item', 'group_product_id', 'name']);
                    $this->crud->callbackColumn('value', function ($value, $row) {
                        return number_format($value, '2', '.', ',');
                    });
                    break;

                case 'perfil':
                    $this->crud->where(['id' => session('user')->id]);
                    $this->crud->unsetAdd();
                    $this->crud->unsetDelete();
                    $this->crud->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');
                    $this->crud->setRelation('role_id', 'roles', 'name');
                    $this->crud->displayAs([
                        'name'  => 'Nombre',
                        'photo' => 'Foto',
                        'username'  => 'Usuario',
                        'status'    => 'Estado',
                        'role_id'   => 'Rol'
                    ]);
                    $this->crud->unsetEditFields(['role_id', 'usuario']);
                    $this->crud->uniqueFields(['email', 'username']);
                    break;
                case 'history':
                    $this->crud->displayAs([
                        'user_id'   => 'Usuario',
                        'attempts'  => 'Intentos de inicio',
                        'created_at'    => 'Fecha de creación'
                    ]);
                    $this->crud->defaultOrdering('id', 'DESC');
                    $this->crud->unsetOperations();
                    $this->crud->unsetColumns(['updated_at']);
                    
                    $this->crud->setRelation('user_id', 'users', '{name}');
                    break;
                default:
                    break;   
            }
            $output = $this->crud->render();
            if (isset($output->isJSONResponse) && $output->isJSONResponse) {
                header('Content-Type: application/json; charset=utf-8');
                echo $output->output;
                exit;
            }

            $this->viewTable($output, $component[0]->title, $component[0]->description);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detail($data, $id)
    {
        $title = '';
        $description = '';
        $this->id = $id;
        if($data) {
            $this->crud->setTable($data);
            switch ($data) {
                case 'users':
                    $this->crud->setTable('passwords');
                    $this->crud->where(['user_id' => $this->id]);
                    $this->crud->unsetDelete();
                    $this->crud->unsetEdit();
                    $this->crud->unsetColumns(['password', 'user_id', 'updated_at']);
                    $this->crud->fieldType('password', 'password');
                    $this->crud->addFields(['password']);
                    $this->crud->callbackBeforeInsert(function ($info){
                        $info->data['created_at']   = date('Y-m-d H:i:s');
                        $info->data['updated_at']   = date('Y-m-d H:i:s');
                        $info->data['user_id']      = $this->id;
                        $info->data['temporary']    = 'Si';
                        $info->data['password']     = password_hash($info->data['password'], PASSWORD_DEFAULT);
                        $p_model = new Password();
                        $passwords = $p_model->where(['user_id' => $this->id, 'status' => 'active'])->findAll();
                        foreach ($passwords as $key => $password) {
                            $p_model->save([
                                'id'        => $password->id,
                                'status'    => 'inactive'
                            ]);
                        }
                        return $info;
                    });

                    $this->crud->displayAs([
                        'attempts'      => 'N° Intentos',
                        'status'        => 'Estado',
                        'created_at'    => 'Fecha de creación',
                        'password'      => 'Contraseña',
                        'temporary'     => 'Temporal'
                    ]);
                    break;
                default:
                    break;   
            }
            $output = $this->crud->render();
            if (isset($output->isJSONResponse) && $output->isJSONResponse) {
                header('Content-Type: application/json; charset=utf-8');
                echo $output->output;
                exit;
            }

            $this->viewTable($output, $title, $description);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
