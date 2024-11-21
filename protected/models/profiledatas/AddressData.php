<?php

/**
* Objeto Address para perfil del usuario
*/
class AddressData extends FormModel
{
    public $ciudad;
    public $barrio;
    public $codigoDireccion;
    public $codigoCiudad;
    public $codigoBarrio;
    public $direccion;
    public $principal;
    public $tipo;
    public $observacion;
    public $accion;
    public $numero;
    public $departamento;
    public $edificio;
    public $piso;

    public function rules()
    {
        return [
            ['ciudad, codigoDireccion, codigoCiudad, codigoBarrio, direccion, principal, tipo, observacion, numero, departamento, edificio, piso', 'safe'],
        ];
    }

    public function attributeLabels(){
        return array(
            'codigoCiudad'  => Yii::t('updateProfile', 'Ciudad'),
            'codigoBarrio'  => Yii::t('updateProfile', 'Barrio'),
            'direccion'     => Yii::t('updateProfile', 'Dirección'),
            'principal'     => Yii::t('updateProfile', 'Principal'),
            'tipo'          => Yii::t('updateProfile', 'Tipo'),
            'observacion'   => Yii::t('updateProfile', 'Observación'),
            'numero'        => Yii::t('updateProfile', 'Nro Casa'),
            'departamento'  => Yii::t('updateProfile', 'Nro Departamento'),
            'edificio'      => Yii::t('updateProfile', 'Edificio'),
            'piso'          => Yii::t('updateProfile', 'Piso'),
        );
    }

    public function getId()
    {
        return $this->codigoDireccion;
    }

    public function getCanDelete()
    {
        return $this->accion == 'E';
    }

    public function formConfig($options)
    {
        return [
            'showErrorSummary'=>false,
            'elements' => [
                'codigoCiudad' => array(
                    'type'          => 'dropdownlist',
                    'items'         => $options['cities'],
                    'empty'         => 'Seleccione un País',
                    'widgetOptions' => [
                        'htmlOptions' => [
                            'data-onchangeurl'  => Yii::app()->createUrl('/user/districtOptionsAjax'),
                            'data-labelid'      => 'ciudad',
                        ],
                    ],
                ),
                'codigoBarrio' => array(
                    'type'  => 'dropdownlist',
                    'items' => $options['districts'],
                    'empty' => 'Seleccione una ciudad',
                    'widgetOptions' => [
                        'htmlOptions' => [
                            'data-labelid' => 'barrio',
                        ],
                    ],
                ),
                'direccion' => array(
                    'type'  => 'text',
                    'widgetOptions' => [
                        'htmlOptions' => [
                            'required' => 'required',
                        ],
                    ],
                ),
                'numero' => [
                    'type' => 'text',
                ],
                'edificio' => [
                    'type' => 'text',
                ],
                'piso' => [
                    'type' => 'text',
                ],
                'departamento' => [
                    'type' => 'text',
                ],
                'principal' => array(
                    'type'  => 'dropdownlist',
                    'items' => array(
                        'N' => 'No',
                        'S' => 'Si',
                    ),
                ),
                'tipo' => array(
                    'type'  => 'dropdownlist',
                    'items' => array(
                        'P'=>'Particular',
                        'L'=>'Laboral',
                        'O'=>'Otra'
                    ),
                ),
                'observacion' => array(
                    'type'  => 'textarea',
                ),
            ],
        ];
    }

    public function renderColumn($column)
    {

        switch ($column) {
            case 'codigoCiudad':
                $label = $this->ciudad;
                $label .= CHtml::activeHiddenField($this, '['.$this->codigoDireccion.']ciudad');
                $label .= CHtml::activeHiddenField($this, '['.$this->codigoDireccion.']codigoDireccion', [
                    'class' => 'modelId',
                ]);
                break;

            case 'codigoBarrio':
                $label = $this->barrio;
                $label .= CHtml::activeHiddenField($this, '['.$this->codigoDireccion.']barrio');
                break;

            case 'principal':
                $label = $this->principal == 'S' ? 'Si' : 'No';
                break;

            case 'tipo':
                $label = $this->tipo == 'L' ? 'Laboral' : ($this->tipo=='P' ? 'Particular' : 'Otro');
                break;
            
            default:
                $label = $this->{$column};
                break;
        }
        return $label.CHtml::activeHiddenField($this, '['.$this->codigoDireccion.']'.$column);
    }

    public static function columnsConfig()
    {
        return [
            'codigoCiudad' => [
                'name' => 'codigoCiudad',
                'header' => 'Ciudad',
                'type' => 'raw',
            ],
            'codigoBarrio' => [
                'name' => 'codigoBarrio',
                'header' => 'Barrio',
                'type' => 'raw',
            ],
            'direccion' => [
                'name' => 'direccion',
                'header' => 'Dirección',
                'type' => 'raw',
            ],
            'numero' => [
                'name' => 'numero',
                'header' => 'Nro Casa',
                'type' => 'raw',
            ],
            'edificio' => [
                'name' => 'edificio',
                'header' => 'Edificio',
                'type' => 'raw',
            ],
            'piso' => [
                'name' => 'piso',
                'header' => 'Piso',
                'type' => 'raw',
            ],
            'departamento' => [
                'name' => 'departamento',
                'header' => 'Nro Departamento',
                'type' => 'raw',
            ],
            'principal' => [
                'name' => 'principal',
                'header' => 'Es Principal',
                'type' => 'raw',
            ],
            'tipo' => [
                'name' => 'tipo',
                'header' => 'Tipo',
                'type' => 'raw',
            ],
            'observacion' => [
                'name' => 'observacion',
                'header' => 'Observación',
                'type' => 'raw',
            ],
        ];
    }
}
