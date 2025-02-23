<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('salaryPayment', 'Pagos de Salarios');
?>

<h3>
	<?php echo Yii::t('salaryPayment', 'Pagos de Salarios'); ?>
	<small><?php echo Yii::t('salaryPayment', 'Complete los Datos del Formulario'); ?></small>
</h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
		    'id'=>'salaray-payment-form',
		    'enableAjaxValidation'=>false,
		    'action' => 'salariesVerification',
		)); ?>

			<?php $this->widget('booster.widgets.TbGridView', array(
				'type'=>'striped condensed',
				'dataProvider'=>$dataProvider,
				'enableSorting'=>false,
				'template'=>'{items}{pager}',
				'columns'=>array(
					'id:text:'.Yii::t('salaryPayment', 'Orden'),
					array(
						'name'=>Yii::t('commons', 'Cuenta'),
						'type'=>'raw',
						'value'=>'CHtml::textField("accountNumber[$data->id]",$data->accountNumber)',
					),
					array(
						'name'=>Yii::t('commons', 'Monto'),
						'type'=>'raw',
						'value'=>'CHtml::textField("amount[$data->id]",$data->amount)',
					),
				),
			)); ?>

			<?php echo CHtml::hiddenField('model', json_encode($model->attributes)); ?>

			<div class="form-actions">
				<?php $this->widget('booster.widgets.TbButton', array(
					'buttonType' => 'submit',
					'label' => Yii::t('commons', 'Verificar Pago'),
				)); ?>
			</div>

		<?php $this->endWidget(); ?>
	</div>
</div>