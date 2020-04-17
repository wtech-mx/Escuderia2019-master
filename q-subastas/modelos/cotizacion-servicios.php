<?php

class cotizacionservicios
{


    // Datos de la tabla "cotizacion"
    const NOMBRE_TABLA = "Cotizacion_Servicios";
    const IDCOTIZACION = "idCotizacion";
    const IDSUBSERVICIO = "idSubServicio";



    public static function post($peticion)
    {
      
        
        if ($peticion[0] == 'registro') {
          
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   


   public static function registrarCS($idCotizacion,$subservicios)
    {
        try{
            foreach ($subservicios as $SS) {
             
               
                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . "(" .
                            self::IDCOTIZACION . "," .
                            self::IDSUBSERVICIO . ")" .
                            "VALUES (?,?)";

                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1,$idCotizacion);
                $sentencia->bindParam(2,$SS["idSubServicios"]);

                $resultado = $sentencia->execute();

            }
            return -1;
        }catch(PDOException $e){
           print_r($e);
            throw new ExceptionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
        }
    }
    


   
    
}