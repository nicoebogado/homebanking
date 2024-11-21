<?php

class ActivateKeyForm extends FormModel
{

  public $document;
  public $key;

  public function rules()
  {
    return array(
      array('document, key', 'required', 'message'=>'Ingrese el valor para {attribute}.'),
    );
  }

  public function attributeLabels(){
		return array(
      'document'=>Yii::t('register', 'Cédula'),
      'key'=>Yii::t('register', 'Clave'),
    );
  }

  public function formConfig()
	{
		return array(
			'id' => 'ActivateKeyForm',
			'elements'=>array(
                'document'=>array(
					'type'=>'text',
				    'groupOptions'=>array('class'=>'col-md-12'),
				),
                'key'=>array(
    				'type'=>'password',
                    'groupOptions'=>array('class'=>'col-md-12'),
                    'widgetOptions' => array(
                        'htmlOptions' => array(
                            'class' => 'secureKeypadInput',
                        ),
                    ),
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
