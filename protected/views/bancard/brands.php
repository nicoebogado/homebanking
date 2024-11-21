<?php
    HScript::register([
        'plugins/jquery.filterizr.min',
        'bancard/brands',
    ]);

    HCss::register('bancard/brands.css', false);

    $count = 0;
    $this->pageTitle=Yii::app()->name . ' - ' . Yii::t('bancard', 'Lista de Servicios');
?>

<h3>
    <?php echo Yii::t('bancard', 'Pago de Servicios'); ?>
    <small><?php echo Yii::t('bancard', 'Lista de Servicios') ?></small>
</h3>

<div class="panel panel-default">
    <div class="panel-header">
        <div id="brand-loading">Cargando la lista de los Servicios...</div>
        <div class="show-on-document-ready navbar">
            <div class="container-fluid">
                <div class="collapse navbar-collapse">
                    <?= CHtml::form('', 'get', ['class'=>'navbar-form navbar-left']) ?>
                        <!-- <div class="form-group">
                            <select class="form-control">
                                <option data-filter="all">Todos</option>
                                <?php foreach ($brands->categoryLabels as $k => $label): ?>
                                    <option data-filter="<?= $k ?>">
                                        <?= $label ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div> -->
                        <div class="form-group">
                            <input type="text" class="form-control filtr-search" placeholder="Buscar..." data-search>
                        </div>
                    <?= CHtml::endForm() ?>
                </div>
            </div>
        </div>
    </div>
    <div class="show-on-document-ready panel-body">
        <div class="row filtr-container">
            <?php foreach ($brands->brands as $brand): ?>
                <?= $this->renderPartial('_brand_item', compact('brand'))?>
            <?php endforeach ?>
        </div>

    </div>
</div>

<?php $this->beginWidget('booster.widgets.TbModal', array('id'=>'brand-modal')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4 id="brand-name"></h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div id="brand-logo" class="col-md-4">
                
            </div>
            <div id="brand-services" class="col-md-8">
                
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>

