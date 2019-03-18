<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            echo 'Index de Carcontroller AUTENTICADO';
        } else {

            echo 'Index de Carcontroller NO AUTENTICADO';
        }
    }

    public function store( Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            // Recoger datos por post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            // CONSEGUIR USUARIO IDENTIFICADO
            $user = $jwtAuth->checkToken($hash, true);

            // Validacion
            $request->merge($params_array);

            try {

                $validate = $this->validate($request, [
                    'title' => 'required|min:5',
                    'description' => 'required',
                    'price' => 'required',
                    'status' => 'required'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return $e->getResponse();
            }


            // Guardar el coche
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;

            $car->save();

            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => 200
            );

        } else {

            // Devolver error
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 400
            );

        }

        return response()->json($data);

    }
} //End class
