<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('accountDetails', 'Detalles de Cuentas');
?>

<h3>
	<?php echo Yii::t('accountDetails', 'Detalles de Cuentas') ?>
	<small>
		<?php echo $datas['accountDenomination'].' - '.(isset($datas['maskedCreditCardNumber'])?
			$datas['maskedCreditCardNumber']:
			((Yii::app()->params['maskedAccountNumber']=='N')?
				$datas['accountNumber']:
				$datas['maskedAccountNumber']
			));
		?>
	</small>
</h3>

<div class="row">
	<?php
	 	// Solamente muestra esta columna si el tipo de implemntación es distinta a la de casa de préstamos
		if( isset( Yii::app()->user->deploymentType ) && Yii::app()->user->deploymentType!='9999' ):
	?>
	<div class="col-md-4 col-xs-12">
		<div class="panel widget">
			<div class="panel-body">
				<div class="text-right text-muted">
					<em class="fa fa-<?php echo $datas['header'][0]['icon'] ?> fa-2x"></em>
				</div>
				<h3 class="mt0"><?php echo $datas['header'][0]['data'] ?></h3>
				<p class="text-muted"><?php echo $datas['header'][0]['label'] ?></p>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="col-md-4 col-xs-12">
		<div class="panel widget">
			<div class="panel-body">
				<div class="text-right text-muted">
					<em class="fa fa-<?php echo $datas['header'][1]['icon'] ?> fa-2x"></em>
				</div>
				<h3 class="mt0"><?php echo $datas['header'][1]['data'] ?></h3>
				<p class="text-muted"><?php echo $datas['header'][1]['label'] ?></p>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-xs-12">
		<div class="panel widget">
			<div class="panel-body">
				<div class="text-right text-muted">
					<em class="fa fa-<?php echo $datas['header'][2]['icon'] ?> fa-2x"></em>
				</div>
				<h3 class="mt0"><?php echo $datas['header'][2]['data'] ?></h3>
				<p class="text-muted"><?php echo $datas['header'][2]['label'] ?></p>
			</div>
		</div>
	</div>

	<?php
		// Compensa con una columna si el tipo de implemntación es la de casa de préstamos
		if( isset( Yii::app()->user->deploymentType ) && Yii::app()->user->deploymentType=='9999' ):
	?>
	<div class="col-md-4 col-xs-12">
	</div>
	<?php endif;?>

</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title">
					<?php echo $datas['panel']['accountDesc'] ?>
				</div>
			</div>
			<div class="list-group">

				<div class="list-group-item">
					<div class="row">
						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][0]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0"><?php echo $datas['panel'][0]['label'] ?></span>
										<p class="m0">
											<?php echo $datas['panel'][0]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][1]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0"><?php echo $datas['panel'][1]['label'] ?></span>
										<p class="m0">
											<?php echo $datas['panel'][1]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="list-group-item">
					<div class="row">

						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][2]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0">
											<?php echo $datas['panel'][2]['label'] ?>
										</span>
										<p class="m0">
											<?php echo $datas['panel'][2]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][3]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0">
											<?php echo $datas['panel'][3]['label'] ?>
										</span>
										<p class="m0">
											<?php echo $datas['panel'][3]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="list-group-item">
					<div class="row">

						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][4]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0">
											<?php echo $datas['panel'][4]['label'] ?>
										</span>
										<p class="m0">
											<?php echo $datas['panel'][4]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>

						<?php if (isset($datas['panel'][5])): ?>
						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][5]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0">
											<?php echo $datas['panel'][5]['label'] ?>
										</span>
										<p class="m0">
											<?php echo $datas['panel'][5]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>
						<?php endif ?>

					</div>
				</div>

				<?php if (isset($datas['panel'][6])): ?>
				<div class="list-group-item">
					<div class="row">

						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][6]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0">
											<?php echo $datas['panel'][6]['label'] ?>
										</span>
										<p class="m0">
											<?php echo $datas['panel'][6]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>

						<?php if (isset($datas['panel'][7])): ?>
						<div class="col-md-6">
							<div class="media-box">
								<div class="pull-left">
									<span class="fa-stack">
										<em class="fa fa-circle fa-stack-2x text-info"></em>
										<em class="fa fa-<?php echo $datas['panel'][7]['icon'] ?> fa-stack-1x fa-inverse text-white"></em>
									</span>
								</div>
								<div class="media-box-body clearfix">
									<div class="media-box-heading">
										<span class="text-info m0">
											<?php echo $datas['panel'][7]['label'] ?>
										</span>
										<p class="m0">
											<?php echo $datas['panel'][7]['data'] ?>
										</p>
									</div>
								</div>
							</div>
						</div>
						<?php endif ?>

					</div>
				</div>
				<?php endif ?>

			</div>
		</div>
	</div>
</div>

<?php /*$this->widget('booster.widgets.TbDetailView', array(
	'data'=>$details,
	'attributes'=>array(
		'descripciontipocuenta:text:'.Yii::t('accountDetails', 'Tipo Cuenta'),
		array(
			'label'=>$labels->fechainicio,
			'value'=>WebServiceClient::formatDate($details->fechainicio, 'long'),
		),
		array(
			'label'=>$labels->fechavencimiento,
			'value'=>WebServiceClient::formatDate($details->fechavencimiento, 'long'),
		),
		'diasmora:number:'.Yii::t('accountDetails', 'Mora (días)'),
		'codigomoneda:text:'.Yii::t('commons', 'Moneda'),
		array(
			'label'=>$labels->monto1,
			'value'=>Yii::app()->numberFormatter->formatDecimal($details->monto1),
		),
		array(
			'label'=>$labels->monto2,
			'value'=>Yii::app()->numberFormatter->formatDecimal($details->monto2),
		),
		array(
			'label'=>$labels->monto3,
			'value'=>Yii::app()->numberFormatter->formatDecimal($details->monto3),
		),
		array(
			'label'=>empty($details->monto4) ? '' : $labels->monto4,
			'value'=>Yii::app()->numberFormatter->formatDecimal($details->monto4),
			'visible'=>!empty($details->monto4),
		),
		array(
			'label'=>Yii::t('accountDetails', 'Tasa'),
			'value'=>isset($details->tasa) ? $details->tasa : null,
		),
		array(
			'label'=>Yii::t('accountDetails', 'Cheque devuelto'),
			'name'=>'chequedevuelto',
			'value'=>$details->chequedevuelto == 'N' ? 'No' : 'Si',
		),
	),
));*/ ?>
