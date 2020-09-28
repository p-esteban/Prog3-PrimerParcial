<?php


use \Firebase\JWT\JWT;

use function PHPSTORM_META\type;

class auth
{
    private static $secret_key = 'primerparcial';


    public static function signIn($email, $type)
    {

        $payload = array(
            'email'=>$email,
            'type' => $type
            
        );
        

        return JWT::encode($payload, self::$secret_key);
    }
    public static function check($token)
    {
       
        try {
            $response = new stdClass();
            if (empty($token)) {

                throw new Exception('Invalid token');
            }
            
            $response->data = JWT::decode($token, self::$secret_key,array('HS256'));
            $response->status='success';
            
        } catch (Exception $e) {
            $response->msg ='Failed check: '. $e->getMessage();
            $response->status = 'fail';
            return $response;
        }
        
        return $response;
    }

    // public static function GetData($token)
    // {
    //     return JWT::decode($token, self::$secret_key)->data;
    // }
}
