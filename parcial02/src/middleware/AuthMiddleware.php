<?php

namespace App\Middlewares;

use App\Controllers\UserController;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use PsrJwt\Factory\Jwt;
use ReallySimpleJWT\Token;

class AuthMiddleware
{
    public $roles;

    public function __construct(string $role1, string $role2 = '', string $role3 = '')
    {
        $this->roles = array();
        array_push($this->roles, $role1, $role2, $role3);
    }
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $valido = false;
        $allowed = false;
        $token = $request->getHeader('token')[0] ?? '';
        $jwt = new Jwt();

        if(!empty($token)){
            $valido = Token::validate($token, $jwt->key);
            $payload = Token::getPayload($token, $jwt->key);
            $allowed = in_array($payload['tipo'], $this->roles);
        }

        if (!$valido || !$allowed) {
            $response = new Response();
            throw new \Slim\Exception\HttpForbiddenException($request);
        }
        
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
        $resp = new Response();
        $resp->getBody()->write($existingContent);
        return $resp;
        
        
    }
}

