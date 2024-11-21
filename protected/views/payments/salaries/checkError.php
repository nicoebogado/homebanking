<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('salaryPayment', 'Pagos de Salarios');
?>

<!--<?php if ($amountError): ?>
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">&times;</span>
			<span class="sr-only">Close</span>
		</button>
		<?php echo $amountErrorMsg ?>
	</div>
<?php endif ?>-->

<h3>
	<?php echo Yii::t('salaryPayment', 'Pagos de Salarios'); ?>
	<small><?php echo Yii::t('commons', 'Complete los datos del formulario') ?></small>
</h3>

<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
    'id'=>'salaray-payment-check-form',
    'enableAjaxValidation'=>false,
    'action' => Yii::app()->createUrl('payments/salariesCheckError'),
)); ?>

	<?php $this->widget('booster.widgets.TbGridView', array(
		'type'=>'striped condensed',
		'dataProvider'=>$dataProvider,
		'enableSorting'=>false,
		'template'=>'{items}{pager}',
		'rowCssClassExpression' => '
			( $row%2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) .
			( $data->error === "N" ? null : " danger" )',
		'columns'=>array(
			array(
				'name' => Yii::t('commons', 'Cuenta'),
				'type' => 'raw',
				'value' => '$data->error === "S"?
					CHtml::textField("correctedAccounts[$data->numerocuenta]",$data->numerocuenta):
					$data->numerocuenta
				',
			),
			array(
				'name' => Yii::t('commons', 'Tipo'),
				'value'=>'$data->tipo === "C" ? Yii::t("commons", "Crédito") : Yii::t("commons", "Débito")',
			),
			'nombrecuenta:text:'.Yii::t('commons', 'Denominación'),
			'codigomoneda:text:'.Yii::t('commons', 'Moneda'),
			array(
				'name'=>Yii::t('commons', 'Monto'),
				'type'=>'raw',
				'value'=>$amountError ?
					'CHtml::textField("correctedAmounts[$data->numerocuenta]",$data->monto)':
					'Yii::app()->numberFormatter->formatDecimal($data->monto)',
			),
			'mensajerespuesta:text:'.Yii::t('commons', 'Respuesta'),
			array(
				'class'=>'booster.widgets.TbButtonColumn',
				'template'=>'{delete}',
				'header'=>Yii::t('commons', 'Opciones'),
				'buttons'=>array(
					'delete'=>array(
						'label'=>Yii::t('commons', 'Eliminar'),
						'url'=>'"#"',
						'icon'=>'fa fa-trash-o',
						'click'=>'function(){deleteRow(this);}',
					),
				),
			),
		),
	)); ?>

	<?php //si hubo error de monto renderizar campo para corregir el monto total ?>
	<?php if($amountError) echo Yii::t('commons', 'Monto Total').' '.CHtml::textField("correctedAmounts[total]",$model->totalAmount) ?>

	<?php echo CHtml::hiddenField('model', json_encode($model->attributes)); ?>
	<?php echo CHtml::hiddenField('accounts', json_encode($dataProvider->rawData)); ?>
	<br>
	<div class="form-actions">
		<?php $this->widget('booster.widgets.TbButton', array(
			'buttonType' => 'submit',
			'context' => 'primary',
			'label' => 'Verificar',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	$('tr:not(.danger)').find('.button-column').each(function( index ) {
		if(index!=0){
			$(this).html('');
		}
	});

	function deleteRow(element){

		var jElement=$(element);
		jElement.tooltip('destroy');
		var trElement=jElement.parent().parent();
		var id=trElement.find('td:eq(0) input').attr('id');
		id=id.split('_');
		var accountNumber=id[1];

		$('<input>').attr({
    	type: 'hidden',
    	id: 'deletedAccount_'+accountNumber,
			value: accountNumber,
    	name: 'deletedAccount['+accountNumber+']'
		}).appendTo('#salaray-payment-check-form');

		trElement.remove();

	}

	$('form input').keydown(function (e) {
		if (e.keyCode == 13) {
		e.preventDefault();
		return false;
		}
	});

</script>
