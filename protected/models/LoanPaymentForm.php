<?php

class LoanPaymentForm extends FormModel
{

	public $debitAccount;
	public $loanNumber;
	public $feesAmount;
	public $confirm;
	public $operationAmount;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('debitAccount, feesAmount', 'required'),
			array('feesAmount', 'numerical', 'integerOnly' => true),

			array('loanNumber, confirm, operationAmount', 'safe'),
			array('loanNumber, confirm, operationAmount', 'required', 'on' => 'confirm'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'debitAccount' => Yii::t('commons', 'Cuenta Débito'),
			'feesAmount' => Yii::t('loanPayment', 'Cantidad de Cuotas a Pagar'),
		);
	}

	public function formConfig($debitAccountOptions)
	{
		return array(
			'showErrorSummary' => false,
			'elements' => array(
				'feesAmount' => array(
					'type' => 'number',
					'widgetOptions' => array(
						'htmlOptions' => array(
							'min' => '1',
						),
					),
				),
				'debitAccount' => array(
					'type' => 'application.components.widgets.RadioGridInput',
					'gridItems' => $debitAccountOptions,
				),
			),
			'buttons' => array(
				'submit' => array(
					'buttonType' => 'submit',
					'context' => 'primary',
					'label' => Yii::t('commons', 'Verificar Pago'),
				),
				'cancel' => array(
					'buttonType' => 'link',
					'context' => 'danger',
					'label' => Yii::t('commons', 'Cancelar'),
					'url' => array('/site/index'),
				),
			),
		);
	}

	public function hiddenFormConfig()
	{
		return array(
			'elements' => array(
				'debitAccount' => array(
					'type' => 'hidden',
				),
				'loanNumber' => array(
					'type' => 'hidden',
				),
				'feesAmount' => array(
					'type' => 'hidden',
				),
				'confirm' => array(
					'type' => 'hidden',
				),
				'operationAmount' => array(
					'type' => 'hidden',
				),
			),
			'buttons' => array(
				'submit' => array(
					'buttonType' => 'submit',
					'context' => 'primary',
					'label' => Yii::t('commons', 'Confirmar Pago'),
				),
				'cancel' => array(
					'buttonType' => 'link',
					'context' => 'danger',
					'label' => Yii::t('commons', 'Cancelar'),
					'url' => array('/site/index'),
				),
			),
		);
	}
}
