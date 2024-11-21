<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('internationalTransactions', 'Transferencias Internacionales');
?>

<h3>
	<?php echo Yii::t('internationalTransactions', 'Transferencias Internacionales'); ?>
</h3>

<div class="panel panel-default">
	<div class="panel-heading">
		<?php echo Yii::t('internationalTransactions', 'Transferencias Enviadas'); ?>
	</div>
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>'striped condensed',
			'dataProvider'=>$dpenv,
			'enableSorting'=>true,
			'template'=>'{items}{pager}',
			'columns'=>array(
				'numerooperacion:text:'.Yii::t('internationalTransactions', 'Nro. Operación'),
				'solicitante:text:'.Yii::t('internationalTransactions', 'Solicitante'),
				'beneficiario:text:'.Yii::t('internationalTransactions', 'Beneficiario'),
				'estado:text:'.Yii::t('commons', 'Estado'),
				array(
					'name'=>Yii::t('commons', 'Monto'),
					'value'=>'$data->codigomoneda." ".Yii::app()->numberFormatter->formatDecimal($data->monto)',
				),
				array(
					'class'=>'booster.widgets.TbButtonColumn',
					'template'=>'{details}',
					'header'=>Yii::t('commons', 'Opciones'),
					'buttons'=>array(
						'details'=>array(
							'label'=>Yii::t('commons', 'Detalles'),
							'url'=>'Yii::app()->createUrl("/report/internationalTransactionDetails", array("id"=>$data->numerooperacion))',
							'icon'=>'th-list',
						)
					),
				),
			),
		)); ?>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<?php echo Yii::t('internationalTransactions', 'Transferencias Recibidas'); ?>
	</div>
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>'striped condensed',
			'dataProvider'=>$dprec,
			'enableSorting'=>true,
			'template'=>'{items}{pager}',
			'columns'=>array(
				'numerooperacion:text:'.Yii::t('internationalTransactions', 'Nro. Operación'),
				'solicitante:text:'.Yii::t('internationalTransactions', 'Solicitante'),
				'beneficiario:text:'.Yii::t('internationalTransactions', 'Beneficiario'),
				'estado:text:'.Yii::t('commons', 'Estado'),
				array(
					'name'=>Yii::t('commons', 'Monto'),
					'value'=>'$data->codigomoneda." ".Yii::app()->numberFormatter->formatDecimal($data->monto)',
				),
				array(
					'class'=>'booster.widgets.TbButtonColumn',
					'template'=>'{details}',
					'header'=>Yii::t('commons', 'Opciones'),
					'buttons'=>array(
						'details'=>array(
							'label'=>Yii::t('commons', 'Detalles'),
							'url'=>'Yii::app()->createUrl("/report/internationalTransactionDetails", array("id"=>$data->numerooperacion))',
							'icon'=>'th-list',
						)
					),
				),
			),
		)); ?>
	</div>
</div>