<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\TypeDocument;

use CodeIgniter\API\ResponseTrait;

class ProductsController extends BaseController
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
        $this->td_model     = new TypeDocument();
        $this->dataTable    = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 1,
            'page'      => $_GET['page'] ?? ceil(($start - 1) / $length + 1)
        ];
    }
}
