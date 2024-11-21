<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('cdaCoupons', 'Cupones CDA');
?>

<h3><?php echo Yii::t('cdaCoupons', 'Cupones CDA') ?></h3>

<?php echo $this->renderPartial('/commons/_form', array('form'=>$form)); ?>