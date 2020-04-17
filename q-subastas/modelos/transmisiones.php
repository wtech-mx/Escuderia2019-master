<?php

class transmisiones
{
    
    public function __construct($id = 0, $desc = "", $estatus = 1)
    {
        $this->id = $id;
        $this->desc = $desc;
        $this->estatus = $estatus;
      
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "cat_transmision";
    const ID = "id";
    const DESC = "descripcion";
    const ESTATUS = "estatus";

    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
      
        if ($peticion[0] == 'listar') {
            return self::lista();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

    
    private function lista()
    {
        $cuerpo = file_get_contents('php://input');
        $tipos = json_decode($cuerpo);
        
        $resultado = self::listar();
       
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
    private function listar()
    {
        $estatus = $_POST['estatus'];
        
        $comando = "SELECT " .
            self::ID . "," .
            self::DESC . "," .
            self::ESTATUS .
            " FROM " . self::NOMBRE_TABLA .
            (($estatus > -1) ? " WHERE " . self::ESTATUS . "=?" : "");

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