<?php

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\TypeDocument;
use App\Models\HistoryUser;

class DashboardController extends BaseController
{
	protected $i_model;
    protected $hu_model;
    protected $td_model;
    protected $user;

	public function __construct(){
        $this->i_model		= new Invoice();
		$this->hu_model		= new HistoryUser();
		$this->td_model 	= new TypeDocument();
		$this->user 		= session('user')->role_id == 3 ? session('user')->id : null;
    }

	public function index()
	{
		$type_documents = $this->td_model->setAdditionalParams(['origin' => 'home'])->findAll();
		$fechaEspecifica = new \DateTime(session('user')->password->created_at);
		$fechaActual = new \DateTime('now');
		$diferencia = $fechaEspecifica->diff($fechaActual);

		$historial = $this->hu_model
			->select([
				'history_users.*',
				'users.name as user_name',
				'users.photo as user_photo',
				'roles.name as user_rol',
			])
			->join('users', 'users.id = history_users.user_id', 'left')
			->join('roles', 'roles.id = users.role_id', 'left')
		->orderBy('id', 'DESC')->paginate(5);

		$this->i_model->setAdditionalParams(['origin' => 'home']);

		$invoices = $this->i_model
			// ->getFilteredInvoices($this->user)
			->select([
				'invoices.*',
				'td.name as document_name',
				'td.code as document_code',
			])
			->join('type_documents as td', 'td.id = invoices.type_document_id', 'left')
			->orderBy('invoices.id', 'DESC')->findAll();

		$date_init = (clone $fechaActual)->modify('-1 year')->modify('+1 month')->format('Y-m-d 00:00:00');
		$date_now = $fechaActual->format('Y-m-d 23:59:59');
		$inv_dates = $this->i_model
			// ->getFilteredInvoices($this->user)
			->select([
				'YEAR(created_at) as year', 
				'MONTH(created_at) as month', 
				'COUNT(invoices.id) as total_invoices',
				'type_document_id'
			])
			->where([
				'created_at >=' 	=> $date_init,
				'created_at <=' 	=> $date_now
			])
			->groupBy('type_document_id', 'YEAR(created_at)', 'MONTH(created_at)')
			->orderBy('invoices.id', 'DESC')->findAll();
		
		foreach ($type_documents as $key => $td) {
			$td->total = 0;
			$td->dates = [];
			foreach ($invoices as $key => $invoice) if($td->id == $invoice->type_document_id) $td->total += $invoice->payable_amount;
			foreach ($inv_dates as $key => $inv) if($td->id == $inv->type_document_id) $td->dates[] = $inv;
		}

		// var_dump([$type_documents, $invoices]); die;

	  	return  view('pages/home', [
			'day' 		=> (90 - $diferencia->days),
			'type_documents'	=> $type_documents,
			'historial'	=> $historial
		]);
	}

	public function about()
  {
    return view('pages/about');
  }

}
