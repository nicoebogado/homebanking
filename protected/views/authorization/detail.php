<?php
HScript::register([
	'libs/emn178/md5.min',
	'libs/emn178/sha256.min',
	'plugins/jquery.secureKeypad.min',
	'commons/securizeKeypad',
]);
$this->pageTitle = Yii::app()->name . ' -' . Yii::t('authorization', 'Detalle de la Operación');

?>



<div class="panel panel-default">
	<div class="panel-body">
		<h2><?php echo $details->descripcionmensaje ?></h2>
		<hr>

		<table id="table1" class="table table-condensed">
			<tr>
				<td><?php echo Yii::t('authorization', 'Nro. Operación') ?></td>
				<td><?php echo $details->numerocomprobante ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td><?php echo Yii::t('commons', 'Fecha') ?></td>
				<td><?php echo $details->fechaoperacion ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td><?php echo Yii::t('commons', 'Cuenta') ?></td>
				<td><?php echo $details->cuentaprincipal ?></td>
				<td><?php echo Yii::t('commons', 'Tipo') ?></td>
				<td><?php echo $details->tipocuenta ?></td>
			</tr>
			<tr>
				<td><?php echo Yii::t('commons', 'Moneda') ?></td>
				<td><?php echo $details->monedaprincipal ?></td>
				<td><?php echo Yii::t('authorization', 'Oficina') ?></td>
				<td><?php echo $details->oficinaprincipal ?></td>
			</tr>
		</table>

		<hr>

		<table id="table2" class="table table-condensed">
			<tr>
				<td><?php echo Yii::t('commons', 'Monto') ?></td>
				<td><?php echo $details->codigomonedaprincipal . ' ' .
						Yii::app()->numberFormatter->formatDecimal($details->montoprincipal) ?></td>
			</tr>
			<?php if ($authType != 'snp') : ?>
				<tr>
					<td><?php echo Yii::t('authorization', 'Cargo') ?></td>
					<td><?php echo $details->montocargo ?></td>
				</tr>
				<tr>
					<td><?php echo Yii::t('commons', 'IVA') ?></td>
					<td><?php echo $details->montoiva ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($authType === 'salary') : ?>
				<tr>
					<td><?php echo Yii::t('authorization', 'Tipo de Ente') ?></td>
					<td><?php echo $details->tipoentesalario ?></td>
				</tr>
			<?php endif ?>
			<tr>
				<td><?php echo Yii::t('commons', 'Concepto') ?></td>
				<td><?php echo $details->descripcionmovimiento ?></td>
			</tr>
			<?php if (isset($details->cantidad)) : ?>
				<tr>
					<td><?php echo Yii::t('commons', 'Cantidad') ?></td>
					<td><?php echo $details->cantidad; ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td><?php echo $details->etiqueta ?></td>
				<td><?php echo (!isset($details->dato))?$details->dato2:$details->dato ?></td>
			</tr>
			<?php if (isset($details->etiqueta2) && isset($details->dato2)): ?>
				<tr>
					<td><?php echo $details->etiqueta2 ?></td>
					<td><?php echo $details->dato2 ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td><?php echo Yii::t('authorization', 'Ingresado por') ?></td>
				<td><?php echo $details->nombreingreso ?></td>
			</tr>
			<tr>
				<td><?php echo Yii::t('authorization', 'Autorizaciones Requeridas') ?></td>
				<td><?php echo $details->autorizacionesrequeridas ?></td>
			</tr>
			<?php if (isset($details->cantidadautorizadores)) : ?>
				<tr>
					<td><?php echo Yii::t('authorization', 'Autorizaciones Realizadas') ?></td>
					<td><?php echo $details->cantidadautorizadores ?></td>
				</tr>
			<?php endif ?>
			<tr>
				<td><?php echo Yii::t('authorization', 'Autorizaciones Faltantes') ?></td>
				<td><?php echo $details->autorizacionesfaltantes ?></td>
			</tr>
		</table>
		<?php
		if ($authType === 'salary') {
			echo "
				<hr>
				<h3>" . Yii::t('authorization', 'Detalles de Créditos') . "</h3>
				<hr>";
			$this->widget('booster.widgets.TbGridView', array(
				'type' => 'striped condensed',
				'dataProvider' => $dataProvider,
				'enableSorting' => false,
				'template' => '{items}{pager}',
				'selectableRows' => 0,
				'columns' => array(
					'numerocuenta:text:' . Yii::t('commons', 'Cuenta'),
					'nombrecuenta:text:' . Yii::t('authorization', 'Nombre de Cuenta'),
					'codigomoneda:text:' . Yii::t('commons', 'Moneda'),
					array(
						'name' => Yii::t('commons', 'Monto'),
						'value' => 'Yii::app()->numberFormatter->formatDecimal($data->monto)',
					),
				)
			));
		} elseif ($authType === 'supplier') {
			echo "<hr>
				<style>
				table {
						display: block !important;
						overflow-x: auto !important;
						white-space: nowrap !important;
					}	
				</style>
				<h3>" . Yii::t('authorization', 'Detalles de Créditos') . "</h3>
				<hr>";
			$this->widget('booster.widgets.TbGridView', array(
				'type' => 'striped condensed',
				'dataProvider' => $dataProvider,
				'enableSorting' => false,
				'template' => '{items}{pager}',
				'selectableRows' => 0,
				'columns'=>array(
					'numeroorden:text:'.Yii::t('supplierPayment', 'Número Orden'),
					'numerofactura:text:'.Yii::t('supplierPayment', 'Número Facturaa'),
					'formapago:text:'.Yii::t('commons', 'Tipo de Pago'),
					'numerocuentacredito:text:'.Yii::t('commons', 'Cuenta Beneficiario'),
					'montocredito:text:'.Yii::t('commons', 'Monto'),
					'nombrebeneficiario:text:'.Yii::t('commons', 'Beneficiario'),
					'codigomoneda:text:'.Yii::t('commons', 'Moneda'),
					'referenciafacturas:text:'.Yii::t('commons', 'Ref Facturas'),
					'tipodocumento:text:'.Yii::t('commons', 'Tipo Documento'),
					'descripcionbanco:text:'.Yii::t('commons', 'Entidad Beneficiaria'),
					'numerodocumento:text:'.Yii::t('commons', 'Documento beneficiario'),
					'numerocuentabeneficiario:text:'.Yii::t('commons', 'Cuenta Crédito SIPAP'),
					'tipomt:text:'.Yii::t('commons', 'Indice Valor'),
					
			
				),
			));
		}
		?>
	</div>
</div>

<div>
	<?php if ($type === 1) : ?>
		<?php echo Yii::t('authorization', '¿Desea consultar otro comprobante?') ?>
		<?php $this->widget(
			'booster.widgets.TbButtonGroup',
			array(
				'buttonType' => 'link',
				'buttons' => array(
					array('label' => Yii::t('commons', 'Si'), 'url' => array('listWithDetail')),
					array('label' => Yii::t('commons', 'No'), 'url' => array('/')),
				),
			)
		); ?>
	<?php elseif ($type === 2) : ?>
		<div class="form">
			<!--<?php echo $this->renderPartial('/commons/_form', array('form' => $form)); ?> Acá pedia pin transaccional  -->
				
				<div class="panel panel-default">
					<div class="panel-body">
						<form class="form-horizontal" id="yw1" action="<?php echo Yii::app()->createUrl("authorization/salaryDetailWithActions/$details->numerocomprobante"); ?>" method="post">
							<input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
							<div style="display:none">
								<input type="hidden" value="1" name="yform_397432f7" id="yform_397432f7">
							</div>
							<input name="AuthorizationConfirmForm[isToken]" id="TransferForm_isToken" type="hidden" value="true">
							<div class="form-actions">
							<div class="col-xs-2">
							<input name="tokenF" id="tokenF" type="number" class="form-control" placeholder="Ingrese clave del token" value="" required>
							</div>
							<button name="validaToken" class="btn btn-primary" id="yw22" type="button">Confirmar Operación</button>
							<button name="cancel" class="btn btn-danger" onclick="rechazar()" id="rechazarOpe" type="button">Rechazar Operación</button>
							<p id="errortoken" style="color:red;" hidden>Error al validar token f&iacute;sico</p>
							<p>Ingrese la clave que aparece en su token</p>
							</div>
						</form>

					</div>
				</div>
				
		</div>
	<?php elseif ($type === 3) : ?>
		<div class="center-block">
			<?php echo Yii::t('authorization', 'Esta operación ha sido autorizada. Falta(n) {autorizacionesfaltantes} firma(s) para su confirmación.', array(
				'{autorizacionesfaltantes}' => $details->autorizacionesfaltantes
			)) ?>

		</div>
		<div class="center-block">
			<?php echo Yii::t('authorization', '¿Desea autorizar otro comprobante?') ?>
			<?php $this->widget(
				'booster.widgets.TbButtonGroup',
				array(
					'buttonType' => 'link',
					'buttons' => array(
						array('label' => Yii::t('commons', 'Si'), 'url' => array('listWithAction')),
						array('label' => Yii::t('commons', 'No'), 'url' => array('/')),
					),
				)
			); ?>
		</div>
		<?php if ($authType != 'snp') : ?>
			<?php if ($details->autorizacionesfaltantes == 0) : ?>
				<div class="center-block">
					<a class="btn btn-success" id="printVoucher" href="javascript:void(0);">Imprimir</a>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php elseif ($type === 4) : ?>
		<div class="center-block">
			<?php echo Yii::t('authorization', 'Esta operación ha sido rechazada') ?>
		</div>
		<div class="center-block">
			<?php echo Yii::t('authorization', '¿Desea verificar otro comprobante?') ?>
			<?php $this->widget(
				'booster.widgets.TbButtonGroup',
				array(
					'buttonType' => 'link',
					'buttons' => array(
						array('label' => 'Si', 'url' => array('listWithAction')),
						array('label' => 'No', 'url' => array('/')),
					),
				)
			); ?>
		</div>
	<?php endif; ?>
</div>
<script id="script" type="text/javascript">
	jQuery(document).ready(function($) {
		$('#printVoucher').click(function(event) {
			//var $html='<div class="content-wrapper">'+$('.content-wrapper').html()+'</div>';
			var html = '<div class="content-wrapper"><div class="panel panel-default"><div class="panel-body">';
			html = html + '<h2>' + $('.content-wrapper h2:nth-child(1)').html() + '</h2><hr>';
			html = html + '<table class="table table-condensed">' + $('#table1').html() + '</table><hr>';
			html = html + '<table class="table table-condensed">' + $('#table2').html() + '</table><hr>';
			html = html + '</div></div></div>';
			var WinPrint = window.open("", "", "left=0,top=0,width=1024,height=900,toolbar=0,scrollbars=0,status=0");
			WinPrint.document.write('<html><head>');
			WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("app.css") ?>" />');
			WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?php echo HCss::url("backend.css") ?>" />');
			WinPrint.document.write('</head><body>');
			WinPrint.document.write(html);
			WinPrint.document.write('<script>(function(){window.print()})();<\/script><\/body>');
			WinPrint.document.close();
			WinPrint.focus();
		});
		
		//Validate hard token button confirm
		$("#yw22").click(function(){

            $("#yw22").attr("disabled", true);

            var btn = document.getElementById("yw22");
            btn.innerHTML = 'Verificando...';

            var tokenVar = document.getElementById("tokenF").value;
            var errortoken = document.getElementById("errortoken");

            $.post("<?php echo Yii::app()->createUrl('transfer/confirm'); ?>",
            {
                YII_CSRF_TOKEN: "<?php echo Yii::app()->request->csrfToken; ?>",
                tokenF: tokenVar
            },
            function(data, status){
                $("#yw22").attr("disabled", false);
                btn.innerHTML = 'Confirmar Transferencia';
                console.log(data);
                console.log(status);
                if(status == 'success'){
                    if(data == 801){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else if(data == '0'){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else if(data == 0){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else{
                        console.log('opt invalid');
                        errortoken.style.display = "block";
                    }
                }
            });
        });
		
		//enter
		$('form input').keydown(function (e) {
			if (e.keyCode == 13) {
				e.preventDefault();
				return false;
			}
		});

		
	});

	function rechazar(){
		jQuery(document).ready(function($){
			$("#rechazarOpe").attr("disabled", true);

		var btn = document.getElementById("rechazarOpe");
		btn.innerHTML = 'Verificando...';

		var tokenVar = document.getElementById("tokenF").value;
		var errortoken = document.getElementById("errortoken");

		$.post("<?php echo Yii::app()->createUrl('transfer/confirm'); ?>",
		{
			YII_CSRF_TOKEN: "<?php echo Yii::app()->request->csrfToken; ?>",
			tokenF: tokenVar
		},
		function(data, status){
			$("#rechazarOpe").attr("disabled", false);
				btn.innerHTML = 'Rechazar Operación';
				console.log(data);
				console.log(status);
				if(status == 'success'){
					if(data == 801){
						console.log('opt valid');
						btn.innerHTML = 'Enviando datos...';
						$("form#yw1").attr('action', '<?php echo Yii::app()->createUrl("authorization/decline/$details->numerocomprobante"); ?>');
						$('form#yw1').submit();
					}else if(data == '0'){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else if(data == 0){
                        console.log('opt valid');
                        btn.innerHTML = 'Enviando datos...';
                        $('form#yw1').submit();
                    }else{
						console.log('opt invalid');
                        errortoken.style.display = "block";
					}
				}
			});
		});
	}
</script>