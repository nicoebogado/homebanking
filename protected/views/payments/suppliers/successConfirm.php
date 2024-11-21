<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('supplierPayment', 'Pagos de Salarios');
?>
<style>
	table {
		display: block !important;
		overflow-x: auto !important;
		white-space: nowrap !important;
	}	
</style>
<h3>
	<?php echo Yii::t('supplierPayment', 'Pagos de Proveedores'); ?>
	<small>
		<?php echo Yii::t('commons', 'Detalle de los Pagos a Realizar'); ?>
	</small>
</h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php echo $this->renderPartial('suppliers/_list', array('dataProvider'=>$dataProvider));?>
	</div>
	<div class="panel-body">
		<?php echo Yii::t('supplierPayment', 'Esta operación ha sido registrada. Falta(n) {authNeeded} firma(s) para su confirmación.', array(
			'{authNeeded}' => $authNeeded,
		)) ?>
	</div>
</div>