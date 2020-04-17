<?php

class municipios
{
    
    public function __construct($id = 0, $estado_id = 0, $clave = "", $nombre = "", $activo = 0)
    {
        $this->id = $id;
        $this->estado_id = $estado_id;
        $this->clave = $clave;
        $this->nombre = $nombre;
        $this->activo = $activo;
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "municipios";
    const ID = "id";
    const ESTADO_ID = "estado_id";
    const CLAVE = "clave";
    const NOMBRE = "nombre";
    const ACTIVO = "activo";

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
        $estado_id = $_POST['id_estado'];
        
        $comando = "SELECT " .
            self::ID . "," .
            self::ESTADO_ID. ",".
            self::CLAVE . "," .
            self::NOMBRE . "," .
            self::ACTIVO .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE ". self::ESTADO_ID ." = ?".
            (($estatus >= 0) ? " AND " . self::ACTIVO . "=?" : "");



        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        
        $sentencia->bindParam(1, $estado_id);
        if($estatus >= 0){
            $sentencia->bindParam(2, $estatus);
        }

        if ($sentencia->execute())
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        else
            return null;
    }

    
    
}