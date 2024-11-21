<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('aditionalCards', 'Tarjetas Adicionales');
?>

<h3><?php echo Yii::t('aditionalCards', 'Tarjetas Adicionales') ?></h3>

<?php echo $this->renderPartial('/commons/_form', array('form'=>$form)); ?>