<?php
HScript::register([
    'libs/emn178/md5.min',
    'libs/emn178/sha256.min',
    'plugins/jquery.secureKeypad.min',
    'commons/securizeKeypad',
    'payments/form',
]);
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('salaryPayment', 'Pagos de Salarios');
?>

<h3>
    <?php echo Yii::t('salaryPayment', 'Pagos de Salarios'); ?>
</h3>

<div class="form">
    <?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>
</div>

<style>
    #salary-payment-form .form-group:nth-child(3) {
        margin-left: 15px;
    }
</style>