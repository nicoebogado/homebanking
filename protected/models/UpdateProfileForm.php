<?php

class UpdateProfileForm extends FormModel
{

  public $email;
  public $summaryType;
  public $courierOffice;
  public $clientOffice;
  public $hold;

  public $district;
  public $city;
  public $addressCode;
  public $address;
  public $isDefaultAddres;
  public $addressType;
  public $shippingType;

  public $areaCode;
  public $telephoneCode;
  public $extensionNumber;
  public $isDefaultTelephone;
  public $telephoneNumber;
  public $telephoneType;
  public $lineType;

  public function rules()
	{
		return array();
	}

  public function attributeLabels(){
		return array(
      'email'=>Yii::t('updateProfile', 'Email'),
      'summaryType'=>Yii::t('updateProfile', 'Extracto'),
      'courierOffice'=>Yii::t('updateProfile', 'Courrier'),
      'clientOffice'=>Yii::t('updateProfile', 'Ofic.Cliente'),
      'hold'=>Yii::t('updateProfile', 'Retener'),
      'city[]'=>Yii::t('updateProfile', 'Ciudad'),
      'district[]'=>Yii::t('updateProfile', 'Barrio'),
      'addressCode[]'=>Yii::t('updateProfile', 'Código de Dirección'),
      'address[]'=>Yii::t('updateProfile', 'Dirección'),
      'isDefaultAddres[]'=>Yii::t('updateProfile', 'Principal'),
      'addressType[]'=>Yii::t('updateProfile', 'Tip.Dirección'),
      'shippingType[]'=>Yii::t('updateProfile', 'Tip.Envío'),
      'areaCode[]'=>Yii::t('updateProfile', 'Cód.Area'),
      'telephoneCode[]'=>Yii::t('updateProfile', 'telephoneCode'),
      'extensionNumber[]'=>Yii::t('updateProfile', 'Interno'),
      'isDefaultTelephone[]'=>Yii::t('updateProfile', 'Principal'),
      'telephoneNumber[]'=>Yii::t('updateProfile', 'Teléfono'),
      'telephoneType[]'=>Yii::t('updateProfile', 'Tip.Teléfono'),
      'lineType[]'=>Yii::t('updateProfile', 'Tip.Línea'),
    );
  }

  public function formConfig($citiesOptions)
	{
		return array(
			'id' => 'UpdateProfileForm',
			'elements'=>array(
				'email'=>array(
					'type'=>'text',
					'widgetOptions'=>array('htmlOptions'=>array(
						'value'=>''
					)),
          'groupOptions'=>array('class'=>'col-md-8'),
				),
				'summaryType'=>array(
          'type' => 'hidden',
          'items' => array(
            'C' => 'Impreso',
            'Z' => 'Mail',
            'N' => 'No',
            'X' => 'Mail e Impreso',
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
				),
        'courierOffice'=>array(
          'type' => 'hidden',
          'items' => array(
            'S' => 'Si',
            'N' => 'No',
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'clientOffice'=>array(
          'type' => 'hidden',
          'groupOptions'=>array('class'=>'col-md-4'),
        ),
        'hold'=>array(
          'type' => 'hidden',
          'items' => array(
            'S' => 'Si',
            'N' => 'No',
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'city[]'=>array(
          'type'=>'dropdownlist',
					'items'=>$citiesOptions,
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'district[]'=>array(
          'type'=>'dropdownlist',
					'items'=>array(),
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'addressCode[]'=>array(
          'type' => 'hidden'
        ),
        'address[]'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'isDefaultAddres[]'=>array(
          'type' => 'dropdownlist',
          'items' => array(
            'N' => 'No',
            'S' => 'Si',
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'addressType[]'=>array(
          'type' => 'dropdownlist',
          'items' => array(
            'P'=>'Particular',
            'L'=>'Laboral',
            'O'=>'Otra'
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'shippingType[]'=>array(
          'type' => 'hidden',
          'items' => array(
             'D' => 'Direccion', 'R' => 'Retener', 'C' => 'Casilla de Correo', 'K' => 'Casillero',
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'areaCode[]'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'telephoneCode[]'=>array(
          'type' => 'hidden'
        ),
        'extensionNumber[]'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'isDefaultTelephone[]'=>array(
          'type' => 'dropdownlist',
          'items' => array(
            'N' => 'No',
            'S' => 'Si',
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'telephoneNumber[]'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-3'),
        ),
        'telephoneType[]'=>array(
          'type' => 'dropdownlist',
          'items' => array(
            'L'=>'Laboral','P'=>'Particular'
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'lineType[]'=>array(
          'type' => 'dropdownlist',
          'items' => array(
            'B'=>'Baja', 'C'=> 'Celular',
          ),
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
			),
			'buttons'=>array(
				'submit'=>array(
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('commons', 'Actualizar'),
				),
				'cancel'=>array(
					'buttonType'=>'link',
					'context'=>'danger',
					'label'=>Yii::t('commons', 'Cancelar'),
					'url'=>array('/site/index'),
				),
			),
		);
	}

}
