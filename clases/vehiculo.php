<?php


include_once __DIR__ . "/fileManager.php";
include_once __DIR__ . "/servicio.php";
class vehiculo extends fileHandler
{

    public $_patente;
    public $_fecha_ingreso;
    public $_tipo;
    public $_fecha_egreso;
    public $_importe;
    public $_email;


    public function __construct($patente, $fecha_ingreso, $tipo, $email, $fecha_egreso = null, $importe = null)
    {
        $this->_patente = $patente;
        $this->_fecha_ingreso = $fecha_ingreso;
        $this->_tipo = $tipo;
        $this->_email = $email;
        $this->_fecha_egreso = $fecha_egreso;
        $this->_importe = $importe;
    }

    public function add()
    {
        $response = new stdClass();
        if ($this != null) {
            $response = $this->SaveJson("ARCHIVOS/autos.json");
        } else {
            $response->status = 'fail';
        }
        unset($response->data);
        return $response;
    }
    public static function read()
    {

        $arrayJson = parent::ReadJson("ARCHIVOS/autos.json");
        $autoList = array();
        //var_dump($arrayJson);
        foreach ($arrayJson->data as $item) {
            //var_dump("ITEM: ". $item);
            if (count((array)$item) == 6) {
                //         var_dump($item);
                $newAuto = new vehiculo($item->_patente, $item->_fecha_ingreso, $item->_tipo, $item->_email, $item->_fecha_egreso, $item->_importe);
                array_push($autoList, $newAuto);
            }
        }
        return $autoList;
    }



    public static function withdraw($patente)
    {
        $autoList = self::read();
        $precioList = servicio::read();
        $response = new stdClass();
       
        if (count($autoList) > 0 && count($precioList) > 0) {
            
            foreach ($autoList as $auto) {
               if ($auto->_patente == $patente) {
                   $auto->_fecha_egreso = date('d-m-Y h:i A');
                   $response->patente = $auto->_patente;
                   $response->fecha_ingreso = $auto->_fecha_ingreso;
                   $response->fecha_egreso = $auto->_fecha_egreso;
                   foreach ($precioList as $tipo) {
                       if ($tipo ==  $auto->_tipo) {
                        $auto->_importe = $tipo;
                        $response->importe = $auto->_importe;
                       }
                   }
                break;
               }
            }
            parent::SaveJsonArray($autoList,"ARCHIVOS/autos.json");
            return $response;
        }else{
            return $response->status = 'fail';
        }
    }
}
