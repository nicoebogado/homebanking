<?php
Yii::import('application.models.traits.HasDetectIdInput');

class TransferForm extends FormModel
{
	use HasDetectIdInput;

	public $debitAccount;
	public $creditAccount;
	public $isThird;
	public $thirdCreditAccount;
	public $thirdDocType;
	public $thirdDocNumber;
	public $isToken;

	public $amount;
	public $concept;
	public $confirm;

	public $hasAgreement;

	public $exchangeContract;
	public $creditQuotation;
	public $saveFrequent;
	public $beneficiaryName;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return $this->addDetectIdInputRules([
			['debitAccount, amount, concept, isThird, amount', 'required'],
			['confirm, hasAgreement, exchangeContract, creditQuotation, creditAccount, thirdCreditAccount, thirdDocType, thirdDocNumber, saveFrequent, beneficiaryName', 'safe'],
		]);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return $this->addDetectIdAttributeLabels([
			'debitAccount' => Yii::t('commons', 'Cuenta Débito'),
			'creditAccount' => Yii::t('commons', 'Cuenta Crédito'),
			'thirdCreditAccount' => Yii::t('commons', 'Cuenta Crédito'),
			'thirdDocType' => Yii::t('commons', 'Tipo de documento'),
			'thirdDocNumber' => Yii::t('commons', 'Nro. de documento'),
			'isThird' => Yii::t('transfers', 'Es Otro Titular?'),
			'amount' => Yii::t('commons', 'Monto'),
			'concept' => Yii::t('transfers', 'Concepto'),
			'hasAgreement' => Yii::t('transfers', '<!--Acordamos Cotización?-->'),
			'exchangeContract' => Yii::t('transfers', 'Número del Contrato de Cambio'),
			'creditQuotation' => Yii::t('transfers', 'Cotización Acordada'),
			'detectIdToken' => 'Mobile Token',
			'saveFrequent' => Yii::t('transfers', 'Grabar Frecuente'),
			'beneficiaryName' => Yii::t('transfers', 'Nombre'),
		]);
	}

	public function formConfig($accountOptions, $gridOptions, $docTypeOptions)
	{
		return array(
			'id' => 'transferForm',
			'action' => array('verify'),
			'elements' => array(
				'debitAccount' => array(
					'type' => 'dropdownlist',
					'items' => $accountOptions,
					'widgetOptions' => array('htmlOptions' => array(
						'prompt' => Yii::t('commons', 'Seleccione una Cuenta'),
						'class' => 'required',
					)),
				),
				'isThird' => array(
					'type' => 'dropdownlist',
					'items' => array(
						0 => 'No',
						1 => 'Si',
					),
					'groupOptions' => array('class' => 'col-md-2'),
				),
				'creditAccount' => array(
					'type' => 'dropdownlist',
					'items' => $accountOptions,
					'widgetOptions' => array('htmlOptions' => array(
						'prompt' => Yii::t('commons', 'Seleccione una Cuenta'),
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-10'),
				),
				'thirdCreditAccount' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'hidden col-md-3'),
				),
				'thirdDocType' => array(
					'type' => 'dropdownlist',
					'items' => $docTypeOptions,
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'hidden col-md-3'),
				),
				'thirdDocNumber' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'hidden col-md-4'),
				),
				'beneficiaryName' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'readonly' => 'readonly',
					)),
					'groupOptions' => array('class' => 'hidden col-md-12'),
				),
				'saveFrequent' => array(
					'type' => 'dropdownlist',
					'items' => array(
						0 => 'No',
						1 => 'Si',
					),
					'groupOptions' => array('class' => 'hidden col-md-10'),
				),
				'amount' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
						'data-inputmask' => "'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true"
					)),
					'groupOptions' => array('class' => 'col-md-3'),
				),
				'concept' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-9'),
				),
				'hasAgreement' => array(
					'type' => 'dropdownlist',
					'items' => array(
						0 => 'No',
						1 => 'Si',
					),
					'widgetOptions' => array('htmlOptions' => array(
						'style'=>'display: none;'
					)),
					'groupOptions' => array('class' => 'hidden col-md-2'),
				),
				'exchangeContract' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'hidden col-md-5'),
				),
				'creditQuotation' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'hidden col-md-5'),
				),
			),
			'buttons' => array(
				'submit' => array(
					'buttonType' => 'submit',
					'context' => 'primary',
					'label' => Yii::t('transfers', 'Verificar Transferencia'),
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
		$fields = array(
			'action' => array('confirm'),
			'elements' => array(
				'debitAccount' => array(
					'type' => 'hidden',
				),
				'isThird' => array(
					'type' => 'hidden',
				),
				'creditAccount' => array(
					'type' => 'hidden',
				),
				'thirdCreditAccount' => array(
					'type' => 'hidden',
				),
				'thirdDocType' => array(
					'type' => 'hidden',
				),
				'thirdDocNumber' => array(
					'type' => 'hidden',
				),
				'beneficiaryName' => array(
					'type' => 'hidden',
				),
				'saveFrequent' => array(
					'type' => 'hidden',
				),
				'amount' => array(
					'type' => 'hidden',
				),
				'concept' => array(
					'type' => 'hidden',
				),
				'confirm' => array(
					'type' => 'hidden',
				),
				'exchangeContract' => array(
					'type' => 'hidden',
				),
				'creditQuotation' => array(
					'type' => 'hidden',
				),
			),
			'buttons' => array(
				'submit' => array(
					'buttonType' => 'submit',
					'context' => 'primary',
					'label' => Yii::t('transfers', 'Confirmar Transferencia'),
				),
				'cancel' => array(
					'buttonType' => 'link',
					'context' => 'danger',
					'label' => Yii::t('commons', 'Cancelar'),
					'url' => array('/site/index'),
				),
			),
		);

		if ($this->isThird && $this->isToken != 'H') {
			$fields = $this->addDetectIdInputsConfig($fields);
		}

		return $fields;
	}
}
