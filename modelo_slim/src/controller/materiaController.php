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
        return $response->withHeader('Content-type', 'application/json');
    }

    public function addOne(Request $request, Response $response, $args){
        $materia = new Materia;     
        $newName = $request->getParsedBody()['nombre'];
        $newCuatri = $request->getParsedBody()['cuatrimestre'];
        $newCupo = $request->getParsedBody()['cupos'];
        
        $materiaExists = Materia::where('nombre', $newName)->where('cuatrimestre', $newCuatri)->first();        

        if(!empty($materiaExists)){
            echo "La Materia ya existe!";
        } else{ 
            if( !empty($newName) && !empty($newCuatri) && $this->validarCuatrimestre($newCuatri) && !empty($newCupo)){
            
            $materia['nombre'] = $newName;
            $materia['cuatrimestre'] = $newCuatri;
            $materia['cupos'] = $newCupo;
            $response->getBody()->write("Materia creada con éxito!");
            $materia->save();
            }
            else{
                $response->getBody()->write("Error al crear la Materia");
            }
        }
        
        return $response->withHeader('Content-type', 'application/json');
    }

    public function updateOne(Request $request, Response $response, $args){
        $materia = Materia::find($args);
        $updateSql = array();
        
        $newName = $request->getParsedBody()['nombre'];
        $newCuatri = $request->getParsedBody()['cuatrimestre'];
        $newCupo = $request->getParsedBody()['cupos'];

        if(!empty($newName))
        {
            $updateSql['nombre'] = $newName;
            echo 'Nombre de la materia modificado exitosamente <br>';
        }
        
        if(!empty($newCuatri))
        {
            if($this->validarCuatrimestre($newCuatri)){
                $updateSql['cuatrimestre'] = $newCuatri;
                echo 'Cuatrimestre modificado exitosamente <br>';
            } else {
                echo 'Número de cuatrimestre inválido. <br>';
            }
            
        }

        if(!empty($newCupo))
        {
            $updateSql['cupos'] = $newCupo;
            echo 'Cupo modificado exitosamente <br>';
        }

        $materia[0]->update($updateSql);
        
        return $response->withHeader('Content-type', 'application/json');
    }

    public function deleteOne(Request $request, Response $response, $args){
        Materia::destroy($args);
        
        $response->getBody()->write("Materia eliminada con éxito!");
        return $response->withHeader('Content-type', 'application/json');
    }

    public function validarCuatrimestre($args){
        return ($args > 0 && $args <= 2);
    }
}

?>