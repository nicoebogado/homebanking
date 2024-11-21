<?php

$this->pageTitle=Yii::app()->name . ' - '. Yii::t('aditionalCards', 'Tarjetas Adicionales');
?>
<h3><?php echo Yii::t('aditionalCards', 'Tarjetas Adicionales') ?></h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>array('striped'),
			'dataProvider'=>$dataProvider,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				'nombreembozado:text:'.Yii::t('aditionalCards', 'Nombre embozado'),
				array(
					'name'=>Yii::t('aditionalCards', 'Número de Tarjeta'),
					'value'=>'"xxxxxxxxxx".substr($data->numerotarjeta, -4)',
				),
				array(
					'name'=>Yii::t('commons', 'Fecha de vencimiento'),
					'value'=>'WebServiceClient::formatDate($data->fechavencimiento)',
				),
			),
		)); ?>
	</div>
</div>

