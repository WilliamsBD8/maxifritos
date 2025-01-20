<?php

namespace App\Models;

use CodeIgniter\Model;

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
        'invoice_amount',
        'payable_amount',
        'discount_amount',
        'discount_percentage',
        'address_origin'
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
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

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

    public function getFilteredInvoices($user_id = null)
    {
        if ($user_id != null) {
            $this->where(['invoices.user_id' => $user_id]);
        }
        return $this;
    }
}
