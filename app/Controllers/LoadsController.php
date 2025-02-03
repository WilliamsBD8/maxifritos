<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\API\ResponseTrait;

use App\Models\GroupProduct;
use App\Models\Product;
use App\Models\Customer;

class LoadsController extends BaseController
{
    use ResponseTrait;

    private $gp_model;
    private $p_model;
    private $c_model;

    public function __construct(){
        $this->gp_model = new GroupProduct();
        $this->p_model = new Product();
        $this->c_model = new Customer();
    }

    public function products()
    {
        try {
            // Cargar el archivo Excel
            $rutaArchivo = WRITEPATH."uploads/upload/excel/DATOS-SIMIPLUS.xlsx";
            $spreadsheet = IOFactory::load($rutaArchivo);
    
            // Obtener la primera hoja
            $spreadsheet->setActiveSheetIndex(0);
            $hoja = $spreadsheet->getActiveSheet();
    
            // Obtener los datos de la hoja
            $datos = $hoja->toArray(null, true, true, true);

            $grupo = null;
            $code_group = null;

            foreach ($datos as $key => $data) {
                $data = (object) $data;
                if($key >= 14){
                    if($data->C != NULL){
                        $code_group = strtoupper(substr($data->C, 0, 3));
                        $this->gp_model->save([
                            'name'  => $data->C,
                            'code'  => $code_group
                        ]);
                        $grupo = $this->gp_model->insertID();
                    }
                    if (isset($data->E) && !empty($data->E)) {
                        $precio = (float) str_replace([",", "$"], "", $data->E);
                    } else {
                        $precio = 0;  // Asignar un valor predeterminado si no hay precio
                    }
                    $data_product = [
                        'group_product_id'  => $grupo,
                        'code'              => "{$data->B}{$code_group}",
                        'code_item'         => $data->B,
                        'name'              => $data->D,
                        'value'             => $precio
                    ];
                    $this->p_model->save($data_product);
                }
            }



    
            return $this->respond($datos);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die('Error al leer el archivo Excel: ' . $e->getMessage());
        }    
    }

    public function customers(){
        try{
            // Cargar el archivo Excel
            $rutaArchivo = WRITEPATH."uploads/upload/excel/clientes_2025_maxi_v3.csv";
            $spreadsheet = IOFactory::load($rutaArchivo);
    
            // Obtener la primera hoja
            $spreadsheet->setActiveSheetIndex(0);
            $hoja = $spreadsheet->getActiveSheet();
    
            // Obtener los datos de la hoja
            $datos = $hoja->toArray(null, true, true, true);
            array_shift($datos);

            foreach ($datos as $key => $object) {
                $prevValue = null;
                foreach ($object as $field => $value) {
                    if ($value === null && $prevValue === null) {
                        unset($datos[$key][$field]);
                    } else {
                        $prevValue = $value;
                    }
                }
            }

            $cc = 0;
            $datas = [];
            foreach ($datos as $key => $data) {
                $data = (object) $data;
                $name = [];
                $direccion = "";
                $origin = 0;
                foreach ($data as $key => $value) {
                    switch ($origin) {
                        case 0:
                            $name[] = $value;
                            break;
                        case 1:
                            $direccion = $value;
                            $origin++;
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                    if(empty($value))
                        $origin++;
                }
                $datas[] = [
                    'type_customer_id'                  => 1,
                    'type_document_identification_id'   => 3,
                    'user_origin_id'                    => session('user')->id,
                    'name'                              => trim(implode(" ", $name)),
                    'identification_number'             => $cc,
                    'address'                           => $direccion,
                ];
                $cc++;
            }

            foreach ($datas as $key => $customer) {
                $this->c_model->save($customer);
            }

            return $this->respond($datas);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die('Error al leer el archivo Excel: ' . $e->getMessage());
        } 
    }

    public function env(){
        try {
            // Usar cURL para obtener los commits desde la API de GitHub (con autenticación si es necesario)
            $ch = curl_init();

            // Configuración de cURL para obtener los commits
            curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/WilliamsBD8/maxifritos/commits");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");  // GitHub requiere un User-Agent
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/vnd.github.v3+json',
                // Si es necesario, puedes incluir un token de autenticación aquí
                // 'Authorization: token YOUR_GITHUB_TOKEN'
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            // Decodificar la respuesta JSON de GitHub
            $commits = json_decode($response);

            // Crear un objeto con el último commit
            $commit = $commits[0]->sha;

            // Retornar la respuesta
            // return $this->respond($commit);

            $envFile = APPPATH . '../.env';
            $envContent = file_get_contents($envFile);
            $key = "GIT_COMMIT_HASH";
            $pattern = "/^" . preg_quote($key, '/') . "=.*/m";

            if (preg_match($pattern, $envContent)) {
                // Si la clave existe, la reemplazamos
                $envContent = preg_replace($pattern, "{$key}={$commit}", $envContent);
            } else {
                // Si no existe, la agregamos al final
                $envContent .= PHP_EOL . "{$key}={$commit}";
            }

            log_message('info', "Env: ".$envContents);
            return $this->respond([$commit]);
        } catch (\Exception $th) {
            die('Error: ' . $th->getMessage());
        }
    }
}
