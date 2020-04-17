<?php

class tiposubastas
{
    
    public function __construct($idTipo =0, $tipoSubasta = "", $estatus = 0)
    {
        $this->idTipo = $idEmpresa;
        $this->tipoSubasta = $tipoSubasta;
        $this->estatus = $estatus;
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "tiposubastas";
    const ID_TIPO = "idTipo";
    const DESCRIPCION = "tipoSubasta";
    const ESTATUS = "estatus";
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
     
      
        if ($peticion[0] == 'listar') {
            return self::tipoSubasta();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

    
    private function tipoSubasta()
    {
        $cuerpo = file_get_contents('php://input');
        $tipos = json_decode($cuerpo);
        
        $resultado = self::listar($tipos);
       
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
            self::ID_TIPO . "," .
            self::DESCRIPCION . "," .
            self::ESTATUS .
            " FROM " . self::NOMBRE_TABLA .
            (($estatus >= 0) ? " WHERE " . self::ESTATUS . "=?" : "");



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