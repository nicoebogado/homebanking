<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("voucher.css") ?>" />
<h3><?php echo Yii::t('', 'Confirmación'); ?></h3>
<?php $now = date('d/m/Y H:i:s'); ?>
<div class="contenedor">
  <?php if(empty($bandera)){ ?>
    <div>
    <?php echo HImage::html('logo.png', 'Logo', array(
      'class' => 'img-responsive',
      'width' => '200px',
    )) ?>
    <table>
      <tr>
        <td>Fecha de Pago:</td>
        <td><?php echo $now; ?></td>
      </tr>
      <tr>
        <td>Cuenta Débito:</td>
        <td>
          <?php echo ((Yii::app()->params['maskedAccountNumber'] == 'N') ?
            $debitAccount['accountNumber'] :
            $debitAccount['maskedAccountNumber']) . ' - ' . $debitAccount['denomination']
          ?>
        </td>
      </tr>
      <tr>
        <td>Beneficiario:</td>
        <td>
          <?php echo ((Yii::app()->params['maskedAccountNumber'] !== 'N' && isset($creditAccount['maskedAccountNumber'])) ?
            $creditAccount['maskedAccountNumber'] :
            $creditAccount['accountNumber']) . ' - ' . $creditAccount['denomination']
          ?>
        </td>
      </tr>
      <tr>
        <td>Monto</td>
        <td><?php echo $debitAccount['currency'] . ' ' . $operationAmount; ?></td>
      </tr>
    </table>
  </div>
  <button class="btn btn-link" data-toggle="tooltip" title="" id="print" type="button" data-original-title="Imprimir">
    <i class="icon-printer fa-2x"></i>
  </button>
</div>
<?php } ?>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $('#print').click(function(event) {
      var $html = $($('.contenedor').html());
      $html.find('#print').remove();
      html = '<div class="contenedor"><div>' + $html.html() + '</div></div>';
      var WinPrint = window.open("", "", "left=0,top=0,width=1024,height=900,toolbar=0,scrollbars=0,status=0");
      WinPrint.document.write('<html><head>');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("app.css") ?>" />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("backend.css") ?>" />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("voucher.css") ?>"  />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("utils/print.css") ?>"  />');
      WinPrint.document.write('</head><body>');
      WinPrint.document.write(html);
      WinPrint.document.write('<script>(function(){window.print()})();<\/script><\/body>');
      WinPrint.document.close();
      WinPrint.focus();
    });
  });
</script>