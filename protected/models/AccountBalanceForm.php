<?php

class AccountBalanceForm extends FormModel
{
	public $account;
	public $days = 30; // valor por defecto

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('account', 'required', 'message'=>Yii::t('commons', 'Debe seleccionar una cuenta')),
			array('days', 'numerical', 'integerOnly'=>true, 'min'=>0, 'max'=>180),
			['account, days', 'safe'],
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'account'=>Yii::t('commons', 'Cuenta'),
			'days'=>Yii::t('commons', 'Días'),
		);
	}

	public function formConfig($accounts)
	{
		return array(
			'elements'=>array(
				'account'=>array(
					'type'=>'application.components.widgets.RadioGridInput',
					'gridItems'=>$accounts,
					// 'labelOptions' => array('label'=>false),
				),
				'days'=>array(
					'type'=>'text',
				),
			),
			'buttons'=>array(
				'submit'=>array(
					'id'=>'submit',
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('accountBalance', 'Consultar Saldos'),
					'url'=>array('checkbooks'),
				),
				'cancel'=>array(
					'context'=>'danger',
					'label'=>Yii::t('commons', 'Cancelar'),
					'url'=>array('/site/index'),
				),
			),
		);
	}
}
