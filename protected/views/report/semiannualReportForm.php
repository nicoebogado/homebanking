<?php
$this->pageTitle = Yii::app()->name . ' - Reporte semestral';
?>

<h3>Reporte semestral</h3>

<div class="form">
    <?php echo $this->renderPartial('/commons/_form', compact('form')); ?>
</div>