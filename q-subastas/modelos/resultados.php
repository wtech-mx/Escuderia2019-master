<?php

class resultados
{

    public function __construct($autoid = 0, $marca="", $modelo="", $anio = 0, $precio = 0, $foto = "", $oferta= 0, $usuarioganador = 0, $usuario ="", $ofertas = [], $puja = "", $hora_puja = "")
    {
    	$this->autoid = $autoid;
    	$this->marca = $marca;
    	$this->modelo= $modelo;
    	$this->anio = $anio;
    	$this->precio = $precio;
    	$this->foto = $foto;
    	$this->oferta = $oferta;
    	$this->usuarioganador = $usuarioganador;
    	$this->usuario = $usuario;
    	$this->ofertas = $ofertas;
        $this->puja = $puja;
        $this->hora_puja = $hora_puja;
        $this->ts = 0;
        $this->ganancia = 0;
        $this->estatus = 0;
        $this->motivo = '';

    }
}
