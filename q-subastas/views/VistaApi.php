<?php

abstract class VistaApi{
    
    // Código de error
    public $estado;
    public $data;

    public abstract function imprimir($cuerpo);
}