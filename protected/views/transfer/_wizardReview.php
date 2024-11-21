<?php $this->widget('booster.widgets.TbDetailView', array(
	'data'=>array(
		'debitAccount' => '',
		'creditAccount' => '',
		'amount' => '',
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
	),
)); ?>
