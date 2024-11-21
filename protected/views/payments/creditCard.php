<?php
HScript::register([
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad',
	'payments/creditCard',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('creditCardPayment', 'Pago de Tarjetas');
?>

<?php if ($mode === 'list') : ?>

	<h3>
		<?php echo Yii::t('creditCardPayment', 'Pago de Tarjetas'); ?>
		<small><?php echo Yii::t('creditCardPayment', 'Lista de Tarjetas') ?></small>
	</h3>

	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbGridView', array(
				'type' => 'striped condensed',
				'dataProvider' => $dataProvider,
				'enableSorting' => false,
				'template' => '{items}{pager}',
				'selectableRows' => 0,
				'columns' => array(
					'denomination:text:' . Yii::t('commons', 'Denominación'),
					'currency:text:' . Yii::t('commons', 'Moneda'),
					'accountTypeDesc:text:' . Yii::t('creditCardPayment', 'Tipo de Tarjeta'),
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
								'url' => 'Yii::app()->createUrl("/payments/creditCard", array("id"=>$data["hash"]))',
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
		<?php echo Yii::t('creditCardPayment', 'Pago de Tarjetas'); ?>
	</h3>

	<div class="form">
		<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>
	</div>

<?php elseif ($mode === 'verification') : ?>

	<h3>
		<?php echo Yii::t('creditCardPayment', 'Pago de Tarjetas'); ?>
		<small><?php echo Yii::t('commons', 'Confirmación'); ?></small>
	</h3>


	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbDetailView', array(
				'data' => $details,
				'attributes' => array(
					'nombretarjeta:text:' . Yii::t('creditCardPayment', 'Nombre de la Tarjeta'),
					'numerotarjeta:text:' . Yii::t('creditCardPayment', 'Número de la Tarjeta'),
					'denominacioncuenta:text:' . Yii::t('creditCardPayment', 'Denominación de la Cuenta'),
					'numerocuentadebito:text:' . Yii::t('creditCardPayment', 'Número de Cuenta Débito'),
					'monedadebito:text:' . Yii::t('creditCardPayment', 'Moneda Débito'),
					array(
						'label' => Yii::t('creditCardPayment', 'Importe Débito'),
						'value' => Yii::app()->numberFormatter->formatDecimal($details->importedebito),
					),
					array(
						'label' => Yii::t('creditCardPayment', 'Pago Mínimo'),
						'value' => Yii::app()->numberFormatter->formatDecimal($details->montopagominimo),
					),
				),
			)); ?>
		</div>
	</div>

	<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>

<?php endif ?>