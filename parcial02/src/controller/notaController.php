<?php

namespace App\Controllers;

use App\Models\Inscripcion;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class NotaController{
    
    public function getOne(Request $request, Response $response, $args){
        $rta = Inscripcion::where('id_materia', $args['id'])->first();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function updateOne(Request $request, Response $response, $args){
        
        $newNota = $request->getParsedBody()['nota'];
        $id_alumno = $request->getParsedBody()['idAlumno'];
        $inscripcion = Inscripcion::where('id_alumno', $id_alumno)->first();
        $updateSql = array();
        
        if(!empty($newNota) && !empty($id_alumno)){
            $updateSql['nota'] = $newNota;
            $inscripcion->update($updateSql);
        } else {
            $response->getBody()->write(json_encode(['error'=>"Error al cargar la nota"]));
        }
            
        return $response;
    }

    public function validarCuatrimestre($args){
        return ($args > 0 && $args <= 4);
    }
}

?>