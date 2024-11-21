<?php

class PromissoryNoteDiscountingForm extends FormModel{

	public $beneficiaryName;
	public $beneficiaryDocument;
	public $beneficiaryDocType;
	public $timeLimit;
	public $amount;
	public $branchOffice;

	public $noteLimit;
	public $noteAmount;
	public $date;
	public $expirationDate;
	public $drawerDocType;
	public $drawerDocNumber;
	public $drawerName;
	public $noteImage;

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
			'beneficiaryDocument'=>Yii::t('loans', 'Documento'),
			'beneficiaryDocType'=>Yii::t('loans', 'Tipo de documento'),
			'timeLimit'=>Yii::t('loans', 'Plazo'),
			'amount'=>Yii::t('loans', 'Monto'),
			'branchOffice'=>Yii::t('loans', 'Sucursal de Desembolso'),
			'noteLimit'=>Yii::t('loans', 'Plazo'),
			'noteAmount'=>Yii::t('loans', 'Monto'),
			'date'=>Yii::t('loans', 'Emisión'),
			'expirationDate'=>Yii::t('loans', 'Vencimiento'),
			'drawerDocType'=>Yii::t('loans', 'Tipo Doc.Librador'),
			'drawerDocNumber'=>Yii::t('loans', 'Nro.Doc.Librado'),
			'drawerName'=>Yii::t('loans', 'Librador'),
			'noteImage'=>Yii::t('loans', 'Imagen'),
		);
	}

	public function formConfig($offices,$docTypes)
	{
		return array(
			'id' => 'PromissoryNoteDiscountingForm',
			'enctype'=>'multipart/form-data',
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
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-3'),
				),'amount'=>array(
					'type'=>'text',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'branchOffice'=>array(
					'type' => 'dropdownlist',
					'items' => $offices,
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-6'),
				),
				'noteLimit'=>array(
					'type'=>'text',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'noteAmount'=>array(
					'type'=>'text',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'date'=>array(
					'type'=>'date',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'expirationDate'=>array(
					'type'=>'date',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-3'),
				),
				'drawerDocType'=>array(
					'type' => 'dropdownlist',
					'items' => $docTypes,
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-2'),
				),
				'drawerDocNumber'=>array(
					'type'=>'text',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-2'),
				),
				'drawerName'=>array(
					'type'=>'text',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-8'),
				),
				'noteImage'=>array(
					'type'=>'file',
					/*'widgetOptions'=>array('htmlOptions'=>array(
						'class'=>'required',
					)),*/
					'groupOptions'=>array('class'=>'col-md-8'),
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
