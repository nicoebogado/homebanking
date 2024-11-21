<?php
HScript::register([
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad',
	'plugins/jquery.detectIdInput',
	'commons/initDetectIdInput',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('transfers', 'Transferencias Entre Cuentas');
?>

<h3>
	<?php echo Yii::t('transfers', 'Transferencias Entre Cuentas'); ?>
	<small><?php echo Yii::t('commons', 'Confirmación'); ?></small>
</h3>
<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbDetailView', array(
			'data' => $details,
			'attributes' => array(
				'monedadebito:text:' . Yii::t('transfers', 'Moneda Débito'),
				array(
					'label' => 'Monto',
					'value' => Yii::app()->numberFormatter->formatDecimal($details->monto),
				),
				'monedacredito:text:' . Yii::t('transfers', 'Moneda Crédito'),
				array(
					'label' => 'Monto Crédito',
					'value' => Yii::app()->numberFormatter->formatDecimal($details->montocredito),
				),
				'nombrectacredito:text:' . Yii::t('transfers', 'Nombre de la Cuenta Crédito'),
			),
		)); ?>
	</div>
</div>

<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>