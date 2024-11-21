<?php

$this->pageTitle=Yii::app()->name . ' - '. Yii::t('returnedChecks', 'Cheques Devueltos');
?>

<h3><?php echo Yii::t('returnedChecks', 'Cheques Devueltos'); ?></h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>array('striped'),
			'dataProvider'=>$dataProvider,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				'banco:text:'.Yii::t('commons', 'Banco'),
				'motivodevolucion:text:'.Yii::t('returnedChecks', 'Motivo'),
				'numerocheque:text:'.Yii::t('returnedChecks', 'Número de cheque'),
				array(
					'name'=>Yii::t('returnedChecks', 'Nro de cuenta del cheque'),
					'value'=>'"**********".substr($data->numerocuenta, -4)',
				),
			),
		)); ?>
	</div>
</div>