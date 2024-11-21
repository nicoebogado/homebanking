<?php

class PersonalLoanForm extends FormModel{

	public $beneficiaryName;
	public $beneficiaryDocument;
	public $beneficiaryDocType;
	public $timeLimit;
	public $amount;
	public $branchOffice;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('beneficiaryName, beneficiaryDocument, beneficiaryDocType, timeLimit, amount, branchOffice', 'required'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'beneficiaryName'=>Yii::t('loans', 'Nombre y Apellido'),
			'beneficiaryDocType'=>Yii::t('loans', 'Tipo de documento'),
			'beneficiaryDocument'=>Yii::t('loans', 'Documento'),
			'timeLimit'=>Yii::t('loans', 'Plazo'),
			'amount'=>Yii::t('loans', 'Monto'),
			'branchOffice'=>Yii::t('loans', 'Sucursal de Desembolso')
		);
	}

	public function formConfig($offices,$docTypes)
	{
		return array(
			'id' => 'PersonalLoanForm',
			'action'=>'',
			'elements'=>array(
				'beneficiaryName'=>array(
					'type'=>'text',
					'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
						'value'=>Yii::app()->user->getState('clientArea')['nombrecompleto'],
						'readonly'=>'readonly',
					)),
					'groupOptions'=>array('class'=>'col-md-6'),
				),
				'beneficiaryDocument'=>array(
					'type'=>'text',
					'widgetOptions'=>array('htmlOptions'=>array(
						'value'=>Yii::app()->user->getState('clientArea')['documento'],
						'class'=>'required',
						'readonly'=>'readonly',
					)),
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'beneficiaryDocType'=>array(
					'type' => 'dropdownlist',
					'items' => $docTypes,
					'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
						'readonly'=>'readonly',
					)),
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'timeLimit'=>array(
					'type'=>'text',
					'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),
					'groupOptions'=>array('class'=>'col-md-3'),
				),'amount'=>array(
					'type'=>'text',
					'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
						'data-inputmask'=>"'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true"
					)),
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'branchOffice'=>array(
					'type' => 'dropdownlist',
					'items' => $offices,
					'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),
					'groupOptions'=>array('class'=>'col-md-6'),
				),
			),
			'buttons'=>array(
				'submit'=>array(
					'buttonType'=>'submit',
					'context'=>'primary',
					'label'=>Yii::t('transfers', 'Verificar Transferencia'),
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
