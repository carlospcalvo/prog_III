<?php

namespace App\Controllers;

use App\Models\Asignacion;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;


class UserController{

    public function getAll(Request $request, Response $response, $args){
        $rta = User::get();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function getOne(Request $request, Response $response, $args){
        $rta = User::find($args['id']);      

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function addOne(Request $request, Response $response, $args){
        $user = new User;     
        $newEmail = $request->getParsedBody()['email'];
        $newName = $request->getParsedBody()['nombre'];
        $newPass = $request->getParsedBody()['clave'];
        $newUserType = $request->getParsedBody()['tipo'];
        
        $emailExists = User::where('email', $newEmail)->first();
        $nameExists = User::where('nombre', $newName)->first();

        if(!empty($emailExists) || !empty($nameExists)){
            $response->getBody()->write(json_encode(['error'=>"Nombre o email ya existentes."]));
        } else{ 
            if( !empty($newEmail) && $this->validateUserName($newName) && !empty($newUserType) &&
                filter_var($newEmail, FILTER_VALIDATE_EMAIL) && $this->validateUserType($newUserType))
            {
                $user['nombre'] = strtolower($newName);
                $user['clave'] = password_hash(strtolower($newPass), PASSWORD_BCRYPT);
                $user['email'] = strtolower($newEmail);
                $user['tipo'] = strtolower($newUserType);
                
                if($user->save()){
                    $response->getBody()->write(json_encode(['exito'=>'User creado con éxito!']));
                } else {
                    $response->getBody()->write(json_encode(['error'=>"Error al guardar el usuario."]));
                }

            } else {
                $response->getBody()->write(json_encode(['error'=>"Error al crear el usuario. Parámetros inválidos."]));
            }
        }
        
        return $response;
    }

    public function validateUserType($args){
        return ($args == 'admin' || $args == 'alumno' || $args == 'profesor');
    }

    public function validateUserName($args){
        return (!empty($args) && !preg_match('/\s/', $args));
    }
}

/*********************************** SQL JOINS ********************************************* 
    INNER JOIN
        
    $data = User::select('alumnos.nombre', 'alumnos.apellido', 'materias.nombre')
                ->join('inscripciones', 'alumnos.id', '=', 'inscripciones.id_alumno')
                ->join('materias', 'inscripciones.id_materia', '=', 'materias.id')
                ->get();

    LEFT JOIN

    $data = User::select('alumnos.nombre', 'alumnos.apellido', 'materias.nombre')
                ->leftjoin('inscripciones', 'alumnos.id', '=', 'inscripciones.id_alumno')
                ->leftjoin('materias', 'inscripciones.id_materia', '=', 'materias.id')
                ->get();

*/



?>