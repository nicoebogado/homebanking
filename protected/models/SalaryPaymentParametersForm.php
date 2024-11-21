<?php

class SalaryPaymentParametersForm extends FormModel{

	public $id;
	public $accountNumber;
	public $amount;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('id, accountNumber, amount', 'required'),
			array('amount', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('app', 'Orden'),
			'accountNumber'=>Yii::t('app', 'Nro. de Cuenta'),
			'amount'=>Yii::t('app', 'Monto'),
		);
	}
}
?>
