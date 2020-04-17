<?php

class usuarioautomovil
{
	const NOMBRE_TABLA = "usuario_automovil";
	const IDUSUARIO = "idUsuario";
	const PLACA = "placa";
	const IDMARCA = "idMarca";
	const IDMODELO = "idModelo";
    const ESTATUS = "estatus";
	const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_ACTUALIZACION_EXITOSA = "ACTUALIZADO";
    const ESTADO_ELIMINACION_EXITOSA = "BORRADO";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
    	if($peticion[0] == 'guardar'){
    		return self::registrarOut();
    	}
    	else if($peticion[0] == 'listar'){
            return self::listarOut();
    	}
    	else{
    		throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,"URL mal Formada",400);
    		
    	}
    }

    private function registrarOut(){

    	$resultado = self::registrar($_POST);
        
    	switch ($resultado) {
    		case self::ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                
                return "Se guardo el vehiculo correctamente.";

                break;
                case self::ESTADO_ACTUALIZACION_EXITOSA:
                http_response_code(200);
                
                return "Se actualizo el vehiculo correctamente.";

                break;
                case self::ESTADO_ELIMINACION_EXITOSA:
                http_response_code(200);
                
                return "El vehiculo fue eliminado.";

                break;
    		case self::ESTADO_CREACION_FALLIDA:
                
    			throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA,"Ha Ocurrido ");
    			
    			break;
    		default:
               
    			http_response_code(200);
                break;
    		
    	}

    }

    private function registrar($usuarioauto){
        try{
    	
    		 $getUsuaId= self::obtenerIdUsuaxCorreo($usuarioauto["correoUsua"]);  



         if ($getUsuaId>0) {

             $pdo = ConexionBD::obtenerInstancia()->obtenerBD();



             $comando = "SELECT " . self::PLACA . 
                        " FROM " . self::NOMBRE_TABLA .
                        " WHERE " . self::PLACA . " = ? AND " . 
                        self::IDUSUARIO ." = ?";
                      
            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1,$usuarioauto["numPlaca"]);
            $sentencia->bindParam(2,$getUsuaId);
            $cuenta = 0;

            if($sentencia->execute()){
                $resultado = $sentencia->fetchall(PDO::FETCH_ASSOC);
                $cuenta = count($resultado);
            }


            if ($cuenta<1){
        
    	       $comando = "INSERT INTO " . self::NOMBRE_TABLA . " (" .
    				self::IDUSUARIO . ", ".
    				self::PLACA  . ", " .
    				self::IDMODELO  . ", " .
    				self::IDMARCA   . ") " .
    				"VALUES (?,?,?,?)";

    	           $sentencia = $pdo->prepare($comando);
    	           $sentencia->bindParam(1, $getUsuaId);
    	           $sentencia->bindParam(2, $usuarioauto["numPlaca"]);    	
    	           $sentencia->bindParam(3, $usuarioauto["idModelo"]);    	
    	           $sentencia->bindParam(4, $usuarioauto["idMarca"]);


                    $resultado = $sentencia->execute();

                    
                    if ($resultado) {
                        $idAuto = $pdo->lastInsertId();
                    
                        return self::ESTADO_CREACION_EXITOSA; 
                

                    } else {
                         return self::ESTADO_CREACION_FALLIDA;
                 }
            }else{

                $comando = "UPDATE " . self::NOMBRE_TABLA . " SET " .
                        self::IDMARCA .  " = ?, " .
                        self::IDMODELO . " = ?, " .
                        self:: ESTATUS . " = ? " .
                        " WHERE " . self::IDUSUARIO . " = ? " .
                        " AND " . self::PLACA . " = ?";

                $sentencia = $pdo->prepare($comando);
                  
                   $sentencia->bindParam(1, $usuarioauto["idMarca"]);    
                   $sentencia->bindParam(2, $usuarioauto["idModelo"]);      
                   $sentencia->bindParam(3, $usuarioauto["estatus"]);   
                   $sentencia->bindParam(4, $getUsuaId);
                    $sentencia->bindParam(5, $usuarioauto["numPlaca"]);   
                    
                   $resultado = $sentencia->execute();
                   if($resultado){
                   
                        if($usuarioauto["estatus"] == 0){
                            return self::ESTADO_ELIMINACION_EXITOSA;
                        }else{
                            return self::ESTADO_ACTUALIZACION_EXITOSA;
                        }

                   }else{
                        return self::ESTADO_CREACION_FALLIDA;
                   }

                
            }


            }else {
                return self::ESTADO_CREACION_FALLIDA;
            }


        } catch (PDOException $e) {

            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
    	}

    }

    private function listarOut(){
        
        $resultado = self::listar($_POST);
     
        switch (sizeof($resultado)) {
            case '0':
                http_response_code(200);
                throw new ExcepcionApi(self::SIN_RESULTADOS,"OK", 200,null);
                break;
            
            default:
                http_response_code(200);
                return $resultado;
                break;
        }


    }

    private function listar($usuarioAuto){
       


        $getUsuaId= self::obtenerIdUsuaxCorreo($usuarioAuto["correoUsua"]);
      
        if ($getUsuaId>0) {

             $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            $comando = "select cmarca.id idMarca,cmarca.descripcion MarcaNombre, cmod.id idModelo,cmod.descripcion ModeloNombre,placa from usuario_automovil ua, cat_marca cmarca, cat_modelo cmod where cmarca.id =cmod.id_marca
                and ua.idMarca = cmod.id_marca and ua.idModelo = cmod.id and ua.estatus=1 and idUsuario = " . $getUsuaId;
                     

            $sentencia = $pdo->prepare($comando);
            
            


            if ($sentencia->execute()) {
                
                $resultado = $sentencia->fetchall(PDO::FETCH_ASSOC);
                
                return $resultado;
            }else{
                 return null;
            }
         }else {
             return null;
         }

        // return $usuarioAuto;

    }


    private function obtenerIdUsuaxCorreo($correo){
        

        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

         $comando = "select idUsuario from usuario where correo = '" . $correo . "'";
          
        $sentencia = $pdo->prepare($comando);

         
        
        if ($sentencia->execute()){
             $resultado =$sentencia->fetchall(PDO::FETCH_ASSOC);
            if (count($resultado)){
                return $resultado[0]["idUsuario"];
            }else{
                return 0;
            }

        }else{
            return 0;
        }
    }

}