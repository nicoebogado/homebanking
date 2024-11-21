<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('accountBalance', 'Histórico de Saldo Diario');
?>

<h3>
	<?php echo Yii::t('accountBalance', 'Histórico de Saldo Diario') ?>
	<small><?php echo $accountType.' - '.$account.' - '.$accountDenomination;?></small>
</h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>'striped condensed',
			'dataProvider'=>$dataProvider,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				array(
					'name'=>''.Yii::t('commons', 'Fecha'),
					'value'=>'$data->fecha',
				),
				array(
					'name'=>''.Yii::t('commons', 'Moneda'),
					'value'=>function($data,$row) use ($currency){
                					return $currency;
            					},
				),
				array(
					'name'=>''.Yii::t('commons', 'Saldo'),
					'value'=>'Yii::app()->numberFormatter->formatDecimal($data->saldo)',
				),
			),
		)); ?>
	</div>
</div>
