<?php

/**
 * Last Account Statement Request
 */
class LastStatementRequest
{
    public $Nrotarjeta;
    public $Idusuario;
    public $Pwdusuario;
    public $Nombrecliente;
    public $Marca;
    public $Clase;
    public $Afinidad;
    public $Moneda;
    public $Lineacredito;
    public $Dispcomprasnormal;
    public $Dispcompracuotas;
    public $Dispavanceefectivo;
    public $Deudacompranormal;
    public $Deudacompracuotas;
    public $Deudatotal;
    public $Deudaenmora;
    public $Pagominpendiente;
    public $Fechavtopagomin;
    public $Fechaproxcierre;
    public $Sdtpv_tarconmovimientos;
    public $Sdtpv_lineasdetalle;
    public $Mensajeextracto;
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
