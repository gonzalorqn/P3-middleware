<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once './vendor/autoload.php';
require_once './verificadora.php';
require_once './AccesoDatos.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("GET => Bienvenido!!! a SlimFramework");
    return $response;
});
$app->post('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("POST => Bienvenido!!! a SlimFramework");
  return $response;
});
$app->put('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("PUT => Bienvenido!!! a SlimFramework");
  return $response;
});
$app->delete('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("DELETE => Bienvenido!!! a SlimFramework");
  return $response;
});



$app->group('/credenciales', function () {    
    $this->post('[/]', function (Request $request, Response $response) { 
        $ArrayDeParametros = $request->getParsedBody();
        $response->getBody()->write("POST (grupo) => Bienvenido " . $ArrayDeParametros["nombre"] . "!!!");
      return $response;
    
    });
    $this->get('[/]', function (Request $request, Response $response, $args) { 
        $response->getBody()->write("GET (grupo) => Bienvenido!!!");
      return $response;
    
    });
  })->add(function($request,$response,$next){
      if($request->isget())
      {
        $response->getBody()->write("Ingresando al middleware por GET<br>");
        $response = $next($request,$response);
      }
      else
      {
        $response->getBody()->write("Ingresando al middleware por POST<br>");
        $ArrayDeParametros = $request->getParsedBody();
        if($ArrayDeParametros["tipo"] == "administrador")
        {
            $response = $next($request,$response);
        }
        else
        {
            $response->getBody()->write("NO tiene permitido el acceso<br>");
        }
      }

      return $response;
  });

  $app->group('/credenciales/POO', function () {    
    $this->post('[/]', function (Request $request, Response $response) { 
        $ArrayDeParametros = $request->getParsedBody();
        $response->getBody()->write("POST => Bienvenido " . $ArrayDeParametros["nombre"] . "!!! Ya puede agregar usuarios");
      return $response;
    
    })->add(\Verificadora::class . "::ComprobarAdmin");
    $this->delete('[/]', function (Request $request, Response $response) { 
        $ArrayDeParametros = $request->getParsedBody();
        $response->getBody()->write("DELETE => Bienvenido " . $ArrayDeParametros["nombre"] . "!!! Ya puede eliminar usuarios");
      return $response;
    
    })->add(\Verificadora::class . "::ComprobarSuperAdmin");
    $this->get('[/]', function (Request $request, Response $response, $args) { 
        $response->getBody()->write("GET => Bienvenido!!!");
      return $response;
    
    });
  })->add(\Verificadora::class . ":VerificarCredenciales");

$app->run();