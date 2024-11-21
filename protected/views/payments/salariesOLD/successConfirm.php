<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('salaryPayment', 'Pagos de Salarios');
?>

<h3>
	<?php echo Yii::t('salaryPayment', 'Pagos de Salarios'); ?>
	<small>
		<?php echo Yii::t('salaryPayment', 'Detalle de los Pagos a Realizar'); ?>
	</small>
</h3>

<div class="row">
	<div class="col-md-6">
		<?php $this->widget('booster.widgets.TbDetailView', array(
			'data'=>$model,
			'attributes'=>array(
				'entityCode:text:'.Yii::t('commons', 'Cliente'),
				'currencyCode:text:'.Yii::t('commons', 'Moneda'),
				array(
					'label'=>Yii::t('commons', 'Total Débitos'),
					'value'=>Yii::app()->numberFormatter->formatDecimal($model->totalAmount),
				),
				'recordsReadNumber:text:'.Yii::t('salaryPayment', 'Cantidad de Débitos'),
			),
		)); ?>
	</div>
</div>

<?php $this->widget('booster.widgets.TbGridView', array(
	'type'=>'striped condensed',
	'dataProvider'=>$dataProvider,
	'enableSorting'=>false,
	'template'=>'{items}{pager}',
	'columns'=>array(
		'numerocuenta:text:'.Yii::t('commons', 'Cuenta'),
		'tipo:text:'.Yii::t('commons', 'Tipo'),
		'nombrecuenta:text:'.Yii::t('commons', 'Denominación'),
		'codigomoneda:text:'.Yii::t('commons', 'Moneda'),
		array(
			'name'=>Yii::t('commons', 'Monto'),
			'value'=>'Yii::app()->numberFormatter->formatDecimal($data->monto)',
		),
		'mensajerespuesta:text:'.Yii::t('commons', 'Respuesta'),
	),
)); ?>

<div>
<?php echo Yii::t('salaryPayment', 'Esta operación ha sido registrada. Falta(n) {authNeeded} firma(s) para su confirmación.', array(
	'{authNeeded}' => $authNeeded,
)) ?>
</div>