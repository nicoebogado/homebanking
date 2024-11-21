<?php
//TODO sumatoria de subtotales por p�gina (transporte)
Yii::import('ext.pdf.EPDF_Extract_Mov');

class ExtractOnRequest_TAE extends EPDF_Extract_Mov
{
	/**
	 * @var array Para uso interno de contadores, banderas y corte de control
	 */
	private $_vars = array();
	public $tae = "";
	public $fechaReporte = "";
	public $textoCabecera = "";
	public $cateRiesgo = "";
	public $fechaFormat = "";

	public function init()
	{

		
		$this->fechaReporte = $this->datas['dataProvider']['cuentascliente']->rawData[0]->fechaextracto;
		$this->cateRiesgo = $this->datas['dataProvider']['cuentascliente']->rawData[0]->catriesgo;

		$header = $this->datas['header'];
		$footer =  $this->datas['footer']['mensaje'];

		$this->textoCabecera = "Cumplimos en Informarle que vuestra calificación de riesgo correspondiente al $this->fechaReporte es de $this->cateRiesgo conforme los criterios definidos por la Resolución Nro. 1, Acta N° 60 de fecha 28 de Setiembre de 2007, del Directorio del Banco Central del Paraguay. \nEste producto se encuentra cubierto por el Fondo de Garantía de Depósitos; asimismo, que las contribuciones obligatorias realizadas por las entidades de intermediación financiera, en virtud de lo dispuesto en el Articulo 3° Numeral 2 de la Ley Nro. 2334/2003, constituyen gastos propios de la gestión de la entidad, que no deben trasladarse directamente como tales a sus Clientes.";

		//test footer
		$this->config['footerRenderizer'] = utf8_decode($footer);

		if ($header) {
			$this->config['headerRenderizer'] = function ($pdfObj) use ($header) {

				$logo = Yii::getPathOfAlias('webroot.themes') . '/' . Yii::app()->theme->name . '/img/logo.png';
				$pdfObj->Image($logo, null, null, 50, 15);

				$pdfObj->SetFont('Arial', '', 7);
				$pdfObj->Cell(25, 5, 'Estado de Cta. al', 1, 0, 'C');
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 10);
				$pdfObj->Cell(136, 7, $header['nombrecliente'], 'LTR', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', '', 7);
				$pdfObj->Cell(25, 5, utf8_decode('Página'), 1, 1, 'C');
				//----------
				$pdfObj->Cell(25, 5, $this->fechaReporte, 1, 0, 'C');
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(136, 5, substr($header['direccioncliente'], 0, 90), 'LR', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(25, 5, $pdfObj->PageNo() . ' / {nb}', 1, 1, 'C');
				//----------
				$pdfObj->Cell(27, 5, '', 0, 0);
				$pdfObj->Cell(136, 5, utf8_decode('Teléfono: ') . isset($header['telefonocliente']) ? $header['telefonocliente'] : '', 'LR', 0);
				$pdfObj->Cell(27, 5, '', 0, 1);
				//----------
				$pdfObj->Cell(25, 5, 'A Requerimiento', 1, 0, 'C');
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(136, 5, 'Oficial de cuentas: ' . Yii::app()->user->getState('officerName'), 'LR', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(25, 5, 'Nro. de Cliente', 1, 1, 'C');
				//----------
				$pdfObj->Cell(25, 5, '', 1, 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(68, 5, utf8_decode('Teléfono: ') . Yii::app()->user->getState('officerPhone'), 'LB', 0);
				$pdfObj->Cell(68, 5, 'e-mail: ' . Yii::app()->user->getState('officerEmail'), 'RB', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(25, 5, $header['codigocliente'], 1, 1, 'C');
				//------------
				$pdfObj->Ln(3);

				/*$pdfObj->Cell(25, 5, 'TAE', 1, 0, 'C');
				$pdfObj->Cell(2, 5, '', 0, 0);
				
				$pdfObj->Cell(136, 5, '' . Yii::app()->user->getState('officerName'), 'LR', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(25, 5, 'TAN', 1, 1, 'C');
				//------------
				$pdfObj->Cell(25, 5, $this->$tae, 1, 0, 'C');
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(136, 5, '' . Yii::app()->user->getState('officerName'), 'LR', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(25, 5, $this->$tan, 1, 1, 'C');*/
				//Celda de texto explicativo
				$pdfObj->MultiCell(190, 5, utf8_decode($this->textoCabecera), '','J', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);				
				
			};

			$this->config['secondHeaderRenderizer'] = function ($pdfObj) use ($header) {
				$pdfObj->SetFont('Arial', '', 7);
				$pdfObj->Cell(25, 5, 'Estado de Cta. al', 1, 0, 'C');
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 10);
				$pdfObj->Cell(136, 7, $header['nombrecliente'], 'LTR', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', '', 7);
				$pdfObj->Cell(25, 5, utf8_decode('Página'), 1, 1, 'C');
				//----------
				$pdfObj->Cell(25, 5, $this->fechaReporte, 1, 0, 'C');
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(136, 5, substr($header['direccioncliente'], 0, 90), 'LRB', 0);
				$pdfObj->Cell(2, 5, '', 0, 0);

				$pdfObj->Cell(25, 5, $pdfObj->PageNo() . ' / {nb}', 1, 1, 'C');

				//-------Abajo celdas de test Added:27-04-2022
				$pdfObj->Ln(0.5);
				$pdfObj->SetFillColor(135, 127, 125);
				
				$pdfObj->SetTextColor(255);
				
				/*$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(21, 11, 'Nro de Cuenta', 0, 0, 'C','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);*/

				/*$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(21, 11, 'Mon', 0, 0, 'C','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);*/

				$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(19, 11, 'Fecha Conf.', 0, 0, 'L','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(19, 11, 'Fecha Tran.', 0, 0, 'L','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(26, 11, 'Nro Comprob.', 0, 0, 'L','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(56, 11, 'Concepto', 0, 0, 'L','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(20, 11, 'Importe Deb.', 0, 0, 'R','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(24, 11, 'Importe Cred.', 0, 0, 'R','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);

				$pdfObj->SetFont('Arial', 'b', 7.5);
				$pdfObj->Cell(25.5, 11, 'Saldo Actual', 1.5, 1, 'R','T');
				$pdfObj->Cell(0.1, 5, '', 0, 0);
				
			};

			
		}

		$tipo = array('PF', 'CDA', 'PR', 'TA', 'LS', 'TI');

		foreach ($this->datas['dataProvider'] as $k => $dp) {

			if (is_array($dp)){
				$datas[] = $dp;
				$datas[0]["title"] = utf8_decode($datas[0]["title"]);
			} else {

				$key = $k;
				if (in_array($k, $tipo)) {
					$key = 'resumenescuenta';
				}

				$datas[] = array(
					'title'			=> $this->_getTitle($k),
					'dataProvider'	=> $dp,
					'columns'		=> $this->_getColumns($key, $k),
					'colAligns'	=> $this->_getAligns($k),
				);
			}
		}

		$this->datas = $datas;

		parent::init();
	}

	protected function _getAligns($k)
	{
		$t = [
	  		'MOV'		=> array('C', 'C', 'R', 'L', 'R', 'R', 'R'),
			'cuentascliente'		=> array('L', 'C', 'C', 'C', 'C', 'C', 'C'),
			'detallesmovimiento'	=> array('L', 'L', 'L', 'L', 'R', 'R', 'R'),
			'PF'  => array('L', 'L', 'C', 'C', 'R', 'R', 'R'),
			'TA'  => array('L', 'L', 'C', 'C', 'R', 'R', 'R'),													];
    		if (array_key_exists($k, $t)) {
		    return $t[$k];
    		}else{
 			return array();
		}
	}

	protected function _getTitle($k)
	{


		return [
			'cuentascliente'		=> utf8_decode('Resumen de cuenta'),
			'detallesmovimiento'	=> utf8_decode('Detalles de los movimientos'),
			'PF'  => utf8_decode('Resumen de cuentas de tipo Plazo Fijo'),
			'CDA' => utf8_decode('Resumen de cuentas de tipo Certificado de depósitos de ahorro'),
			'PR'  => utf8_decode('Resumen de cuentas de tipo Préstamos'),
			'TA'  => utf8_decode('Resumen de cuentas de tipo Tarjetas de Crédito'),
			'LS'  => utf8_decode('Resumen de cuentas de tipo Línea de Sobregiro'),
			'TI'  => utf8_decode('Resumen de cuentas de tipo Títulos de Inversión'),
			'retencionescuenta' => utf8_decode('Depósitos a Confirmar'),
			'bloqueoscuenta'		=> utf8_decode('Bloqueos de cuentas'),
			'MOV'		=> utf8_decode('Movimientos'),
		][$k];
	}

	protected function _getColumns($k, $key)
	{

		switch ($key) {
			case 'TA':
				$titulo1 = utf8_decode(Yii::t('app', 'Pago Mínimo'));
				$titulo2 = Yii::t('app', 'Deuda Actual');
				$titulo3 = Yii::t('app', 'Disponible');
				$valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
				$valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
				$valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
				break;
			case 'PR':
				$titulo1 = utf8_decode(Yii::t('app', 'Monto Préstamo'));
				$titulo2 = Yii::t('app', 'Importe Cuota');
				$titulo3 = Yii::t('app', 'Saldo Actual');
				$valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
				$valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
				$valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
				break;
			case 'CDA':
				$titulo1 = Yii::t('app', 'Monto Capital');
				$titulo2 = Yii::t('app', utf8_decode('Monto Interés'));
				$titulo3 = Yii::t('app', utf8_decode('Importe Cupón'));
				$valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
				$valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
				$valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
				break;
			default:
				$titulo1 = Yii::t('app', 'Monto Capital');
				$titulo2 = utf8_decode(Yii::t('app', 'Monto Interés'));
				$titulo3 = Yii::t('app', 'Saldo Disponible');
				$valor1 = 'Yii::app()->numberFormatter->formatDecimal($data->montocapital)';
				$valor2 = 'Yii::app()->numberFormatter->formatDecimal($data->montointeres)';
				$valor3 = 'Yii::app()->numberFormatter->formatDecimal($data->saldodisponible)';
				break;
		}

		$columns = array(
			'cuentascliente' => array(
				'descripcion:text:' . utf8_decode(Yii::t('app', 'Descripción')),
				'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
				array(
					'header' => Yii::t('app', 'Saldo Anterior'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoanteriordisponible)',
				),
				array(
					'header' => Yii::t('app', 'Saldo Actual'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualcontable)',
				),
				//Comentado para mostrar solo reporte de movimiento por cuenta.
				/*array(
					'header' => Yii::t('app', 'Saldo Actual Bloqueado'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualbloqueado)',
				),
				array(
					'header' => Yii::t('app', 'Saldo Actual Retenido'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualretenido)',
				),
				array(
					'header' => Yii::t('app', 'Saldo Actual Disponible'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldoactualdisponible)',
				),*/
				array(
					'header' => Yii::t('app', 'Saldo Promedio'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldopromedio)',
				),

				array(
					'header' => Yii::t('app', 'TAE'),
					//'value' => 'Yii::app()->numberFormatter->formatDecimal($data->tasefectiva)',
					'value' => 'number_format($data->tasefectiva,2,",",".")',
				),

				array(
					'header' => Yii::t('app', 'TAN'),
					//'value' => 'Yii::app()->numberFormatter->formatDecimal($data->tasnominal)',
					'value' => 'number_format($data->tasnominal,2,",",".")',
				),
			),
			'detallesmovimiento' => array(
				/*'numerocuenta:text:' . Yii::t('app', 'Nro de Cuenta'),
				'codigomoneda:text:' . Yii::t('app', 'Mon'),*/
				'fechaconfirmacion:text:' . Yii::t('app', 'Fecha conf.'),
				'fechatransaccion:text:' . Yii::t('app', 'Fecha tran.'),
				'numerocomprobante:text:' . Yii::t('app', 'Nro Comprob.'),
				'concepto:text:' . Yii::t('app', 'Concepto'),
				array(
					'header' => utf8_decode(Yii::t('app', 'Importe Deb.')),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->montodebito)',
				),
				array(
					'header' => utf8_decode(Yii::t('app', 'Importe Cred.')),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->montocredito)',
				),
				array(
					'header' => Yii::t('app', 'Saldo Actual'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldo)',
				),
			),
			'MOV' => array(
				array(
					'header' => Yii::t('movements', 'Fecha movimiento'),
					'value' => 'WebServiceClient::formatDate($data->fecha)',
				),
				array(
					'header' => utf8_decode(Yii::t('movements', 'Fecha confirmación')),
					'value' => 'WebServiceClient::formatDate($data->fechavalor)',
				),
				'numerodocumento:text:' . Yii::t('movements', 'Documento'),
				'descripciontransaccionpadre:text:' . utf8_decode(Yii::t('commons', 'Descripción')),
				array(
					'header' => utf8_decode(Yii::t('commons', 'Débito')),
					'value' => '$data->tipomovimiento === "D" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
				),
				array(
					'header' => utf8_decode(Yii::t('commons', 'Crédito')),
					'value' => '$data->tipomovimiento === "C" ? Yii::app()->numberFormatter->formatDecimal($data->monto) : ""',
				),
				array(
					'header' => Yii::t('commons', 'Saldo'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldo)',
				),
			),
			'resumenescuenta' => array(
				'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
				'modalidad:text:' . Yii::t('app', 'Modalidad'),
				'codigomoneda:text:' . Yii::t('app', 'Moneda'),
				'fechavencimiento:text:' . Yii::t('app', 'Fecha de Vencimiento'),
				array(
					'header' => $titulo1,
					'value' => $valor1,
				),
				array(
					'header' => $titulo2,
					'value' => $valor2,
				),
				array(
					'header' => $titulo3,
					'value' => $valor3,
				),
			),
			'retencionescuenta' => array(
				'numerodocumento:text:' . Yii::t('app', 'Nro. de Documento'),
				'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
				'descripcion:text:' . utf8_decode(Yii::t('app', 'Descripción')),
				'fechaliberacion:text:' . utf8_decode(Yii::t('app', 'Fecha Liberación')),
				'fechamovimiento:text:' . Yii::t('app', 'Fecha Movimiento'),
				array(
					'header' => Yii::t('app', 'Monto'),
					'value' => 'Yii::app()->numberFormatter->formatDecimal($data->monto)',
				),
			),
			'bloqueoscuenta' => array(
				'numerocuenta:text:' . Yii::t('app', 'Cuenta'),
				'fechainicio:text:' . Yii::t('app', 'Fecha inicio'),
				'causabloqueo:text:' . Yii::t('app', 'Causa'),
				array(
					'header' => Yii::t('app', 'Monto'),
					'value' => '$data->codigomoneda." ".Yii::app()->numberFormatter->formatDecimal($data->monto)',
				),
			),
		);

		return $columns[$k];
	}
}
