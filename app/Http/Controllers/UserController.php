<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\JwtAuth;
use App\User;

class UserController extends Controller
{
    public function register(Request $request)
    {

        // Recoger las variables por post
        $json = $request->input('json', null);

        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if(!is_null($email) && !is_null($password) && !is_null($name)) {

            // Crear el usuario
            $user = new User();
            $user->email = $email;
            $user->role = $role;
            $user->name = $name;
            $user->surname = $surname;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            // Comporbar usuario duplicado
            $isset_user = User::where('email', '=', $email)->count();

            if($isset_user == 0){

                // Guardar el usuario
                $user->save();


                $data = array(
                    'status' => true,
                    'code' => 200,
                    'message' => 'Usuario registrado correctamente'
                );

            }else {

                // No guardarlo por que ya existe
                $data = array(
                    'status' => false,
                    'code' => 400,
                    'message' => 'Usuario ya existe'
                );
            }


        } else {
            $data = array(
                'status' => false,
                'code' => 400,
                'message' => 'Usuario no creado'
            );
        }

        return response()->json($data, 200);

    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();

        // Recibir datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = ( !is_null($json) && isset($params->email ) ) ? $params->email : null;
        $password = ( !is_null($json) && isset($params->password ) ) ? $params->password : null;
        $getToken = ( !is_null($json) && isset($params->getToken ) ) ? $params->getToken : null;

        // Cifrar la password
        $pwd = hash('sha256', $password);

        if( $email &&  $password && ($getToken == null || $getToken == 'false') ){

            $signup = $jwtAuth->signup($email, $pwd);

        }elseif($getToken != null){

            $signup = $jwtAuth->signup($email, $pwd, $getToken);

        } else {

            $signup = array(
                'status' => 'error',
                'message' => 'Envia tus datos por post'
            );
        }

        return response()->json($signup, 200);

    }
}
