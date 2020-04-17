<?php

class precio
{
    
    public function __construct($id = 0, $desc = "", $estatus = 1)
    {
        $this->id = $id;
        $this->desc = $desc;
        $this->estatus = $estatus;
      
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "cat_precio";
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
            return self::listar();
        }else if($peticion[0] == 'guardar'){
            return self::registraOut();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 500);
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

    private function registraOut(){

          $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);
        $resultado = self::registrar($_POST);
        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:     
                http_response_code(200);
                return "ok";
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExceptionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            
            default:
                http_response_code(200);
                return $resultado;
        }
    }

    private function registrar($carac){

        try{
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            if ($carac["id"] == "0") {
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::ID . "," .
                    self::DESC . "," .
                    self::ESTATUS . ") " .
                    "VALUES (?,?,?)";

                    $sentencia =  $pdo->prepare($comando);
                    $sentencia->bindParam(1,$carac["id"]);
                    $sentencia->bindParam(2,$carac["descripcion"]);
                    $sentencia->bindParam(3,$carac["estatus"]);

                    $resultado = $sentencia->execute();

                    if ($resultado) {       

                        return $pdo->lastInsertId();
                    } else{
                        return -1;
                    }                       

            }else{  
                $comando = "UPDATE " . self::NOMBRE_TABLA . " SET " .
                    self::DESC . " = ?," .
                    self::ESTATUS . " = ?" .
                    " WHERE " . self::ID . " = ?";
                    $sentencia = $pdo->prepare($comando);
                    $sentencia->bindParam(1,$carac["descripcion"]);
                    $sentencia->bindParam(2,$carac["estatus"]);
                    $sentencia->bindParam(3,$carac["id"]);
                    $resultado = $sentencia->execute();

                if($resultado){

                    return $carac["id"];

                }else{
                    return -1;
                }
            }


        }catch(PDOException $e){
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,$e->getMessage(),400);
            
        }

    }
    
    
}