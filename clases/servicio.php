<?php


include_once __DIR__ . "/fileManager.php";

class servicio extends fileHandler{
    public $_precio_hora;
    public $_precio_estadia;
    public $_precio_mensual;

    public function __construct($precio_hora,$precio_estadia,$precio_mensual)
    {
        $this->_precio_hora = $precio_hora;
        $this->_precio_estadia =$precio_estadia;
        $this->_precio_mensual =$precio_mensual;
    }


    public function save()
    {
        $response = new stdClass();
        if ($this!=null) {
            $response = $this->SaveJson("ARCHIVOS/precios.json");
        } else {
            $response->status = 'fail';
           
        }
        unset($response->data);
        return $response;
    }



    public static function read()
    {

        $arrayJson = parent::ReadJson("ARCHIVOS/precios.json");
        $servicioList = array();
        //var_dump($arrayJson);
        foreach ($arrayJson->data as $item) {
            //var_dump("ITEM: ". $item);
            if (count((array)$item) == 3) {
                //         var_dump($item);
                $newServ = new servicio($item->_precio_hora, $item->_precio_estadia, $item->_precio_mensual);
                array_push($servicioList, $newServ);
            }
        }
        return $servicioList;
    }


}