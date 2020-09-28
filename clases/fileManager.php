<?php

class fileHandler
{



    ////////////// FUNCIONA JOYA //////////////////

    public function SaveLine()
    {
        $fileName = get_class($this) . ".txt";
        $response = new stdClass();
        try {

            $fp = fopen($fileName, 'a+');
            if ($fp != null) {
                fwrite($fp, $this . PHP_EOL);

                ($this->ReadLastLineStr($fileName) == $this) ?
                    $response->status = 'success' : $response->status = 'fail';
            } else {
                $response->status = 'fail';
                $response->msg = 'File not found';
            }
        } catch (Exception $e) {
            $response->status = 'fail';
            $response->msg = $e->getMessage();
        } finally {
            fclose($fp);
            return $response;
        }
    }

    public static function ReadTxt()
    {
        $fileName = get_called_class() . ".txt";
        $response = new stdClass();
        try {
            $readFile = array();
            $fp = fopen($fileName, 'a+');
            if ($fp != null) {
                while (!feof($fp)) {
                    $data = explode('*', fgets($fp));
                    if (count($data) > 0) {
                        array_push($readFile, $data);
                    }
                }
                $response->data = $readFile;
            } else {
                $response->status = 'fail';
                $response->msg = 'File not found';
            }
        } catch (Exception $e) {
            $response->status = 'fail';
            $response->msg = $e->getMessage();
        } finally {

            fclose($fp);
            return $response;
        }
    }





    private function ReadLastLineStr($fileName)
    {
        try {
            $fp = fopen($fileName, 'r');
            $pos = -2;
            $t = " ";
            while ($t != "\n") {
                fseek($fp, $pos, SEEK_END);
                $t = fgetc($fp);
                $pos = $pos - 1;
            }
            $t = fgets($fp);
        } catch (Exception $e) {
            throw $e;
        } finally {
            fclose($fp);
            return trim($t);
        }
    }
    public function SaveJson($fileName = null)
    {
        try {
            // $fileName =  "ARCHIVOS/".get_class($this) . ".json";
           $fileName  ??$fileName = get_class($this) . ".json";
         
             $response = new stdClass();

            if (file_exists($fileName)) {
                $response = fileHandler::ReadJson($fileName);
                $arrayJson = (array)$response->data;
                clearstatcache();
            } else {
                $arrayJson = array();
            }

            array_push($arrayJson, $this);
            $fp = fopen($fileName, 'w');
            if ($fp != null) {

                fwrite($fp, json_encode($arrayJson)) > 0 ?
                    $response->status = 'success' : $response->status = 'fail';
            } else {
                $response->status = 'fail';
            }
        } catch (Exception $e) {
            $response->status = 'fail';
            $response->msg = $e->getMessage();
        } finally {
            fclose($fp);
            return $response;
        }
    }

    public static function SaveJsonArray($array, $fileName = null){
        try {
            // $fileName =  "ARCHIVOS/".get_class($this) . ".json";
           $fileName  ??$fileName =  get_called_class() . ".json";
         
             $response = new stdClass();

           
            $fp = fopen($fileName, 'w');
            if ($fp != null) {

                fwrite($fp, json_encode($array)) > 0 ?
                    $response->status = 'success' : $response->status = 'fail';
            } else {
                $response->status = 'fail';
            }
        } catch (Exception $e) {
            $response->status = 'fail';
            $response->msg = $e->getMessage();
        } finally {
            fclose($fp);
            return $response;
        }
    }

    public static function ReadJson($fileName = null)
    {
        // $fileName =  "ARCHIVOS/".get_called_class() . ".json";
        $fileName ?? $fileName = get_called_class() . ".json";
        
        try {
            $response = new stdClass();
            $response->status = 'success';
            if (file_exists($fileName)) {

                $fp = fopen($fileName, 'r');
                $size = filesize($fileName);

                if ($size > 0) {

                    $read = fread($fp, $size);
                } else {
                    $read = "{}";
                    $response->msg = 'empty file';
                }
              
                $response->data = json_decode($read);
                //var_dump($response->data);
                fclose($fp);
            } else {
                $response->msg = 'file not exists';
                $response->data = array();
            }
        } catch (Exception $e) {
            $response->status = 'fail';
            $response->msg = $e->getMessage();
        }finally{
           
            return $response;
        }
    }

    ////////////////// ACA TERMINA /////////////////////


    public static function readSerialized($fileName = null)
    {

        $fileName ?? $fileName = get_called_class() . ".txt";
        if (file_exists($fileName)) {

            $fp = fopen($fileName, 'r');
            $size = filesize($fileName);

            if ($size > 0) {

                $read = fread($fp, $size);
            } else {
                // $read = "{}";
            }
            fclose($fp);
            $arrayJson = unserialize($read);
        } else {

            $arrayJson = array();
        }
        return $arrayJson;
    }


    public function saveSerialized()
    {
        $fileName = get_class($this) . ".txt";
        if (file_exists($fileName)) {

            $arrayJson = fileHandler::readSerialized($fileName);
            clearstatcache();
        } else {
            $arrayJson = array();
        }

        array_push($arrayJson, $this);
        $fp = fopen($fileName, 'w');

        if ($fp != null) {
            fwrite($fp, serialize($arrayJson));

            fclose($fp);
        }
    }



    public static function MarcaAgua($pathOriginal, $pathDestino)
    {
        // Cargar la estampa y la foto para aplicarle la marca de agua
    $im = imagecreatefromjpeg($pathOriginal);

    // Primero crearemos nuestra imagen de la estampa manualmente desde GD
    $estampa = imagecreatetruecolor(100, 70);
    imagefilledrectangle($estampa, 0, 0, 99, 69, 0x0000FF);
    imagefilledrectangle($estampa, 9, 9, 90, 60, 0xFFFFFF);
    $im = imagecreatefromjpeg($pathOriginal);
    imagestring($estampa, 5, 20, 20, 'PROGRAMACION 3', 0x0000FF);
    imagestring($estampa, 3, 20, 40, '2020', 0x0000FF);

    // Establecer los márgenes para la estampa y obtener el alto/ancho de la imagen de la estampa
    $margen_dcho = 10;
    $margen_inf = 10;
    $sx = imagesx($estampa);
    $sy = imagesy($estampa);

    // Fusionar la estampa con nuestra foto con una opacidad del 50%
    imagecopymerge($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa), 50);

    // Guardar la imagen en un archivo y liberar memoria
    imagepng($im, $pathDestino);
    imagedestroy($im);
    }
}
