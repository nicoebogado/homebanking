<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('scheduledSavings', 'Formulario de Plan de Ahorro Programado');
?>

<div class="form">
    <?php
    // Renderizar el formulario utilizando TbForm
    echo $form->render();
    ?>
</div>
