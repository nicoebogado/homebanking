<?php
HScript::register([
    'libs/emn178/md5.min',
    'libs/emn178/sha256.min',
    'plugins/jquery.detectIdInput',
    'commons/initDetectIdInput',
    'bancard/invoicesForm',
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
                <h2>Seleccione una factura</h2>
                <?php $this->widget('booster.widgets.TbGridView', array(
                    'type'              => 'striped',
                    'dataProvider'      => $dataProvider,
                    'enableSorting'     => false,
                    'template'          => '{items}{pager}',
                    'selectableRows'    => 0,
                    'columns'           => [
                        'description:text:Descripción',
                        [
                            'name'  => 'Monto',
                            'value' => '$data->f_amount',
                        ],
                        [
                            'name'  => 'Pago Mínimo',
                            'value' => '$data->f_minimum_payment',
                        ],
                        [
                            'name'  => 'Fecha de Vencimiento',
                            'value' => '$data->f_due_date',
                        ],
                        'button:raw:Opciones',
                    ],
                )); ?>
            </div>
        </div>
    </div>

    <div class="panel-footer clearfix">
        <div class="pull-right">
            <a href="<?= $this->createUrl('/bancard/list/') ?>" class="btn btn-primary">
                Volver a la lista de Servicios
            </a>
        </div>
    </div>
</div>

<?php $this->beginWidget('booster.widgets.TbModal', [
    'id'            => 'invoice-form-modal',
    'dialogClass'   => 'modal-lg',
]); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h2><?= $model->service->name ?></h2>
</div>

<div class="modal-body">
    <?php $this->widget('booster.widgets.TbDetailView', array(
        'id' => 'table-bill-detail',
        'data' => [],
        'attributes' => [
            [
                'label'  => 'Descripción',
                'value' => '',
                'cssClass' => 'bill-description',
            ],
            [
                'label'  => 'Monto',
                'value' => '',
                'cssClass' => 'bill-amount',
            ],
            [
                'label'  => 'Pago Mínimo',
                'value' => '',
                'cssClass' => 'bill-minpay',
            ],
            [
                'label'  => 'Fecha de Vencimiento',
                'value' => '',
                'cssClass' => 'bill-duedate',
            ],
        ],
    )); ?>
</div>

<div class="modal-footer clearfix">
    <div class="pull-right">
        <?= $this->renderPartial('/commons/_form', [
            'id' => 'form-pay-bill',
            'form' => $form,
            'action' => Yii::app()->createUrl(
                $model->service->commission_bill_fields ? '/bancard/billCommission' : '/bancard/payBill',
                ['bd' => $_GET['bd']]
            ),
            'withoutPanel' => true,
        ]) ?>
    </div>
</div>
<?php $this->endWidget(); ?>