<?php

/**
 * DepositsToConfirmForm class.
 */
class DepositsToConfirmForm extends FormModel
{
	public $account;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('account', 'required'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'account'=>Yii::t('commons', 'Cuenta'),
		);
	}

	public function formConfig($accounts)
	{
		return array(
			// 'title'=> 'Seleccione una cuenta',
			'showErrorSummary'=>false,
			'attributes'=>array('id'=>'select-account-form'),
			'elements'=>array(
				'account'=>array(
					'type'=>'application.components.widgets.RadioGridInput',
					'gridItems'=>$accounts,
				),
			),
			'buttons'=>array(
				'submit'=>array(
					'id'=>'submit',
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('commons', 'Consultar'),
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
