<?php
$this->pageTitle=Yii::app()->name . ' - '. Yii::t('bancard', 'Pago de Servicio');
?>

<h3><?php echo Yii::t('bancard', 'Pago de Servicio') ?></h3>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= $service->name ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?= $brandData[1]?>" alt="<?= $brandData[0]?> logo">
                    </div>
                    <?php if ($service->sample_bill): ?>
                        <div class="col-md-12">
                                <img src="<?= $service->sample_bill?>" alt="Ejemplo de factura">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-8">
                <span><?= $service->tip ?></span>
                <?= $this->renderPartial('/commons/_form', ['form'=>$form, 'withoutPanel'=>true]) ?>
            </div>
        </div>
   </div>
</div>