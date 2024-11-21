<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('depositsToConfirm', 'Depósitos a Confirmar');
?>

<h3><?php echo Yii::t('depositsToConfirm', 'Depósitos a Confirmar'); ?></h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>array('striped'),
			'dataProvider'=>$dataProvider,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				'nroboleta:text:'.Yii::t('commons', 'ID'),
				'nrocuenta:text:'.Yii::t('depositsToConfirm', 'Número de cuenta'),
				array('name'=>Yii::t('depositsToConfirm', 'Importe'),
		 			'value'=>'Yii::app()->numberFormatter->formatDecimal($data->importe)'),
				array('name'=>Yii::t('depositsToConfirm', 'Fecha de depósito'),
		 			'value'=>'WebServiceClient::formatDate($data->fechadeposito)'),
			),
		)); ?>
	</div>
</div>
