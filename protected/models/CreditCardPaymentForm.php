<?php

class CreditCardPaymentForm extends FormModel
{

	public $debitAccount;
	public $creditAccount;
	public $amountOption;
	public $amount;
	public $confirm;
	public $totalDebt;
	public $closingDebt;
	public $minPayment;
	public $currency;
	public $closingDate;
	public $dueDate;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('debitAccount, amount', 'required'),
			array('amount', 'numerical', 'integerOnly' => true),

			array('creditAccount, confirm, totalDebt, closingDebt, minPayment, otherAmount, currency', 'safe'),
			array('creditAccount, confirm', 'required', 'on' => 'confirm'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'debitAccount' => Yii::t('commons', 'Cuenta Débito'),
			'creditAccount' => Yii::t('commons', 'Cuenta Crédito'),
			'amountOption' => Yii::t('commons', 'Opciones de Pago'),
			'amount' => Yii::t('commons', 'Monto'),
		);
	}

	public function getClosingDateDescription()
	{
		return WebServiceClient::formatDate($this->closingDate);
	}

	public function getDueDateDescription()
	{
		return WebServiceClient::formatDate($this->dueDate);
	}

	public function formConfig($cardDenomination, $debitAccountOptions)
	{
		return array(
			'id' => 'creditCardPaymentForm',
			'title' => $this->_formTitle($cardDenomination),
			'showErrorSummary' => false,
			'elements' => array(
				'debitAccount' => array(
					'type' => 'application.components.widgets.RadioGridInput',
					'gridItems' => $debitAccountOptions,
				),
				'amountOption' => $this->_paymentOptions(),
				'amount' => array(
					'type' => 'text',
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
				'creditAccount' => array(
					'type' => 'hidden',
				),
				'amount' => array(
					'type' => 'hidden',
				),
				'confirm' => array(
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

	private function _formTitle($cardDenomination)
	{
		return '
			<h3>Pagar Tarjeta "' . $cardDenomination . '"</h3>
			<div>
				<b>Fecha de cierre:</b> ' . $this->closingDateDescription . '
			</div>
			<div>
				<b>Fecha de vencimiento:</b> ' . $this->dueDateDescription . '
			</div>';
	}

	private function _paymentOptions()
	{
		return '
		<div class="form-group">
			<label class="col-sm-3 control-label" for="CreditCardPaymentForm_amountOption">
				Opciones de Pago
			</label>
			<div class="col-sm-9" style="margin-left:20px">
				<span id="CreditCardPaymentForm_amountOption">
					<label class="radio">
						<input value="' . $this->totalDebt . '" name="amountOption" type="radio">
						Deuda Total (' . $this->_amountDescription($this->totalDebt) . ')
					</label>
					<label class="radio">
						<input value="' . $this->closingDebt . '" name="amountOption" type="radio">
						Deuda al Cierre (' . $this->_amountDescription($this->closingDebt) . ')
					</label>
					<label class="radio">
						<input value="' . $this->minPayment . '" name="amountOption" type="radio">
						Pago Mínimo (' . $this->_amountDescription($this->minPayment) . ')
					</label>
					<label class="radio">
						<input value="om" name="amountOption" type="radio">
						Otro monto
					</label>
				</span>
			</div>
		</div>';
	}

	private function _amountDescription($amount)
	{
		return $this->currency . ' ' . Yii::app()->numberFormatter->formatDecimal($amount);;
	}
}
