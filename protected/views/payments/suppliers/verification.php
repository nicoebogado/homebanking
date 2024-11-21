<?php
HScript::register([
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('supplierPayment', 'Pagos a Proveedores');
?>

<h3>
	<?php echo Yii::t('supplierPayment', 'Pagos a Proveedores'); ?>
	<small>
		<?php echo Yii::t('commons', 'Detalle de los Pagos a Realizar'); ?>
	</small>
</h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php echo $this->renderPartial('suppliers/_list', array('dataProvider' => $dataProvider)); ?>
	</div>

	<div class="panel-body">
		<?php $form = $this->beginWidget('booster.widgets.TbActiveForm', array(
			'id' => 'salaray-payment-confirm-form',
			'enableAjaxValidation' => false,
			'action' => 'suppliersConfirm',
			'type' => 'horizontal',
		)); ?>

		<?php echo CHtml::hiddenField('model', json_encode($model->attributes)); ?>
		<?php echo CHtml::label(Yii::t('commons', 'Clave Transaccional'), 'transactionalKey'); ?>
		<br>
		<?php echo CHtml::passwordField('transactionalKey', '', array(
			'class' => 'secureKeypadInput',
		)); ?>
		<br>
		<div class="form-actions">
			<?php
			$this->widget('booster.widgets.TbButton', array(
				'buttonType' => 'submit',
				'context' => 'primary',
				'label' => Yii::t('commons', 'Confirmar Pago'),
			));
			echo '&nbsp;&nbsp;';
			$this->widget('booster.widgets.TbButton', array(
				'buttonType' => 'link',
				'context' => 'danger',
				'label' => Yii::t('commons', 'Cancelar'),
				'url' => array('/site/index'),
			));

			?>
		</div>

		<?php $this->endWidget(); ?>
	</div>
</div>