<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('depositsToConfirm', 'Depósitos a Confirmar');
?>

<h3><?php echo Yii::t('depositsToConfirm', 'Depósitos a Confirmar') ?></h3>

<?php echo $this->renderPartial('/commons/_form', array('form'=>$form)); ?>