<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("sipap-voucher.css")?>"  />
<?php $this->pageTitle=Yii::app()->name . ' - ' . Yii::t('sipapTransfer', 'Comprobante de Transferencia Emitida'); ?>
<?php $initdata=Yii::app()->user->getState('initdata'); ?>
<div class="voucher">
	<div>
	<button class="btn btn-link" data-toggle="tooltip" title="" id="print" type="button" data-original-title="Imprimir">
		<i class="icon-printer fa-2x"></i>
	</button>
	<div class="head">
		<?php echo HImage::html('logo.png', 'Logo', array(
			'class' => 'img-responsive',
		))?>
	</div>
	<div class="holder">
		<p> <?php echo  $titulo != "" ? $titulo : "Transferencia enviada dentro del pa&iacute;s" ?> </p>
		<table>
			<tr>
				<td>
					Pte.Remitente:
				</td>
				<td>
					<?php echo $data->codigoswiftemisor.' - '.$data->entidademisor; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Yii::t('sipapTransfer','Remitente'); ?>:
				</td>
				<td>
					<?php 
						if($data->tipo == 'R'){
							echo $nombreremitente;
						}else if(isset($codigoempresa)){
							echo $codigoempresa." - ".$entityName;
						}else{
							echo $initdata->codigocliente." ".$initdata->nombrecompleto;
						}
						 
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Yii::t('sipapTransfer','Documento'); ?>:
				</td>
				<td>
					<?php echo $data->documentosolicitante; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Yii::t('commons','Fecha'); ?>:
				</td>
				<td>
					<?php echo WebServiceClient::formatDate($data->fechavalor); ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="charges">
		<table class="items table table-condensed">
			<thead>
				<tr>
					<th>
						<?php echo Yii::t('sipapTransfer','Descripción'); ?>
					</th>
					<th>
						<?php echo Yii::t('commons','Cuenta'); ?>
					</th>
					<th>
						<?php echo Yii::t('commons','Débito'); ?>
					</th>
					<th>
						<?php echo Yii::t('commons','Crédito'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						DEBITO EN CUENTA
					</td>
					<td>
						<?php echo $data->numerocuentadebito." ".$data->moneda ; ?>
					</td>
					<td>
						<?php echo Yii::app()->numberFormatter->formatDecimal($data->monto) ; ?>
					</td>
					<td>
						0
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td>
						Total:
					</td>
					<td></td>
					<td>
						<?php echo Yii::app()->numberFormatter->formatDecimal($data->monto) ; ?>
					</td>
					<td>
						0
					</td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td>
							<?php echo Yii::t('commons','Importe Neto'); ?>:
					</td>
					<td>
						<?php echo Yii::app()->numberFormatter->formatDecimal($data->monto) ; ?>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						Son : <?php echo $data->montotexto ; ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="details">
		<table>
			<tr>
				<td>
					Pte. Beneficiario:
				</td>
				<td>
					<?php echo $data->codigoswift.' - '.$data->entidad ; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Yii::t('transfers','Beneficiario'); ?>:
				</td>
				<td>
					<?php echo $data->nombrebeneficiario ; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Yii::t('sipapTransfer','Documento'); ?>:
				</td>
				<td>
					<?php echo $data->documentobeneficiario; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Yii::t('sipapTransfer','Nro Cta Beneficiario'); ?>:
				</td>
				<td>
					<?php echo $data->numerocuentacredito; ?>
				</td>
			</tr>

		</table>
	</div></div>
</div>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $('#print').click(function(event) {
			var $html = $($('.voucher').html());
			$html.find('#print').remove();
			html='<div class="voucher">'+$html.html()+'</div>';
		  var WinPrint = window.open("", "", "left=0,top=0,width=1024,height=900,toolbar=0,scrollbars=0,status=0");
      WinPrint.document.write('<html><head>');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("app.css")?>" />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("backend.css")?>" />');
      WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("sipap-voucher.css")?>"  />');
      WinPrint.document.write('</head><body>');
      WinPrint.document.write(html);
      WinPrint.document.write('<script>(function(){window.print()})();<\/script><\/body>');
      WinPrint.document.close();
      WinPrint.focus();
    });
  });
</script>
