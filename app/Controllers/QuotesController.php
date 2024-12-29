<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\Invoice;
use App\Models\Status;
use App\Models\Customer;
use App\Models\Product;
use App\Models\TypeDocument;

use CodeIgniter\API\ResponseTrait;

class QuotesController extends BaseController
{
    use ResponseTrait;

    protected $i_model;
    protected $p_model;
    protected $dataTable;
    protected $columns;
    protected $invoices;
    protected $user;

    public function __construct(){
        $this->i_model            = new Invoice();
        $this->p_model            = new Product();
        $this->dataTable                = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 1,
            'page'      => $_GET['page'] ?? ceil(($start - 1) / $length + 1)
        ];
        $this->columns = $_GET['columns'] ?? [];
		$this->user 		= session('user')->role_id == 3 ? session('user')->id : null;
        $this->invoices = $this->i_model->getFilteredInvoices($this->user)->orderBy('invoices.id', 'DESC');
    }
    
    public function index(){
        $s_model = new Status();
        $td_model = new TypeDocument();
        $status = $s_model->findAll();
        $type_documents = $td_model->findAll();
        $periods = [
            (object) ['value' => "", 'name' => 'Personalizado'],
            (object) ['value' => "day", 'name' => 'Hoy'],
            (object) ['value' => "yesterday", 'name' => 'Ayer'],
            (object) ['value' => "weekend", 'name' => 'Esta semana'],
            (object) ['value' => "last_weekend", 'name' => 'Semana Pasada'],
            (object) ['value' => "month", 'name' => 'Este mes'],
            (object) ['value' => "last_month", 'name' => 'Mes pasado'],
        ];
        return view('quotes/index', [
            'status'            => $status,
            'type_documents'    => $type_documents,
            'periods'           => $periods
        ]);
    }

    public function data(){
        $dataPost = (object) $this->request->getGet();
        $data = $this->invoices
            ->select([
                'invoices.*',
                'c.name as customer',
                's.name as seller',
                'td.name as td_name',
                'u.name as u_name',
            ])
            ->where([
                'invoices.created_at >=' => "{$dataPost->date_init} 00:00:00",
                'invoices.created_at <=' => "{$dataPost->date_end} 23:59:59"
            ])
            ->join('type_documents as td', 'td.id = invoices.type_document_id', 'left')
            ->join('customers as c', 'c.id = invoices.customer_id', 'left')
            ->join('customers as s', 's.id = invoices.seller_id', 'left')
            ->join('users as u', 'u.id = invoices.user_id', 'left');
        
        $data = $this->dataTable->length == -1 ? $data->findAll() : $data->paginate($this->dataTable->length, 'dataTable', $this->dataTable->page);

        $indicadores = $this->invoices
            ->select([
                'invoices.type_document_id as document',
                'COUNT(invoices.id) as count',
                'SUM(invoices.payable_amount) as payable_amount'
            ])
            ->where([
                'invoices.created_at >=' => "{$dataPost->date_init} 00:00:00",
                'invoices.created_at <=' => "{$dataPost->date_end} 23:59:59"
            ])
            ->groupBy('invoices.type_document_id')
            ->findAll();

        $count_data = $this->invoices->where([
            'invoices.created_at >=' => "{$dataPost->date_init} 00:00:00",
            'invoices.created_at <=' => "{$dataPost->date_end} 23:59:59"
        ])->countAllResults();
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
        $customers = $c_model->where(['status' => 'active'])->findAll();
        $products = $p_model->where(['status' => 'active'])->findAll();
        return view('quotes/created', [
            'customers' => $customers,
            'products'  => $products
        ]);
    }

    public function editar($id){
        $c_model = new Customer();
        $p_model = new Product();
        $customers = $c_model->where(['status' => 'active'])->findAll();
        $products = $p_model->where(['status' => 'active'])->findAll();
        $invoice = $this->invoices->find($id);
        $invoice->line_invoice = $this->i_model->getLineInvoice($invoice->id);
        return view('quotes/edit', [
            'customers' => $customers,
            'products'  => $products,
            'invoice'   => $invoice
        ]);
    }

    public function invoice($id){
        $invoice = $this->invoices->find($id);
        $invoice->line_invoices = $this->i_model->getLineInvoices($invoice->id);
        $invoice->customer = $this->i_model->getCustomer($invoice->customer_id);
        $invoice->seller = $this->i_model->getCustomer($invoice->seller_id);
        return view('quotes/invoice', [
            'invoice' => $invoice
        ]);
    }

    public function products(){
        $data = $this->request->getJson();
        $products = $this->p_model
        ->select([
            'products.*',
            "COALESCE(
                (SELECT li.value
                FROM line_invoices li
                JOIN invoices i ON i.id = li.invoice_id
                WHERE i.customer_id = {$data->customer}
                AND li.product_id = products.id
                ORDER BY li.created_at DESC
                LIMIT 1), products.value) AS value"
        ])
        ->where(['products.status' => 'active'])->findAll();
        return $this->respond(['data' => $products, 'info'  => $data]);
    }
}
