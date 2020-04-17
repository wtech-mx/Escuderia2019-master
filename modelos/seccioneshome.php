<?php

class seccioneshome
{
    
    public function __construct($idAuto =0, $idFeature = 0)
    {
        $this->idAuto = $idAuto;
        $this->idFeature = $idFeature;
 
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "cat_seccioneshome";
    const ID = "id";
    const DESCRIPCION = "descripcion";
    const TAG = "tag";
    const URL = "url";
    const ESIMG = "esimg";
    const ESTATUS = "estatus";
   
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
     
      
        if ($peticion[0] == 'update') {
            return self::update();
        }else if ($peticion[0] == 'listar') {
            return self::listar();
        }else if($peticion[0] == 'updatejson'){
            return self::updatejson();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   


     private function listar(){

        $esheader = $_POST["esheader"];
        

        $comando ="SELECT id, descripcion, tag, ancho, alto, url, ubicacion, esimg, eslink, link, estatus FROM cat_seccioneshome "; 
            
        $sentencia =ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        if ($sentencia->execute()){
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        }else
        {
            return null;
        }

            
    }

    private function update(){

        try{
     
            $paramNum = 4;
            $comando ="update cat_seccioneshome set ubicacion = ?, eslink = ?, link = ? ";
            if($_POST['esimg'] == 0){
                $comando .= $comando . ", url = ?";
                $paramNum = 5;
            } 
            $comando .=  "WHERE id = ?";

            
            $sentencia =ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $_POST['ubicacion']);
            $sentencia->bindParam(2, $_POST['eslink']);
            $sentencia->bindParam(3, $_POST['link']);
             if($_POST['esimg'] == 0){
                $sentencia->bindParam(4, $_POST['url']);
                
            } 

            $sentencia->bindParam($paramNum, $_POST['id']);


            
            if ($sentencia->execute())
            {
            
                return "OK";    
            }else{
                return "ERROR";    
            }

            
         }
        catch(Exception $ex){

            return "ERROR";
        }

    }

     private function updatejson(){

        try{
     

            $comando ="SELECT id, descripcion, tag, ancho, alto, url, ubicacion, esimg, eslink, link, estatus FROM cat_seccioneshome "; 
                
            $sentencia =ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $json = "";
            if ($sentencia->execute())
            {
            
                $json =  $sentencia->fetchall(PDO::FETCH_ASSOC);
            
            }else
            {
                $json = "";
            }

            $json = json_encode($json, JSON_PRETTY_PRINT);

            $myfile = fopen( "data/home.json", "w") or die("Unable to open file!");
            fwrite($myfile, $json);
            fclose($myfile);

            return  "OK";
         }
        catch(Exception $ex){

            return "ERROR";
        }

   }
}