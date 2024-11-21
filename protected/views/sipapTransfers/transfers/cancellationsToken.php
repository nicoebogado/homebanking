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
         
			?>
		</div>
        <form id="yw1" action="<?php echo Yii::app()->createUrl('sipapTransfers/confirmCancellation/'.$data->numeroreferencia) ?>" method="post">
<input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN"><div class="form-actions" style="padding:20px 0px;">
<div class="form-actions">
        <div class="col-xs-2">
        <input name="tokenF" id="tokenF" type="number" class="form-control" placeholder="Ingrese clave del token" value="" required>
        </div>
        <button name="validaToken" class="btn btn-primary" id="yw22" type="button">Confirmar Cancelación</button>
        <a name="cancel" class="btn btn-danger" id="yw3" href="<?php echo Yii::app()->createUrl('sipapTransfers/cancellations'); ?>">Cancelar</a>
        <p id="errortoken" style="color:red;" hidden>Error al validar token f&iacute;sico</p>
        <p>Ingrese la clave que aparece en su token </p>
        </div>
</div>
</form>


	</div>
<?php endif; ?>
<script>
    $(document).ready(function () {
		
        $('form input').keydown(function (e) {
			if (e.keyCode == 13) {
				e.preventDefault();
				return false;
			}
		});

        $("#yw22").click(function(){

            $("#yw22").attr("disabled", true);

            var btn = document.getElementById("yw22");
            btn.innerHTML = 'Verificando...';

            var tokenVar = document.getElementById("tokenF").value;
            var errortoken = document.getElementById("errortoken");

            $.post("<?php echo Yii::app()->createUrl('transfer/confirm'); ?>",
            {
                YII_CSRF_TOKEN: "<?php echo Yii::app()->request->csrfToken; ?>",
                tokenF: tokenVar
            },
            function(data, status){
                $("#yw22").attr("disabled", false);
                btn.innerHTML = 'Confirmar Cancelación';
                console.log(data);
                console.log(status);
                if(status == 'success'){
                    if(data == 801){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else if(data == '0'){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else if(data == 0){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else{
						console.log('opt invalid');
                        errortoken.style.display = "block";
                    }
                }
            });
        });
    });
</script>