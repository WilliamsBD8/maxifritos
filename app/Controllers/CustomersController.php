<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use CodeIgniter\API\ResponseTrait;

use App\Models\Customer;
use App\Models\User;
use App\Models\TypeDocumentIdentifications;

class CustomersController extends BaseController
{
    use ResponseTrait;
    private $c_model;
    private $u_model;
    private $tdi_model;
    private $dataTable;

    public function __construct(){
        $this->c_model      = new Customer();
        $this->u_model      = new User();
        $this->tdi_model    = new TypeDocumentIdentifications();
        $this->dataTable    = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 1,
            'page'      => $_GET['page'] ?? ceil(($start - 1) / $length + 1)
        ];
    }

    public function index()
    {
        $type_documents_identifications = $this->tdi_model->findAll();
        $sellers = session('user')->role_id == 3 ? [] : $this->u_model->where(['role_id' => 3])->findAll();
        return view('customers/index',[
            'type_documents_identifications'    => $type_documents_identifications,
            "sellers"                           => $sellers
        ]);
    }

    public function data(){
        $this->c_model->setAdditionalParams(['origin' => 'customer_index']);

        $count = $this->c_model->countAllResults(false);

        $data = $this->c_model
            ->orderBy('id', 'DESC')
            ->paginate($this->dataTable->length, 'dataTable', $this->dataTable->page);

        return $this->respond([
            'data'              => $data,
            'draw'              => $this->dataTable->draw,
            'recordsTotal'      => $count,
            'recordsFiltered'   => $count,
            'post'              => $this->dataTable,
        ]);
    }

    public function created(){
        try{
            $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
            $rules = [
                'email'                 => 'required|valid_email|is_unique[customers.email]',
                'identification_number' => 'required|integer|is_unique[customers.identification_number]',
                'phone'                 => 'required|min_length[6]|is_unique[customers.phone]',
            ];
            $messages = [
                'email' => [
                    'required'    => 'El campo email es obligatorio.',
                    'valid_email' => 'Debe proporcionar un email válido.',
                    'is_unique'     => 'Este email ya está registrado.',
                ],
                'identification_number' => [
                    'required'      => 'El campo numero de identificación es obligatorio.',
                    'integer'       => 'El campo numero de identificación debe ser un número entero.',
                    'is_unique'     => 'Este numero de identificación ya está registrado.',
                ],
                'phone' => [
                    'required'      => 'El campo numero de telefono es obligatorio.',
                    'min_length'    => 'El numero de telefono debe tener al menos 6 caracteres.',
                    'is_unique'     => 'Este numero de telefono ya está registrado.',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return $this->respond([
                    'status'  => 'error',
                    'errors'  => $this->validator->getErrors(),
                    $data
                ]);
            }

            $data->coordenadas = json_decode($data->coordenadas);
            $data->coordenadas = "{$data->coordenadas->lat}, {$data->coordenadas->lng}";

            $data_save = [
                'type_customer_id'                  => 1,
                'type_document_identification_id'   => $data->type_document_identification,
                'user_origin_id'                    => !empty($data->user_origin) ? $data->user_origin : session('user')->id,
                'name'                              => $data->name,
                'email'                             => $data->email,
                'identification_number'             => $data->identification_number,
                'phone'                             => $data->phone,
                'address'                           => $data->address,
                'discount_percentage'               => $data->discount_percentage,
                'discount_detail'                   => $data->discount_detail,
                'address_origin'                    => $data->coordenadas,
            ];
            $this->c_model->save($data_save);

            return $this->respond([
                'status'    => "success",
                'title'     => 'Cliente creado con exito',
                'data'      => $data
            ]);
        }catch(\Exception $e){
            return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit(){
        try{
            $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
            $validationRules = [
                'email' => [
                    'rules' => "required|valid_email|is_unique[customers.email,id,{$data->id_customer}]",
                    'errors' => [
                        'required'    => 'El campo correo electrónico es obligatorio.',
                        'valid_email' => 'Debe ser un correo electrónico válido.',
                        'is_unique'   => 'Este correo ya está registrado en el sistema.'
                    ]
                ],
                'identification_number' => [
                    'rules' => "required|integer|is_unique[customers.identification_number,id,{$data->id_customer}]",
                    'errors' => [
                        'required'      => 'El campo numero de identificación es obligatorio.',
                        'integer'       => 'El campo numero de identificación debe ser un número entero.',
                        'is_unique'     => 'Este numero de identificación ya está registrado.',
                    ]
                ],
                'phone' => [
                    'rules' => "required|min_length[6]|is_unique[customers.phone,id,{$data->id_customer}]",
                    'errors' => [
                        'required'      => 'El campo numero de telefono es obligatorio.',
                        'min_length'    => 'El numero de telefono debe tener al menos 6 caracteres.',
                        'is_unique'     => 'Este numero de telefono ya está registrado.',
                    ]
                ]
            ];
            if (!$this->validate($validationRules)) {
                return $this->respond([
                    'status'  => 'error',
                    'errors'  => $this->validator->getErrors(),
                    $data
                ]);
            }

            $data->coordenadas = json_decode($data->coordenadas);
            $data->coordenadas = "{$data->coordenadas->lat}, {$data->coordenadas->lng}";

            $data_save = [
                'id'                                => $data->id_customer,
                'type_customer_id'                  => 1,
                'type_document_identification_id'   => $data->type_document_identification,
                'user_origin_id'                    => !empty($data->user_origin) ? $data->user_origin : session('user')->id,
                'name'                              => $data->name,
                'email'                             => $data->email,
                'identification_number'             => $data->identification_number,
                'phone'                             => $data->phone,
                'address'                           => $data->address,
                'discount_percentage'               => $data->discount_percentage,
                'discount_detail'                   => $data->discount_detail,
                'address_origin'                    => $data->coordenadas,
                'status'                            => $data->status
            ];
            $this->c_model->save($data_save);

            return $this->respond([
                'status'    => "success",
                'title'     => 'Cliente actualizado con exito',
                'data'      => $data
            ]);
        }catch(\Exception $e){
            return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete(){
        try{
            $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
            if (is_null($data->id_customer) || !is_numeric($data->id_customer)) {
                return $this->respond([
                    'icon' => 'error',
                    'title' => 'ID inválido o no proporcionado.',
                ], 400);
            }
            $customer = $this->c_model->find($data->id_customer);
            if (!$customer) {
                return $this->respond([
                    'icon' => 'error',
                    'title' => 'El registro no existe.',
                ], 404);
            }

            $this->c_model->delete($data->id_customer);
            return $this->respond([
                'icon' => 'success',
                'title' => 'Cliente eliminado correctamente.',
            ], 200);
        }catch(\Exception $e){
            return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
        }
    }

}
