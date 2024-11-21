<?php

class CreateKeyForm extends FormModel
{

  public $document;
  public $area;
  public $telephone;

  public function rules()
  {
    return array(
      array('document, area, telephone', 'required', 'message'=>'Ingrese el valor para {attribute}.'),
    );
  }

  public function attributeLabels(){
		return array(
      'document'=>Yii::t('register', 'Cédula'),
      'key'=>Yii::t('register', 'Area'),
      'telephone'=>Yii::t('register', 'Celular'),
    );
  }

  public function formConfig()
	{
		return array(
			'id' => 'CreateKeyForm',
			'elements'=>array(
        'document'=>array(
					'type'=>'text',
				  'groupOptions'=>array('class'=>'col-md-12'),
				),'area'=>array(
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
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
        'telephone'=>array(
          'type' => 'text',
          'groupOptions'=>array('class'=>'col-md-6'),
        ),
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
				)
			),
		);
	}

}
