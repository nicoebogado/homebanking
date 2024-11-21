<?php

class ServiceProvidersForm extends FormModel{

	public $debitAccount;
	public $transactionalKey;

	public function rules()
	{
    return array(
			array('debitAccount', 'required', 'message'=>Yii::t('serviceProviders', 'Debe seleccionar una cuenta')),
			array('transactionalKey', 'required', 'message'=>Yii::t('serviceProviders', 'Debe Ingresar su clave transaccional')),
    );
	}

	public function attributeLabels(){
			 return array(
					 'debitAccount'=>Yii::t('serviceProviders',"Cuenta"),
					 'transactionalKey'=> Yii::t('serviceProviders',"Clave Transaccional"),
			 );
	 }

	public function formConfig($debitAccountOptions)
	{
		return array(
			'title'=> Yii::t('serviceProviders','Seleccione una cuenta para Pago de Servicios'),
			'showErrorSummary'=>false,
			'elements'=>array(
				'debitAccount'=>array(
					'type'=>'application.components.widgets.RadioGridInput',
					'gridItems'=>$debitAccountOptions,
				),
				'transactionalKey'=>array(
						'type' => 'password',
						'widgetOptions' => array(
							'htmlOptions' => array(
								'class' => 'secureKeypadInput',
								'autocomplete'=>'off',
							),
						),
					),
			),
			'buttons'=>array(
				'submit'=>array(
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('commons', 'Enviar'),
				),
				'cancel'=>array(
					'buttonType'=>'link',
					'context'=>'danger',
					'label'=>Yii::t('commons', 'Cancelar'),
					'url'=>array('/site/index'),
				),
			),
		);

	}

}
?>
