<?php
HScript::register([
  'libs/emn178/md5.min',
  'libs/emn178/sha256.min',
  'plugins/jquery.detectIdInput',
  'commons/initDetectIdInput',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('sipapTransfer', 'Confirmación de Transferencia'); ?>
<h3><?php echo Yii::t('sipapTransfer', 'Confirmación'); ?></h3>
<small><?php echo Yii::t('sipapTransfer', 'Confirmación'); ?></small>
<div class="panel panel-default">
  <div class="panel-body">
    <?php $this->widget('booster.widgets.TbDetailView', array(
      'data' => $details,
      'attributes' => array(
        'nombrectadebito:text:' . Yii::t('sipapTransfer', 'Denominación de la Cuenta Débito'),
        'cuentadebito:text:' . Yii::t('sipapTransfer', 'Número de Cuenta Débito'),
        'beneficiario:text:' . Yii::t('sipapTransfer', 'Denominación de la Cuenta Crédito'),
        'cuentacredito:text:' . Yii::t('sipapTransfer', 'Número de Cuenta Crédito'),
        'participante:text:' . Yii::t('sipapTransfer', 'Banco'),
        'monedacredito:text:' . Yii::t('transfers', 'Moneda Crédito'),
        array(
          'label' => Yii::t('sipapTransfer', 'Monto'),
          'value' => Yii::app()->numberFormatter->formatDecimal($details->montocredito),
        ),
      ),
    )); ?>
  </div>
</div>

<!--test confirm-->
<div class="panel panel-default">
<div class="panel-body">
  <form class="form-horizontal" id="yw1" action="<?php echo Yii::app()->createUrl('sipapTransfers/Verify'); ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <div style="display:none">
      <input type="hidden" value="1" name="yform_3460a757" id="yform_3460a757"></div>
    <input name="TransferForm[transferType]" id="TransferForm_transferType" type="hidden" value="<?php echo $datos['transferType']; ?>">
    <input name="TransferForm[actionType]" id="TransferForm_actionType" type="hidden" value="<?php echo $datos['actionType']; ?>">
    <input name="TransferForm[debitAccount]" id="TransferForm_debitAccount" type="hidden" value="<?php echo $datos['debitAccount']; ?>">
    <input name="TransferForm[currency]" id="TransferForm_currency" type="hidden" value="<?php echo $datos['currency']; ?>">
    <input name="TransferForm[amount]" id="TransferForm_amount" type="hidden" value="<?php echo $datos['amount']; ?>">
    <input name="TransferForm[date]" id="TransferForm_date" type="hidden" value="<?php echo $datos['date']; ?>">
    <input name="TransferForm[reason]" id="TransferForm_reason" type="hidden" value="<?php echo $datos['reason']; ?>">
    <input name="TransferForm[concept]" id="TransferForm_concept" type="hidden" value="<?php echo $datos['concept']; ?>">
    <input name="TransferForm[exchangeContract]" id="TransferForm_exchangeContract" type="hidden" value="<?php echo $datos['exchangeContract']; ?>">
    <input name="TransferForm[creditQuotation]" id="TransferForm_creditQuotation" type="hidden" value="<?php echo $datos['creditQuotation']; ?>">
    <input name="TransferForm[financialEntity]" id="TransferForm_financialEntity" type="hidden" value="<?php echo $datos['financialEntity']; ?>">
    <input name="TransferForm[creditAccount]" id="TransferForm_creditAccount" type="hidden" value="<?php echo $datos['creditAccount']; ?>">
    <input name="TransferForm[name]" id="TransferForm_name" type="hidden" value="<?php echo $datos['name']; ?>">
    <input name="TransferForm[address]" id="TransferForm_address" type="hidden" value="<?php echo $datos['address']; ?>">
    <input name="TransferForm[documentType]" id="TransferForm_documentType" type="hidden" value="<?php echo $datos['documentType']; ?>">
    <input name="TransferForm[documentData]" id="TransferForm_documentData" type="hidden" value="<?php echo $datos['documentData']; ?>">
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