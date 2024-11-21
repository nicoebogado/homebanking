<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('transfers', 'Cancelaciones');
?>

<h3><?php echo Yii::t('transfers', 'Cancelaciones'); ?></h3>

<?php $this->widget('booster.widgets.TbGridView', array(
	'type'=>array('striped'),
	'dataProvider'=>$dataProvider,
	'enableSorting'=>false,
	'template'=>'{items}{pager}',
	'selectableRows'=>0,
	'columns'=>array(
		'date:text:'.Yii::t('commons', 'Fecha'),
		'type:text:'.Yii::t('commons', 'Tipo'),
		'account:text:'.Yii::t('commons', 'Nro. Cuenta'),
		'beneficiary:text:'.Yii::t('transfers', 'Beneficiario'),
		'entity:text:'.Yii::t('commons', 'Entidad'),
		'currency:text:'.Yii::t('commons', 'Moneda'),
		array(
			'name'=>Yii::t('commons', 'Monto'),
			'value'=>'Yii::app()->numberFormatter->formatDecimal($data->amount)',
		),
	),
)); ?>

<p><?php echo Yii::t('transfers', 'El Cliente podrá realizar transferencias con fecha futura, pudiendo incluso cancelarlas antes de cumplirse la fecha valor, motivo por el cual se excluye de toda responsabilidad a la Entidad por reclamos que puedan derivarse.') ?></p>