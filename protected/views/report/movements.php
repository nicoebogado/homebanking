<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('movements', 'Movimientos de Cuentas');
?>

<h3>
    <?php echo Yii::t('movements', 'Movimientos de Cuentas'); ?>
    <small><?php echo $accountType . ' - ' . $account . ' - ' . $accountDenomination; ?></small>
</h3>
<?php if (isset(Yii::app()->user->deploymentType) && Yii::app()->user->deploymentType != '9999' && isset($chartDatas)) : ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php $this->widget(
                'booster.widgets.TbHighCharts',
                array(
                    'htmlOptions' => array(
                        'style' => 'height:200px',
                    ),
                    'options' => array(
                        'chart' => array('type' => 'spline'),
                        'title' => array(
                            'text' => null,
                        ),
                        'credits' => array('enabled' => false),
                        'exporting' => array('enabled' => false),
                        'xAxis' => array(
                            'type' => 'datetime',
                            'title' => array('text' => Yii::t('commons', 'Fecha')),
                        ),
                        'yAxis' => array(
                            'title' => array('text' => Yii::t('commons', 'Monto')),
                        ),
                        'legend' => array(
                            'layout' => 'vertical',
                            'align' => 'right',
                            'verticalAlign' => 'middle',
                            'borderWidth' => 0,
                        ),
                        'series' => array(
                            array(
                                'name' => Yii::t('commons', 'Saldo'),
                                'data' => $chartDatas,
                            ),
                        )
                    ),
                )
            ); ?>
        </div>
    </div>
<?php endif; ?>

<?php /*$this->widget('ext.ENavLinks', array('links'=>array(
    Yii::t('movements', 'Ver Detalles de la Cuenta') => array('accountDetails', 'id'=>$id),
    Yii::t('commons', 'Consultar Otra Cuenta') => array('movements'),
    Yii::t('commons', 'Volver al Inicio') => array('site/index'),
)));*/ ?>

<?php if (isset($form)) echo $this->renderPartial('/commons/_form', array('form' => $form)); ?>

<div class="panel panel-default">
    <div class="panel-body">
        <?php
        if ($typeAccount != 'TJ' && $typeAccount != 'CDA') {
            $this->widget('booster.widgets.TbGridView', array(
                'type' => 'striped condensed',
                'ajaxType' => 'POST',
                'beforeAjaxUpdate' => 'js:function(id, options){
                options.data={
                    MovementForm:{
                        accountNumber:"' . $model->accountNumber . '",
                        dateFrom:"' . $model->dateFrom . '",
                        dateTo:"' . $model->dateTo . '",
                        viewOpt:true
                    },
                    YII_CSRF_TOKEN: csrfToken
                };
            }',
                'dataProvider' => $dataProvider,
                'enableSorting' => false,
                'template' => '{items}{pager}',
                'selectableRows' => 0,
                'columns' => array(
                    array(
                        'name' => Yii::t('movements', 'Fecha del movimiento'),
                        'value' => 'WebServiceClient::formatDate($data->fecha)." ".$data->hora',
                    ),
                    array(
                        'name' => Yii::t('movements', 'Fecha de confirmación'),
                        'value' => 'WebServiceClient::formatDate($data->fechavalor)." ".$data->hora',
                    ),
                    'numerodocumento:text:' . Yii::t('movements', 'Nro. Documento'),
                    'descripciontransaccionpadre:text:' . Yii::t('commons', 'Descripción'),
                    array(
                        'name' => Yii::t('commons', 'Débito'),
                        'value' => '$data->tipomovimiento === "D" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                    array(
                        'name' => Yii::t('commons', 'Crédito'),
                        'value' => '$data->tipomovimiento === "C" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
					),
                    ),
                    array(
                        'name' => Yii::t('commons', 'Saldo'),
                        'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldo)',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                ),
            ));
        } elseif ($typeAccount === 'TJ') {
            $mainTj = array_shift($tjProviders);
            echo '<h3>' . $mainTj['title'] . '</h3>';
            $this->widget('booster.widgets.TbGridView', array(
                'type' => 'striped condensed',
                'ajaxType' => 'POST',
                'beforeAjaxUpdate' => 'js:function(id, options){
                    options.data={
                        MovementForm:{
                            accountNumber:"' . $model->accountNumber . '",
                            dateFrom:"' . $model->dateFrom . '",
                            dateTo:"' . $model->dateTo . '",
                            viewOpt:true
                        },
                        YII_CSRF_TOKEN: csrfToken
                    };
                }',
                'dataProvider' => $mainTj['dataProvider'],
                'enableSorting' => false,
                'template' => '{items}{pager}',
                'selectableRows' => 0,
                'columns' => array(
                    array(
                        'name' => Yii::t('movements', 'Fecha del movimiento'),
                        'value' => 'WebServiceClient::formatDate($data->fecha)." ".$data->hora',
                    ),
                    array(
                        'name' => Yii::t('movements', 'Fecha de confirmación'),
                        'value' => 'WebServiceClient::formatDate($data->fechavalor)." ".$data->hora',
                    ),
                    'numerodocumento:text:' . Yii::t('movements', 'Nro. Documento'),
                    'descripciontransaccionpadre:text:' . Yii::t('commons', 'Descripción'),
                    array(
                        'name' => Yii::t('commons', 'Débito'),
                        'value' => '$data->tipomovimiento === "D" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
                    ),
					),
                    array(
                        'name' => Yii::t('commons', 'Crédito'),
                        'value' => '$data->tipomovimiento === "C" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
						'htmlOptions'=>array(
						'style'=>'text-align: right;'
					),
                    ),
                ),
            ));

            foreach ($tjProviders as $provider) {
                echo '</div></div>
                <div class="panel panel-default"><div class="panel-body">
                <h3>' . $provider['title'] . '</h3>';

                $this->widget('booster.widgets.TbGridView', array(
                    'type' => 'striped condensed',
                    'ajaxType' => 'POST',
                    'beforeAjaxUpdate' => 'js:function(id, options){
                        options.data={
                            MovementForm:{
                                accountNumber:"' . $model->accountNumber . '",
                                dateFrom:"' . $model->dateFrom . '",
                                dateTo:"' . $model->dateTo . '",
                                viewOpt:true
                            },
                            YII_CSRF_TOKEN: csrfToken
                        };
                    }',
                    'dataProvider' => $provider['dataProvider'],
                    'enableSorting' => false,
                    'template' => '{items}{pager}',
                    'selectableRows' => 0,
                    'columns' => array(
                        array(
                            'name' => Yii::t('movements', 'Fecha del movimiento'),
                            'value' => 'WebServiceClient::formatDate($data->fecha)." ".$data->hora',
                        ),
                        array(
                            'name' => Yii::t('movements', 'Fecha de confirmación'),
                            'value' => 'WebServiceClient::formatDate($data->fechavalor)." ".$data->hora',
                        ),
                        'numerodocumento:text:' . Yii::t('movements', 'Nro. Documento'),
                        'descripciontransaccionpadre:text:' . Yii::t('commons', 'Descripción'),
                        array(
                            'name' => Yii::t('commons', 'Débito'),
                            'value' => '$data->tipomovimiento === "D" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
							'htmlOptions'=>array(
								'style'=>'text-align: right;'
                        ),
						),
                        array(
                            'name' => Yii::t('commons', 'Crédito'),
                            'value' => '$data->tipomovimiento === "C" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
							'htmlOptions'=>array(
								'style'=>'text-align: right;'
							),
                        ),
                    ),
                ));
            }
        } else {
            $this->widget('booster.widgets.TbGridView', array(
                'type' => 'striped condensed',
                'ajaxType' => 'POST',
                'beforeAjaxUpdate' => 'js:function(id, options){
                    options.data={
                        MovementForm:{
                            accountNumber:"' . $model->accountNumber . '",
                            dateFrom:"' . $model->dateFrom . '",
                            dateTo:"' . $model->dateTo . '",
                            viewOpt:true
                        },
                        YII_CSRF_TOKEN: csrfToken
                    };
                }',
                'dataProvider' => $dataProvider,
                'enableSorting' => false,
                'template' => '{items}{pager}',
                'selectableRows' => 0,
                'columns' => array(
                    array(
                        'name' => Yii::t('movements', 'Fecha del movimiento'),
                        'value' => 'WebServiceClient::formatDate($data->fecha)." ".$data->hora',
                    ),
                    array(
                        'name' => Yii::t('movements', 'Fecha de confirmación'),
                        'value' => 'WebServiceClient::formatDate($data->fechavalor)." ".$data->hora',
                    ),
                    'numerodocumento:text:' . Yii::t('movements', 'Nro. Documento'),
                    'descripciontransaccionpadre:text:' . Yii::t('commons', 'Descripción'),
                    array(
                        'name' => Yii::t('commons', 'Débito'),
                        'value' => '$data->tipomovimiento === "D" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
                    ),
                    array(
                        'name' => Yii::t('commons', 'Crédito'),
                        'value' => '$data->tipomovimiento === "C" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
                    ),
                ),
            ));
        }
        ?>

    </div>
</div>