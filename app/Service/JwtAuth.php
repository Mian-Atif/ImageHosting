<?php

namespace App\Service;
//require_once('vendor/autoload.php');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtAuth
{
    public function createToken($user)
    {
        try{
        $iss = "localhost";
        $iat = time();
        $nbf = time();
        // $exp = $iat +4550;
        $aud = 'user';



        $secret_key = config('constant.secret');
        $payload_info=array(
            "iss"=> $iss,
            "iat"=> $iat,
            "nbf"=> $nbf,
           // "exp"=> $exp,
            "aud"=> $aud,
            "data"=>$user,
        );
        $jwt = JWT::encode($payload_info, $secret_key,'HS512');
        return $jwt;
    }
    catch(Exception $ex){
        return response()->error($ex->getMessage());
    }
    }
         public function decodeToken($token)
         {
             try{
                $secret_key = config('constant.secret');
            $decoded_data = JWT::decode($token, new key($secret_key,"HS512"));
            return $decoded_data;
             }
             catch(Exception $ex){
                 return response()->error($ex->getMessage(),400);
             }
         }
}
