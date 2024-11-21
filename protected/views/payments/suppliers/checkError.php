<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('supplierPayment', 'Pagos a Proveedores');
?>

<h3>
	<?php echo Yii::t('supplierPayment', 'Pagos a Proveedores'); ?>
	<small><?php echo Yii::t('commons', 'Complete los datos del formulario') ?></small>
</h3>

<?php $form=$this->beginWidget('booster.widgets.TbActiveForm', array(
    'id'=>'salaray-payment-check-form',
    'enableAjaxValidation'=>false,
    'action' => 'suppliersCheckError',
)); ?>

	<?php echo $this->renderPartial('suppliers/_list', array('dataProvider'=>$dataProvider,'deleteOption'=>true));?>

	<?php echo CHtml::hiddenField('model', json_encode($model->attributes)); ?>
	<?php echo CHtml::hiddenField('paymentDatas', json_encode($dataProvider->rawData)); ?>

	<div class="form-actions">
		<?php $this->widget('booster.widgets.TbButton', array(
			'buttonType' => 'submit',
			'label' => 'Verificar',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

<?php
	$fixFile=false;
	foreach ($dataProvider->rawData as $row) {
		if($row->error==='S' && $row->mensajerespuesta==='Cuenta duplicada en el archivo'){
			$fixFile=true;
			break;
		}
	}
?>

<?php if($fixFile): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".alert-danger").html(
			 '<a href="#" class="close" data-dismiss="alert">×</a>Cuenta duplicada en el archivo. Corrija el archivo e reinicie el proceso'
			);
			$('.form-actions').remove();
		});
	</script>
<?php endif;?>

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
		var id=trElement.find('td:eq(2) input').attr('id');
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

</script>
