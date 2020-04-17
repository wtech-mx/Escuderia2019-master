<?php

class servicios
{


    // Datos de la tabla "cotizacion"
    const NOMBRE_TABLA = "servicios";
    const IDSERVICIOS = "idServicios";
    const NOMBRE = "nombre";
    const ESTATUS = "estatus";



    public static function post($peticion)
    {

        if ($peticion[0] == 'registro') {
            return self::registrar();
        } else if ($peticion[0] == 'listar') {
            return self::listarServicios();
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

  private function registrar()
    {
        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);

        $resultado = self::crear($_POST);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
                //self::registraServicios($_POST);
               http_response_code(200);
               return "OK";
               
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Falla desconocida", 400);
        }
    }

    private function crear($servicio)
    {
        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::ESTATUS . ")" .
                " VALUES(?,?)";
              
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $cotiza["nombre"]);
                       
            $sentencia->bindParam(2, $cotiza["estatus"]);
              
            $resultado = $sentencia->execute();
           
            if ($resultado) {

               return self::ESTADO_CREACION_EXITOSA;
            } else {
                return self::ESTADO_CREACION_FALLIDA;
            }
        } catch (PDOException $e) {
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }

   
   
    private function listarServicios(){

        $cuerpo = file_get_contents('php://input');
        $servicios = json_decode($cuerpo);

        $resultado = self::listar($servicios);
        switch (sizeof($resultado)) {
            case '0':
                http_response_code(200);
                throw new ExceptionApi(self::SIN_RESULTADOS,"OK", 200,null);
                break;
            
            default:
                http_response_code(200);
                return $resultado;
                break;
        }

    }
    

    private function listar($datosListar){
        print_r($datosListar);
        $estatus = $_POST['estatus'];

        $comando = "SELECT " . 
                    self::IDSERVICIOS . "," .
                    self::NOMBRE . " FROM " .
                    self::NOMBRE_TABLA .
                    (($estatus >=0) ? " WHERE " . self::ESTATUS . "=?" : "");
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
      
        if ($estatus >=0) {
            $sentencia->bindParam(1,$estatus);        
        }            

        if ($sentencia->execute()) {
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        }else{
            return null;
        }

    }
}