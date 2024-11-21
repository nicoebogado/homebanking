<?php
HScript::register([
    'libs/emn178/md5.min',
    'libs/emn178/sha256.min',
    'plugins/jquery.detectIdInput',
    'commons/initDetectIdInput',
    'bancard/commissionForm',
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
                <span class="text-muted">
                    El servicio <?= $model->service->name ?> de <?= $brandData[0] ?> transfiere comisión al usuario. Confirmas que aceptas el monto total si finalizas el pago.
                </span>
                <?php $this->widget('booster.widgets.TbDetailView', array(
                    'data' => $model,
                    'attributes' => $model->commissionDetailsConfig(),
                )); ?>

                <label class="checkbox">
                    <input type="checkbox" id="showInputs">
                    ¿Quieres facturas con datos?
                </label>

                <?= $this->renderPartial('/commons/_form', [
                    'id' => 'commission-inputs',
                    'form' => $form,
                    'withoutPanel' => true,
                ]) ?>
            </div>
        </div>
    </div>
</div>