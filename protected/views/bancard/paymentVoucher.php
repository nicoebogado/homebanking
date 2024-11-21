<?php
    HCss::register('utils/print.css', false);

    $this->pageTitle=Yii::app()->name . ' - '. Yii::t('bancard', 'Comprobante de Pago');
?>

<h3><?php echo Yii::t('bancard', 'Comprobante de Pago') ?></h3>

<div id="section-to-print" class="panel panel-default">
    <div class="panel-heading">
        <?= $model->service->name ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?= $brandData[1]?>" alt="<?= $brandData[0]?> logo">
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <?php $this->widget('booster.widgets.TbDetailView', array(
                    'data'=>$model,
                    'attributes'=>$model->voucherDetailsConfig($brandData[0]),
                )); ?>
            </div>
        </div>
    </div>
    <div class="panel-footer clearfix">
        <div class="pull-right">
            <button class="btn btn-link" onclick="window.print();">
                <span class="icon-printer fa-2x" aria-hidden="true"></span>
            </button>
            <a href="<?= $this->createUrl('/bancard/list/') ?>" class="btn btn-primary">
                Volver a la lista de Servicios
            </a>
        </div>
    </div>
</div>
