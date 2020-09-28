<?php

require __DIR__ . '/vendor/autoload.php';

include_once __DIR__ . "/clases/user.php";
include_once __DIR__ . "/clases/auth.php";
include_once __DIR__ . "/clases/vehiculo.php";
include_once __DIR__ . "/clases/servicio.php";

$method = $_SERVER['REQUEST_METHOD'];

$path_info = $_SERVER['PATH_INFO'];

$response = new stdClass();


switch ($method) {
    case 'POST':

        switch ($path_info) {
            case '/registro': // punto1
                if (
                    isset($_POST['email'])  && isset($_POST['password']) && isset($_POST['tipo']) &&
                    $_POST['email'] != "" && $_POST['password'] != "" &&  $_POST['tipo'] != ""
                ) {

                    $user = new user($_POST['email'], convert_uuencode($_POST['password']), $_POST['tipo']);

                    $response = $user->addUser();
                } else {
                    $response->msg = 'incorrect data';
                    $response->status = 'fail';
                }
                break;
            case '/login':
                // echo "ENTRE";
                if (
                    isset($_POST['email'])  && isset($_POST['password']) &&
                    $_POST['email'] != "" && $_POST['password'] != ""
                ) { //cambiar parametros de POST

                    $user = new user($_POST['email'], convert_uuencode($_POST['password'])); //cambiar clase USER
                    $newUser = $user->onList();

                    if ($newUser != null) {
                        $response->status = 'success';
                        $response->token = auth::signIn($newUser->_email, $newUser->_type);
                    } else {
                        $response->msg = 'unregistered';
                        $response->status = 'fail';
                    }
                }
                break;

            case '/precio': //pto3
                $header = getallheaders();
                $token = $header['token'] ?? '';


                $response =  auth::check($token);

                if (isset($response->data) && $response->data->type == 'admin') {

                    if (
                        isset($_POST['precio_hora']) && isset($_POST['precio_estadia']) && isset($_POST['precio_mensual'])
                        && !empty($_POST['precio_hora']) && !empty($_POST['precio_estadia'] && !empty($_POST['precio_mensual']))
                    ) {

                        $servicio = new servicio(
                            $_POST['precio_hora'],
                            $_POST['precio_estadia'],
                            $_POST['precio_mensual']
                        );
                        // var_dump($servicio);

                        $response = $servicio->save();
                        unset($response->data);
                    }
                } else {
                    unset($response->data);
                    $response->msg = 'Failed check: ';
                    $response->status = 'fail';
                }


                break;

            case '/ingreso': //pto4
                $header = getallheaders();
                $token = $header['token'] ?? '';


                $response =  auth::check($token);

                if (isset($response->data) && $response->data->type == 'user') {

                    if (
                        isset($_POST['patente']) &&  isset($_POST['tipo'])
                    ) {

                        $vehiculo = new vehiculo(
                            $_POST['patente'],
                            date('d-m-Y h:i A'),
                            $_POST['tipo'],
                            $response->data->email

                        );
                        // var_dump($servicio);

                        $response = $vehiculo->add();
                        unset($response->data);
                    }
                } else {
                    unset($response->data);
                    $response->msg = 'Failed check: ';
                    $response->status = 'fail';
                }


                break;






            default:
                $response->msg = 'invalid request';
                $response->status = 'fail';
                break;
        }
    break;






    case 'GET':
        
        switch ($path_info) {
           
            case '/retiro': //pto5
                $header = getallheaders();
                $token = $header['token'] ?? '';
                
                
                $response =  auth::check($token);

                if (isset($response->data) && $response->data->type == 'user') {
                    
                    if (isset($_GET['patente'])) {
                       
                        $response = vehiculo::withdraw($_GET['patente']);


                    }else{
                        $response->msg = 'incorrect data';
                        $response->status = 'fail';
                    }
                }
            break;

            default:
                # code...
                break;
        }
}
echo json_encode($response);
