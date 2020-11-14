<?php

namespace App\Controllers;
use App\Models\Usuario;
use PsrJwt\Factory\Jwt;
use Psr\Http\Message\ServerRequestInterface as Request;


class LoginController{

    public static function login(Request $request){
        $token = '';
        //var_dump($request->getParsedBody()['email']);
        
        $user = Usuario::where('email', $request->getParsedBody()['email'])->first();
    
        if(empty($user)){
            echo "Usuario inexistente.";
        } else {
            $passwordVerify = password_verify($request->getParsedBody()['password'], $user['password']);
            
            if($passwordVerify){
                $factory = new Jwt();

                $builder = $factory->builder();
        
                $token = $builder->setSecret($factory->key)
                    ->setPayloadClaim('email', $user['email'])
                    ->setPayloadClaim('user_type', $user['user_type'])
                    ->build();

                return $token->getToken();
            } else {
                echo "Contraseña incorrecta.";
            }   
        }
        return $token;
    }

}


?>