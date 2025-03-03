<?php


namespace App\Controllers;


use App\Models\User;
use Config\Services;
use App\Models\Plantilla;
use App\Models\Password;
use App\Models\HistoryUser;
use CodeIgniter\API\ResponseTrait;


class AuthController extends BaseController
{
    use ResponseTrait;
    
    public function login()
    {
        GenerateCaptcha();

        return view('auth/login');
    }

    public function validation()
    {
        try{
            $data = validUrl() ? $this->request->getJson() : (object) $this->request->getPost();
            $username = $data->email_username;
            $password = $data->password;
            $captcha = $data->captcha;
            $validationCaptcha = ValidateReCaptcha($captcha);
            if($validationCaptcha->code == 3){
                $user = new User();
                $data = $user
                    ->select(['users.*', 'roles.name as role_name'])
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('username', $username)
                    ->orWhere('email', $username)->first();
                if ($data) {
                    if ($data->status == 'active') {
                        $data->password = $user->getPassword($data->id);
                        if((int) $data->password->attempts < 5){
                            if (password_verify($password, $data->password->password)) {
                                $hu_model = new HistoryUser();
                                $hu_model->save([
                                    'user_id'   => $data->id,
                                    'attempts'  => (int) $data->password->attempts + 1
                                ]);
                                if($data->password->attempts > 0){
                                    $p_model = new Password();
                                    $p_model->save([
                                        'id'        => $data->password->id,
                                        'attempts'  => 0
                                    ]);
                                }
                                $session = session();
                                $session->set('user', $data);
                                return redirect()->to(base_url(['dashboard']));
                            } else {
                                $p_model = new Password();
                                $p_model->save([
                                    'id'        => $data->password->id,
                                    'attempts'  => (int) $data->password->attempts + 1
                                ]);
                                return $this->respond([
                                    'title'     => 'Validación de usuario',
                                    'msg'   => "Las credenciales no concuerdan. Numeros de intentos restantes <b>".(4 - $data->password->attempts)."</b>"
                                ], 403);
                                return redirect()->to(base_url(['login']))->with('errors', "Las credenciales no concuerdan. Numeros de intentos restantes <b>".(4 - $data->password->attempts)."</b>");
                            }
                        }else{
                            return $this->respond([
                                'title'     => 'Validación de usuario',
                                'msg'   => 'Limite de intentos superados.'
                            ], 403);
                            // return redirect()->to(base_url(['login']))->with('errors', 'Limite de intentos superados.');
                        }
                    } else {
                        return $this->respond([
                            'title'     => 'Validación de usuario',
                            'msg'   => 'La cuenta no se encuentra activa.'
                        ], 403);
                        // return redirect()->to(base_url(['login']))->with('errors', 'La cuenta no se encuentra activa.');
                    }
                } else {
                    return $this->respond([
                        'title'     => 'Validación de usuario',
                        'msg'   => 'Las credenciales no concuerdan.'
                    ], 403);
                    // return redirect()->to(base_url(['login']))->with('errors', 'Las credenciales no concuerdan.');
                }
            }else {

                return $this->respond([
                    'title'     => 'Validación de usuario',
                    'msg'       => $validationCaptcha->message,
                    'error'     => true, 
                    'captcha'   => session('captcha')

                ], $validationCaptcha->code == 1 ? 200 : 403);
            }
        }catch(\Exception $e){
			return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
		}
    }

    // public function register()
    // {
    //     $validation = Services::validation();
    //     return view('auth/register', ['validation' => $validation]);
    // }

    // public function create()
    // {
    //     $validation = Services::validation();
    //     if ($this->validate([
    //         'name'              => 'required|max_length[45]',
    //         'username'          => 'required|is_unique[users.username]|max_length[40]',
    //         'email'             => 'required|valid_email|is_unique[users.email]|max_length[100]',
    //         'password'          => 'required|min_length[6]',
    //         'password_confirm'  => 'required|matches[password]',
    //     ], [
    //         'name' => [
    //             'required' => 'El campo Nombres y Apellidos es obrigatorio.',
    //             'max_length' => 'El campo Nombres Y Apellidos no debe tener mas de 45 caracteres.'
    //         ],
    //         'username' => [
    //             'required' => 'El campo Nombre de Usuario es obligatorio',
    //             'is_unique' => 'Lo sentimos. El nombre de usuario ya se encuentra registrado.',
    //             'max_length' => 'El campo Nombre de Usuario no puede superar mas de 20 caracteres.'
    //         ],
    //         'email' => [
    //             'required' => 'El campo Correo Electronico es obrigatorio.',
    //             'is_unique' => 'Lo sentimos. El correo ya se encuentra registrado.'
    //         ],
    //         'password' => [
    //             'required' => 'El campo Contraseña es obligatorio.',
    //             'min_length' => 'El campo Contraseña debe tener minimo 6 caracteres.'
    //         ],
    //         'password_confirm' => [
    //             'required'      => 'La confirmacion de la contraseña es obligatoria.',
    //             'matches'       => 'Las contraseñas no coinciden.'
    //         ]

    //     ])) {
    //         $info = $this->request->getJson();
    //         $data = [
    //             'name' => $info->name,
    //             'username' => $info->username,
    //             'email' => $info->email,
    //             'status' => 'inactive',
    //             'role_id' => 3
    //         ];

    //         $u_model = new User();
    //         $u_model->save($data);

    //         $user_id = $u_model->insertID();
    //         $p_model = new Password();
    //         $p_model->save([
    //             'user_id'   => $user_id,
    //             'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
    //         ]);
    //         return $this->respond([
    //             'status'    => '200',
    //             'title'     => 'Creación de éxitosa',
    //             'msg'   => "Esperando a activar la cuenta."
    //         ]);
    //     } else {
    //         $errors = implode("<br>", $validation->getErrors());
    //         return $this->respond([
    //             'status'    => '403',
    //             'title'     => 'Error en los datos de creación.',
    //             'msg'   => $errors
    //         ]);
    //     }


    // }

    public function resetPassword()
    {
        return view('auth/reset_password');
    }

    public function forgotPassword()
    {
        $request = Services::request();
        $user = new User();
        $info = $this->request->getJson();
        $data = $user->where('email', $info->email)->first();
        if (!empty($data) > 0) {
            $email = new EmailController();
            $password = $this->encript();
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $response = $email->send('wabox324@gmail.com', 'wabox', $info->email, 'Recuperacion de contraseña', password($password));
            if($response->status){
                $p_model = new Password();
                if($p_model->set(['status' => 'inactive'])->where(['user_id' => $data->id, 'status' => 'active'])->update()){
                    if($p_model->save(['user_id' => $data->id, 'password' => $password_hash, 'temporary' => 'Si'])){
                        return $this->respond([
                            'title'     => 'Contraseña actualizada con éxito',
                            'msg'   => $response->message
                        ]);
                    }else
                        return $this->respond([
                            'title'     => 'Error al recuperar la contraseña',
                            'msg'   => 'Error al actualizar la contraseña.'
                        ], 403);
                }else{
                    return $this->respond([
                        'title'     => 'Error al recuperar la contraseña',
                        'msg'   => 'Error al actualizar la contraseña.'
                    ], 403);
                }
            }else{
                return $this->respond([
                    'title'     => 'Error al recuperar la contraseña',
                    'msg'   => $response->message
                ], 403);
            }
        } else {
            return $this->respond([
                'title'     => 'Error al recuperar la contraseña',
                'msg'   => 'No se encontró el correo electrónico.'
            ], 403);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url(['login']));
    }

    public function encript($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function validatePassword(){

    }
}
