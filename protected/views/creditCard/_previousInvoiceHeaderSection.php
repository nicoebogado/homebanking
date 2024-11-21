<div class="panel panel-default mt-lg">
    <div class="list-group">
        <div class="list-group-item">

            <div class="row">

                <div class="col-md-4">
                    <?= $this->renderPartial('_invoiceHeaderItem', [
                        'icon' => 'credit-card',
                        'title' => 'Denominación',
                        'content' => $balance->Marca . ' ' . $balance->Clase . ' ' . $balance->Afinidad,
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $this->renderPartial('_invoiceHeaderItem', [
                        'icon' => 'usd',
                        'title' => 'Moneda de la Tarjeta',
                        'content' => $balance->Moneda,
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $this->renderPartial('_invoiceHeaderItem', [
                        'icon' => 'money',
                        'title' => 'Línea de Crédito',
                        'content' => Yii::app()->numberFormatter->formatDecimal($balance->Lineacredito),
                    ]) ?>
                </div>

            </div>

            <div class="row">

                <div class="col-md-4">
                    <?= $this->renderPartial('_invoiceHeaderItem', [
                        'icon' => 'money',
                        'title' => 'Pago Mínimo',
                        'content' => Yii::app()->numberFormatter->formatDecimal($balance->Pagominimo),
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $this->renderPartial('_invoiceHeaderItem', [
                        'icon' => 'calendar',
                        'title' => 'Vencimiento de Pago Mínimo',
                        //'content' => WebServiceClient::formatDate($balance->Fechavtopagomin),
                        //added:11-05-2022,Higinio Samaniego, se corrige error en fecha de origen de bancard
						'content' => WebServiceClient::formatDate($balance->Fechavtopagomin, 'medium', true),
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $this->renderPartial('_invoiceHeaderItem', [
                        'icon' => 'money',
                        'title' => 'Deuda Total',
                        'content' => Yii::app()->numberFormatter->formatDecimal($balance->Deudatotal),
                    ]) ?>
                </div>

            </div>

        </div>
    </div>
</div>