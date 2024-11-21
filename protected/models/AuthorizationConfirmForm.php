<?php

class AuthorizationConfirmForm extends FormModel
{
	public $transactionalKey;
	public $authorize;
	public $reject;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('transactionalKey', 'required'),
			//array('transactionalKey', 'numerical', 'integerOnly'=>true),
			array('authorize, reject', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'transactionalKey'=>Yii::t('commons', 'Clave Transaccional'),
		);
	}

	public function formConfig()
	{
		return array(
			'elements'=>array(
				'transactionalKey'=>array(
					'type'=>'password',
					'widgetOptions' => array(
						'htmlOptions' => array(
							'class' => 'secureKeypadInput',
						),
					),
				),
			),
			'buttons'=>array(
				'AuthorizationConfirmForm[authorize]'=>array(
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('authorization', 'Autorizar'),
					'htmlOptions'=>array('value'=>true),
				),
				'AuthorizationConfirmForm[reject]'=>array(
					'buttonType'=>'submit',
					'context'=>'danger',
					'label'=>Yii::t('authorization', 'Rechazar'),
					'htmlOptions'=>array('value'=>true),
				),
			),
		);
	}
}