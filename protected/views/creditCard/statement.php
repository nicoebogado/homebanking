<?php
HScript::register('credit-card/extracts.min');

$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('creditCard', 'Extracto');
?>

<h3>
    <?= Yii::t('creditCard', 'Extracto de Tarjeta') ?>
    <small>
        <?= $balance->Nombrecliente.' - *****'.substr($balance->Nrotarjeta, -4);?>
    </small>
</h3>

<?= $this->renderPartial('_invoiceHeaderSection', compact('balance')) ?>
<div class="panel panel-default">
   <div class="panel-body">
      <button class="btn btn-primary" data-toggle="tooltip" title="" id="print" type="button" data-original-title="Imprimir">
         Descargar PDF
      </button>
   </div>
</div>
<div class="panel panel-default mt-lg">
    <div class="panel-heading">
        <div class="panel-title">
            <?= $title ?>

            <div class="pull-right">
                <?= CHtml::beginForm() ?>
                    <input type="hidden" name="account" value="<?= $balance->Nrotarjeta ?>">
                    <label>Ver Periodo</label>
                    <select name="period" id="period">
                        <option value="actual">Actual</option>

                        <?php foreach ($previousPeriods as $period): ?>
                            <option
                                value="<?= $period->FechaCierre ?>"
                                <?= $period->FechaCierre == $selectedPeriod ? 'selected="selected"' : '' ?>
                            >
                                <?= WebServiceClient::formatDate($period->FechaCierre, "medium", true) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?= CHtml::endForm() ?>
            </div>
        </div>
    </div>
    <div class="panel-body">
<div class="contenedor">

        <?php $this->widget('booster.widgets.TbGridView', array(
            'type'=>'striped condensed',
            'dataProvider'=>$dataProvider,
            'enableSorting'=>false,
            'template'=>'{items}{pager}',
            'selectableRows'=>0,
            'columns'=>array(
                array(
                    'name'=>Yii::t('creditCard', 'Fecha Operación'),
                    'value'=>'WebServiceClient::formatDate($data->FechaOperacion, "medium", true)',
                ),
                array(
                    'name'=>Yii::t('creditCard', 'Fecha Proceso'),
                    'value'=>'WebServiceClient::formatDate($data->FechaProceso, "medium", true)',
                ),
                'NroCupon:text:'.Yii::t('creditCard', 'Comprobante'),
                array(
                    'name'=>Yii::t('creditCard', 'Tipo Transacción'),
                    'value'=>'$data->TipoTransaccion === "C" ? "Confirmado" : "Pendiente"',
                ),
                'Descripcion:text:'.Yii::t('app', 'Descripción'),
                array(
                    'name'=>Yii::t('creditCard', 'Monto'),
                    'value'=>'Yii::app()->numberFormatter->formatDecimal($data->Importe)',
                    'htmlOptions'=>array(
                        'style'=>'text-align: right;'
                ),
                ),
            ),
        )); ?>
</div>
    </div>
</div>

<div class="panel widget">
    <div class="panel-body">
        <p class="text-muted"><?= isset($balance->Mensajeextracto) ? $balance->Mensajeextracto : ''?></p>
    </div>
</div>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $('#print').click(function(event) {
      var $html = $($('.contenedor').html());
      $html.find('#print').remove();
      html='<div class="contenedor"><div>'+$html.html()+'</div></div>';
      var WinPrint = window.open("", "", "left=0,top=0,width=1200,height=900,toolbar=0,scrollbars=0,status=0");
      WinPrint.document.write('<html><head>');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("app.css")?>" />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("backend.css")?>" />');
      //WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("voucher.css")?>"  />');
      WinPrint.document.write('</head><body>');
      WinPrint.document.write('<h4>Extracto de Tarjeta</h4>');
      WinPrint.document.write('<p><b>Nombre cliente: </b><?= $balance->Nombrecliente;?></p>');
      WinPrint.document.write('<p><b>Número de Tarjeta</b><?= '*****'.substr($balance->Nrotarjeta, -4);?></p>');
      WinPrint.document.write('<p><b>Datos al </b><?= WebServiceClient::formatDate($period->FechaCierre, "medium", true) ?></p>');
      WinPrint.document.write(html);
      WinPrint.document.write('<script>(function(){window.print()})();<\/script><\/body>');
      WinPrint.document.close();
      WinPrint.focus();
    });
  });
</script>
