<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Models\Usuario;
use App\Controllers\UsuarioController;
use App\Controllers\MateriaController;
use App\Controllers\LoginController;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use Slim\Psr7\Message;
use Config\Database;
use PsrJwt\Factory\JwtMiddleware;
use PsrJwt\Handler\Json;



//require __DIR__.'/config/database.php';
require __DIR__ . './vendor/autoload.php';
$key = 'todorojo';
$app = AppFactory::create();
$conn = new Database();
//si no esta en la raiz hay que indicarle la raiz
$app->setBasePath('/modelo_slim');
$app->addErrorMiddleware(true, false, false);

//LOGIN
$app->post('/login[/]', function (Request $request, Response $response, $args){

    $rta = LoginController::login($request);

    $response->getBody()->write("JSON Web Token: " .PHP_EOL.json_encode($rta,JSON_PRETTY_PRINT));

    return $response;
});
//RUTAS USUARIO
$app->group('/usuario', function(RouteCollectorProxy $group){
    
    $group->get('[/]', UsuarioController::class . ":getAll")->add(new AuthMiddleware('admin'));

    $group->get('/{id}', UsuarioController::class . ":getOne")->add(new AuthMiddleware('admin'));

    $group->post('[/]', UsuarioController::class . ":addOne");

    $group->put('/{id}', UsuarioController::class . ":updateOne")->add(new AuthMiddleware('admin'));

    $group->put('/{id}', UsuarioController::class . ":updateAlumno")->add(new AuthMiddleware('admin', 'alumno'));
    
    $group->delete('/{id}', UsuarioController::class . ":deleteOne")->add(new AuthMiddleware('admin'));

});

$app->group('/materia', function(RouteCollectorProxy $group){

    $group->get('[/]', MateriaController::class . ":getAll");

    $group->get('/{id}', MateriaController::class . ":getOne");

    $group->post('[/]', MateriaController::class . ":addOne");

    $group->put('/{id}', MateriaController::class . ":updateOne");
    
    $group->delete('/{id}', MateriaController::class . ":deleteOne");

})->add(new AuthMiddleware('admin'));




$app->addBodyParsingMiddleware();
$app->run();



// hosts gratuitos - tienen que tener c panel
// ar.000webhost.com
// hostinger.com.ar 