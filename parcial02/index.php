<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Models\User;
use App\Controllers\UserController;
use App\Controllers\MateriaController;
use App\Controllers\NotaController;
use App\Controllers\InscripcionController;
use App\Controllers\LoginController;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use Config\Database;
use PsrJwt\Factory\Jwt;
use ReallySimpleJWT\Token;


//require __DIR__.'/config/database.php';
require __DIR__ . './vendor/autoload.php';
$key = 'todorojo';
$app = AppFactory::create();
$conn = new Database();
//si no esta en la raiz hay que indicarle la raiz
$app->setBasePath('/parcial02');
$app->addErrorMiddleware(true, false, false);

//LOGIN
$app->post('/login[/]', function (Request $request, Response $response, $args){

    $rta = LoginController::login($request, $response);

    if(!empty($rta)){
        $token = ["JSON Web Token" => $rta];
        $response->getBody()->write(json_encode($token));
    }

    return $response;
})->add(new JsonMiddleware);

//RUTAS USUARIO
$app->group('/users', function(RouteCollectorProxy $group){

    $group->post('[/]', UserController::class . ":addOne");

})->add(new JsonMiddleware);

$app->group('/materia', function(RouteCollectorProxy $group){

    $group->get('[/]', MateriaController::class . ":getAll");

    $group->get('/{id}', MateriaController::class . ":getOne")->add(new AuthMiddleware('admin', 'profesor'));

    $group->post('[/]', MateriaController::class . ":addOne")->add(new AuthMiddleware('admin'));

})->add(new JsonMiddleware);



$app->group('/inscripcion', function(RouteCollectorProxy $group){

    $group->get('/{id}', InscripcionController::class . ":getOne")->add(new AuthMiddleware('admin', 'profesor'));

    $group->post('/{id}', InscripcionController::class . ":addOne")->add(new AuthMiddleware('admin', 'alumno'));

})->add(new JsonMiddleware);


$app->group('/notas', function(RouteCollectorProxy $group){

    $group->get('/{id}', NotaController::class . ":getOne");

    $group->put('/{id}', NotaController::class . ":updateOne")->add(new AuthMiddleware('profesor'));

})->add(new JsonMiddleware);


$app->addBodyParsingMiddleware();
$app->run();



// hosts gratuitos - tienen que tener c panel
// ar.000webhost.com
// hostinger.com.ar 