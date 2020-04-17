<?php

class estados
{
    
    public function __construct($id = 0, $clave = "", $nombre = "", $abrev = "", $activo = 0)
    {
        $this->id = $id;
        $this->clave = $clave;
        $this->nombre = $nombre;
        $this->abrev = $abrev;
        $this->activo = $activo;
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "estados";
    const ID = "id";
    const CLAVE = "clave";
    const NOMBRE = "nombre";
    const ABREV = "abrev";
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
        
        $comando = "SELECT " .
            self::ID . "," .
            self::CLAVE . "," .
            self::NOMBRE . "," .
            self::ABREV . "," .
            self::ACTIVO .
            " FROM " . self::NOMBRE_TABLA .
            (($estatus >= 0) ? " WHERE " . self::ACTIVO . "=?" : "");



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