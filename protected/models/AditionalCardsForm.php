<?php

/**
 * AditionalCardsForm class.
 */
class AditionalCardsForm extends FormModel
{
	public $account;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('account', 'required'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'account'=>Yii::t('commons', 'Cuenta'),
		);
	}

	public function formConfig($accounts)
	{
		return array(
			//'title'=> 'Seleccione una cuenta',
			'elements'=>array(
				'account'=>array(
					'type'=>'application.components.widgets.RadioGridInput',
					'gridItems'=>$accounts,
				),
			),
			'buttons'=>array(
				'submit'=>array(
					'id'=>'submit',
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('commons', 'Consultar'),
					'url'=>array('aditionalCards'),
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
