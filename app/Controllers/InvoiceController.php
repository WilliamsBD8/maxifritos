<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use Config\Services;

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

            $value_total = array_reduce($data->products, function($carry, $product){
                $product = (object) $product;
                return $carry += $product->quantity * $product->value;
            }, 0);

            if($data->discount_amount > 0)
                $discount = $data->discount_amount;
            else if($data->discount_percentage > 0)
                $discount = ($data->discount_percentage / 100) * $value_total;
            else
                $discount = array_reduce($data->products, function($carry, $product){
                    $product = (object) $product;
                    $value = $product->discount_percentage == 0 ? $carry : ($product->discount_percentage / 100) * $product->value;
                    return $carry += $product->quantity * $value;
                }, 0);

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
                'invoice_amount'        => $value_total,
                'payable_amount'        => $value_total - $discount,
                'discount_amount'       => $data->discount_amount,
                'discount_percentage'   => $data->discount_percentage,
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

            $value_total = array_reduce($data->products, function($carry, $product){
                $product = (object) $product;
                return $carry += $product->quantity * $product->value;
            }, 0);

            if($data->discount_amount > 0)
                $discount = $data->discount_amount;
            else if($data->discount_percentage > 0)
                $discount = ($data->discount_percentage / 100) * $value_total;
            else
                $discount = array_reduce($data->products, function($carry, $product){
                    $product = (object) $product;
                    $value = $product->discount_percentage == 0 ? $carry : ($product->discount_percentage / 100) * $product->value;
                    return $carry += $product->quantity * $value;
                }, 0);

            $dataInvoice = [
                'id'                    => $data->id,
                'customer_id'           => $data->customer_id,
                'seller_id'             => $data->seller_id,
                // 'user_id'               => session('user')->id,
                'status_id'             => $data->status_id,
                'address'               => $data->address,
                'note'                  => $data->notes,
                'invoice_amount'        => $value_total,
                'payable_amount'        => $value_total - $discount,
                'discount_amount'       => $data->discount_amount,
                'discount_percentage'   => $data->discount_percentaje
            ];
            if($this->i_model->save($dataInvoice)){
                foreach($data->products as $product){
                    $product = validUrl() ? $product : (object) $product;
                    $product->isDelete = validUrl() ? $product->isDelete : filter_var($product->isDelete, FILTER_VALIDATE_BOOLEAN);
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
        $invoice->seller = $this->i_model->getSeller($invoice->seller_id);
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

    public function load_order(){
        try{

            $dataPost = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();

            $data = $this->i_model
                ->setAdditionalParams(['origin' => 'load_order'])
                ->select([
                    'c.name as customer',
                    'c.id as id_customer',
                    'li.product_id',
                    'li.quantity',
                    'p.name',
                    'p.code'
                ])
                ->join('customers as c', 'c.id = invoices.customer_id', 'left')
                ->join('line_invoices as li', 'li.invoice_id = invoices.id', 'left')
                ->join('products as p', 'li.product_id = p.id', 'left')
                ->where([
                    'invoices.type_document_id' => 2,
                    'invoices.created_at >='    => "{$dataPost->date_init} 00:00:00",
                    'invoices.created_at <='    => "{$dataPost->date_end} 23:59:59"  
                ])
                ->findAll();
            if(!empty($data['data'])){

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet()->setTitle('Hoja de carga');
    
                $sheet->setCellValue('B1', "FECHA");
                $sheet->setCellValue('C1', "Desde {$dataPost->date_init} hasta {$dataPost->date_end}");
                $sheet->setCellValue('B2', "NOMBRE TRANSPORTADOR")->getStyle("B2")->getAlignment()->setWrapText(true);
                $sheet->setCellValue('A3', "CONTEO REFERENCIAS")->getStyle("A3")->getAlignment()->setTextRotation(90)->setWrapText(true);
    
                $sheet->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('B2')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('C2')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
    
                $headers = array_merge(['COD', 'DESCRIPCIÓN DEL PRODUCTO'], array_reduce($data['customers'], function($carry, $item){
                    $carry[] = strtoupper($item['name']);
                    return $carry;
                }, []), ['TOTAL']);
    
                $sheet->fromArray($headers, null, 'B3');
    
                $products = [];
    
                foreach ($data['products'] as $key => $product) {
                    $product = (object) $product;
                    foreach ($data['customers'] as $key => $customer) {
                        $customer = (object) $customer;
                        $results = array_filter($data['data'], function ($item) use ($product, $customer) {
                            $item = (object) $item;
                            return $item->id_customer == $customer->id_customer && $item->product_id == $product->product_id;
                        });
                        // $firstResult = $result ? array_shift($result) : null;
                        $products[$product->product_id][] = array_reduce($results, function($carry, $item){
                            $carry += (int) $item->quantity;
                            return $carry;
                        }, 0);
                    }
                }
    
                $row = 4;
                foreach ($data['products'] as $key => $product) {
                    $product = (object) $product;
                    $rowData = [$product->code, $product->name];
                    foreach ($products as $key => $prs) {
                        if($key == $product->product_id){
                            $rowData = array_merge($rowData, array_reduce($prs, function($carry, $item){
                                $carry[] = $item;
                                return $carry;
                            }, []), [array_reduce($prs, function($carry, $item){
                                $carry += (int) $item;
                                return $carry;
                            }, 0)]);
                        }
                    }
                    $sheet->fromArray($rowData, null, "B{$row}");
                    $row++;
    
                }
                for ($i=1; $i <= (count($data['customers']) + 1) ; $i++) {
                    $column = getColumnLetter($i + 3);
                    $sheet->getStyle("{$column}3")->getAlignment()->setTextRotation(90);
                    $sheet->getStyle("{$column}3")->getAlignment()->setWrapText(true);
                }
    
                for ($i=1; $i <= (count($data['customers']) + 4) ; $i++) {
                    $column = getColumnLetter($i);
                    $sheet->getStyle("{$column}3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("{$column}3")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    for ($j=3; $j < (count($data['products']) + 4); $j++) { 
                        $sheet->getStyle("{$column}{$j}")->getAlignment()->setIndent(1); // Indentar el texto hacia la derecha (simula margen)
                        $sheet->getStyle("{$column}{$j}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    }
                }
    
                $sheet->getRowDimension(3)->setRowHeight("80");
                $sheet->getColumnDimension('B')->setWidth(17);
                $sheet->getColumnDimension('A')->setWidth(7);
    
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
                for ($col = 3; $col <= $highestColumnIndex; $col++) {
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                }
    
                $writer = new Xlsx($spreadsheet);
                $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
                
                $writer->save($tempFile);
    
                $fileContent = file_get_contents($tempFile);
                $base64Content = base64_encode($fileContent);
                unlink($tempFile);
            }else{
                $base64Content = "";
            }


            return $this->respond([
                'data'      => $data,
                "file"      => $base64Content,
                "type"      => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'status'    => !empty($data['data'])
            ]);
        }catch(\Exception $e){
            return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
        }
    }
}
