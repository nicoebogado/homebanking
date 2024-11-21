<?php

class CreditCardBalanceForm extends FormModel
{
	public $account;

	/**
	 * Declares the validation rules
	 */
	public function rules()
	{
		return array(
			array('account', 'required', 'message' => Yii::t('commons', 'Debe seleccionar una cuenta')),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'account' => Yii::t('commons', 'Cuenta'),
		);
	}

	public function formConfig($accounts)
	{
		return array(
			'elements' => array(
				'account' => array(
					'type' => 'application.components.widgets.RadioGridInput',
					'gridItems' => $accounts,
				),
			),
			'buttons' => array(
				'submit' => array(
					'id' => 'submit',
					'buttonType' => 'submit',
					'context' => 'primary',
					'label' => Yii::t('accountBalance', 'Consultar Extracto'),
					'url' => array('checkbooks'),
				),
			),
		);
	}
}
