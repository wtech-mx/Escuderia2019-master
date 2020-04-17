<?php

class colores
{
    
    public function __construct($id = 0, $descripcion = "", $estatus = 1, $id_marca = 0)
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->estatus = $estatus;
        $this->id_marca = $id_marca;
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "cat_colores";
    const ID = "id";
    const DESCRIPCION = "descripcion";
    const ESTATUS = "estatus";
    

    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
      
        if ($peticion[0] == 'listar') {
            return self::lista();
        }else if ($peticion[0] == 'guardar'){
            return self::registraOut();
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
        
        $comando = "SELECT " .
            self::ID . "," .
            self::DESCRIPCION. ",".
            self::ESTATUS . 
            " FROM " . self::NOMBRE_TABLA .
            (($estatus >= -1) ? " WHERE " . self::ESTATUS . "=?" : "").
            " order by " . self::DESCRIPCION . " ASC";



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

        $resultado = self::registra($_POST);
        if($resultado){
            return 1;
        }else{
            return 0;
        }
        

    }

    private function registra($colores){

       try{

         $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        if ($colores["id"] == "0") {
            
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::ID . "," .
                self::DESCRIPCION . "," .
                self::ESTATUS . ")" .
                " VALUES (?,?,?)";

                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1,$colores["id"]);
                $sentencia->bindParam(2,$colores["descripcion"]);
                $sentencia->bindParam(3,$colores["estatus"]);

                $resultado = $sentencia->execute();

                if ($resultado) {
                    return $pdo->lastInsertId();
                }else{
                    return -1;
                }

        }else{

            $comando = "UPDATE " . self::NOMBRE_TABLA . " SET " .
                self::DESCRIPCION . " = ?," .
                self::ESTATUS . " = ?" .
                " WHERE " . self::ID . " =?";

                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1,$colores["descripcion"]);
                $sentencia->bindParam(2,$colores["estatus"]);
                $sentencia->bindParam(3,$colores["id"]);

                $resultado = $sentencia->execute();
                if ($resultado) {
                    return $colores["id"];
                }else
                {
                    return -1;
                }



        }


       } catch(PDOEsception $e){

            print_r ($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,$e->getMessage(),400);
            

       }

    }

    
    
}