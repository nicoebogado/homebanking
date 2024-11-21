<?php $this->widget('booster.widgets.TbDetailView', array(
	'data'=>array(
		'beneficiaryName',
		'beneficiaryDocument',
		'timeLimit',
		'amount',
		'branchOffice',
	),
	'attributes'=>array(
		array(
			'label'=>Yii::t('loans', 'Nombre y Apellido'),
			'value'=>'',
			'cssClass'=>'beneficiaryNameDetailView',
		),
		array(
			'label'=>Yii::t('loans', 'Documento'),
			'value'=>'',
			'cssClass'=>'beneficiaryDocumentDetailView',
		),
		array(
			'label'=>Yii::t('loans', 'Plazo'),
			'value'=>'',
			'cssClass'=>'timeLimitDetailView',
		),
		array(
			'label'=>Yii::t('loans', 'Monto'),
			'value'=>'',
			'cssClass'=>'amountDetailView',
		),
		array(
			'label'=>Yii::t('loans', 'Sucursal de Desembolso'),
			'value'=>'',
			'cssClass'=>'branchOfficeDetailView',
		)
	),
)); ?>
