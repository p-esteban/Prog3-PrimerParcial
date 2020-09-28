<?php

include_once __DIR__ . "/fileManager.php";

class user extends fileHandler
{
    public $_email;
    public $_pass;
    public $_type;

    public function __construct($email, $pass, $type=null)
    {
        $this->_email = $email;
        $this->_pass = $pass;
        $this->_type = $type??"";
    }

    public static function read()
    {

        $arrayJson = parent::ReadJson("ARCHIVOS/users.json");
        $usersList = array();
        //var_dump($arrayJson);
        foreach ($arrayJson->data as $item) {
            //var_dump("ITEM: ". $item);
            if (count((array)$item) == 3) {
                $newUser = new user($item->_email, $item->_pass, $item->_type);
                array_push($usersList, $newUser);
            }
        }
        return $usersList;
    }

    public function addUser()
    {
        $response = new stdClass();
        if (!$this->onList()) {
            $response = $this->SaveJson("ARCHIVOS/users.json");
            
        }else{
            $response->status = 'fail';
            $response->msg = 'registered user';
        }
        unset($response->data);
        return $response;
    }

    public function onList()
    {   
        $usersList = self::read();
        
        foreach ($usersList as $item) {
            if($item->_email == $this->_email ) {
                return $item ;
            }
        }
        return null;
    }

}
