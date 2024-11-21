<?php if (isset($form)) echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>

<!-- Cabecera -->

<table width="100%">
    <tbody>
        <tr>
            <td class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div>Estado de Cta. al</div>
                            <div><?php echo $header['date'] ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div>Nro. de Cliente</div>
                            <div><?php echo $header['codigocliente'] ?></div>
                        </div>
                    </div>
                </div>
            </td>
            <td class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class=col-md-9>
                            <div>
                                <?php echo $header['nombrecliente'] ?>
                            </div>
                            <div>
                                <?php echo $header['direccioncliente'] ?>
                            </div>
                            <div>
                                Teléfono: <?php echo $header['telefonocliente'] ?>
                            </div>
                            <div>
                                Oficial del Cliente: <?php echo Yii::app()->user->getState('officerName'); ?>
                            </div>
                            <div>
                                <div>Teléfono: <?php echo Yii::app()->user->getState('officerPhone'); ?></div>
                                <div>e-mail: <?php echo Yii::app()->user->getState('officerEmail'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <?= HImage::html('logo.png', 'Logo', array(
                                'style' => 'max-width: 200px;',
                            )) ?>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<!-- Cuerpo -->

<div class="panel panel-default">
    <div class="panel-heading">
        <h3><?php echo Yii::t('extractOnRequest', 'Detalle de los movimientos') ?></h3>
    </div>
    <div class="panel-body">
        <?php $this->widget('booster.widgets.TbGridView', array(
            'type' => 'striped condensed',
            'dataProvider' => $dataProvider['detallesmovimiento'],
            'enableSorting' => false,
            'template' => '{items}{pager}',
            'selectableRows' => 0,
            'columns' => array(
                'fechaconfirmacion:text:' . Yii::t('app', 'Fecha conf.'),
                'fechatransaccion:text:' . Yii::t('app', 'Fecha tran.'),
                'numerocomprobante:text:' . Yii::t('app', 'Nro Comprobante'),
                'concepto:text:' . Yii::t('app', 'Concepto'),
                'descripcionoficina:text:' . Yii::t('app', 'Oficina'),
                array(
                    'name' => Yii::t('app', 'Importe Débito'),
                    'value' => 'Yii::app()->numberFormatter->formatDecimal($data->montodebito)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
                ),
				),
                array(
                    'name' => Yii::t('app', 'Importe Crédito'),
                    'value' => 'Yii::app()->numberFormatter->formatDecimal($data->montocredito)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
                ),
				),
                array(
                    'name' => Yii::t('app', 'Saldo Actual'),
                    'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldo)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
					),
                ),
            ),
        )); ?>
    </div>
</div>