<?php
Yii::import('application.models.traits.HasDetectIdInput');

class TransferForm extends FormModel
{
	use HasDetectIdInput;

	public $debitAccount;

	public $currency;
	public $amount;
	public $date;
	public $charges;
	public $reason;
	public $concept;
	public $hasAgreement;
	public $exchangeContract;
	public $creditQuotation;

	public $financialEntity;
	public $creditAccount;
	public $name;
	public $address;
	public $documentType;
	public $documentData;
	public $transferType;
	public $actionType;
	public $saveFrequentData;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return $this->addDetectIdInputRules(array(
			array('debitAccount, currency, amount, date, charges, reason, concept, financialEntity, creditAccount, name, documentType, documentData', 'required'),
			array('exchangeContract, creditQuotation', 'required', 'on' => 'withAgreement'),
			array('exchangeContract, creditQuotation, address, transferType, actionType, hasAgreement, saveFrequentData', 'safe'),
			array('concept', 'length', 'max' => 31),
		));
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return $this->addDetectIdAttributeLabels(array(
			'debitAccount' => Yii::t('commons', 'Cuenta Débito'),
			'currency' => Yii::t('transfers', 'Moneda Crédito'),
			'amount' => Yii::t('transfers', 'Monto a Transferir'),
			'date' => Yii::t('transfers', 'Fecha de Acreditación (Hasta un max. de 7 días hábiles)'),
			'charges' => Yii::t('sipapTransfer', 'Cobro de Cargos'),
			'reason' => Yii::t('sipapTransfer', 'Motivo'),
			'concept' => Yii::t('transfers', 'Concepto'),
			'hasAgreement' => Yii::t('transfers', 'Acordamos Cotización?'),
			'exchangeContract' => Yii::t('transfers', 'Número del Contrato de Cambio'),
			'creditQuotation' => Yii::t('transfers', 'Cotización Acordada'),
			'financialEntity' => Yii::t('transfers', 'Entidad Financiera'),
			'creditAccount' => Yii::t('transfers', 'Nro. de Cuenta'),
			'name' => Yii::t('transfers', 'Nombre'),
			'address' => Yii::t('transfers', 'Dirección'),
			'documentType' => Yii::t('transfers', 'Tipo de Documento'),
			'documentData' => Yii::t('transfers', 'Nro. de Documento'),
			'actionType' => '',
			'transactionalKey' => Yii::t('commons', 'Clave Transaccional'),
			'saveFrequentData' => Yii::t('sipapTransfer', 'Grabar como Transferencia Frecuentes'),
		));
	}

	public function formConfig($accountOptions, $gridOptions, $financialEntity, $documentType, $reasons)
	{
		return array(
			'id' => 'sipapTransferForm',
			'action' => array('Verify'),
			'elements' => array(
				'transferType' => array(
					'type' => 'hidden',
				),
				'actionType' => array(
					'type' => 'hidden',
				),
				'debitAccount' => array(
					'type' => 'dropdownlist',
					'items' => $accountOptions,
					'widgetOptions' => array('htmlOptions' => array(
						'prompt' => Yii::t('commons', 'Seleccione una Cuenta'),
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'currency' => array(
					'type' => 'dropdownlist',
					'items' => self::currencyOptions(),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'amount' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'date' => array(
					'type' => 'TbDatePicker',
					'htmlOptions' => array(
						'class' => 'required',
					),
					'options' => array(
						'format' => "dd/mm/yyyy",
						'orientation' => "bottom left",
						'autoclose' => true,
						'language' => 'es',
					),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'charges' => array(
					'type' => 'checkbox',
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'reason' => array(
					'type' => 'dropdownlist',
					'items' => $reasons,
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'concept' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'hasAgreement' => array(
					'type' => 'dropdownlist',
					'items' => array(
						0 => 'No',
						1 => 'Si',
					),
					'groupOptions' => array('class' => 'hidden col-md-4 exchangeAgreement'),
				),
				'exchangeContract' => array(
					'type' => 'text',
					'groupOptions' => array('class' => 'hidden col-md-4 exchangeAgreement'),
				),
				'creditQuotation' => array(
					'type' => 'text',
					'hint' => Yii::t('transfers', 'Se permiten hasta cuatro decimales'),
					'groupOptions' => array('class' => 'hidden col-md-4 exchangeAgreement'),
				),
				'financialEntity' => array(
					'type' => 'dropdownlist',
					'items' => $financialEntity,
					'widgetOptions' => array('htmlOptions' => array(
						'prompt' => Yii::t('commons', 'Seleccione una Entidad'),
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'creditAccount' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'name' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'address' => array(
					'type' => 'text',
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'documentType' => array(
					'type' => 'dropdownlist',
					'items' => $documentType,
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'documentData' => array(
					'type' => 'text',
					'widgetOptions' => array('htmlOptions' => array(
						'class' => 'required',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
				),
				'saveFrequentData' => array(
					'type' => 'checkbox',
					'widgetOptions' => array('htmlOptions' => array(
						'value' => 'S',
					)),
					'groupOptions' => array('class' => 'col-md-12'),
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
		return $this->addDetectIdInputsConfig(array(
			'elements' => array(
				'transferType' => array(
					'type' => 'hidden',
				),
				'actionType' => array(
					'type' => 'hidden',
				),
				'debitAccount' => array(
					'type' => 'hidden',
				),
				'currency' => array(
					'type' => 'hidden',
				),
				'amount' => array(
					'type' => 'hidden',
				),
				'date' => array(
					'type' => 'hidden',
				),
				'charges' => array(
					'type' => 'hidden',
				),
				'reason' => array(
					'type' => 'hidden',
				),
				'concept' => array(
					'type' => 'hidden',
				),
				'exchangeContract' => array(
					'type' => 'hidden',
				),
				'creditQuotation' => array(
					'type' => 'hidden',
				),
				'financialEntity' => array(
					'type' => 'hidden',
				),
				'creditAccount' => array(
					'type' => 'hidden',
				),
				'name' => array(
					'type' => 'hidden',
				),
				'address' => array(
					'type' => 'hidden',
				),
				'documentType' => array(
					'type' => 'hidden',
				),
				'documentData' => array(
					'type' => 'hidden',
				),
			),
			'buttons' => array(
				'submit' => array(
					'buttonType' => 'submit',
					'context' => 'primary',
					'label' => Yii::t('commons', 'Confirmar'),
				),
				'cancel' => array(
					'buttonType' => 'link',
					'context' => 'danger',
					'label' => Yii::t('commons', 'Cancelar'),
					'url' => array('/site/index'),
				),
			),
		));
	}

	public static function currencyOptions()
	{
		return array(
			'GS' => 'Guaraníes',
			'USD' => 'Dolares',
		);
	}
}
