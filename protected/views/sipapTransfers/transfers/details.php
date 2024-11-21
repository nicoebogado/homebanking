<?php 

$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('sipapTransfer', 'Consulta de Transferencia Interbancaria'); ?>
 <script type="text/javascript">
    //Evitar reenviio de formulario
    if (window.history.replaceState) { // verificamos disponibilidad
       window.history.replaceState(null, null, "https://secure.fic.com.py/homebanking/sipapTransfers/TransfersDetails");
    }
</script>
<h3><?php echo Yii::t('sipapTransfer', 'Consulta de Transferencia Interbancaria');?></h3>
<div class="panel panel-default">
	<div class="panel-body">
	<style media="screen">
		#formDate td{padding: 5px;}
		#formDate {margin: 20px;}
	</style>
	<center>
        <?= CHtml::form() ?>
        	<table id="formDate">
        		<tr>
        			<td>
        				<?php echo Yii::t('commons','Desde'); ?>:
        			</td>
        			<td>
        				<?php
        					$this->widget('zii.widgets.jui.CJuiDatePicker',array(
        						'name'=>'fromDate',
								'language'=>'es',
        						'options'=>array('dateFormat'=>'dd/mm/yy',),
        						'value'=>$fromDate,
        						'htmlOptions'=>array(
        								'id'=>'fromDate',
        								'class'=>"form-control",
        						),
        					));
        				?>
        			</td>
        			<td>
        				<?php echo Yii::t('commons','Hasta'); ?>:
        			</td>
        			<td>
        				<?php
        					$this->widget('zii.widgets.jui.CJuiDatePicker',array(
        						'name'=>'toDate',
        						'options'=>array('dateFormat'=>'dd/mm/yy',),
								'language'=>'es',
        						'value'=>$toDate,
        						'htmlOptions'=>array(
        								'id'=>'toDate',
        								'class'=>"form-control",
        						),
        					));
        				?>
        			</td>
        			<td>
        				<?php
        					echo CHtml::htmlButton(Yii::t('commons','Ver'),array(
        											'submit' => array('sipapTransfers/TransfersDetails'),
        											"id"=>'chtmlbutton',
        											'class'=>'btn btn-primary'));
        				?>
        			</td>
        		</tr>
        	</table>
        <?= CHtml::endForm() ?>
    </center>
		<h4 style="margin:30px 0px;color:#929292;font-family:inherit;font-weight:normal;"><?php echo Yii::t('sipapTransfer','Transferencias Emitidas');?></h4>
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>'striped condensed',
			'dataProvider'=>$dataProvider1,
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
									'value'=>'@$data->numeroctabeneficiario',
				),
				array(
					'name'=>Yii::t('transfers', 'Beneficiario'),
									'value'=> '@$data->nombrebeneficiario',
				),
					array(
					'name'=>Yii::t('sipapTransfer', 'Entidad'),
									'value'=>'$data->entidad',
				),
				array(
					'name'=>Yii::t('commons', 'Moneda'),
							'value'=>'$data->codigomoneda'
				),
				array(
					'name'=>Yii::t('commons', 'Monto'),
							'value'=>'Yii::app()->numberFormatter->formatDecimal($data->monto)',
							'htmlOptions'=>array(
								'style'=>'text-align: right;'
							),
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
              'label'=>Yii::t('commons', 'Ver'),
              'url'=>'Yii::app()->createUrl("/sipapTransfers/detail", array("id"=>$data->numeroreferencia))',
              'icon'=>'fa fa-search',
            ),
          ),
        ),
				array(
          'class'=>'booster.widgets.TbButtonColumn',
          'template'=>'{pay}',
          'buttons'=>array(
            'pay'=>array(
              'label'=>Yii::t('commons', 'Comprobante'),
                          'url'=>'Yii::app()->createUrl("/sipapTransfers/voucher", array("id"=>$data->numeroreferencia))',
              'icon'=>'fa fa-print',
            ),
          ),
        ),
			),
		)); ?>
		<h4 style="margin:30px 0px;color:#929292;font-family:inherit;font-weight:normal;"><?php echo Yii::t('sipapTransfer','Transferencias Emitidas a Fecha Futura');?></h4>
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
									'value'=>'@$data->numeroctabeneficiario',
				),
				array(
					'name'=>Yii::t('transfers', 'Beneficiario'),
									'value'=>'@$data->nombrebeneficiario',
				),
					array(
					'name'=>Yii::t('sipapTransfer', 'Entidad'),
									'value'=>'$data->entidad',
				),
				array(
					'name'=>Yii::t('commons', 'Moneda'),
							'value'=>'$data->codigomoneda'
				),
				array(
					'name'=>Yii::t('commons', 'Monto'),
							'value'=>'Yii::app()->numberFormatter->formatDecimal($data->monto)',
							'htmlOptions'=>array(
								'style'=>'text-align: right;'
							),
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
              'label'=>Yii::t('commons', 'Ver'),
              'url'=>'Yii::app()->createUrl("/sipapTransfers/detail", array("id"=>$data->numeroreferencia))',
              'icon'=>'fa fa-search',
            ),
          ),
        ),
				array(
          'class'=>'booster.widgets.TbButtonColumn',
          'template'=>'{pay}',
          'buttons'=>array(
            'pay'=>array(
              'label'=>Yii::t('commons', 'Comprobante'),
                          'url'=>'Yii::app()->createUrl("/sipapTransfers/voucher", array("id"=>$data->numeroreferencia))',
              'icon'=>'fa fa-print',
            ),
          ),
        ),
			),
		)); ?>
		<h4 style="margin:30px 0px;color:#929292;font-family:inherit;font-weight:normal;"><?php echo Yii::t('sipapTransfer','Transferencias Recibidas');?></h4>
		<?php $this->widget('booster.widgets.TbGridView', array(
			'type'=>'striped condensed',
			'dataProvider'=>$dataProvider3,
			'enableSorting'=>false,
			'template'=>'{items}{pager}',
			'selectableRows'=>0,
			'columns'=>array(
				array(
					'name'=>Yii::t('commons', 'Fecha'),
					'value'=>'WebServiceClient::formatDate($data->fechavalor)',
				),
				array(
					'name'=>Yii::t('commons', 'Tipo'),
									'value'=>'$data->tipo',
				),
					array(
					'name'=>Yii::t('sipapTransfer', 'Cuenta Débito'),
									'value'=>'@$data->numeroctabeneficiario',
				),
				array(
					'name'=>Yii::t('transfers', 'Remitente'),
									'value'=>'@$data->nombrebeneficiario',
				),
					array(
					'name'=>Yii::t('sipapTransfer', 'Entidad'),
									'value'=>'$data->entidad',
				),
				array(
					'name'=>Yii::t('commons', 'Moneda'),
							'value'=>'$data->codigomoneda'
				),
				array(
					'name'=>Yii::t('commons', 'Monto'),
							'value'=>'Yii::app()->numberFormatter->formatDecimal($data->monto)',
							'htmlOptions'=>array(
								'style'=>'text-align: right;'
							),
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
              'label'=>Yii::t('commons', 'Ver'),
              'url'=>'Yii::app()->createUrl("/sipapTransfers/detail", array("id"=>$data->numeroreferencia))',
              'icon'=>'fa fa-search',
            ),
          ),
        ),
		array(
			'class'=>'booster.widgets.TbButtonColumn',
			'template'=>'{pay}',
			'buttons'=>array(
			  'pay'=>array(
				'label'=>Yii::t('commons', 'Comprobante'),
							'url'=>'Yii::app()->createUrl("/sipapTransfers/voucher", array("id"=>$data->numeroreferencia,"titulo"=>"Transferencias Recibidas","nombreremitente"=>$data->nombrebeneficiario))',
				'icon'=>'fa fa-print',
			  ),
			),
		  ),
			),
		)); ?>


	</div>
</div>
