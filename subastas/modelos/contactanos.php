<?php

class contactanos
{


    // Datos de la tabla "cotizacion"
    const NOMBRE_TABLA = "contactanos";
    const ESTADO_URL_INCORRECTA = "Url incorrecta";
    const PERPAGE = 20;


    public static function post($peticion)
    {
      
        
        if ($peticion[0] == 'guardar') {
            return self::guardar($_POST);
        } 
        elseif ($peticion[0] == 'listar') {
            return self::listar($_POST);
        } 
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   


   public static function guardar($contactanos)
    {
        try{
           
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . 
                        "(nombre,correo,telefono,mensaje,fecha,estatus)" .
                        "VALUES (?,?,?,?,sysdate,0)";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1,$contactanos["nombre"]);
            $sentencia->bindParam(2,$contactanos["correo"]);
            $sentencia->bindParam(3,$contactanos["telefono"]);
            $sentencia->bindParam(4,$contactanos["mensaje"]);

            $resultado = $sentencia->execute();
            
            return  $pdo -> lastInsertId();;
        }catch(Exception $e){
            return 0;
            //throw new ExceptionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
        }
    }
    
     public static function listar($contactanos)
    {
        try{
           
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $comando = " SELECT id as folio, nombre, correo, telefono, mensaje, fecha, estatus from  contactanos ";

            
            if(isset($contactanos["estatus"])){
                if($contactanos["estatus"] != -1){
                    $comando .= " where estatus = ?";
                }
            }

            
            $sentencia = $pdo->prepare($comando);


            if(isset($contactanos["estatus"])){
                if($contactanos["estatus"] != -1){
                    $sentencia->bindParam(1,$contactanos["estatus"]);
                }
            }

             if(isset($contactanos["page"])){
                if($contactanos["page"] != 1){
                    $comando .= " limit ".(self::PERPAGE * ($contactanos["page"] - 1)).",".self::PERPAGE;
                }else{
                    $comando .= " limit 0,".self::PERPAGE;    
                }
            }else{
                $comando .= " limit 0,".self::PERPAGE;
            }

                           
            $sentencia->execute();
            
            
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
          

            

        }catch(Exception $e){
        
            return 0;
        }
    }

   
    
}   