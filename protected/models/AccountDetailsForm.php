<?php

class AccountDetailsForm extends FormModel
{
	public $accountNumber;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('accountNumber', 'required', 'message'=>Yii::t('commons', 'Debe seleccionar una cuenta ')),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'accountNumber'=>Yii::t('commons', 'Cuenta'),
		);
	}

	public function formConfig($accountOptions)
	{
		return array(
			'showErrorSummary'=>false,
			'elements'=>array(
				'accountNumber'=>array(
					'type'=>'application.components.widgets.RadioGridInput',
					'gridItems'=>$accountOptions,
				),
			),
			'buttons'=>array(
				'submit'=>array(
					'id'=>'submit',
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('accountDetails', 'Ver Detalles'),
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
