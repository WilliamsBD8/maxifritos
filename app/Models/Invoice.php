<?php

namespace App\Models;

use CodeIgniter\Model;

use CodeIgniter\Config\Services;

class Invoice extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_id',
        'seller_id',
        'user_id',
        'type_document_id',
        'status_id',
        'resolution',
        'resolution_reference',
        'address',
        'note',
        'branch_office',
        'invoice_amount',
        'payable_amount',
        'discount_amount',
        'discount_percentage',
        'address_origin',
        'delivery_date'
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
    protected $beforeFind     = ["functionBeforeFind"];
    protected $afterFind      = ["functionAfterFind"];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected $additionalParams = ["origin" => ""];

    public function setAdditionalParams(array $params)
    {

        $request = Services::request();
        $getData = !empty($request->getVar()) ? (object)$request->getVar() : $request->getJson();

        $this->additionalParams = $params;
        $params = (object) $this->additionalParams;
        switch($params->origin){
            case 'quotes_data':
            case 'home':
                if (session()->has('user') && session('user')->role_id == 3) {
                    $this->groupStart()
                        ->where('invoices.user_id', session('user')->id)
                        ->orWhere('invoices.seller_id', session('user')->id)
                        ->groupEnd();
                }
                break;
            default:
                break;
        }
        if(isset($getData->date_init) && isset($getData->date_end)){
            $this->where([
                'invoices.created_at >=' => "{$getData->date_init} 00:00:00",
                'invoices.created_at <=' => "{$getData->date_end} 23:59:59"  
            ]);
        }

        if(isset($getData->resolution) && !empty($getData->resolution))
            $this->where([
                'invoices.resolution' => $getData->resolution,
            ]);

        if(isset($getData->customer_id) && !empty($getData->customer_id))
            $this->where([
                'invoices.customer_id' => $getData->customer_id,
            ]);

        if(isset($getData->type_document) && !empty($getData->type_document))
            $this->where([
                'invoices.type_document_id' => $getData->type_document,
            ]);
        if(isset($getData->delivery_date) && !empty($getData->delivery_date))
            $this->where([
                'invoices.delivery_date' => $getData->delivery_date,
            ]);
            
        if(isset($getData->product_id) && !empty($getData->product_id)){
            $this->where([
                'line_invoices.product_id' => $getData->product_id,
            ]);
        }

        if(isset($getData->seller_id) && !empty($getData->seller_id)){
            $this->where([
                'invoices.seller_id' => $getData->seller_id,
            ]);
        }

        return $this;
    }

    public function getLineInvoice($id){
        $data = $this->builder('line_invoices')
            ->where(['invoice_id' => $id])->get()->getResult();
        return $data;
    }

    public function getLineInvoices($id){
        $data = $this->builder('line_invoices')
            ->select([
                'line_invoices.*',
                'products.name as product_name',
                'products.code as product_code',
            ])
            ->join('products', 'products.id = line_invoices.product_id', 'left')
            ->where(['invoice_id' => $id])->get()->getResult();
        return $data;
    }

    public function getCustomer($id){
        $data = $this->builder('customers')
            ->select([
                'customers.*',
                'type_document_identifications.code as type_document'
            ])
            ->join('type_document_identifications', 'customers.type_document_identification_id = type_document_identifications.id', 'left')
            ->where(['customers.id' => $id])->get()->getResult();
        return $data[0];
    }

    public function getSeller($id){
        $data = $this->builder('users')
            ->where(['id' => $id])->get()->getResult();
        return $data[0];
    }

    public function getFilteredInvoices($user_id = null)
    {
        if ($user_id != null) {
            $this->where(['invoices.user_id' => $user_id]);
        }
        return $this;
    }

    protected function functionBeforeFind(array $data){
        $getData = !empty($_GET) ? (object) $_GET : (object) $_POST;
        if (!empty($data['method']) && $data['method'] === 'findAll') {
            // if(session('user')->role_id == 3 && $data['limit'] == 10)
            //     $this->where(['user_id' => session('user')->id])
            //         ->orWhere('user_id', session('user')->id);

            
        }
        return $data;
    }

    protected function functionAfterFind(array $data){
        $params = (object) $this->additionalParams;
        switch ($params->origin) {
            case 'load_order':
                $data['data'] = ['data' => array_values($data['data'])];
                $products = array_reduce($data['data']['data'], function ($carry, $item) {
                    $id = $item->product_id;
                    $carry[$id] = [
                        "product_id"    => $id,
                        "name"          => $item->name,
                        "code"          => $item->code,
                    ];
                    return $carry;
                }, []);
                $customers = array_reduce($data['data']['data'], function ($carry, $item) {
                    $id = $item->id_customer;
                    $carry[$id] = [
                        "id_customer"   => $id,
                        "name"          => $item->customer,
                    ];
                    return $carry;
                }, []);
                $data['data']['products'] = array_values($products);
                $data['data']['customers'] = array_values($customers);
                break;
            case 'quotes_data':
                foreach ($data['data'] as $key => $inv) {
                    $ref = $this->builder('invoices')->where(['id' => $inv->resolution_reference])->get()->getResult();
                    $inv->inv_resolution = !empty($ref) ? $ref[0]->resolution : 'No Aplica';
                }
                break;
            
            default:
                # code...
                break;
        }
        return $data;
    }
}
