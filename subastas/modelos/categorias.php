<?php

class categorias
{
    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "categorias";
    const ID_CATEGORIA = "id";
    const DESCRIPCION = "descripcion";
    const STATUS = "status";
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";

    public static function post($peticion)
    {
     
      
        if ($peticion[0] == 'listar') {
            return self::listarCategorias();
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

    
    private function listarCategorias()
    {
        $cuerpo = file_get_contents('php://input');
        $categorias = json_decode($cuerpo);
        
        $resultado = self::listar($categorias);
       
        switch (sizeof($resultado)) {
            case 0:
               http_response_code(200);
               throw new ExcepcionApi(self::SIN_RESULTADOS, "OK",200, null);
               break;
            
            default:
                http_response_code(200);
                return $resultado;
        }
        
    }
    private function listar($datosListar)
    {
        $estatus = $_POST['estatus'];
        
        $comando = "SELECT " .
            self::ID_CATEGORIA . "," .
            self::DESCRIPCION . "," .
            self::STATUS .
            " FROM " . self::NOMBRE_TABLA .
            (($estatus >= 0) ? " WHERE " . self::STATUS . "=?" : "");

        

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        
        if($estatus >= 0){
            $sentencia->bindParam(1, $estatus);
        }

        if ($sentencia->execute())
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        else
            return null;
    }

    
    
}