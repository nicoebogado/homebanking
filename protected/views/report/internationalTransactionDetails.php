<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('internationalTransactions', 'Detalles de Transacción');
?>

<h3><?php echo Yii::t('internationalTransactions', 'Detalles de Transacción') ?></h3>

<?php $this->widget('booster.widgets.TbDetailView', array(
	'data'=>$data,
	'attributes'=>array(
		array(
			'label'=>Yii::t('commons', 'Tipo'),
			'value'=>$data->tipotransferencia === "ENV" ? "Enviado" : "Recibido",
		),
		'estado:text:Estado',
		'beneficiario:text:'.Yii::t('internationalTransactions', 'Beneficiario'),
		'solicitante:text:'.Yii::t('internationalTransactions', 'Solicitante'),
		'numerooperacion:text:'.Yii::t('internationalTransactions', 'Nro. operación'),
		'nombrebancocorresponsal:text:'.Yii::t('internationalTransactions', 'Banco Corresponsal'),
		array(
			'label'=>Yii::t('commons', 'Monto'),
			'value'=>$data->codigomoneda." ".Yii::app()->numberFormatter->formatDecimal($data->monto),
		),
		//'fechavalor:text:Fecha Valor',
		//'fechaoperacion:text:Fecha operación',
		'numerocuentareferencia:text:'.Yii::t('internationalTransactions', 'Nro. Cuenta de Referencia'),
		'nroreferenciaexterna:text:'.Yii::t('internationalTransactions', 'Nro. de Referencia Externa'),
		array(
			'type'=> 'html',
			'label' => Yii::t('internationalTransactions', 'Mensaje Swift'),
			'value' => str_replace("\n", '<br>', $data->mensajeswift),
		),
	),
)); ?>