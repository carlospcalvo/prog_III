<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Materia;

class MateriaController{

    public function getAll(Request $request, Response $response, $args){
        $rta = Materia::get();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function getOne(Request $request, Response $response, $args){
        $rta = Materia::find($args['id']);

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function addOne(Request $request, Response $response, $args){
        $materia = new Materia;     
        $newName = $request->getParsedBody()['materia'];
        $newCuatri = $request->getParsedBody()['cuatrimestre'];
        $newCupo = $request->getParsedBody()['cupos'];
        
        $materiaExists = Materia::where('materia', $newName)->where('cuatrimestre', $newCuatri)->first();        

        if(!empty($materiaExists)){
            $response->getBody()->write(json_encode(['error'=>"La materia ya existe!"]));
        } else{ 
            if( !empty($newName) && !empty($newCuatri) && $this->validarCuatrimestre($newCuatri) && !empty($newCupo)){           
            $materia['materia'] = $newName;
            $materia['cuatrimestre'] = $newCuatri;
            $materia['cupos'] = $newCupo;
            if($materia->save()){
                $response->getBody()->write(json_encode(['exito'=>"Materia creada con éxito"]));
            } else {
                $response->getBody()->write(json_encode(['error'=>"Error al crear la materia"]));
            } 
        }
            else{
                $response->getBody()->write(json_encode(['error'=>"Error al crear la materia. Parámetros inválidos."]));
            }
        }
        return $response;
    }

    public function validarCuatrimestre($args){
        return ($args > 0 && $args <= 4);
    }
}

?>