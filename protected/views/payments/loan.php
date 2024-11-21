<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('loanPayment', 'Pago de Préstamos');
?>

<?php if ($mode === 'list') : ?>

	<h3><?php echo Yii::t('loanPayment', 'Pago de Préstamos'); ?>
		<small><?php echo Yii::t('loanPayment', 'Lista de Préstamos'); ?></small></h3>

	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbGridView', array(
				'type' => 'striped condensed',
				'dataProvider' => $dataProvider,
				'enableSorting' => false,
				'template' => '{items}{pager}',
				'selectableRows' => 0,
				'columns' => array(
					array(
						'name' => Yii::t('accountSummary', 'Préstamos'),
						'value' => '$data["accountNumber"]',
					),
					'denomination:text:' . Yii::t('loanPayment', 'Denominación/Titular'),
					'currency:text:' . Yii::t('commons', 'Moneda'),
					'accountTypeDesc:text:' . Yii::t('loanPayment', 'Tipo de Crédito'),
					array(
						'name' => Yii::t('commons', 'Saldo'),
						'value' => 'Yii::app()->numberFormatter->formatDecimal($data["credit"])',
					),
					array(
						'class' => 'booster.widgets.TbButtonColumn',
						'template' => '{pay}',
						'header' => Yii::t('commons', 'Opciones'),
						'buttons' => array(
							'pay' => array(
								'label' => Yii::t('commons', 'Pagar'),
								'url' => 'Yii::app()->createUrl("/payments/loan", array("id"=>$data["hash"]))',
								'icon' => 'fa fa-money',
							),
						),
					),
				),
			)); ?>
		</div>
	</div>

<?php elseif ($mode === 'form') : ?>

	<h3>
		<?php echo Yii::t('loanPayment', 'Pago de Préstamos'); ?> <br>
		<small>
			<?php echo Yii::t('loanPayment', 'Pagar Préstamo de la Cuenta "{account}"', array(
				'{account}' => $accDenomination,
			)) ?>
		</small>
	</h3>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo Yii::t('loanPayment', 'Lista de Cuotas Pendientes') ?>
		</div>
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbGridView', array(
				'type' => 'striped condensed',
				'dataProvider' => $dataProvider,
				'enableSorting' => false,
				'template' => '{items}{pager}',
				'selectableRows' => 0,
				'columns' => array(
					'numerocuota:number:' . Yii::t('loanPayment', 'Nro. de Cuota'),
					array(
						'name' => Yii::t('commons', 'Monto'),
						'value' => 'Yii::app()->numberFormatter->formatDecimal($data->montocuota)',
					),
					array(
						'name' => Yii::t('commons', 'Fecha de vencimiento'),
						'value' => 'WebServiceClient::formatDate($data->fechavencimiento)',
					),
				),
			)); ?>
		</div>
	</div>

	<?php if ($dataProvider->itemCount > 0) : ?>
		<?php if (isset(Yii::app()->user->operationMode) && Yii::app()->user->operationMode === 'T') : ?>
			<div class="form">
				<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>
			</div>
		<?php endif ?>
	<?php endif ?>

<?php elseif ($mode === 'verification') : ?>
	<?php
	HScript::register([
		'libs/emn178/md5.min',
		'libs/emn178/sha256.min',
		'plugins/jquery.secureKeypad.min',
		'commons/securizeKeypad',
	]);
	?>
	<h3>
		<?php echo Yii::t('loanPayment', 'Pago de Préstamos'); ?>
		<small><?php echo Yii::t('commons', 'Confirmación'); ?></small>
	</h3>

	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbDetailView', array(
				'data' => $details,
				'attributes' => array(
					'numeroprestamo:text:' . Yii::t('loanPayment', 'Número de Préstamo'),
					'numeroctadebito:text:' . Yii::t('loanPayment', 'Número de la Cuenta Débito'),
					'nombrectadebito:text:' . Yii::t('loanPayment', 'Nombre de la Cuenta Débito'),
					'tipoctadebito:text:' . Yii::t('loanPayment', 'Tipo de Cuenta Débito'),
					'codigomoneda:text:' . Yii::t('commons', 'Moneda'),
					array(
						'label' => Yii::t('loanPayment', 'Total Cargos'),
						'value' => Yii::app()->numberFormatter->formatDecimal($details->totalcargos),
					),
					array(
						'label' => Yii::t('loanPayment', 'Total Impuestos'),
						'value' => Yii::app()->numberFormatter->formatDecimal($details->totalimpuestos),
					),
					array(
						'label' => Yii::t('loanPayment', 'Total Mora'),
						'value' => Yii::app()->numberFormatter->formatDecimal($details->totalmora),
					),
					array(
						'label' => Yii::t('loanPayment', 'Total Interés'),
						'value' => Yii::app()->numberFormatter->formatDecimal($details->totalinteres),
					),
					array(
						'label' => Yii::t('loanPayment', 'Monto de la Operación'),
						'value' => Yii::app()->numberFormatter->formatDecimal($details->montooperacion),
					),
					array(
						'label' => Yii::t('loanPayment', 'Cantidad de Cuotas a Pagar'),
						'value' => $form->model->feesAmount,
					),
				),
			)); ?>
		</div>
	</div>

	<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>

<?php endif ?>
