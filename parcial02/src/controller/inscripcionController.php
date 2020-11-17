<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PsrJwt\Factory\Jwt;
use ReallySimpleJWT\Token;
use App\Models\Inscripcion;
use App\Models\User;
use App\Models\Materia;

class InscripcionController{

    public function getOne(Request $request, Response $response, $args){
        
        $rta = User::select('users.nombre', 'materias.materia AS materia')
                        ->join('inscripciones', 'users.id', '=', 'inscripciones.id_alumno')
                        ->join('materias', 'inscripciones.id_materia', '=', 'materias.id')
                        ->where('users.id', $args)
                        ->get();
        
        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        
        return $response;
    }

    public function addOne(Request $request, Response $response, $args){
        $inscripcion = new Inscripcion;     
        $id_materia = $args;
        $id_materia = $id_materia['id'];
        //conseguir id alumno desde token
        $token = $request->getHeader('token')[0] ?? '';
        $jwt = new Jwt();
        $payload = Token::getPayload($token, $jwt->key);        
        $userEmail = $payload['email'];
        $alumno = User::where('email', $userEmail)->first();
        $id_alumno = $alumno['id'];
        
        //check de cupo
        $cupoMateria = Materia::select('cupos')->where('id', $id_materia)->first();
        $cupoTomado = Inscripcion::where('id_materia', $id_materia);
        $cupoTomado = $cupoTomado->count();
        $cupoDisp = $cupoMateria['cupos'] - $cupoTomado;
            
        if($cupoDisp <= 0){
            $response->getBody()->write(json_encode(['error'=>"No hay cupos disponibles"]));
        } else {
            if( !empty($id_alumno) && !empty($id_materia)){           
                $inscripcion['id_alumno'] = $id_alumno;
                $inscripcion['id_materia'] = $id_materia;
                
                if($inscripcion->save()){
                    $response->getBody()->write(json_encode(['exito'=>"Inscripción realizada con éxito"]));
                } else {
                    $response->getBody()->write(json_encode(['error'=>"Error al realizar la inscripción"]));
                }
            } else {
                $response->getBody()->write(json_encode(['error'=>"Error al realizar la inscripción. Parámetros inválidos"]));
            }
        }
        
        return $response;
    }

}

?>