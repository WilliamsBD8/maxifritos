<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\Invoice;
use App\Models\LineInvoice;

use CodeIgniter\API\ResponseTrait;

use Mpdf\Mpdf;

class InvoiceController extends BaseController
{
    use ResponseTrait;

    public function __construct(){
        $this->i_model    = new Invoice();
        $this->li_model = new LineInvoice();
    }

    public function created()
    {
        try{
            $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
            $invoice = $this->i_model->where(['type_document_id' => $data->type_document])->orderBy('id', 'DESC')->first();

            $data->coordenadas = json_decode($data->coordenadas);
            $data->coordenadas = "{$data->coordenadas->lat}, {$data->coordenadas->lng}";

            $dataInvoice = [
                'customer_id'           => $data->customer_id,
                'seller_id'             => $data->seller_id,
                'user_id'               => session('user')->id,
                'type_document_id'      => $data->type_document, 
                'address'               => $data->address,
                'note'                  => $data->notes,
                'status_id'             => isset($data->resolution_reference) ? 2 : 1,
                'resolution'            => !empty($invoice) ? $invoice->resolution + 1 : 1,
                'resolution_reference'  => isset($data->resolution_reference) ? $data->resolution_reference : null,
                'description'           => null,
                'invoice_amount'        => isset($data->invoice_amount) ? $data->invoice_amount:$data->value_invoice,
                'payable_amount'        => isset($data->payable_amount) ? $data->payable_amount : ($data->value_invoice - $data->value_descount),
                'discount_amount'       => $data->discount_amount,
                'discount_percentage'   => isset($data->discount_percentage) ? $data->discount_percentage:$data->discount_percentaje,
                'address_origin'        => $data->coordenadas
            ];
            if($this->i_model->save($dataInvoice)){
                $invoice_id = $this->i_model->insertID();
                foreach($data->products as $product){
                    $product = validUrl() ? $product : (object) $product;
                    $this->li_model->save([
                        'product_id'            => isset($product->product_id) ? $product->product_id : $product->id,
                        'invoice_id'            => $invoice_id,
                        'quantity'              => $product->quantity,
                        'value'                 => $product->value,
                        'discount_amount'       => $product->discount_amount,
                        'discount_percentage'   => $product->discount_percentage
                    ]);
                }
            }
            switch ($data->type_document) {
                case '2':
                    if($this->i_model->save([
                        'id' => $data->resolution_reference,
                        'status_id' => 2
                    ])){
                        $msg = 'Cotización remisionada con éxito';
                    }else{
                        return $this->respond(['title' => 'Error al actualizar la cotización', 'msg' => 'No se pudo actualizar el estado de la cotización'], 500);
                    }
                    break;
                
                default:
                    $msg = 'Cotización realizada con éxito';
                    break;
            }

            return $this->respond(['title' => $msg]);
        }catch(\Exception $e){
			return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
		}
    }

    public function edit(){
        try{
            $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
            $dataInvoice = [
                'id'                    => $data->id,
                'customer_id'           => $data->customer_id,
                'seller_id'             => $data->seller_id,
                'user_id'               => session('user')->id,
                'status_id'             => 1,
                'address'               => $data->address,
                'note'                  => $data->notes,
                'invoice_amount'        => $data->value_invoice,
                'payable_amount'        => $data->value_invoice - $data->value_descount,
                'discount_amount'       => $data->discount_amount,
                'discount_percentage'   => $data->discount_percentaje
            ];
            if($this->i_model->save($dataInvoice)){
                foreach($data->products as $product){
                    if($product->isDelete && $product->line_invoice_id != null){
                        $this->li_model->delete($product->line_invoice_id);
                    }else if(!$product->isDelete){
                        $data_save = [
                            'product_id'            => $product->id,
                            'quantity'              => $product->quantity,
                            'value'                 => $product->value,
                            'discount_amount'       => $product->discount_amount,
                            'discount_percentage'   => $product->discount_percentage
                        ];
                        if($product->line_invoice_id != null) $data_save['id'] = $product->line_invoice_id;
                        else $data_save['invoice_id'] = $data->id;
                        $this->li_model->save($data_save);
                    }
                }
            }
            return $this->respond(['title' => 'Cotización editada con éxito', $dataInvoice]);
        }catch(\Exception $e){
            return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function decline(){
        try{
            $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
            $dataSave = [
                'id'    => $data->id,
                'status_id' => 3,
                'invoice_amount'   => 0,
                'payable_amount'   => 0,
                'discount_amount'   => 0,
                'discount_percentage'   => 0,
            ];
            $this->i_model->save($dataSave);
            $line_invoices = $this->li_model->where(['invoice_id' => $data->id])->findAll();
            foreach ($line_invoices as $key => $line) {
                $line_data = [
                    'id'                    => $line->id,
                    'quantity'              => 0,
                    'value'                 => 0,
                    'discount_amount'       => 0,
                    'discount_percentage'   => 0
                ];
                $this->li_model->save($line_data);
            }
            return $this->respond(['title' => 'Cotización rechazada con exito.']);
        }catch(\Exception $e){
			return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);  
        }
    }

    public function download($id){
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new Mpdf([
			'mode'          => 'utf-8',
			'format'        => 'Letter',
			"margin_left"   => 5,
			"margin_right"  => 5,
			"margin_top"    => 5,
			"margin_bottom" => 17,
			"margin_header" => 0
		]);
        $mpdf->SetHTMLFooter('
        	<hr>
			<table width="100%">
				<tr>
					<td width="50%" align="left">Software elaborado por IPlanet Colombia SAS</td>
					<td width="50%" align="right">Pagina {PAGENO}/{nbpg}</td>
				</tr>
			</table>
		');
        $invoice = $this->i_model
            ->select([
                'invoices.*',
                'type_documents.name as name_document'
            ])
            ->join('type_documents', 'type_documents.id = invoices.type_document_id', 'left')
            ->find($id);
        $invoice->line_invoices = $this->i_model->getLineInvoices($invoice->id);
        $invoice->customer = $this->i_model->getCustomer($invoice->customer_id);
        $invoice->seller = $this->i_model->getCustomer($invoice->seller_id);
        $page = view('pdf/invoice', [
            'invoice' => $invoice
        ]);
        $css = file_get_contents(base_url(['pdf/invoice.css']));
        $inter = file_get_contents(base_url(['pdf/inter.css']));
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($inter, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($page);
        $mpdf->Output("{$invoice->name_document}_{$invoice->resolution}.pdf", 'I');
    }
}
