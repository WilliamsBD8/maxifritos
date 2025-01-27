<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeDocument extends Model
{
    protected $table            = 'type_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'code'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
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
        $this->additionalParams = $params;
        $params = (object) $this->additionalParams;
        switch($params->origin){
            case 'quotes_data':
                if(session('user')->role_id == 3)
                    $this->groupStart()
                        ->where(['user_id' => session('user')->id])
                        ->orWhere('seller_id', session('user')->id)
                    ->groupEnd();
                break;
            default:
                break;
        }
        return $this; // Permite el encadenamiento de métodos
    }

    protected function functionBeforeFind(array $data){
        $getData = (object) $_GET;
        $params = (object) $this->additionalParams;
        log_message('info', json_encode($data));
        switch ($params->origin) {
            case 'home':
            case 'quotes_index':    
                break;
            default:
                // if(session('user')->role_id == 3)
                //     $this->groupStart()
                //         ->where(['i.user_id' => session('user')->id])
                //         ->orWhere('i.seller_id', session('user')->id)
                //     ->groupEnd();
                break;
        }
        return $data;
    }

    protected function functionAfterFind(array $data){
        log_message('info', json_encode($data));
        $params = (object) $this->additionalParams;
        switch ($params->origin) {
            case 'quotes_data':
                $grouped = array_reduce($data['data'], function ($carry, $item) {
                    $doc = $item->document;
                    $id = $item->invoice_id; 
                
                    if (!isset($carry[$doc])) {
                        $carry[$doc] = [
                            "document"          => $doc,
                            "count"             => 0,
                            "payable_amount"    => 0,
                            "products"          => 0,
                            "processed_ids" => [],
                        ];
                    }
                    if ($id !== null && !in_array($id, $carry[$doc]['processed_ids'], true)) {
                        $carry[$doc]['count']++;
                        $carry[$doc]['payable_amount'] += (float)$item->payable_amount;
                        $carry[$doc]['processed_ids'][] = $id;
                    }
                    $carry[$doc]['products'] += (int)$item->quantity;
                    return $carry;
                }, []);
                $result = array_map(function ($group) {
                    unset($group['processed_ids']);
                    return $group;
                }, array_values($grouped));
                $data['data'] = $result;
                break;
            
            default:
                # code...
                break;
        }
        return $data;
    }
}
