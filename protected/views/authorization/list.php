<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('authorization', 'Autorizaciones del Día');
?>

<h3>
	<?php 
		if($type == 1){
			echo Yii::t('authorization', 'Operaciones Pendientes');
		}else{
			echo Yii::t('authorization', 'Autorizar Operaciones');
		}
	?>
</h3>

<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>'striped condensed',
			'dataProvider'=>$dataProvider,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				'numerodocumento:text:'.Yii::t('authorization', 'Comprobante'),
				'fecha:text:'.Yii::t('commons', 'Fecha'),
				'descripcion:text:'.Yii::t('app', 'Tipo de operación'),
				'numerocuenta:text:'.Yii::t('commons', 'Cuenta'),
				array(
					'name'=>Yii::t('commons', 'Monto'),
					'value'=>'$data->moneda." ".Yii::app()->numberFormatter->formatDecimal($data->monto)',
				),
				'estado:text:'.Yii::t('commons', 'Estado'),
				array(
						'class'=>'booster.widgets.TbButtonColumn',
						'template'=>'{details}',
						'header'=>Yii::t('commons', 'Opciones'),
						'buttons'=>array(
							'details'=>array(
								'label'=>Yii::t('commons', 'Detalles'),
								'url'=>'Yii::app()->createUrl("/authorization/salaryDetail'.($type===2?'WithActions':'').'", array("id"=>$data->numerodocumento))',
								'icon'=>'fa fa-file-text',
							),
						),
					),
			),
		)); ?>
	</div>
</div>
