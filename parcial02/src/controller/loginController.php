<?php

namespace App\Controllers;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use PsrJwt\Factory\Jwt;
use Psr\Http\Message\ServerRequestInterface as Request;


class LoginController{

    public static function login(Request $request, Response $response){
        $token = '';
        //var_dump($request->getParsedBody()['email']);
        
        if(empty($request->getParsedBody()['email'])){
            $user = User::where('nombre', strtolower($request->getParsedBody()['nombre']))->first();
        } else {
            $user = User::where('email', strtolower($request->getParsedBody()['email']))->first();
        }
        
    
        if(empty($user)){
            echo json_encode(['error'=>"Usuario inexistente."]);
        } else {
            $passwordVerify = password_verify($request->getParsedBody()['clave'], $user['clave']);
            
            if($passwordVerify){
                $factory = new Jwt();

                $builder = $factory->builder();
        
                $token = $builder->setSecret($factory->key)
                    ->setPayloadClaim('email', strtolower($user['email']))
                    ->setPayloadClaim('nombre', strtolower($user['nombre']))
                    ->setPayloadClaim('tipo', $user['tipo'])
                    ->build();

                return $token->getToken();
            } else {
                $response->getBody()->write(json_encode(['error'=>"Contraseña incorrecta."]));
            }   
        }
        return $token;
    }

}


?>