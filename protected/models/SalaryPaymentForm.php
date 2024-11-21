<?php

class SalaryPaymentForm extends FormModel{

	public $entityCode;
	public $controlMode;
	public $currencyCode;
	public $totalAmount;
	public $remunerationType;
	public $amountOfCreditAccounts;
	public $recordsReadNumber;
	public $parameters;
	public $transactionalKey;
	public $tokenKey;

	public $paymentFile;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('entityCode, controlMode, totalAmount, remunerationType, recordsReadNumber', 'required'),
			array('totalAmount', 'numerical', 'integerOnly'=>true),

			array('amountOfCreditAccounts, parameters, transactionalKey, tokenKey, paymentFile', 'safe'),
			array('paymentFile', 'safe'),
			array('currencyCode', 'safe'),
			array('paymentFile', 'file', 'types'=>'txt', 'allowEmpty'=>false, 'on'=>'validatePaymentWithFile'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'entityCode'=>Yii::t('commons', 'Empresa'),
			'controlMode'=>Yii::t('salaryPayment', 'Método de Carga'),
			'currencyCode'=>Yii::t('commons', ''),
			'totalAmount'=>Yii::t('salaryPayment', 'Monto Total en Guaraníes'),
			'remunerationType'=>Yii::t('salaryPayment', 'Tipo de Remuneración'),
			'amountOfCreditAccounts'=>Yii::t('salaryPayment', 'Cantidad de Cuentas de Crédito'),
			'recordsReadNumber'=>Yii::t('salaryPayment', 'Cantidad de Registros a Leer'),
			'transactionalKey'=>Yii::t('commons', 'Clave Transaccional'),
		);
	}

	public function formConfig($entities)
	{
		return array(
			'id' => 'salary-payment-form',
			'enctype' => 'multipart/form-data',
			'action' => 'salariesVerification',

			'elements'=>array(
				'entityCode'=>array(
					'type'=>'dropdownlist',
					'items'=>$entities,
				),
				'remunerationType'=>array(
					'type'=>'radiolist',
					'items'=>$this->remunerationTypeOptions(),
					'empty'=>'',
				),
				'recordsReadNumber'=>array(
					'type'=>'text',
				),
				'totalAmount'=>array(
					'type'=>'text',
				),
				'currencyCode'=>array(
					//'type'=>'dropdownlist',
					'widgetOptions'=>array('htmlOptions'=>array(
						'value'=>'GS',
						'style'=>'display:none',
					)),
					'type'=>'text',
					//'items'=>$this->currencyCodeOptions(),
				),
				'controlMode'=>array(
					'type'=>'dropdownlist',
					'items'=>$this->controlModeOptions(),
				),
				'paymentFile'=>array(
					'type'=>'file',
					'label'=>false,
				),
			),
			'buttons'=>array(
				'submit'=>array(
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('commons', 'Enviar'),
				),
			),
		);

	}

	public function remunerationTypeOptions()
	{
		return array(
			'S' => Yii::t('salaryPayment', 'Sueldo'),
			'A' => Yii::t('salaryPayment', 'Aguinaldo'),
			'C' => Yii::t('salaryPayment', 'Comisión'),
			'T' => Yii::t('salaryPayment', 'Tarjeta'),
		);
	}

	public function currencyCodeOptions()
	{
		return array(
			'GS' => Yii::t('salaryPayment', 'Guaraníes'),
			'USD' => Yii::t('salaryPayment', 'Dólares Estadounidenses'),
			'EUR' => Yii::t('salaryPayment', 'Euros'),
			'RS' => Yii::t('salaryPayment', 'Reales'),
		);
	}

	public function controlModeOptions()
	{
		return array(
			'V' => Yii::t('salaryPayment', 'Archivo'),
			'L' => Yii::t('salaryPayment', 'Carga Manual'),
		);
	}
}
?>
