<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('invoicesList', 'Lista de Facturas');
?>

<?php if ($mode === 'list') : ?>
	<h3>
		<?php echo Yii::t('invoicesList', 'Lista de Facturas'); ?>
		<small><?php echo Yii::t('invoicesList', 'Seleccione la factura que desea consultar') ?></small>
	</h3>

	<div class="panel panel-default">
		<div class="panel-body">
			<?php $this->widget('booster.widgets.TbGridView', array(
				'type' => 'striped condensed',
				'dataProvider' => $dataProvider,
				'enableSorting' => false,
				'template' => '{items}{pager}',
				'selectableRows' => 0,
				'columns' => array(
					'numerofactura:text:' . Yii::t('invoicesList', 'Nro. de Factura'),
					array(
						'name' => Yii::t('commons', 'Fecha'),
						'value' => 'WebServiceClient::formatDate($data->fecha)',
					),
					'tipo:text:' . Yii::t('commons', 'Tipo'),
					'codigomoneda:text:' . Yii::t('commons', 'Moneda'),
					array(
						'name' => Yii::t('commons', 'Monto'),
						'value' => 'Yii::app()->numberFormatter->formatDecimal($data->totalfactura)',
					),
					array(
						'name' => Yii::t('commons', 'IVA'),
						'value' => 'Yii::app()->numberFormatter->formatDecimal($data->totaliva)',
					),
					array(
						'class' => 'booster.widgets.TbButtonColumn',
						'template' => '{pay}',
						'header' => Yii::t('commons', 'Opciones'),
						'buttons' => array(
							'pay' => array(
								'label' => Yii::t('commons', 'Ver Detalles'),
								'url' => 'Yii::app()->createUrl("/report/invoice", array("id"=>$data->numerofactura,"fecha"=>$data->fecha))',
								'icon' => 'fa fa-list-ul',
							),
						),
					),
				),
			)); ?>
		</div>
	</div>
<?php elseif ($mode === 'details') : ?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#print-invoice-button').click(function(event) {
				var $html = $($('.contenedor').html());
				// $html.find('#print').remove();
				html = '<div class="contenedor"><div>' + $html.html() + '</div></div>';
				var WinPrint = window.open("", "", "left=0,top=0,width=1024,height=900,toolbar=0,scrollbars=0,status=0");
				WinPrint.document.write('<html><head>');
				WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("app.css") ?>" />');
				WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("backend.css") ?>" />');
				WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("invoice.css") ?>"  />');
				WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("utils/print.css") ?>"  />');

				WinPrint.document.write('</head><body>');
				WinPrint.document.write(html);
				WinPrint.document.write('<script>(function(){window.print()})();<\/script><\/body>');
				WinPrint.document.close();
				WinPrint.focus();
			});
		});
	</script>
	<?php HCss::register('invoice.css'); ?>
	<h3>
		<?php echo Yii::t('invoicesList', 'Detalles de la Factura'); ?>
		<?php $this->widget('booster.widgets.TbButton', array(
			'id' => 'print-invoice-button',
			'icon' => 'icon-printer fa-2x',
			'context' => 'link',
			'htmlOptions' => array(
				'class' => 'pull-right',
				'data-toggle' => 'tooltip',
				'title' => 'Imprimir',
			),
		)); ?>
	</h3>

	<div id="invoice-table" class="contenedor">
		<div class="row invoice-container">
			<div class="col-xs-12 invoice-header">
				<div class="row">
					<div class="col-xs-8">
						<div style="width:30%; display:inline-grid">
							<?php echo HImage::html('logo.png', 'Logo', array(
								'style' => 'width: 100%;',
							)) ?>
						</div>
						<div style="display: inline-block; text-align:center; width:69%;">
							<?php echo $kude ?><br />
							<?php echo $invoice->direccionmatriz ?><br />
							<?php echo $invoice->direccionoficina ?><br />
							Teléfono:<?php echo $invoice->telefono ?><br />
							ASUNCION, PARAGUAY<br />
							<b>OTROS TIPOS DE INTERMEDIACION MONETARIA</b>
						</div>
					</div>
					<div class="col-xs-4">
						<div>
							Timbrado No. <?php echo $invoice->numerotimbrado ?><br />
							Fecha de Inicio de Vigencia: 26-12-2023<br /> 
							<?php echo $invoice->numerorucentidad ?><br />
							<b><?php echo $invoice->tipo." Electr&oacute;nica"; ?></b><br />
							<?php echo $invoice->numerofactura ?>
						</div>
						<!--<div class="pull-right">
							<?php echo $invoice->tipoimpresion ?>
						</div>-->
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						Fecha de emisión: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $invoice->fecha ?> <br />
						Nombre o Razón Social: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $invoice->nombrecliente ?>
					</div>


					<div class="col-xs-6">
						Condición de venta:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contado
						<input type="checkbox" <?php
												if ($invoice->tipoventa === 'CO') echo 'checked="checked"';
												?>>
						Crédito
						<input type="checkbox" <?php
												if ($invoice->tipoventa === 'CR') echo 'checked="checked"';
												?>> <br />
						Moneda: <?php echo $invoice->codigomoneda == 'GS'? 'Guaranies' : 'Dolares' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;R.U.C.:&nbsp;&nbsp;&nbsp;<?php echo $invoice->numeroruccliente ?>
					<br> Tipo de Operaci&oacute;n: Prestaci&oacute;n de servicios.
					</div>

				</div>
				<div class="row">
					<div class="col-xs-9">
						Nro de Cliente:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $invoice->numerocliente ?><br />
						Dirección:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo isset($invoice->direccioncliente) ? $invoice->direccioncliente : '' ?> <br />
						Periodo de Facturación:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $invoice->periodo ?>
					</div>
				</div>
			</div>
			<div class="col-xs-12 invoice-body">
				<div class="row">
					<div class="col-xs-1 text-center">
						<b>Cant.</b>
					</div>
					<div class="col-xs-5 text-center">
						<b>Descripción</b>
					</div>
					<div class="col-xs-2 text-center">
						<b>P. unit.</b>
					</div>
					<div class="col-xs-4">
						<div class="row">
							<div class="col-xs-12 text-center">
								<b>Valor de venta</b>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-4 text-center">
								<b>Exentas</b>
							</div>
							<div class="col-xs-4 text-center">
								<b>5%</b>
							</div>
							<div class="col-xs-4 text-center">
								<b>10%</b>
							</div>
						</div>
					</div>
				</div>
				<?php $c = 0; ?>
				<?php foreach ($invoice->listadetallesfacturas->array as $row) : ?>
					<div class="row">
						<div class="col-xs-1"><?php echo $row->cantidad ?></div>
						<div class="col-xs-5"><?php echo $row->descripcion ?></div>
						<div class="col-xs-2"><?php echo Yii::app()->numberFormatter->formatDecimal($row->preciounitario) ?></div>

						<div class="col-xs-4">
							<div class="row">
								<div class="col-xs-4">
									<?php echo Yii::app()->numberFormatter->formatDecimal($row->montoexento) ?>
								</div>
								<div class="col-xs-4">
									<?php echo Yii::app()->numberFormatter->formatDecimal($row->gravado5) ?>
								</div>
								<div class="col-xs-4">
									<?php echo Yii::app()->numberFormatter->formatDecimal($row->gravado10) ?>
								</div>
							</div>
						</div>
					</div>

					<?php $c++; ?>
				<?php endforeach ?>
				<?php if ($c < 10) : ?>
					<div class="row">
						<div class="col-xs-1" style="height:<?php echo 300 - ($c * 30) ?>px"></div>
						<div class="col-xs-5" style="height:<?php echo 300 - ($c * 30) ?>px"></div>
						<div class="col-xs-2" style="height:<?php echo 300 - ($c * 30) ?>px"></div>
						<div class="col-xs-4" style="height:<?php echo 300 - ($c * 30) ?>px">
							<div class="row">
								<div class="col-xs-4" style="height:<?php echo 300 - ($c * 30) ?>px"></div>
								<div class="col-xs-4" style="height:<?php echo 300 - ($c * 30) ?>px"></div>
								<div class="col-xs-4" style="height:<?php echo 300 - ($c * 30) ?>px"></div>
							</div>
						</div>
					</div>
				<?php endif ?>
				<div class="row">
					<div class="col-xs-8">
						Sub totales
					</div>
					<div class="col-xs-4">
						<div class="row">
							<div class="col-xs-4">
								<?php echo Yii::app()->numberFormatter->formatDecimal($invoice->totalexento) ?>
							</div>
							<div class="col-xs-4">
								<?php echo Yii::app()->numberFormatter->formatDecimal($invoice->gravado5) ?>
							</div>
							<div class="col-xs-4">
								<?php echo Yii::app()->numberFormatter->formatDecimal($invoice->gravado10) ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 invoice-footer">
				<div class="row">
					<div class="col-xs-9">
						<?php echo isset($invoice->descuentototalventa) ? $invoice->descuentototalventa : '' ?>
					</div>
					<div class="col-xs-3">
						<?php echo $invoice->codigomoneda . " " . Yii::app()->numberFormatter->formatDecimal($invoice->totalventa) ?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-3">
						Liquidación del IVA:
					</div>
					<div class="col-xs-3">
						5%: <?php echo Yii::app()->numberFormatter->formatDecimal($invoice->impuesto5) ?>
					</div>
					<div class="col-xs-3">
						10%: <?php echo Yii::app()->numberFormatter->formatDecimal($invoice->impuesto10) ?>
					</div>
					<div class="col-xs-3">
						Total: <?php echo Yii::app()->numberFormatter->formatDecimal($invoice->totaliva) ?>
					</div>
				</div>
			</div>
			<?php if($kude != ''){ ?>
				
				<div class="col-xs-3" id="wcontenedorqr" style='padding:10px;'>
						<img src="<?php echo Yii::app()->baseUrl."/img/facturas/".$cdc.'.png' ?>" width='200' height='200'>
				</div>
				
				
				<div class="col-xs-9" style="height: 200px; padding-top:50px;">
					<label>Consulte la validez de este Documento Electrónico de CDC impreso abajo en: </label>
					</br>
					<a href="https://ekuatia.set.gov.py/consultas/" target='_blank'>https://ekuatia.set.gov.py/consultas/</a>
					</br>
					<label><?php echo $cdc; ?></label>
					</br>
					<label>ESTE DOCUMENTO ES UNA REPRESENTACIÓN GRÁFICA DE UN DOCUMENTO ELECTRÓNICO(XML)</label>
					<label>Si su documento electrónico presenta algún error, podrá solicitar la modificación dentro de las 72 horas siguientes de la emisión de este comprobante.</label>
					
				</div>
					
				</div>
				
				
			</div>
			<?php } ?>
		</div>
	</div>
<?php endif ?>