<?php

class ExcepcionApi extends Exception
{
    public $estado;
    public $message;
    public $code;

    public function __construct($estado, $mensaje, $codigo = 400)
    {
        $this->estado = $estado;
        $this->message = $mensaje;
        $this->code = $codigo;
    }
  
}
?>