<?php

class ScheduledSavings extends FormModel
{
    public $monto;
    public $plazo;
    public $fec_inicio;
    public $tipo_ahorro;
    public $ind_renovacion;
    public $nro_cta_debito;
    public $estado;
    public $fec_pri_vencimiento;
    public $cod_moneda;
    public $tip_capitalizacion;
    public $origen;

    public function rules()
    {
        return [
            [['monto', 'plazo', 'fec_inicio', 'tipo_ahorro', 'ind_renovacion', 'nro_cta_debito', 'estado', 'fec_pri_vencimiento', 'cod_moneda', 'tip_capitalizacion', 'origen'], 'required'],
            [['monto', 'plazo'], 'integer'],
            [['fec_inicio', 'fec_pri_vencimiento'], 'date', 'format' => 'php:d/m/Y'],
            [['tipo_ahorro', 'ind_renovacion', 'estado', 'cod_moneda', 'origen'], 'string'],
            [['nro_cta_debito'], 'string', 'max' => 20],
            [['tip_capitalizacion'], 'integer'],
        ];
    }
}