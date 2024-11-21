<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('transfers', 'Cancelaciones');
?>

<h3><?php echo Yii::t('transfers', 'Cancelación de Transferencia con Fecha Valor Futura'); ?></h3>

<h2><?php echo Yii::t('transfers', 'Transferencia Interbancaria'); ?></h2>

<?php $this->widget('booster.widgets.TbDetailView', array(
	'data'=>$details,
	'attributes'=>array(
		'cuenta:text:'.Yii::t('commons', 'Cuenta'),
		'comprobante:text:'.Yii::t('transfers', 'Nro. Comprobante'),
		'fecha:text:'.Yii::t('commons', 'Fecha'),
		'hora:text:'.Yii::t('commons', 'Hora'),
		'moneda:number:'.Yii::t('commons', 'Moneda'),
		'oficina:text:'.Yii::t('commons', 'Oficina'),
		'monto:text:'.Yii::t('commons', 'Monto'),
		'concepto:text:'.Yii::t('commons', 'Concepto'),
		'tipoValor:text:'.Yii::t('transfers', 'Tipo Valor'),
		'estado:text:'.Yii::t('commons', 'Estado'),
		'situacion:text:'.Yii::t('transfers', 'Situación'),
		'detallemensaje:text:'.Yii::t('transfers', 'Detalle del Mensaje'),
		/*array(
			'label'=>'$labels->',
			'value'=>WebServiceClient::formatDate($details->fechainicio, 'long'),
		),*/
		/*array(
			'label'=>'$labels->',
			'value'=>Yii::app()->numberFormatter->formatDecimal($details->monto1),
		),*/
	),
)); ?>

<h3>Este comprobante es una copia</h3>

<p><?php echo Yii::t('transfers', 'La operación solicitada será procesada con la fecha valor indicada, condicionada a la disponibilidad de fondos en la cuenta respectiva al momento de realizarse la transacción, excluyendo de toda responsabilidad a la Entidad por reclamos que puedan derivarse.') ?></p>

<p><?php echo Yii::t('transfers', 'Desea confirmar la cancelación?') ?></p>
 Si No