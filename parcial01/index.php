<?php

require_once __DIR__.'\vendor\firebase\php-jwt\src\JWK.php';
require_once __DIR__ . '\vendor\autoload.php';
require_once __DIR__.'\clases\Files.php';
require_once __DIR__.'\clases\usuario.php';
require_once __DIR__.'\clases\ingreso.php';
require_once __DIR__.'\clases\Precio.php';


use Firebase\JWT\JWT;


$usuarios = Files::readJson("usuarios.json");
$precios = Files::readJson("precios.json");
$key = 'primerparcial';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? 'no_path';


switch ($path) {
    case '/registro':        
        if ($method == 'POST') {
            if(!(empty($_POST['email']) && empty($_POST['tipo']) && empty($_POST['password'])) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $user = new Usuario(
                    $_POST['email'] ?? '', 
                    $_POST['tipo'] ?? '',
                    password_hash($_POST['password'], PASSWORD_BCRYPT) ?? ''
                );

                if($user->usuarioExists($usuarios)){
                    echo "El usuario ya existe. <br>";
                }else{
                    array_push($usuarios, $user);
                    if($user->guardarUsuario('usuarios.json', $usuarios)){
                        echo "Usuario guardado correctamente";
                    }
                    else{
                        echo "Error al guardar.";
                    }       
                }
            } else {
                echo 'Email o contraseña inválida.';
            }
            
        } else {
            echo "Metodo no permitido";
        }
    break;
    case '/login':
        if ($method == 'POST') {
            if(!(empty($_POST['email']) && empty($_POST['password'])) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $user = new Usuario(
                    $_POST['email'] ?? '',
                    '?',
                    base64_encode($_POST['password']) ?? ''
                );
                
                $usuarios = Files::readJson('usuarios.json');
                var_dump($user->login($usuarios));
                if($user->login($usuarios)){
                    echo 'Login exitoso';
                    $jwt = JWT::encode($user, $key);
                    $login = new Login($user, $jwt);
                    echo "<br> JWT: <br>";
                    echo "$jwt";
                } else {
                    echo 'Error al conectarse.';
                }


            } else {
                echo 'Email o contraseña inválida.';
            }
               
        } else {
            echo "Metodo no permitido";
        }
    break;
    case '/precio':        
        if ($method == 'POST') {
            //VER POR QUE NO ME RECONOCE EL TOKEN
            if(Login::verifyToken($_GET['token'], $key, $usuarios) && Usuario::checkAdminEmail($_GET['token'], $key, $usuarios)){
                $precio = new Precio(
                    $_POST['precio_hora'] ?? 0, 
                    $_POST['precio_estadia'] ?? 0,
                    $_POST['precio_mensual'] ?? 0,
                );


                array_push($precios, $precio);
                if($precio->guardarPrecio('precios.json', $precios)){
                    echo "Precios guardado correctamente";
                } else {
                    echo "Error al guardar los precios.";
                }
            } else {
                echo 'Precio o token inválido.';
            }
            
        } else if($method == 'GET'){
            if(Login::verifyToken($token, $key, $usuarios)){
                precio::mostrarPrecios($precios);
            } else {
                echo "Token invalido.";
            }
        }
        else {
            echo "Metodo no permitido";
        }
    break;
    case '/ingreso':        
        if ($method == 'POST') {
            if(!(empty($_POST['patente']) && empty($_POST['fecha_ingreso']) && empty($_POST['tipo']))  && Login::verifyToken($_GET['token'], $key, $usuarios)){
                $materia = new Materia(
                    $_POST['nombre'] ?? '', 
                    $_POST['cuatrimestre'] ?? ''
                );

                if($materia->materiaExists($materias)){
                    echo "La materia ya existe. <br>";
                }else{
                    array_push($materias, $materia);
                    if(Files::saveJson('materias.json', $materias)){
                        echo "Materia guardada correctamente";
                    }       
                }
            } else {
                echo 'Nombre de la materia o cuatrimestre inválidos. / TOKEN INVALIDO.';
            }
            
        } else if($method == 'GET'){
            if(Login::verifyToken($_GET['token'], $key, $usuarios)){
                Materia::mostrarMaterias($materias);
            } else {
                echo "Token invalido.";
            }
        } else {
            echo "Metodo no permitido";
        }
    break;
    
    case '/asignacion':        
        if ($method == 'POST') {
            if(!(empty($_POST['legajo']) && empty($_POST['idMateria']) && empty($_POST['turno'])) && $_POST['legajo'] > 0 && Login::verifyToken($_GET['token'], $key, $usuarios)){
                $asignacion = new Asignacion(
                    $_POST['legajo'] ?? 0,
                    $_POST['idMateria'] ?? 0,                    
                    $_POST['turno'] ?? ''
                );

                if($asignacion->asignacionExists($asignaciones)){
                    echo "Asignacion ya existente. <br>";
                }else{
                    array_push($asignaciones, $asignacion);
                    if(Files::saveJson('materias-precioes.json', $asignaciones)){
                        echo "Asignacion guardada correctamente";
                    }       
                }
            } else {
                echo 'Nombre del precio o legajo inválidos. / TOKEN INVALIDO.';
            }
            
        } else if($method == 'GET'){
            if(Login::verifyToken($_GET['token'], $key, $usuarios)){
                Asignacion::mostrarAsignaciones($asignaciones, $precioes, $materias);
            } else {
                echo "Token invalido.";
            }
        } else {
            echo "Metodo no permitido";
        }
    break;
    default:
        echo 'Path erroneo';        
}

die();
?>