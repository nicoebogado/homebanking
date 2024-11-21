<div class="panel panel-default">
    <div class="panel-heading">
        <h3>Informe Semestral de Gastos, Comisiones e Intereses</h3>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <b>Código de Persona</b>
            </div>
            <div class="col-md-4"><?= $header['codigoCliente'] ?></div>
            <div class="col-md-1">
                <b>Oficial</b>
            </div>
            <div class="col-md-5"><?= $header['oficial'] ?></div>

            <div class="col-md-2">
                <b>Nombre del cliente</b>
            </div>
            <div class="col-md-4"><?= $header['nombreCliente'] ?></div>
            <div class="col-md-1">
                <b>Oficina</b>
            </div>
            <div class="col-md-5"><?= $header['oficina'] ?></div>

            <div class="col-md-2">
                <b>Dirección</b>
            </div>
            <div class="col-md-10"><?= $header['direccion'] ?: 'Sin dirección' ?></div>

            <div class="col-md-2">
                <b>Periodo</b>
            </div>
            <div class="col-md-4"><?= $header['periodo'] ?></div>
            <div class="col-md-1">
                <b>Fecha</b>
            </div>
            <div class="col-md-2"><?= $header['fecha'] ?></div>
            <div class="col-md-1">
                <b>Al</b>
            </div>
            <div class="col-md-2"><?= $header['al'] ?></div>
        </div>
        <hr>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>Nro Cuenta</th>
                    <th>Concepto</th>
                    <th>Fecha de pago</th>
                    <th>Nro Cuota</th>
                    <th class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php $currentProduct = '' ?>
                <?php $currentMoney = '' ?>
                <?php $sumByProduct = 0 ?>
                <?php $sumByMoney = 0 ?>
                <?php $sumTotal = 0 ?>

                <?php foreach ($dataProvider as $row) : ?>

                    <?php if ($currentMoney && ($currentProduct !== $row->desmodulo || $currentMoney !== $row->codigomoneda)) : ?>
                        <?= $this->semiannualReportTotalBlock('Total por moneda', $sumByMoney); ?>
                        <?php $sumByMoney = 0 ?>
                    <?php endif ?>

                    <?php if ($currentProduct !== $row->desmodulo) : ?>
                        <?php $currentProduct = $row->desmodulo ?>
                        <?php $currentMoney = '' ?>

                        <?php if ($sumByProduct) : ?>
                            <?= $this->semiannualReportTotalBlock('Total por producto Gs.', $sumByProduct); ?>
                            <?php $sumByProduct = 0 ?>
                        <?php endif ?>

                        <tr>
                            <th colspan="5">Producto</th>
                        </tr>
                        <tr>
                            <td colspan="5"><?= $currentProduct ?></td>
                        </tr>
                    <?php endif ?>

                    <?php if ($currentMoney !== $row->codigomoneda) : ?>
                        <?php $currentMoney = $row->codigomoneda ?>
                        <tr>
                            <th colspan="5">Moneda</th>
                        </tr>
                        <tr>
                            <td colspan="5"><?= $currentMoney ?></td>
                        </tr>
                    <?php endif ?>

                    <?php $sumByMoney += $row->monto ?>
                    <?php $sumByProduct += $row->montogs ?>
                    <?php $sumTotal += $row->montogs ?>

                    <tr>
                        <td><?= $row->numerocuenta ?></td>
                        <td><?= $row->transaccion ?></td>
                        <td>
                            <?= substr($row->fecha, 0, 2) . '/' . substr($row->fecha, 2, 2) . '/' . substr($row->fecha, 4) ?>
                        </td>
                        <td><?= isset($row->numerocuota) ? $row->numerocuota : '' ?></td>
                        <td class="text-right">
                            <?= Yii::app()->numberFormatter->formatDecimal($row->monto) ?>
                        </td>
                    </tr>
                <?php endforeach ?>

                <?= $this->semiannualReportTotalBlock('Total por moneda', $sumByMoney); ?>
                <?= $this->semiannualReportTotalBlock('Total por producto Gs.', $sumByProduct); ?>
                <?= $this->semiannualReportTotalBlock('Total General Gs.', $sumTotal); ?>
            </tbody>
        </table>
    </div>
</div>