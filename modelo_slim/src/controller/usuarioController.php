<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;


class UsuarioController{

    public function getAll(Request $request, Response $response, $args){
        $rta = Usuario::get();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function getOne(Request $request, Response $response, $args){
        $rta = Usuario::find($args['id']);

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-type', 'application/json');
    }

    public function addOne(Request $request, Response $response, $args){
        $user = new Usuario;     
        $newEmail = $request->getParsedBody()['email'];
        $newPass = $request->getParsedBody()['password'];
        $newUserType = $request->getParsedBody()['user_type'];
        
        $userExists = Usuario::where('email', $newEmail)->first();

        if(!empty($userExists)){
            echo "El usuario ya existe!";
        } else{ 
            if( !empty($newEmail) && !empty($newPass) && !empty($newUserType) &&
                filter_var($newEmail, FILTER_VALIDATE_EMAIL) && $this->validateUserType($newUserType))
            {
            $user['email'] = $newEmail;
            $user['password'] = password_hash($newPass, PASSWORD_BCRYPT);
            $user['user_type'] = $newUserType;
            $response->getBody()->write("Usuario creado con éxito!");
            $user->save();
            }
            else{
                $response->getBody()->write("Error al crear el usuario");
            }
        }
        
        return $response->withHeader('Content-type', 'application/json');
    }

    public function updateOne(Request $request, Response $response, $args){
        $user = Usuario::find($args);
        $updateSql = array();
        
        $newEmail = $request->getParsedBody()['email'];
        $newPass = $request->getParsedBody()['password'];
        $newUserType = $request->getParsedBody()['user_type'];

        if(!empty($newEmail && filter_var($newEmail, FILTER_VALIDATE_EMAIL))
        {
            $updateSql['email'] = $newEmail;
            echo 'Email modificado correctamente';
        }
        
        if(!empty($newPass))
        {
            $updateSql['password'] = password_hash($newPass, PASSWORD_BCRYPT);
            echo 'Contraseña modificada correctamente';
        }

        if(!empty($newUserType))
        {
            if($this->validateUserType($newUserType)){
                $updateSql['user_type'] = $newUserType;
                echo 'Tipo de usuario modificado correctamente';
            } else {
                echo 'Tipo de usuario inválido';
            }
            
        }

        $user[0]->update($updateSql);
        
        return $response->withHeader('Content-type', 'application/json');
    }

    public function deleteOne(Request $request, Response $response, $args){    
        Usuario::destroy($args);
        
        $response->getBody()->write("Usuario eliminado con éxito!");
        return $response->withHeader('Content-type', 'application/json');
    }

    public function updateAlumno(Request $request, Response $response, $args){
        $user = Usuario::find($args);
        $updateSql = array();

        $newEmail = $request->getParsedBody()['email'];
        $imgFile = basename($_FILES["foto"]["name"]);        

        if(!empty($newEmail && filter_var($newEmail, FILTER_VALIDATE_EMAIL))
        {
            $updateSql['email'] = $newEmail;
            echo 'Email modificado correctamente';
        }

        /* VER COMO CARAJO SUBIR IMAGENES
        if(!empty($imgFile))
        {
            $imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION));
            if($this->validateFotoType($imgExt)){
                $targetDir = "./img/alumnos";
                $targetFilePath = $targetDir . $imgFile;
                $tmp_dir = $_FILES['user_image']['tmp_name'];
                $imgSize = $_FILES['user_image']['size'];
                $userpic = $user['id'].".".$imgExt;
                if($imgSize < 5000000){
                    unlink($user['foto']);
                    move_uploaded_file($tmp_dir, $targetDir.$userpic);
                } else {
                    echo 'La foto debe pesar menos de 5MB';
                }

                $updateSql['user_type'] =  $targetDir.$userpic.'/'.$tmp_dir;
                echo 'Foto de alumno modificada correctamente';
            } else {
                echo 'Tipo de usuario inválido';
            }
        }
        */

        $user[0]->update($updateSql);
        
        return $response->withHeader('Content-type', 'application/json');
    }

    public function updateProfesor(Request $request, Response $response, $args){
        $user = Usuario::find($args);
        $materias = array();
        array_push($materias, explode(',', json_encode($request->getParsedBody()['materias'])));
        
        var_dump('Materias: '.$materias);

        $updateSql = array();

        $newEmail = $request->getParsedBody()['email'];
        
        if(!empty($newEmail && filter_var($newEmail, FILTER_VALIDATE_EMAIL))
        {
            $updateSql['email'] = $newEmail;
            echo 'Email modificado correctamente';
        }

        $user[0]->update($updateSql);
        
        return $response->withHeader('Content-type', 'application/json');
    }



    public function validateUserType($args){
        return ($args == 'admin' || $args == 'alumno' || $args == 'profesor');
    }

    public function validateFotoType($args){
        return in_array($args, array('jpg', 'png', 'jpeg'));
    }
}

?>