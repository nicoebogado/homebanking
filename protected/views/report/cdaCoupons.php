<?php

$this->pageTitle=Yii::app()->name . ' - '. Yii::t('cdaCoupons', 'Cupones de CDA');
?>
<h3><?php echo Yii::t('cdaCoupons', 'Cupones de CDA'); ?>
 <small><?php echo $accountType.' - '.$account.' - '.$accountDenomination;?></small>
</h3>


<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>array('striped'),
			'dataProvider'=>$dataProvider,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				'numerocupon:text:'.Yii::t('cdaCupons', 'Numero Cupon'),
				array(
					'name'=>Yii::t('commons', 'Monto'),
					'value'=>'Yii::app()->numberFormatter->formatDecimal($data->monto)',
				),

				array(
					'name'=>Yii::t('commons', 'Fecha de vencimiento'),
					'value'=>'WebServiceClient::formatDate($data->fechavencimiento)',
				),
			),
		)); ?>
	</div>
</div>

