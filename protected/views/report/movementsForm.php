<?php
HScript::register([
    'libs/bootstrap-datepicker/bootstrap-datepicker.min',
    'libs/bootstrap-datepicker/locales/bootstrap-datepicker.es.min',
    'report/movementsForm',
]);

HCss::register('bootstrap-datepicker/datepicker.min.css', false);

$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('movements', 'Movimientos de Cuentas');
?>

<h3><?php echo Yii::t('movements', 'Movimientos de Cuentas'); ?></h3>

<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>