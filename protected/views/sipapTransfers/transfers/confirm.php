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

<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>