<?php

class RegisterForm extends FormModel
{

  public $document;
  public $name;
  public $lastName;
  public $address;
  public $cityCode;
  public $districtCode;
  public $houseNumber;
  public $telephone;
  public $areaCode;
  public $latitude;
  public $longitude;
  public $email;
  public $birthdate;

  public function rules()
  {
    return array(
      array('document, name, email, birthdate, lastName, address, cityCode, districtCode, houseNumber, telephone, latitude, longitude', 'required', 'message'=>'Ingrese el valor para {attribute}.'),
      array('districtCode', 'greaterThanZero')
    );
  }

  public function greaterThanZero($attribute,$params){
    if($this->$attribute<=0){
      $this->addError($attribute, 'Debe seleccionar el barrio');
    }
  }

  public function attributeLabels(){
		return array(
      'document'=>Yii::t('register', 'Cédula'),
      'name'=>Yii::t('register', 'Nombre'),
      'lastName'=>Yii::t('register', 'Apellido'),
      'address'=>Yii::t('register', 'Direción'),
      'cityCode'=>Yii::t('register', 'Ciudad'),
      'districtCode'=>Yii::t('register', 'Barrio'),
      'houseNumber'=>Yii::t('register', 'Número'),
      'telephone'=>Yii::t('register', 'Celular'),
      'areaCode'=>Yii::t('register', 'Código'),
      'latitude'=>Yii::t('register', 'Latitud'),
      'longitude'=>Yii::t('register', 'Longitud'),
      'email'=>Yii::t('register', 'Email'),
      'birthdate'=>Yii::t('register', 'Nacimiento'),
    );
  }

  public function formConfig($citiesOptions)
	{
		return array(
			'id' => 'RegisterForm',
			'elements'=>array(
        'name'=>array(
					'type'=>'text',
				  'groupOptions'=>array('class'=>'col-md-6'),
				),
        'lastName'=>array(
					'type'=>'text',
				  'groupOptions'=>array('class'=>'col-md-6'),
				),
        'document'=>array(
					'type'=>'text',
				  'groupOptions'=>array('class'=>'col-md-6'),
				),
        'email'=>array(
					'type'=>'text',
				  'groupOptions'=>array('class'=>'col-md-6'),
				),
        'cityCode'=>array(
          'type'=>'dropdownlist',
					'items'=>$citiesOptions,
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'districtCode'=>array(
          'type'=>'dropdownlist',
					'items'=>array(),
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'address'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'houseNumber'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'areaCode'=>array(
          'type' => 'dropdownlist',
          'items' => array(
            '0961'=>'0961',
            '0962'=>'0962',
            '0963'=>'0963',
            '0971'=>'0971',
            '0972'=>'0972',
            '0973'=>'0973',
            '0974'=>'0974',
            '0975'=>'0975',
            '0976'=>'0976',
            '0981'=>'0981',
            '0982'=>'0982',
            '0983'=>'0983',
            '0984'=>'0984',
            '0985'=>'0985',
            '0986'=>'0986',
            '0988'=>'0988',
            '0991'=>'0991',
            '0992'=>'0992',
            '0993'=>'0993',
            '0994'=>'0994',
            '0995'=>'0995',
          ),
          'groupOptions'=>array('class'=>'col-md-3'),
        ),
        'telephone'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'birthdate'=>array(
          'type' => 'TbDatePicker',
          'widgetOptions'=>array('htmlOptions'=>array(
            'class'=>'form-control',
          )),
          'options'=>array(
                          'format'=> "dd/mm/yyyy",
                          'orientation'=> "bottom right",
                          'autoclose'=> true,
                        ),
          'groupOptions'=>array('class'=>'col-md-8'),
        ),
        'latitude'=>array(
          'type' => 'hidden',
          'groupOptions'=>array('class'=>'col-md-2'),
        ),
        'longitude'=>array(
          'type' => 'hidden',
          'groupOptions'=>array('class'=>'col-md-2'),
        )
			),
			'buttons'=>array(
				'submit'=>array(
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('commons', 'Generar'),
				),
				'cancel'=>array(
					'buttonType'=>'link',
					'context'=>'danger',
					'label'=>Yii::t('commons', 'Cancelar'),
					'url'=>array('/site/index'),
				),
        'map'=>array(
					'context'=>'success',
					'label'=>Yii::t('commons', 'Ubique su dirección'),
          'id'=>"map",
				),
			),
		);
	}

}
