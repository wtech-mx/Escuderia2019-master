<?php

class empresas
{
    
    public function __construct($idEmpresa =0, $nombreEmpresa = "", $estatus = 0)
    {
        $this->idEmpresa = $idEmpresa;
        $this->nombreEmpresa = $nombreEmpresa;
        $this->estatus = $estatus;
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "empresas";
    const ID_EMPRESA = "idEmpresa";
    const DESCRIPCION = "nombreEmpresa";
    const ESTATUS = "estatus";
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
     
      
        if ($peticion[0] == 'listar') {
            return self::listarEmpresas();
        }else if ($peticion[0] == 'guardar') {
            return self::registrarOut();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

    
    private function listarEmpresas()
    {
        $cuerpo = file_get_contents('php://input');
        $empresas = json_decode($cuerpo);
        
        $resultado = self::listar($empresas);
       
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
            self::ID_EMPRESA . "," .
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

    private function registrar($empresa)
    {
        
 
        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            if ($empresa["idEmpresa"] == "0"){


                // Sentencia INSERT
                    $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                            self::DESCRIPCION . "," .
                            self::ESTATUS . ")" .
                            " VALUES(?,?)";

                    $sentencia = $pdo->prepare($comando);
                    $sentencia->bindParam(1, $empresa["nombreEmpresa"]);
                    $sentencia->bindParam(2, $empresa["estatus"]);
                       
            

 
                  $resultado = $sentencia->execute();

          
             if ($resultado) {
                 return $pdo->lastInsertId();
                } else {
                return -1;
            }

          }else{
            $comando = "UPDATE " . self::NOMBRE_TABLA . " set " .
                        self::DESCRIPCION . " = ? ," . 
                        self::ESTATUS . " = ? " .
                        " WHERE " . self::ID_EMPRESA . " = ? ";
                        $sentencia = $pdo->prepare($comando);
                        $sentencia->bindParam(1, $empresa["nombreEmpresa"]);
                        $sentencia->bindParam(2, $empresa["estatus"]);
                        $sentencia->bindParam(3,$empresa["idEmpresa"]);

                        $resultado = $sentencia->execute();

            if($resultado){
                return $empresa["idEmpresa"];
            } else{
                return -1;
            }

          }



        } catch (PDOException $e) {
            

            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }

    
    
}