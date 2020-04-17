<?php

class marcas
{
    
    public function __construct($id = 0, $desc = "", $estatus = 1)
    {
        $this->id = $id;
        $this->desc = $desc;
        $this->estatus = $estatus;
      
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "cat_marca";
    const ID = "id";
    const DESC = "descripcion";
    const ESTATUS = "estatus";

    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";
    const ESTADO_URL_INCORRECTA = "No se encuentra la acción solicitada";

    public static function post($peticion)
    {
      
        if ($peticion[0] == 'listar') {
            return self::lista();
        }else if($peticion[0] == 'guardar'){
            return self::registrarOut();
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
     private function registrarOut()
    {
        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);
        
        $resultado = self::registrar($_POST);
        
        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
               http_response_code(200);
               return "OK";
               
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
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
            (($estatus > -1) ? " WHERE " . self::ESTATUS . "=?" : "").
            " ORDER BY ".self::DESC . " ASC" ;

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        
        if($estatus >= 0){
            $sentencia->bindParam(1, $estatus);
        }

        if ($sentencia->execute())
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        else
            return null;
    }

    private function registrar($marcas)
    {
        
        
        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();


            if($marcas["id"] == "0"){
            // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
               self::ID . "," .
                self::DESC . "," .
                self::ESTATUS .")" .
                    " VALUES(?,?,?)";
                    
                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $marcas["id"]);
                $sentencia->bindParam(2, $marcas["descripcion"]);
                $sentencia->bindParam(3, $marcas["estatus"]);

                $resultado = $sentencia->execute();

              
                if ($resultado) {
                    return $pdo->lastInsertId();
                } else {
                    return -1;
                }
            }
            else{
                $comando = "UPDATE " . self::NOMBRE_TABLA . " SET  ".
                self::DESC . "= ?, ".
                self::ESTATUS . "= ? ".
                " WHERE ".self::ID." = ?";

                $sentencia = $pdo->prepare($comando);
                
                $sentencia->bindParam(1, $marcas["descripcion"]);
                $sentencia->bindParam(2, $marcas["estatus"]);
                $sentencia->bindParam(3, $marcas["id"]);
                $resultado = $sentencia->execute();

              
                if ($resultado) {
                    
                    return  $marcas["id"];
                } else {
                    return -1;
                }    

            }

            
        } catch (PDOException $e) {

            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }

    
    
}