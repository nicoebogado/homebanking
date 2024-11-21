<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("voucher.css")?>"  />
<h3><?php echo Yii::t('', 'Confirmación'); ?></h3>
<?php $now=date('d/m/Y H:i:s'); ?>
<div class="contenedor">
  <div>
    <?php echo HImage::html('logo.png', 'Logo', array(
        'class' => 'img-responsive',
    ))?>
    <table>
      <tr>
        <td>Fecha de Pago:</td><td><?php echo $now; ?></td>
      </tr>
      <tr>
        <td>Cuenta</td><td><?php echo $debitAccount ?></td>
      </tr>
      <tr>
        <td>Denominación</td><td><?php echo $denomination ?></td>
      </tr>
      <tr>
        <td>Comprobante</td><td><?php echo $transactionId ?></td>
      </tr>
      <tr>
        <td>Servicio</td>
          <?php if ($type=='PT'): ?>
              <td>Pago de Préstamo - <?php echo $creditAccount ?></td>
          <?php endif; ?>
          <?php if ($type=='TJ'): ?>
              <td>Pago de Tarjetas - <?php echo $creditAccount ?></td>
          <?php endif; ?>
      </tr>
      <tr>
        <td>Monto</td><td><?php echo $currency.' '.($currency=='GS'?number_format($operationAmount, 0, ',', '.'):number_format($operationAmount, 2, ',', '.')); ?></td>
      </tr>
    </table>
  </div>
  <button class="btn btn-link" data-toggle="tooltip" title="" id="print" type="button" data-original-title="Imprimir">
    <i class="icon-printer fa-2x"></i>
  </button>
</div>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $('#print').click(function(event) {
      var $html = $($('.contenedor').html());
      $html.find('#print').remove();
      html='<div class="contenedor"><div>'+$html.html()+'</div></div>';
      var WinPrint = window.open("", "", "left=0,top=0,width=1024,height=900,toolbar=0,scrollbars=0,status=0");
      WinPrint.document.write('<html><head>');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("app.css")?>" />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("backend.css")?>" />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("voucher.css")?>"  />');
      WinPrint.document.write('</head><body>');
      WinPrint.document.write(html);
      WinPrint.document.write('<script>(function(){window.print()})();<\/script><\/body>');
      WinPrint.document.close();
      WinPrint.focus();
    });
  });
</script>