<?php
	if(isset($deleteOption)){
	$this->widget('booster.widgets.TbGridView', array(
	'type'=>'striped condensed',
	'dataProvider'=>$dataProvider,
	'enableSorting'=>false,
	'template'=>'{items}{pager}',
	'rowCssClassExpression' => '
			( $row%2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) .
			( $data->error === "N" ? null : " danger" )',
	'columns'=>array(
		'numerofactura:text:'.Yii::t('supplierPayment', 'Número Factura'),
		array(
			'name'=>Yii::t('supplierPayment', 'Forma de Pago'),
			'value'=>'$data->formapago === "C" ? "Crédito en Cuenta" : ($data->formapago === "G" ? "Cheque Gerencia" : "")',
		),
		array(
			'name' => Yii::t('commons', 'Cuenta Crédito'),
			'type' => 'raw',
			'value' => '$data->error === "S"?
				CHtml::textField("correctedAccounts[$data->numerocuentacredito]",$data->numerocuentacredito):
				$data->numerocuentacredito
			',
		),
		array(
			'name'=>Yii::t('commons', 'Monto'),
			'type' => 'raw',
			'value' => '$data->error === "S"?
				CHtml::textField("correctedAmounts[$data->montocredito]",$data->montocredito):
				Yii::app()->numberFormatter->formatDecimal($data->montocredito)
			',
		),
		'nombrebeneficiario:text:'.Yii::t('supplierPayment', 'Beneficiario'),
		'mensajerespuesta:text:'.Yii::t('commons', 'Respuesta'),
		array(
			'class'=>'booster.widgets.TbButtonColumn',
			'template'=>'{delete}',
			'header'=>Yii::t('commons', 'Opciones'),
			'buttons'=>array(
				'delete'=>array(
					'label'=>Yii::t('commons', 'Eliminar'),
					'url'=>'"#"',
					'icon'=>'fa fa-trash-o',
					'click'=>'function(){deleteRow(this);}',
				),
			),
		),
	),
));
}else{
	$this->widget('booster.widgets.TbGridView', array(
	'type'=>'striped condensed',
	'dataProvider'=>$dataProvider,
	'enableSorting'=>false,
	'template'=>'{items}{pager}',
	'rowCssClassExpression' => '
			( $row%2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) .
			( $data->error === "N" ? null : " danger" )',
	'columns'=>array(
		'numerofactura:text:'.Yii::t('supplierPayment', 'Número Factura'),
		array(
			'name'=>Yii::t('supplierPayment', 'Forma de Pago'),
			'value'=>'$data->formapago === "C" ? "Crédito en Cuenta" : ($data->formapago === "G" ? "Cheque Gerencia" : "")',
		),
		array(
			'name' => Yii::t('commons', 'Cuenta Crédito'),
			'type' => 'raw',
			'value' => '$data->error === "S"?
				CHtml::textField("correctedAccounts[$data->numerocuentacredito]",$data->numerocuentacredito):
				$data->numerocuentacredito
			',
		),
		array(
			'name'=>Yii::t('commons', 'Monto'),
			'type' => 'raw',
			'value' => '$data->error === "S"?
				CHtml::textField("correctedAmounts[$data->montocredito]",$data->montocredito):
				Yii::app()->numberFormatter->formatDecimal($data->montocredito)
			',
		),
		'nombrebeneficiario:text:'.Yii::t('supplierPayment', 'Beneficiario'),
		'mensajerespuesta:text:'.Yii::t('commons', 'Respuesta'),
	),
));
}
?>
