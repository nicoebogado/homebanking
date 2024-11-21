<?php $this->widget('booster.widgets.TbDetailView', array(
	'data'=>array(
		'debitAccount' => '',
		'creditAccount' => '',
		'amount' => '',
		'date' => '',
		'concept' => '',
		'documentData' => '',
		'financialEntity' => '',
	),
	'attributes'=>array(
		array(
			'label'=>Yii::t('commons', 'Cuenta Débito'),
			'value'=>'',
			'cssClass'=>'debitAccountDetailView',
		),
		array(
			'label'=>Yii::t('commons', 'Cuenta Crédito'),
			'value'=>'',
			'cssClass'=>'creditAccountDetailView',
		),
		array(
			'label'=>Yii::t('commons', 'Monto'),
			'value'=>Yii::app()->numberFormatter->formatDecimal(0),
			'cssClass'=>'amountDetailView',
		),
		array(
			'label'=>Yii::t('commons', 'Fecha Acreditación'),
			'value'=>'',
			'cssClass'=>'dateDetailView',
		),
		array(
			'label'=>Yii::t('commons', 'Concepto'),
			'value'=>'',
			'cssClass'=>'conceptDetailView',
		),
		array(
			'label'=>Yii::t('commons', 'Documento'),
			'value'=>'',
			'cssClass'=>'documentDataDetailView',
		),
		array(
			'label'=>Yii::t('commons', 'Banco'),
			'value'=>'',
			'cssClass'=>'financialEntityDetailView',
		),

	),
)); ?>
