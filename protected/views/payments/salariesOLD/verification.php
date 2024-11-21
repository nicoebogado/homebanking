<?php
HScript::register([
    'vendor/emn178/md5.min',
    'vendor/emn178/sha256.min',
    'plugins/jquery.secureKeypad.min',
    'commons/securizeKeypad',
]);
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('salaryPayment', 'Pagos de Salarios');
?>

<h3>
	<?php echo Yii::t('salaryPayment', 'Pagos de Salarios'); ?>
	<small>
		<?php echo Yii::t('commons', 'Detalle de los Pagos a Realizar'); ?>
	</small>
</h3>

<div class="row">
	<div class="col-md-6">
		<?php $this->widget('booster.widgets.TbDetailView', array(
			'data'=>$model,
			'attributes'=>array(
				'entityCode:text:'.Yii::t('commons', 'Cliente'),
				'currencyCode:text:'.Yii::t('commons', 'Moneda'),
				array(
					'label'=>Yii::t('commons', 'Total Débitos'),
					'value'=>Yii::app()->numberFormatter->formatDecimal($model->totalAmount),
				),
				'recordsReadNumber:text:'.Yii::t('salaryPayment', 'Cantidad de Débitos'),
			),
		)); ?>
	</div>
</div>

<?php $this->widget('booster.widgets.TbGridView', array(
	'type'=>'striped condensed',
	'dataProvider'=>$dataProvider,
	'enableSorting'=>false,
	'template'=>'{items}{pager}',
	'columns'=>array(
		'numerocuenta:text:'.Yii::t('commons', 'Cuenta'),
		'tipo:text:'.Yii::t('commons', 'Tipo'),
		'nombrecuenta:text:'.Yii::t('commons', 'Denominación'),
		'codigomoneda:text:'.Yii::t('commons', 'Moneda'),
		array(
			'name'=>Yii::t('commons', 'Monto'),
			'value'=>'Yii::app()->numberFormatter->formatDecimal($data->monto)',
		),
		'fecha:text:'.Yii::t('salaryPayment', 'Fecha de Proceso'),
		'mensajerespuesta:text:'.Yii::t('commons', 'Respuesta'),
	),
)); ?>

<div class="form">
	<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
	    'id'=>'salaray-payment-confirm-form',
	    'enableAjaxValidation'=>false,
	    'action' => 'salariesConfirm',
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
				'buttonType'=>'submit',
				'context'=>'primary',
				'label'=>Yii::t('commons', 'Confirmar Pago'),
			));
      echo '&nbsp;&nbsp;';
			$this->widget('booster.widgets.TbButton', array(
				'buttonType'=>'link',
				'context'=>'danger',
				'label'=>Yii::t('commons', 'Cancelar'),
				'url'=>array('/site/index'),
			));

			 ?>
		</div>

	<?php $this->endWidget(); ?>
</div>
