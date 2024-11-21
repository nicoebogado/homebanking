<h3>
    <?= Yii::t('creditCard', 'Extracto de Tarjeta') ?>
    <small>
        <?= $balance->Nombrecliente . ' - *****' . substr($balance->Nrotarjeta, -4); ?>
    </small>
</h3>

<?= $this->renderPartial('_previousInvoiceHeaderSection', compact('balance')) ?>

<div class="panel panel-default mt-lg">
    <div class="panel-heading">
        <div class="panel-title">
            Extracto al <?= WebServiceClient::formatDate($balance->Fechacierre, "medium", true) ?>
        </div>
    </div>
    <div class="panel-body">

        <?php if ($previousPeriods->Codretorno === '00' && !empty($previousPeriods->Sdtpv_cierresdisponibles)) : ?>
            <?= CHtml::beginForm(['creditCard/previousPeriod'], 'post', ['onchange' => 'js:submit()']) ?>
            <input type="hidden" name="account" value="<?= $previousPeriods->Nrotarjeta ?>">
            <select name="period">
                <?php foreach ($previousPeriods->Sdtpv_cierresdisponibles->SDTPV_CierresDisponiblesItem as $period) : ?>
                    <option value="<?= $period->FechaCierre ?>" <?= $period->FechaCierre === $balance->Fechacierre ? 'selected="selected" ' : '' ?>>
                        <?= WebServiceClient::formatDate($period->FechaCierre, "medium", true) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?= CHtml::endForm() ?>
        <?php endif; ?>

        <?php $this->widget('booster.widgets.TbGridView', array(
            'type' => 'striped condensed',
            'dataProvider' => $dataProvider,
            'enableSorting' => false,
            'template' => '{items}{pager}',
            'selectableRows' => 0,
            'columns' => array(
                array(
                    'name' => Yii::t('creditCard', 'Fecha Operación'),
                    'value' => 'WebServiceClient::formatDate($data->FechaOperacion, "medium", true)',
                ),
                array(
                    'name' => Yii::t('creditCard', 'Fecha Proceso'),
                    'value' => 'WebServiceClient::formatDate($data->FechaProceso, "medium", true)',
                ),
                'NroCupon:text:' . Yii::t('creditCard', 'Comprobante'),
                array(
                    'name' => Yii::t('creditCard', 'Tipo Transacción'),
                    'value' => '$data->TipoTransaccion === "C" ? "Confirmado" : "Pendiente"',
                ),
                'Descripcion:text:' . Yii::t('app', 'Descripción'),
                array(
                    'name' => Yii::t('creditCard', 'Monto'),
                    'value' => 'Yii::app()->numberFormatter->formatDecimal($data->Importe)',
					'htmlOptions'=>array(
						'style'=>'text-align: right;'
                ),
                ),
            ),
        )); ?>
    </div>
</div>