<?php

class PaymentToSuppliersForm extends FormModel{

	public $entityCode;
	public $controlMode;
	public $debitAccount;
	public $paymentDate;
	public $paymentType;
	public $parameters;
	public $invoicesNumber;
	public $transactionalKey;
	public $errorTableShow;

	public $paymentFile;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('entityCode, paymentDate, paymentType, debitAccount, invoicesNumber', 'required'),
			array('invoicesNumber', 'numerical', 'integerOnly'=>true),
			array('transactionalKey, parameters', 'safe'),
			array('paymentFile', 'safe'),
			array('paymentFile', 'file', 'types'=>'csv', 'allowEmpty'=>true, 'on'=>'checkError'),

		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'entityCode'=>Yii::t('commons', 'Empresa'),
			'debitAccount'=>Yii::t('commons', 'Cuenta Débito'),
			'paymentDate'=>Yii::t('supplierPayment', 'Fecha de Pago'),
			'paymentType'=>Yii::t('supplierPayment', 'Tipo de Pago'),
			'paymentFile'=>Yii::t('supplierPayment', 'Archivo de Órdenes de Pago'),
			'invoicesNumber'=>Yii::t('supplierPayment', 'Cantidad de Registros'),
			'transactionalKey'=>Yii::t('commons', 'Clave Transaccional'),
		);
	}

	public function formConfig($entities, $accountOptions, $date)
	{
		return array(
			'id' => 'payment-of-supplier-form',
			'enctype' => 'multipart/form-data',
			'action' => array('supplierVerification'),
			'elements'=>array(
				'entityCode'=>array(
					'type'=>'dropdownlist',
					'items'=>$entities,
				),
				'debitAccount'=>array(
					'type'=>'application.components.widgets.RadioGridInput',
					'gridItems'=>$accountOptions,
				),
				'paymentDate'=>array(
					'type'=>'text',
					'widgetOptions' => array(
						'htmlOptions' => array(
							'data-toggle'=>'tooltip',
							'title'=>'Fecha de Pago dd/mm/yyyy',
							'placeholder'=> 'Fecha de Pago dd/mm/yyyy',
							'value'=>$date,
							'readonly'=>'readonly',
						),
					),
				),
				'paymentType'=>array(
					'type'=>'dropdownlist',
					'items'=>array(
						//'C' => 'Crédito en Cuenta',
						'P' => 'Pago a Proveedores'
					),
				),
				'invoicesNumber'=>array(
					'type'=>'number',
					'widgetOptions' => array(
						'htmlOptions' => array(
							'min' => '1',
						),
					),
				),
				'paymentFile'=>array(
					'type'=>'file',
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
}
?>
