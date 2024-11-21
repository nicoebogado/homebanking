<?php
HScript::register([
    'libs/emn178/md5.min',
    'libs/emn178/sha256.min',
    'plugins/jquery.secureKeypad.min',
    'commons/securizeKeypad',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('serviceProviders', 'Pago de Servicios');
?>
<h3>
    <?php echo Yii::t('serviceProviders', 'Pago de Servicios'); ?>
</h3>
<div class="form">
    <?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>
</div>