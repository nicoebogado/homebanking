	<?php 
	if ($data->tipo == 'R'){
		$tipo = 'Recibida';
	}else{
		$tipo = 'Emitida';
	}

	?>

	<?php $this->pageTitle=Yii::app()->name . ' - ' . Yii::t('sipapTransfer', 'Detalle de Transferencia '.$tipo); ?>
	<h3><?php echo Yii::t('sipapTransfer', 'Detalle de Transferencia '.$tipo);?></h3>
	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbDetailView', array(
				'data'=>array(
					'date' => '',
					'creditAccount' => '',
					'name' => '',
					'entity' => '',
					'currency' => '',
					'amount' => '', 
					'situation' => '',
					'status' => '',
				),
				'attributes'=>array(
					array(
						'label'=>Yii::t('commons', 'Fecha'),
						'value'=>WebServiceClient::formatDate($data->fechavalor),
					),
					array(
						'label'=>Yii::t('commons', 'Cuenta Crédito'),
						'value'=>$data->numerocuentacredito,
					),
					array(
						'label'=>Yii::t('transfers', 'Beneficiario'),
						'value'=>$data->nombrebeneficiario,
					),
					array(
						'label'=>Yii::t('commons', 'Entidad'),
						'value'=>$data->entidad,
					),
					array(
						'label'=>Yii::t('commons', 'Moneda'),
						'value'=>$data->moneda,
					),
					array(
						'label'=>Yii::t('commons', 'Monto'),
						'value'=>Yii::app()->numberFormatter->formatDecimal($data->monto),
						'htmlOptions'=>array(
								'style'=>'text-align: right;'
						),
					),
					array(
						'label'=>Yii::t('transfers', 'Situación'),
						'value'=>$data->situacion,
					),
					array(
						'label'=>Yii::t('commons', 'Estado'),
						'value'=>$data->estado,
					),
				),
			));
			?>
		</div>
	</div>
