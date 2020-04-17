<?php

class modelos
{
    
    public function __construct($id = 0, $descripcion = "", $estatus = 1, $id_marca = 0)
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->estatus = $estatus;
        $this->id_marca = $id_marca;
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "cat_modelo";
    const ID = "id";
    const DESCRIPCION = "descripcion";
    const ESTATUS = "estatus";
    const ID_MARCA = "id_marca";
    

    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
      
        if ($peticion[0] == 'listar') {
            return self::lista();
        }else  if ($peticion[0] == 'guardar') {
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
    private function listar()
    {
        $estatus = $_POST['estatus'];
        $estado_id = $_POST['id_marca'];
        
        $comando = "SELECT " .
            self::ID . "," .
            self::DESCRIPCION. ",".
            self::ESTATUS . "," .
            self::ID_MARCA .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE ". self::ID_MARCA ." = ?".
            (($estatus >= -1) ? " AND " . self::ESTATUS . "=?" : "").
            " ORDER BY ".self::DESCRIPCION." ASC";



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


    private function registrarOut(){

        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);

        $resultado = self::registrar($_POST);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                break;
                case self::ESTADO_CREACION_FALLIDA:
                    throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA,"Ha ocurrido un error");
                    break;
            default:
                http_response_code(200);
                return $resultado;
        }


    }

    private function registrar($modelos){

        try{

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            if($modelos["id"] == "0"){

                $comando = "INSERT INTO " .self::NOMBRE_TABLA . " ( ".
                    self::ID . "," .
                    self::DESCRIPCION . "," .
                    self::ESTATUS . "," .
                    self::ID_MARCA . ")" .
                    " VALUES(?,?,?,?)";

                    $sentencia = $pdo->prepare($comando);
                    $sentencia->bindParam(1,$modelos["id"]);
                    $sentencia->bindParam(2,$modelos["descripcion"]);
                    $sentencia->bindParam(3,$modelos["estatus"]);
                    $sentencia->bindParam(4,$modelos["idMarca"]);
                   

                    $resultado = $sentencia->execute();

                    if ($resultado) {
                        return $pdo->lastInsertId();
                    }else{
                        return -1;
                    }

            }else{

                $comando = "UPDATE " . self::NOMBRE_TABLA ."SET " .
                    self::DESCRIPCION . " = ?," .
                    self::ESTATUS . " = ?," .
                    self::ID_MARCA ." = ?" .
                    " WHERE " . self::ID . "= ?";

                    $sentencia = $pdo->prepare($comando);
                    $sentencia->bindParam(1,$modelos["descripcion"]);
                    $sentencia->bindParam(2,$modelos["estatus"]);
                    $sentencia->bindParam(3,$modelos["idMarca"]);
                    $sentencia->bindParam(4,$modelos["id"]);
                    $resultado = $sentencia->execute();

                    if ($resultado) {
                        return $modelos["id"];
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