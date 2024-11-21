<?php if($stage==='list'): ?>
<?php $this->pageTitle=Yii::app()->name . ' - ' . Yii::t('sipapTransfer', 'Cancelación de Operaciones con Fecha Valor Futura'); ?>
<h3><?php echo Yii::t('sipapTransfer', 'Cancelación de Operaciones con Fecha Valor Futura');?></h3>
<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>'striped condensed',
			'dataProvider'=>$dataProvider2,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				array(
					'name'=>Yii::t('commons', 'Fecha'),
					'value'=>'WebServiceClient::formatDate($data->fechavalor)',
				),
				array(
					'name'=>Yii::t('sipapTransfer', 'Tipo'),
									'value'=>'$data->tipo',
				),
					array(
					'name'=>Yii::t('sipapTransfer', 'Cuenta Crédito'),
									'value'=>'$data->numeroctabeneficiario',
				),
				array(
					'name'=>Yii::t('transfers', 'Beneficiario'),
									'value'=>'$data->nombrebeneficiario',
				),
					array(
					'name'=>Yii::t('sipapTransfers', 'Entidad'),
									'value'=>'$data->entidad',
				),
				array(
					'name'=>Yii::t('commons', 'Moneda'),
							'value'=>'$data->codigomoneda'
				),
				array(
					'name'=>Yii::t('commons', 'Monto'),
							'value'=>'Yii::app()->numberFormatter->formatDecimal($data->monto)'
				),
				array(
					'name'=>Yii::t('commons', 'Estado'),
							'value'=>'$data->estado'
				),
				array(
					'name'=>Yii::t('commons', 'Origen'),
							'value'=>'$data->origen'
				),
				array(
          'class'=>'booster.widgets.TbButtonColumn',
          'template'=>'{pay}',
          'buttons'=>array(
            'pay'=>array(
              'label'=>Yii::t('commons', 'Cancelar'),
              'url'=>'Yii::app()->createUrl("/sipapTransfers/cancellation", array("id"=>$data->numeroreferencia))',
              'icon'=>'fa fa-times',
            ),
          ),
        ),
			),
		)); ?>
	</div>
</div>
<?php else: ?>
	<?php $this->pageTitle=Yii::app()->name . ' - ' . Yii::t('sipapTransfer', 'Cancelación de Operaciones con Fecha Valor Futura'); ?>
	<h3><?php echo Yii::t('sipapTransfer', 'Cancelación de Operaciones con Fecha Valor Futura');?></h3>
	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbDetailView', array(
				'data'=>array(
					'date' => '',
					'creditAccount' => '',
					'name' => '',
					'entity' => '',
					'currency' => '',
					'amount' => '',
				),
				'attributes'=>array(
					array(
						'label'=>Yii::t('commons', 'Fecha'),
						'value'=>$data->fechavalor,
					),
					array(
						'label'=>Yii::t('commons', 'Cuenta Crédito'),
						'value'=>$data->numeroctabeneficiario,
					),
					array(
						'label'=>Yii::t('commons', 'Beneficiario'),
						'value'=>$data->nombrebeneficiario,
					),
					array(
						'label'=>Yii::t('commons', 'Entidad'),
						'value'=>$data->entidad,
					),
					array(
						'label'=>Yii::t('commons', 'Moneda'),
						'value'=>$data->codigomoneda,
					),
					array(
						'label'=>Yii::t('commons', 'Monto'),
						'value'=>$data->monto,
					),
				),
			));
            echo CHtml::beginForm(['sipapTransfers/confirmCancellation/','id'=>$data->numeroreferencia]);
            echo '<div class="form-actions" style="padding:20px 0px;">';
            echo CHtml::button(Yii::t('commons','Confirmar'), array('style'=>'margin-right:5px;','class'=>'btn btn-primary','type' => 'submit'));
            echo CHtml::button(Yii::t('commons','Cancelar'), array('class'=>'btn btn-danger','submit' => array('sipapTransfers/cancellations')));
            echo '</div>';
            echo CHtml::endForm();
			?>
		</div>
	</div>
<?php endif; ?>
