<?php

/**
 * Previous Statement Request
 */
class PreviousStatementRequest
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
    public $Saldodisponible;
    public $Saldoanterior;
    public $Intcorriente;
    public $Intmoratorio;
    public $Gtosfinancieros;
    public $Iva5;
    public $Iva10;
    public $Saldopendcuotas;
    public $Pagominimo;

    function __construct($ccNumber, $periodo, $credentials)
    {
        $this->Nrotarjeta = $ccNumber;
        $this->Fechacierre = $periodo;
        $this->Idusuario = $credentials['user'];
        $this->Pwdusuario = $credentials['password'];
    }
}
