<?php
	
	if(isset($deleteOption)){
	$this->widget('booster.widgets.TbGridView', array(
	'type'=>'striped condensed',
	'dataProvider'=>$dataProvider,
	'enableSorting'=>false,
	'template'=>'{items}{pager}',
	'rowCssClassExpression' => '
			( $row%2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) .
			( $data->codigoproceso === "0" ? null : " danger" )',
	'columns'=>array(
		//'numerofactura:text:'.Yii::t('supplierPayment', 'Número Factura'),
		array(
			'name' => Yii::t('commons', 'Nro Orden'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedOrder[$data->numeroorden]",$data->numeroorden)
			',
		),

		array(
			'name'=>Yii::t('commons', 'Número Factura'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedFactura[$data->numerofactura]",$data->numerofactura)
			
			',
		),

		array(
			'name'=>Yii::t('supplierPayment', 'Forma de Pago'),
			'value'=>'$data->formapago === "C" ? "Crédito en Cuenta" : ($data->formapago === "G" ? "Cheque Gerencia" : "Transf. Interbancaria")',
		),
		
		array(
			'name' => Yii::t('commons', 'Cuenta Crédito'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedAccounts[$data->numerocuentacredito]",$data->numerocuentacredito)
			',
		),
		
		array(
			'name' => Yii::t('commons', 'Nro Cuenta Beneficiario'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedAccountsBenef[$data->numerocuentabeneficiario]",$data->numerocuentabeneficiario)
			',
		),

		array(
			'name'=>Yii::t('commons', 'Monto'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedAmounts[$data->montocredito]",$data->montocredito)
			',
		),


		'nombrebeneficiario:text:'.Yii::t('supplierPayment', 'Beneficiario'),

		array(
			'name'=>Yii::t('commons', 'Moneda'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedMoneda[$data->codigomoneda]",$data->codigomoneda)
			',
		),

		array(
			'name'=>Yii::t('commons', 'Ref Factura'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedFacturaRed[$data->referenciafacturas]",$data->referenciafacturas)
			',
		),

		array(
			'name'=>Yii::t('commons', 'Tipo Documento'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedTipoDoc[$data->tipodocumento]",$data->tipodocumento)
			',
		),

		'descripcionbanco:text:'.Yii::t('supplierPayment', 'Entidad Beneficiaria'),

		array(
			'name'=>Yii::t('commons', 'Concepto'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedConcepto[$data->concepto]",$data->concepto)
			',
		),
		
		array(
			'name'=>Yii::t('commons', 'Indice Valor SIPAP'),
			'type' => 'raw',
			'value' => '
				CHtml::textField("correctedIndice[$data->tipomt]",$data->tipomt)
			',
		),

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
		'numeroorden:text:'.Yii::t('supplierPayment', 'Número Orden'),
		'numerofactura:text:'.Yii::t('supplierPayment', 'Número Facturaa'),
		'formapago:text:'.Yii::t('commons', 'Tipo de Pago'),
		'numerocuentacredito:text:'.Yii::t('commons', 'Cuenta Beneficiario'),
		'montocredito:text:'.Yii::t('commons', 'Monto'),
		'nombrebeneficiario:text:'.Yii::t('commons', 'Beneficiario'),
		'codigomoneda:text:'.Yii::t('commons', 'Moneda'),
		'referenciafacturas:text:'.Yii::t('commons', 'Ref Facturas'),
		'tipodocumento:text:'.Yii::t('commons', 'Tipo Documento'),
		'descripcionbanco:text:'.Yii::t('commons', 'Entidad Beneficiaria'),
		'numerodocumento:text:'.Yii::t('commons', 'Documento beneficiario'),
		'numerocuentabeneficiario:text:'.Yii::t('commons', 'Cuenta Crédito SIPAP'),
		'tipomt:text:'.Yii::t('commons', 'Indice Valor'),
		'mensajerespuesta:text:'.Yii::t('commons', 'Respuesta'),

	),
));
}
?>
