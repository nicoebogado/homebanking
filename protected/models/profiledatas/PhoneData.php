<?php

/**
* Objeto Phone para perfil del usuario
*/
class PhoneData extends FormModel
{
    public $area;
    public $interno;
    public $principal;
    public $telefono;
    public $tipo; // particular o laboral
    public $tipolinea; // línea baja o celular
    public $codigotelefono;
    public $accion;

    public function rules()
    {
        return [
            ['area, interno, principal, telefono, tipo, tipolinea, codigotelefono, accion', 'safe'],
        ];
    }

    public function attributeLabels(){
        return array(
          'area'  => Yii::t('updateProfile', 'Área'),
          'interno'  => Yii::t('updateProfile', 'Interno'),
          'principal'     => Yii::t('updateProfile', 'Principal'),
          'telefono'     => Yii::t('updateProfile', 'Teléfono'),
          'tipo'          => Yii::t('updateProfile', 'Tipo'),
          'tipolinea'   => Yii::t('updateProfile', 'Tipo de línea'),
        );
    }

    public function getId()
    {
        return $this->codigotelefono;
    }

    public function getCanDelete()
    {
        return $this->accion == 'E';
    }

    public function formConfig()
    {
        return [
            'showErrorSummary'=>false,
            'elements' => [
                'area' => array(
                    'type'      => 'text',
                ),
                'interno' => array(
                    'type'  => 'text',
                ),
                'principal' => array(
                    'type'  => 'dropdownlist',
                    'items' => array(
                        'N' => 'No',
                        'S' => 'Si',
                    ),
                ),
                'telefono' => array(
                    'type'  => 'text',
                    'widgetOptions' => [
                        'htmlOptions' => [
                            'required' => 'required',
                        ],
                    ],
                ),
                'tipo' => array(
                    'type'  => 'dropdownlist',
                    'items' => array(
                        'P'=>'Particular',
                        'L'=>'Laboral',
                        'O'=>'Otra'
                    ),
                ),
                'tipolinea' => array(
                    'type'  => 'dropdownlist',
                    'items' => array(
                        'B'=>'Línea Baja',
                        'C'=>'Celular',
                        'F'=>'Fax'
                    ),
                ),
            ],
        ];
    }

    public function renderColumn($column)
    {

        switch ($column) {

            case 'principal':
                $label = $this->principal == 'S' ? 'Si' : 'No';
                $label .= CHtml::activeHiddenField($this, '['.$this->codigotelefono.']codigotelefono', [
                    'class' => 'modelId',
                ]);
                break;

            case 'tipo':
                $label = $this->tipo == 'L' ? 'Laboral' : ($this->tipo=='P' ? 'Particular' : 'Otro');
                break;

            case 'tipolinea':
                $label = $this->tipolinea == 'B' ? 'Línea Baja' : ($this->tipolinea=='C' ? 'Celular' : 'Fax');
                break;
            
            default:
                $label = $this->{$column};
                break;
        }

        return $label.CHtml::activeHiddenField($this, '['.$this->codigotelefono.']'.$column);
    }

    public static function columnsConfig()
    {
        return [
            'area' => [
                'name' => 'area',
                'header' => 'Área',
                'type' => 'raw',
            ],
            'interno' => [
                'name' => 'interno',
                'header' => 'Interno',
                'type' => 'raw',
            ],
            'principal' => [
                'name' => 'principal',
                'header' => 'Es Principal',
                'type' => 'raw',
            ],
            'telefono' => [
                'name' => 'telefono',
                'header' => 'Teléfono',
                'type' => 'raw',
            ],
            'tipo' => [
                'name' => 'tipo',
                'header' => 'Tipo',
                'type' => 'raw',
            ],
            'tipolinea' => [
                'name' => 'tipolinea',
                'header' => 'Tipo de línea',
                'type' => 'raw',
            ],
        ];
    }
}
