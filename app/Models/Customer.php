<?php

namespace App\Models;

use CodeIgniter\Model;

class Customer extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type_customer_id',
        'type_document_identification_id',
        'user_id',
        'user_origin_id',
        'name',
        'email',
        'identification_number',
        'phone',
        'address',
        'discount_percentage',
        'discount_detail',
        'address_origin',
        'status'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = ['functionBeforeFind'];
    protected $afterFind      = ['functionAfterFind'];//
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected $additionalParams = ["origin" => ""];

    public function setAdditionalParams(array $params)
    {
        $this->additionalParams = $params;
        $dataGet = isset($_GET) ? (object) $_GET : (object) $_POST;
        $params = (object) $this->additionalParams;
        // switch($params->origin){
        //     case 'customer_index':
        //         if(session('user')->role_id == 3)
        //             $this->where(['user_origin_id' => session('user')->id]);
        //         break;
        //     default:
        //         break;
        // }
        
        if(isset($dataGet->name) && !empty($dataGet->name))
            $this->like("customers.name", "%{$dataGet->name}%");

        if(isset($dataGet->user_origin) && !empty($dataGet->user_origin))
            $this->where("customers.user_origin_id", $dataGet->user_origin);
        return $this; // Permite el encadenamiento de métodos
    }


    protected function functionBeforeFind(array $data){
        log_message('info', json_encode($data));
        $params = (object) $this->additionalParams;
        switch($params->origin){
            case 'customer_index':
                $this
                    ->select([
                        'customers.*',
                        'concat(tdi.name, " - ", tdi.code) as document_identification',
                        'u.name as origin_name'
                    ])
                    ->join('type_document_identifications as tdi', 'tdi.id = customers.type_document_identification_id', 'left')
                    ->join('users as u', 'u.id = customers.user_origin_id', 'left');
                break;
            default:
                break;
        }



        return $data;
    }

    protected function functionAfterFind(array $data){
        log_message('info', json_encode($data));
        if (isset($data['data']) && is_array($data['data'])) {
            $params = (object) $this->additionalParams;
            foreach ($data['data'] as &$customer) {
                switch($params->origin){
                    case 'quotes':
                        $customer->branches = $this->builder('invoices')
                            ->select('branch_office')
                            ->distinct('branch_office')->where(['customer_id' => $customer->id, 'branch_office !=' => NULL])->get()->getResult();
                        break;
                    default:
                        break;
                }
                // Agregar un campo calculado, por ejemplo, un precio con descuento
                // $customer->document_identification = $product->value * 0.9; // 10% de descuento
            }
        }
        return $data;
    }
}
