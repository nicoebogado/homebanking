<?php
HScript::register([
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad',
	'plugins/jquery.detectIdInput',
	'commons/initDetectIdInput',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('transfers', 'Transferencias Entre Cuentas');
?>

<h3>
	<?php echo Yii::t('transfers', 'Transferencias Entre Cuentas'); ?>
	<small><?php echo Yii::t('commons', 'Confirmación'); ?></small>
</h3>
<div class="panel panel-default">
	<div class="panel-body">
		<?php $this->widget('booster.widgets.TbDetailView', array(
			'data' => $details,
			'attributes' => array(
				'monedadebito:text:' . Yii::t('transfers', 'Moneda Débito'),
				array(
					'label' => 'Monto',
					'value' => Yii::app()->numberFormatter->formatDecimal($details->monto),
				),
				'monedacredito:text:' . Yii::t('transfers', 'Moneda Crédito'),
				array(
					'label' => 'Monto Crédito',
					'value' => Yii::app()->numberFormatter->formatDecimal($details->montocredito),
				),
				'nombrectacredito:text:' . Yii::t('transfers', 'Nombre de la Cuenta Crédito'),
			),
		)); ?>
	</div>

    
</div>

<!--test form-->
<div class="panel panel-default">
<div class="panel-body">
    <form class="form-horizontal" id="yw1" action="<?php echo Yii::app()->createUrl('transfer/confirm'); ?>" method="post">
        <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
        <div style="display:none">
            <input type="hidden" value="1" name="yform_397432f7" id="yform_397432f7">
        </div>
        <input name="TransferForm[debitAccount]" id="TransferForm_debitAccount" type="hidden" value="<?php echo $datos['debitAccount']; ?>">
        <input name="TransferForm[isThird]" id="TransferForm_isThird" type="hidden" value="<?php echo $datos['isThird']; ?>">
        <input name="TransferForm[creditAccount]" id="TransferForm_creditAccount" type="hidden" value="<?php echo $datos['creditAccount']; ?>">
        <input name="TransferForm[thirdCreditAccount]" id="TransferForm_thirdCreditAccount" type="hidden" value="<?php echo $datos['thirdCreditAccount']; ?>">
        <input name="TransferForm[thirdDocType]" id="TransferForm_thirdDocType" type="hidden" value="<?php echo $datos['thirdDocType']; ?>">
        <input name="TransferForm[thirdDocNumber]" id="TransferForm_thirdDocNumber" type="hidden" value="<?php echo $datos['thirdDocNumber']; ?>">
        <input name="TransferForm[beneficiaryName]" id="TransferForm_beneficiaryName" type="hidden" value="<?php echo $datos['beneficiaryName']; ?>">
        <input name="TransferForm[saveFrequent]" id="TransferForm_saveFrequent" type="hidden" value="<?php echo $datos['saveFrequent']; ?>">
        <input name="TransferForm[amount]" id="TransferForm_amount" type="hidden" value="<?php echo $datos['amount']; ?>">
        <input name="TransferForm[concept]" id="TransferForm_concept" type="hidden" value="<?php echo $datos['concept']; ?>">
        <input name="TransferForm[confirm]" id="TransferForm_confirm" type="hidden" value="<?php echo $datos['confirm']; ?>">
        <input name="TransferForm[exchangeContract]" id="TransferForm_exchangeContract" type="hidden" value="">
        <input name="TransferForm[creditQuotation]" id="TransferForm_creditQuotation" type="hidden" value="">
		<input name="TransferForm[isToken]" id="TransferForm_isToken" type="hidden" value="true">
        <div class="form-actions">
		<div class="col-xs-2">
        <input name="tokenF" id="tokenF" type="number" class="form-control" placeholder="Ingrese clave del token" value="" required>
		</div>
        <button name="validaToken" class="btn btn-primary" id="yw22" type="button">Confirmar Transferencia</button>
        <a name="cancel" class="btn btn-danger" id="yw3" href="<?php echo Yii::app()->createUrl('site/index'); ?>">Cancelar</a>
        <p id="errortoken" style="color:red;" hidden>Error al validar token f&iacute;sico</p>
        <p>Ingrese la clave que aparece en su token </p>
        </div>
    </form>

	</div>
</div>

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
                btn.innerHTML = 'Confirmar Transferencia';
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