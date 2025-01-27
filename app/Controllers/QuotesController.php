<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\Invoice;
use App\Models\Status;
use App\Models\Customer;
use App\Models\Product;
use App\Models\TypeDocument;
use App\Models\User;

use CodeIgniter\API\ResponseTrait;

class QuotesController extends BaseController
{
    use ResponseTrait;

    protected $i_model;
    protected $td_model;
    protected $p_model;
    protected $dataTable;
    protected $columns;
    protected $invoices;
    protected $user;

    public function __construct(){
        $this->i_model            = new Invoice();
        $this->td_model           = new TypeDocument();
        $this->p_model            = new Product();
        $this->dataTable                = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 1,
            'page'      => $_GET['page'] ?? ceil(($start - 1) / $length + 1)
        ];
        $this->columns = $_GET['columns'] ?? [];
		$this->user 		= session('user')->role_id == 3 ? session('user')->id : null;
        $this->invoices = $this->i_model->orderBy('invoices.id', 'DESC');
    }
    
    public function index(){
        $s_model = new Status();
        $td_model = new TypeDocument();
        $status = $s_model->findAll();
        $type_documents = $td_model->setAdditionalParams(['origin' => 'quotes_index'])->findAll();

        $periods = [
            (object) ['value' => "", 'name' => 'Personalizado', "selected" => false],
            (object) ['value' => "day", 'name' => 'Hoy', "selected" => false],
            (object) ['value' => "yesterday", 'name' => 'Ayer', "selected" => false],
            (object) ['value' => "weekend", 'name' => 'Esta semana', "selected" => false],
            (object) ['value' => "last_weekend", 'name' => 'Semana Pasada', "selected" => false],
            (object) ['value' => "month", 'name' => 'Este mes', "selected" => !false],
            (object) ['value' => "last_month", 'name' => 'Mes pasado', "selected" => false],
        ];

        foreach ($periods as $key => $period) {
            $period->dates = getPeriodDate($period->value);
        }

        return view('quotes/index', [
            'status'            => $status,
            'type_documents'    => $type_documents,
            'periods'           => $periods
        ]);
    }

    public function data(){
        $dataPost = (object) $this->request->getGet();

        $this->invoices->setAdditionalParams(['origin' => 'quotes_data']);

        $count_data = $this->invoices->countAllResults(false);
        $data = $this->invoices
            ->select([
                'invoices.*',
                'c.name as customer',
                's.name as seller',
                'td.name as td_name',
                'u.name as u_name',
            ])
            ->join('type_documents as td', 'td.id = invoices.type_document_id', 'left')
            ->join('customers as c', 'c.id = invoices.customer_id', 'left')
            ->join('users as s', 's.id = invoices.seller_id', 'left')
            ->join('users as u', 'u.id = invoices.user_id', 'left');
        
        $data = $this->dataTable->length == -1 ? $data->findAll() : $data->paginate($this->dataTable->length, 'dataTable', $this->dataTable->page);

        $this->td_model->setAdditionalParams(['origin' => 'quotes_data']);

        $indicadores = [];
        if($this->dataTable->length > 0){
            $indicadores = $this->td_model
                ->select([
                    'type_documents.id as document',
                    'i.id as invoice_id',
                    'COALESCE(i.payable_amount, 0) as payable_amount',
                    'COALESCE(li.quantity, 0) as quantity'
                    // 'COALESCE(COUNT(DISTINCT i.id), 0) as count', // Devuelve 0 si no hay facturas
                    // 'COALESCE(SUM(i.payable_amount), 0) as payable_amount', // Devuelve 0 si no hay monto
                    // 'COALESCE(SUM(li.quantity), 0) as products' // Devuelve 0 si no hay monto
                ])
                ->join('invoices i', "i.type_document_id = type_documents.id AND i.created_at BETWEEN '{$dataPost->date_init} 00:00:00' AND '{$dataPost->date_end} 23:59:59'", 'left') // LEFT JOIN para incluir todos los documentos
                ->join('line_invoices li', "li.invoice_id = i.id", 'left') // LEFT JOIN para incluir todos los documentos
                // ->groupBy('type_documents.id', 'i.id', 'li.product_id') // Agrupa por documento
                ->findAll();
        }
        return $this->respond([
            'data'              => $data,
            'draw'              => $this->dataTable->draw,
            'recordsTotal'      => $count_data,
            'recordsFiltered'   => $count_data,
            'post'              => $this->dataTable,
            'indicadores'       => $indicadores
        ]);
    }

    public function new(){
        $c_model = new Customer();
        $p_model = new Product();
        $u_model = new User();
        $customers = $c_model->where(['type_customer_id' => 1, 'customers.status' => 'active'])->findAll();
        $products = $p_model->where(['status' => 'active'])->findAll();
        $sellers    = $u_model->where(['role_id' => 3, 'status' => 'active'])->findAll();
        return view('quotes/created', [
            'customers' => $customers,
            'sellers'   => $sellers,
            'products'  => $products
        ]);
    }

    public function editar($id){
        $c_model = new Customer();
        $p_model = new Product();
        $u_model = new User();
        $customers = $c_model->where(['type_customer_id' => 1, 'customers.status' => 'active'])->findAll();
        $products = $p_model->where(['status' => 'active'])->findAll();
        $invoice = $this->invoices->find($id);
        $invoice->line_invoice = $this->i_model->getLineInvoice($invoice->id);
        $sellers    = $u_model->where(['role_id' => 3, 'status' => 'active'])->findAll();
        return view('quotes/edit', [
            'customers' => $customers,
            'products'  => $products,
            'invoice'   => $invoice,
            'sellers'   => $sellers,
        ]);
    }

    public function invoice($id){
        $invoice = $this->invoices->find($id);
        $invoice->line_invoices = $this->i_model->getLineInvoices($invoice->id);
        $invoice->customer = $this->i_model->getCustomer($invoice->customer_id);
        $invoice->seller = $this->i_model->getSeller($invoice->seller_id);
        return view('quotes/invoice', [
            'invoice' => $invoice
        ]);
    }

    public function products(){
        $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
        $products = $this->p_model
        ->select([
            'products.*',
            "COALESCE(
                (SELECT li.value
                FROM line_invoices li
                JOIN invoices i ON i.id = li.invoice_id and i.type_document_id = 2
                WHERE i.customer_id = {$data->customer}
                AND li.product_id = products.id
                ORDER BY li.created_at DESC
                LIMIT 1), products.value) AS value"
        ])
        ->where(['products.status' => 'active'])->findAll();
        return $this->respond(['data' => $products, 'info'  => $data]);
    }
}
