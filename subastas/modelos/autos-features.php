<?php

class autosfeatures
{
    
    public function __construct($idAuto =0, $idFeature = 0)
    {
        $this->idAuto = $idAuto;
        $this->idFeature = $idFeature;
 
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "autos_catacteristicas";
    const ID_AUTO = "idAuto";
    const ID_FEATURE = "idFeature";
   
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
     
      
        if ($peticion[0] == 'guardar') {
            return self::registrarOut();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

    


    public static function eliminaEmpresas($idAuto){
        try {

            
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $comando = "DELETE FROM  " . self::NOMBRE_TABLA . " WHERE  " .
                    self::ID_AUTO . " = ?";
                    

                


                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $idAuto);
                       

                $resultado = $sentencia->execute();

              
                if ($resultado) {
                    return $resultado;
                } else {
                    return -1;
                }

                        
            
            return -1;
        } catch (PDOException $e) {
            
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }    

    public static function elimina($idAuto){
        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $comando = "delete from ". self::NOMBRE_TABLA . " WHERE ".self::ID_AUTO ." = ?";
            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idAuto);
            
            if($sentencia->execute()){
                return true;
            }else{
                return false;

            }
        }
        catch(Exception $e){

            print_r($e);
            return false;
        }

    }

    public static function registrar($features, $idAuto)
    {
        
        $resultado = false;
        try {

            
            foreach ($features as $v) {

                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::ID_AUTO . "," .
                    self::ID_FEATURE . ")" .
                    " VALUES(?,?)";

                


                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $idAuto);
                $sentencia->bindParam(2, $v);
             

                $resultado = $sentencia->execute();
               

                        
            }

             if ($resultado) {
                return $resultado;
            } else {
                return false;
            }
            
        } catch (PDOException $e) {
            
            //print_r($e);
            return  new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }

    
    
}