<?php
class Verificadora
{
    public function VerificarCredenciales($request,$response,$next){
        if($request->isget())
        {
          $response->getBody()->write("Ingresando al middleware por GET<br>");
          $response = $next($request,$response);
        }
        else
        {
          $response->getBody()->write("Ingresando al middleware por POST/DELETE<br>");
          $ArrayDeParametros = $request->getParsedBody();
          //$ArrayDeParametros = $request->getHeaders();
          var_dump($ArrayDeParametros);
          $usuario = new stdClass();
          $usuario->nombre = $ArrayDeParametros["nombre"];
          $usuario->clave = $ArrayDeParametros["clave"];

          if(Verificadora::ExisteUsuario($usuario))
          {
              $response = $next($request,$response);
          }
          else
          {
              $response->getBody()->write("Usuario no encontrado<br>");
          }
        }
  
        return $response;
    }

    private static function ExisteUsuario($obj){
        $retorno = false;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM `usuarios` WHERE `clave` = :clave && `nombre` = :nombre");        
        $consulta->bindValue(":clave",$obj->clave,PDO::PARAM_STR);
        $consulta->bindValue(":nombre",$obj->nombre,PDO::PARAM_STR);
        $consulta->execute();
        if($consulta->rowCount() != 0)
        {
            $retorno = true;
        }
        return $retorno;
    }

    public static function ComprobarAdmin($request,$response,$next){
        $ArrayDeParametros = $request->getParsedBody();
        if($ArrayDeParametros["tipo"] == "admin")
          {
              $response = $next($request,$response);
          }
          else
          {
              $response->getBody()->write("NO tiene permitido agregar usuarios<br>");
          }

        return $response;
    }

    public static function ComprobarSuperAdmin($request,$response,$next){
        $ArrayDeParametros = $request->getParsedBody();
        if($ArrayDeParametros["tipo"] == "super_admin")
          {
              $response = $next($request,$response);
          }
          else
          {
              $response->getBody()->write("NO tiene permitido eliminar usuarios<br>");
          }

        return $response;
    }
}