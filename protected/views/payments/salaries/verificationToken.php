<?php
HScript::register([
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('salaryPayment', 'Pagos de Salarios');
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
			'data' => $model,
			'attributes' => array(
				'entityCode:text:' . Yii::t('commons', 'Cliente'),
				'currencyCode:text:' . Yii::t('commons', 'Moneda'),
				array(
					'label' => Yii::t('commons', 'Total Débitos'),
					'value' => Yii::app()->numberFormatter->formatDecimal($model->totalAmount),
				),
				'recordsReadNumber:text:' . Yii::t('salaryPayment', 'Cantidad de Débitos'),
			),
		)); ?>
	</div>
</div>

<?php $this->widget('booster.widgets.TbGridView', array(
	'type' => 'striped condensed',
	'dataProvider' => $dataProvider,
	'enableSorting' => false,
	'template' => '{items}{pager}',
	'columns' => array(
		'numerocuenta:text:' . Yii::t('commons', 'Cuenta'),
		'tipo:text:' . Yii::t('commons', 'Tipo'),
		'nombrecuenta:text:' . Yii::t('commons', 'Denominación'),
		'codigomoneda:text:' . Yii::t('commons', 'Moneda'),
		array(
			'name' => Yii::t('commons', 'Monto'),
			'value' => 'Yii::app()->numberFormatter->formatDecimal($data->monto)',
		),
		'fecha:text:' . Yii::t('salaryPayment', 'Fecha de Proceso'),
		'mensajerespuesta:text:' . Yii::t('commons', 'Respuesta'),
	),
)); ?>

<div class="form">
	<?php $form = $this->beginWidget('booster.widgets.TbActiveForm', array(
		'id' => 'salaray-payment-confirm-form',
		'enableAjaxValidation' => false,
		'action' => Yii::app()->createUrl('payments/salariesConfirm'),
	)); ?>

	<?php echo CHtml::hiddenField('model', json_encode($model->attributes)); ?>
	<?php if($perfil <> 'REGISTRA'){ ?>
		<?php echo CHtml::label(Yii::t('commons', 'Token Físico'), 'transactionalKey'); ?>
	<?php } ?>
	<br>
	<!--Token Fisico-->
	<!--Control por perfil-->
	
	<div class="col-xs-10">
	<?php if($perfil <> 'REGISTRA'){ ?>
		
		<div class="col-xs-2">
			<input name="tokenF" id="tokenF" type="number" class="form-control" placeholder="Ingrese clave del token" value="" required>
		</div>
			<button name="validaToken" class="btn btn-primary" id="yw22" type="button">Confirmar Operación</button>
			<a name="cancel" class="btn btn-danger" id="yw3" href="<?php echo Yii::app()->createUrl('site/index'); ?>">Cancelar</a>
			<p id="errortoken" style="color:red;" hidden>Error al validar token f&iacute;sico</p>
			<p>Ingrese la clave que aparece en su token</p>
		</div>
	<?php }else{ ?>
		<div style="display: inline-block;">
			<button name="validaToken" class="btn btn-primary" id="yw22" type="submit">Confirmar Operación</button>
			<a name="cancel" class="btn btn-danger" id="yw3" href="<?php echo Yii::app()->createUrl('site/index'); ?>">Cancelar</a>
		</div>
	<?php } ?>
	
	<!--Token Fisico-->
	<br>

	<?php $this->endWidget(); ?>
</div>

<?php if($perfil <> 'REGISTRA'){ ?><!--Control JS por perfil-->
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
                btn.innerHTML = 'Confirmar Operación';
                console.log(data);
                console.log(status);
                if(status == 'success'){
                    if(data == 801){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#salaray-payment-confirm-form').submit();
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
<?php } ?>