<?php

/**
* Balance object
*/
class CreditCardBalance
{
    public $Nrotarjeta;// string Nrotarjeta;
    public $Idusuario;//  string Idusuario;
    public $Pwdusuario;//  string Pwdusuario;
    public $Nombrecliente = '';//  string Nombrecliente;
    public $Marca = '';//  string Marca;
    public $Clase = '';//  string Clase;
    public $Afinidad = '';//  string Afinidad;
    public $Moneda = '';//  string Moneda;
    public $Lineacredito = '';//  double Lineacredito;
    public $Dispcomprasnormal = '';//  double Dispcomprasnormal;
    public $Dispcompracuotas = '';//  double Dispcompracuotas;
    public $Dispavanceefectivo = '';//  double Dispavanceefectivo;
    public $Deudacompranormal = '';//  double Deudacompranormal;
    public $Deudacompracuotas = '';//  double Deudacompracuotas;
    public $Deudatotal = '';//  double Deudatotal;
    public $Deudaenmora = '';//  double Deudaenmora;
    public $Pagominpendiente = '';//  double Pagominpendiente;
    public $Fechavtopagomin = '';//  int Fechavtopagomin;
    public $Fechaproxcierre = '';//  int Fechaproxcierre;
    public $Nrotransaccion = '';//  long Nrotransaccion;
    public $Codretorno = '';//  string Codretorno;
    public $Msgretorno = '';//  string Msgretorno;

    function __construct($ccNumber, $credentials)
    {
        $this->Nrotarjeta = $ccNumber;
        $this->Idusuario = $credentials['user'];
        $this->Pwdusuario = $credentials['password'];
    }
}