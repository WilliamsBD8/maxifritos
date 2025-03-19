<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\TypeDocument;

use CodeIgniter\API\ResponseTrait;

class ReportsController extends BaseController
{

    use ResponseTrait;

    private $i_model;
    private $c_model;
    private $p_model;
    private $td_model;
    private $dataTable;

    public function __construct(){
        $this->i_model      = new Invoice();
        $this->c_model      = new Customer();
        $this->p_model      = new Product();
        $this->u_model      = new User();
        $this->td_model     = new TypeDocument();
        $this->dataTable    = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 1,
            'page'      => $_GET['page'] ?? ceil(($start - 1) / $length + 1)
        ];

        // $this->i_model->setAdditionalParams(["origin" => ""]);
    }

    public function index(){

        $type_documents = $this->dataIdx();

        $sellers = $this->u_model->where(['role_id' => 3])->findAll();
        
        // return $this->respond($type_documents);

        return view('reports/index', [
            'documents' => $type_documents,
            'sellers'   => $sellers
        ]);
    }

    public function dataIndex(){
        return $this->respond([
            'data'      => $this->dataIdx()
        ]);
    }

    private function dataIdx(){
        $startOfWeek = date('Y-m-d', strtotime('-7 days'));
        $startOfYear = date('Y-01-01');
        $now = date('Y-m-d');

        $type_documents = $this->td_model->findAll();
        foreach ($type_documents as $key => $type_document) {

            $type_document->data_day = $this->i_model
                ->setAdditionalParams(["origin" => ""])
                ->distinct('invoices.customer_id')
                ->select([
                    'IFNULL(SUM(invoices.payable_amount), 0) as total',
                    'IFNULL(COUNT(invoices.id), 0) as total_inv',
                    'IFNULL(COUNT(invoices.customer_id), 0) as total_cus',
                ])
                ->where([
                    'type_document_id'          => $type_document->id,
                    'DATE(invoices.created_at)' => $now
                ])->groupBy('DAY(invoices.created_at)')
            ->first();

            $type_document->customers = $this->i_model
                ->setAdditionalParams(["origin" => ""])
                ->select([
                    'IFNULL(SUM(invoices.payable_amount), 0) as total',
                    'invoices.customer_id',
                    'c.name'
                ])
                ->where([
                    'type_document_id' => $type_document->id
                ])
                ->join('customers as c', 'c.id = invoices.customer_id', 'left')
                ->groupBy('invoices.customer_id')
                ->orderBy('total', 'DESC')
            ->paginate(5);

            $type_document->sellers = $this->i_model
                ->setAdditionalParams(["origin" => ""])
                ->select([
                    'IFNULL(SUM(invoices.payable_amount), 0) as total',
                    'invoices.seller_id',
                    'u.name'
                ])
                ->where([
                    'type_document_id' => $type_document->id
                ])
                ->join('users as u', 'u.id = invoices.seller_id', 'left')
                ->groupBy('invoices.seller_id')
                ->orderBy('total', 'DESC')
            ->paginate(5);

            
            $type_document->data = $this->i_model
                ->setAdditionalParams(["origin" => ""])
                ->select([
                    'IFNULL(SUM(invoices.payable_amount), 0) as total',
                    'DATE(invoices.created_at) as fecha'
                    // 'MONTH(invoices.created_at) as mes',
                    // 'YEAR(invoices.created_at) as anio',
                ])
                ->where([
                    'type_document_id'              => $type_document->id,
                    'DATE(invoices.created_at) >='  => $startOfWeek,
                    'DATE(invoices.created_at) <='  => $now
                ])->groupBy('fecha')
                // ])->groupBy('MONTH(invoices.created_at)', 'YEAR(invoices.created_at)')
                ->findAll();

            $type_document->month = $this->i_model
                ->setAdditionalParams(["origin" => ""])
                ->select([
                    'IFNULL(SUM(invoices.payable_amount), 0) as total',
                    'DAY(invoices.created_at) as fecha'
                ])
                ->where([
                    'type_document_id'              => $type_document->id,
                    'MONTH(invoices.created_at)'    => date('m'),
                    'YEAR(invoices.created_at)'     => date('Y')
                ])
                ->groupBy('fecha') // Agrupa por día del mes
                ->orderBy('fecha', 'ASC') // Ordena por día del mes
                ->findAll();
            

            $type_document->total = (float) array_reduce($type_document->data, function($carry, $data){
                $data = (object) $data;
                return $carry += $data->total;
            }, 0);

            $type_document->data_year = $this->i_model
                ->setAdditionalParams(["origin" => ""])
                ->select([
                    'IFNULL(SUM(invoices.payable_amount), 0) as total',
                    'MONTH(invoices.created_at) as mes',
                    'YEAR(invoices.created_at) as anio',
                ])
                ->where([
                    'type_document_id'              => $type_document->id,
                    'DATE(invoices.created_at) >='  => $startOfYear,
                    'DATE(invoices.created_at) <='  => $now
                // ])->groupBy('fecha')
                ])->groupBy('MONTH(invoices.created_at)', 'YEAR(invoices.created_at)')
                ->findAll();

            $type_document->semanal = $this->i_model
                ->setAdditionalParams(["origin" => ""])
                ->select([
                    'IFNULL(SUM(invoices.payable_amount), 0) as total',
                    'DATE(invoices.created_at) as fecha'
                ])
                ->where([
                    'type_document_id' => $type_document->id
                ])
                ->where('DATE(invoices.created_at) >=', date('Y-m-d', strtotime('monday this week')))
                ->where('DATE(invoices.created_at) <=', date('Y-m-d', strtotime('sunday this week')))
                ->groupBy('fecha')
                ->orderBy('fecha', 'ASC')
            ->findAll();

            switch ($type_document->id) {
                case '1':
                    $type_document->icon    = "ri-money-dollar-circle-line";
                    $type_document->color   = (object) ["class" => "primary", "rgb" => "#8e24aa"];
                    break;
                
                default:
                    $type_document->icon    = "ri-money-dollar-circle-fill";
                    $type_document->color   = (object) ["class" => "warning", "rgb" => "#fdb528"];
                    break;
            }

        }

        return $type_documents;
    }
    
    public function customers()
    {
        $periods = getPeriod();

        $customers      = $this->i_model->distinct('invoices.customer_id')
            ->select([
                'customers.id',
                'customers.name'
            ])
            ->join('customers', 'customers.id = invoices.customer_id', 'left')
            ->findAll();

        $products       = $this->i_model
            ->distinct('line_invoices.product_id')
            ->select([
                'products.id',
                'products.name',
                'products.code',
            ])
            ->join('line_invoices', 'line_invoices.invoice_id = invoices.id', 'left')
            ->join('products', 'line_invoices.product_id = products.id', 'left')
            ->findAll();

        $type_documents = $this->i_model
            ->distinct('invoices.type_document_id')
            ->select([
                'type_documents.id',
                'type_documents.name',
                'type_documents.code',
            ])
            ->join('type_documents', 'type_documents.id = invoices.type_document_id', 'left')
        ->findAll();

        return view('reports/customers', [
            'periods'           => $periods,
            'customers'         => $customers,
            'products'          => $products,
            'type_documents'    => $type_documents
        ]);
    }

    public function sellers(){
        $periods    = getPeriod();
        $sellers    = $this->i_model->distinct('invoices.seller_id')
            ->select([
                'users.id',
                'users.name'
            ])
            ->join('users', 'users.id = invoices.seller_id', 'left')
            ->findAll();
            $type_documents = $this->i_model
            ->distinct('invoices.type_document_id')
            ->select([
                'type_documents.id',
                'type_documents.name',
                'type_documents.code',
            ])
            ->join('type_documents', 'type_documents.id = invoices.type_document_id', 'left')
        ->findAll();

        $products       = $this->i_model
            ->distinct('line_invoices.product_id')
            ->select([
                'products.id',
                'products.name',
                'products.code',
            ])
            ->join('line_invoices', 'line_invoices.invoice_id = invoices.id', 'left')
            ->join('products', 'line_invoices.product_id = products.id', 'left')
            ->findAll();

        return view('reports/sellers', [
            'periods'           => $periods,
            'sellers'           => $sellers,
            'products'          => $products,
            'type_documents'    => $type_documents
        ]);
    }

    public function data($type){
        $this->i_model
            ->join('line_invoices', 'line_invoices.invoice_id = invoices.id', 'left')
            ->join('products', 'line_invoices.product_id = products.id', 'left')
            ->join('customers', 'customers.id = invoices.customer_id', 'left')
            ->join('users as seller', 'seller.id = invoices.seller_id', 'left')
            ->join('type_documents', 'type_documents.id = invoices.type_document_id', 'left');
        // switch($type){
        //     case 'sellers':
        //         $this->i_model
        //             ->join('users as seller', 'seller.id = invoices.seller_id', 'left')
        //             ->join('type_documents', 'type_documents.id = invoices.type_document_id', 'left');
        //         $selected = [
        //             'seller.name as seller_name',
        //         ];
        //         break;
        //     default:
        //         $selected = [
        //             'products.name as product_name',
        //             'customers.name as customer_name',
        //             'line_invoices.quantity',
        //             'line_invoices.value',
        //             'line_invoices.discount_percentage',
        //             'line_invoices.product_id',
        //         ];
        //         break;
        // }

        $data_count_total = $this->i_model->countAllResults(false);
        $this->i_model->setAdditionalParams(['origin' => ""]);
        $data_count = $this->i_model->countAllResults(false);

        $this->i_model->select([
                'invoices.*',
                'products.name as product_name',
                'customers.name as customer_name',
                'line_invoices.quantity',
                'line_invoices.value',
                'line_invoices.discount_percentage',
                'line_invoices.product_id',
                'seller.name as seller_name',
                'CONCAT(type_documents.name, " - ", type_documents.code) as type_document_name'
        ])->orderBy('invoices.id', 'DESC');
        $inv_model = clone $this->i_model;
        $data = $this->dataTable->length == -1 ? $this->i_model->findAll() : $this->i_model->paginate($this->dataTable->length, 'dataTable', $this->dataTable->page);
        $indicadores = $this->Indicadores();
        return $this->respond([
            'data'              => $data,
            'draw'              => $this->dataTable->draw,
            'recordsTotal'      => $data_count_total,
            'recordsFiltered'   => $data_count,
            'indicadores'       => $indicadores
        ]);
    }

    private function Indicadores(){
    
        $invoices = $this->i_model
            ->select([
                'line_invoices.*'
            ])
            ->join('line_invoices', 'line_invoices.invoice_id = invoices.id', 'left')
            ->join('products', 'line_invoices.product_id = products.id', 'left')
            ->join('customers', 'customers.id = invoices.customer_id', 'left')
            ->setAdditionalParams(['origin' => ""])->findAll();
        
        $data = (object) [
            "total_value"   => array_reduce($invoices, function($carry, $inv){
                $inv = (object) $inv;
                if($inv->discount_amount != 0) $value = (float) $inv->value - (float) $inv->discount_amount;
                else if($inv->discount_percentage != 0) $value = (float) $inv->value - (((int) $inv->discount_percentage / 100) * (float) $inv->value);
                else $value = $inv->value;
                return $carry += $inv->quantity * $value;
            }, 0),

            "quantity"      => array_reduce($invoices, function($carry, $inv){
                $inv = (object) $inv;
                return $carry += $inv->quantity;
            }, 0)
        ];
    
        return $data;  // Usa `findAll()` si necesitas más registros
    }
    
}
