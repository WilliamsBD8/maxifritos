<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use CodeIgniter\API\ResponseTrait;

use App\Models\Customer;
use App\Models\TypeDocumentIdentifications;

class CustomersController extends BaseController
{
    use ResponseTrait;
    private $c_model;
    private $tdi_model;
    private $dataTable;

    public function __construct(){
        $this->c_model      = new Customer();
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
        return view('customers/index',[
            'type_documents_identifications'    => $type_documents_identifications
        ]);
    }

    public function data(){
        $data = $this->c_model
            
            
            ->orderBy('id', 'DESC')
            ->paginate($this->dataTable->length, 'dataTable', $this->dataTable->page);
        $count = $this->c_model->countAllResults();
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
                'identification_number' => 'required|integer|greater_than[0]|is_unique[customers.identification_number]',
                'phone'                 => 'required|min_length[10]|is_unique[customers.phone]',
            ];
            $messages = [
                'email' => [
                    'required'    => 'El campo email es obligatorio.',
                    'valid_email' => 'Debe proporcionar un email válido.',
                    'is_unique'     => 'Este email ya está registrado.',
                ],
                'identification_number' => [
                    'required'      => 'El campo numero de identificación es obligatorio.',
                    'integer'       => 'La edad debe ser un número entero.',
                    'greater_than'  => 'La edad debe ser mayor que 0.',
                    'is_unique'     => 'Este numero de identificación ya está registrado.',
                ],
                'phone' => [
                    'required'      => 'El campo numero de telefono es obligatorio.',
                    'min_length'    => 'El numero de telefono debe tener al menos 10 caracteres.',
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
                'user_origin_id'                    => session('user')->id,
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

}
