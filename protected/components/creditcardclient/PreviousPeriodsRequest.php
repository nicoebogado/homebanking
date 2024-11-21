<?php

/**
 * Previous Periods Request
 */
class PreviousPeriodsRequest
{
    public $NroTarjeta;
    public $IdUsuario;
    public $PwdUsuario;
    public $Nombrecliente;
    public $Marca;
    public $Clase;
    public $Afinidad;
    public $Moneda;
    public $Sdtpv_cierresdisponibles;
    public $Nrotransaccion;
    public $Codretorno;
    public $Msgretorno;

    function __construct($ccNumber, $credentials)
    {
        $this->Nrotarjeta = $ccNumber;
        $this->Idusuario = $credentials['user'];
        $this->Pwdusuario = $credentials['password'];
    }
}

