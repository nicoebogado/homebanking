<?php if (isset($form)) echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>

<!-- Cabecera -->

<table width="100%">
    <tbody>
        <tr>
            <td class="panel panel-default">
                <div class="panel-body">
                    <div>
                        <?= HImage::html('logo.png', 'Logo', array(
                            'style' => 'max-width: 200px;',
                        )) ?>
                    </div>
                    <div>
                        <b>Estado de Cta. al</b> <?php echo $header['date'] ?>
                    </div>
                    <div>
                        <b>Nro. de Cliente</b> <?php echo $header['codigocliente'] ?>
                    </div>
                    <div>
                        <b>A Requerimiento</b>
                    </div>
                </div>
            </td>
            <td class="panel panel-default">
                <div class="panel-body">
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
                </div>
            </td>
        </tr>
    </tbody>
</table>

<!-- Cuerpo -->
<?php
// Muestr si el tipo de implemntación es la de casa de préstamos
if (isset(Yii::app()->user->deploymentType) && Yii::app()->user->deploymentType != '9999') :
?>
    <div class="panel panel-default mt-lg">
        <div class="panel-heading">
            <h3><?php echo Yii::t('extractOnRequest', 'Lista de extractos de cuentas') ?></h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('booster.widgets.TbGridView', array(
                'type' => 'striped condensed',
                'dataProvider' => $dataProvider['cuentascliente'],
                'enableSorting' => false,
                'template' => '{items}{pager}',
                'selectableRows' => 0,
                'columns' => array(
                    'descripcion:text:' . Yii::t('app', 'Descripción'),
                    'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
                    array(
                        'name' => Yii::t('app', 'Saldo Anterior'),
                        'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoanteriordisponible)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                    array(
                        'name' => Yii::t('app', 'Saldo Actual'),
                        'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualcontable)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                    array(
                        'name' => Yii::t('app', 'Saldo Actual Bloqueado'),
                        'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualbloqueado)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                    array(
                        'name' => Yii::t('app', 'Saldo Actual Retenido'),
                        'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualretenido)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                    array(
                        'name' => Yii::t('app', 'Saldo Actual Disponible'),
                        'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualdisponible)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                ),
            )); ?>
        </div>
    </div>
<?php endif; ?>

<?php $tipo = array(
    'PF'  => array('titulo' => 'Plazo Fijo'),
    'CDA' => array('titulo' => 'Certificado de depósitos de ahorro'),
    'PR'  => array('titulo' => 'Préstamos'),
    'TA'  => array('titulo' => 'Tarjetas de Crédito'),
    'LS'  => array('titulo' => 'Línea de Sobregiro'),
    'TI'  => array('titulo' => 'Títulos de Inversión'),
); ?>

<?php foreach ($tipo as $key => $value) : ?>
    <?php if (isset($dataProvider[$key])) : ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3><?php echo Yii::t('extractOnRequest', 'Resumen de cuentas de tipo ' . $value['titulo']) ?></h3>
            </div>
            <div class="panel-body">
                <?php

                switch ($key) {
                    case 'TA':
                        $titulo1 = Yii::t('app', 'Pago Mínimo');
                        $titulo2 = Yii::t('app', 'Deuda Actual');
                        $titulo3 = Yii::t('app', 'Disponible');
                        $valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
                        $valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
                        $valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
                        break;
                    case 'PR':
                        $titulo1 = Yii::t('app', 'Monto Préstamo');
                        $titulo2 = Yii::t('app', 'Importe Cuota');
                        $titulo3 = Yii::t('app', 'Saldo Actual');
                        $valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
                        $valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
                        $valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
                        break;
                    case 'CDA':
                        $titulo1 = Yii::t('app', 'Monto Capital');
                        $titulo2 = Yii::t('app', 'Monto Interés');
                        $titulo3 = Yii::t('app', 'Importe Cupón');
                        $valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
                        $valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
                        $valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
                        break;
                    default:
                        $titulo1 = Yii::t('app', 'Monto Capital');
                        $titulo2 = Yii::t('app', 'Monto Interés');
                        $titulo3 = Yii::t('app', 'Saldo Disponible');
                        $valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
                        $valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
                        $valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
                        break;
                }

                $this->widget('booster.widgets.TbGridView', array(
                    'type' => 'striped condensed',
                    'dataProvider' => $dataProvider[$key],
                    'enableSorting' => false,
                    'template' => '{items}{pager}',
                    'selectableRows' => 0,
                    'columns' => array(
                        'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
                        'modalidad:text:' . Yii::t('app', 'Modalidad'),
                        'codigomoneda:text:' . Yii::t('app', 'Moneda'),
                        'fechavencimiento:text:' . Yii::t('app', 'Fecha de Vencimiento'),
                        array(
                            'name' => $titulo1,
                            'value' => $valor1,
							'htmlOptions'=>array(
						'style'=>'text-align: right;'
                        ),
						),
                        array(
                            'name' => $titulo2,
                            'value' => $valor2,
							'htmlOptions'=>array(
						'style'=>'text-align: right;'
                        ),
						),
                        array(
                            'name' => $titulo3,
                            'value' => $valor3,
							'htmlOptions'=>array(
						'style'=>'text-align: right;'
                        ),
						),
                    ),
                )); ?>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php
// Muestr si el tipo de implemntación es la de casa de préstamos
if (isset(Yii::app()->user->deploymentType) && Yii::app()->user->deploymentType != '9999') :
?>
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
<?php endif; ?>

<?php
// Muestr si el tipo de implemntación es la de casa de préstamos
if (isset(Yii::app()->user->deploymentType) && Yii::app()->user->deploymentType != '9999') :
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3><?php echo Yii::t('extractOnRequest', 'Depósitos a Confirmar') ?></h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('booster.widgets.TbGridView', array(
                'type' => 'striped condensed',
                'dataProvider' => $dataProvider['retencionescuenta'],
                'enableSorting' => false,
                'template' => '{items}{pager}',
                'selectableRows' => 0,
                'columns' => array(
                    'numerodocumento:text:' . Yii::t('app', 'Nro. de Documento'),
                    'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
                    'descripcion:text:' . Yii::t('app', 'Descripción'),
                    'fechaliberacion:text:' . Yii::t('app', 'Fecha Liberación'),
                    'fechamovimiento:text:' . Yii::t('app', 'Fecha Movimiento'),
                    array(
                        'name' => Yii::t('app', 'Monto'),
                        'value' => 'Yii::app()->numberFormatter->formatDecimal($data->monto)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                ),
            )); ?>
        </div>
    </div>
<?php endif; ?>

<?php
// Muestr si el tipo de implemntación es la de casa de préstamos
if (isset(Yii::app()->user->deploymentType) && Yii::app()->user->deploymentType != '9999') :
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3><?php echo Yii::t('extractOnRequest', 'Bloqueo de cuentas') ?></h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('booster.widgets.TbGridView', array(
                'type' => 'striped condensed',
                'dataProvider' => $dataProvider['bloqueoscuenta'],
                'enableSorting' => false,
                'template' => '{items}{pager}',
                'selectableRows' => 0,
                'columns' => array(
                    'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
                    'fechainicio:text:' . Yii::t('app', 'Fecha inicio'),
                    'causabloqueo:text:' . Yii::t('app', 'Causa'),
                    array(
                        'name' => Yii::t('app', 'Monto'),
                        'value' => '$data->codigomoneda." ".Yii::app()->numberFormatter->formatDecimal($data->monto)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
					),
                    ),
                ),
            )); ?>
        </div>
    </div>
<?php endif; ?>