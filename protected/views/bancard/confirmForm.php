<?php
HScript::register([
    'libs/emn178/md5.min',
    'libs/emn178/sha256.min',
    'plugins/jquery.detectIdInput',
    'commons/initDetectIdInput',
]);

$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('bancard', 'Pago de Servicio');
?>

<h3><?php echo Yii::t('bancard', 'Pago de Servicio') ?></h3>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= $model->service->name ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?= $brandData[1] ?>" alt="<?= $brandData[0] ?> logo">
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <?php $this->widget('booster.widgets.TbDetailView', array(
                    'data' => $model,
                    'attributes' => $model->detailsConfig(),
                )); ?>

                <?= $this->renderPartial('/commons/_form', $formParams) ?>
            </div>
        </div>
    </div>
</div>