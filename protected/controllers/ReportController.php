<?php
Yii::import('booster.widgets.TbForm');
include(Yii::app()->getBasePath().'/extensions/phpqrcode/qrlib.php'); 

class ReportController extends Controller
{
	// cambia una fecha tipo dd-mm-yyyy a mm-dd-yyyy
	public function formatoFecha($date)
	{
		list($d, $m, $y) = explode('-', $date);
		return $m . '-' . $d . '-' . $y;
	}

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array(
				'allow',
				'users' => array('@'),
			),
			array(
				'deny',  // deny all users
				'users' => array('*'),
			),
		);
	}


	public function actionExchanges()
	{
		$details = $this->wsClient->getExchanges();
		$dataProvider = new CArrayDataProvider(
			isset($details->listadetallecotizacion->array) ? $details->listadetallecotizacion->array : array(),
			array(
				'keyField' => 'moneda',
				'pagination' => array(
					'pageSize' => 10,
				),
			)
		);

		$this->render('exchanges', array('dataProvider' => $dataProvider));
	}

	/**
	 * Account details action
	 *
	 */
	public function actionAccountDetails($id = null)
	{
		$model = new AccountDetailsForm;
		if (isset($id) || Yii::app()->request->isPostRequest) {
			if (empty($id) && isset($_POST['AccountDetailsForm']['accountNumber'])) {
				$id =  $_POST['AccountDetailsForm']['accountNumber'];
			}
			$model->accountNumber = $id;

			if ($model->validate()) {
				$account = $this->_accountData($id);

				$details = $this->wsClient->getAccountDetails(array(
					'accountNumber' => $account['accountNumber'],
				));
				if (!((int) $details->cantidadcuentas > 0))
					throw new CHttpException(404, Yii::t('accountDetails', 'No se encontraron detalles'));

				/*$labels = $this->wsClient->getAccDetailLabel(array(
					'accountTypeCode' => $account['accountType'],
					'lang' => Yii::app()->language,
				));*/

				// organizar datos para la plantilla
				$details = $details->listacuentas->array[0];
				if ($details->tipocuenta === 'AH') {
					$datas = array(
						'accountDenomination' => $details->denominacion,
						'accountNumber' => $details->numerocuenta,
						'maskedAccountNumber' => $account['maskedAccountNumber'],
						'header' => array(
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Saldo Actual'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto1),
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Saldo Disponible'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto2),
							),
							array(
								'icon' => 'circle-o',
								'label' => Yii::t('accountDetails', 'Tasa de Interés'),
								'data' => isset($details->tasa) ? (($details->codigomoneda != 'GS') ? number_format($details->tasa, 2) : $details->tasa) . ' %' : Yii::t('commons', 'No asignado'),
							),
						),
						'panel' => array(
							'accountDesc' => $details->descripciontipocuenta,
							'accountNumber' => $details->numerocuenta,
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Fecha de Apertura'),
								'data' => WebServiceClient::formatDate($details->fechainicio, 'long'),
							),
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Fecha de Vencimiento'),
								'data' => WebServiceClient::formatDate($details->fechavencimiento, 'long'),
							),
							array(
								'icon' => 'usd',
								'label' => Yii::t('commons', 'Moneda'),
								'data' => $details->codigomoneda,
							),

							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Saldo Retenido'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto3),
							),

							array(
								'icon' => 'reply',
								'label' => Yii::t('accountDetails', 'Cheque Devuelto'),
								'data' => $details->chequedevuelto == 'N' ? 'No' : 'Si',
							),
							null,
						),
					);
				} elseif ($details->tipocuenta === 'AP') {
					$datas = array(
						'accountDenomination' => $details->denominacion,
						'accountNumber' => $details->numerocuenta,
						'maskedAccountNumber' => $account['maskedAccountNumber'],
						'header' => array(
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Saldo Actual'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto1),
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Interes Pendiente'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto2),
							),
							array(
								'icon' => 'circle-o',
								'label' => Yii::t('accountDetails', 'Tasa de Interés'),
								'data' => isset($details->tasa) ? (($details->codigomoneda != 'GS') ? number_format($details->tasa, 2) : $details->tasa) . ' %' : Yii::t('commons', 'No asignado'),
							),
						),
						'panel' => array(
							'accountDesc' => $details->descripciontipocuenta,
							'accountNumber' => $details->numerocuenta,
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Fecha de Apertura'),
								'data' => WebServiceClient::formatDate($details->fechainicio, 'long'),
							),
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Fecha de Vencimiento'),
								'data' => WebServiceClient::formatDate($details->fechavencimiento, 'long'),
							),
							array(
								'icon' => 'usd',
								'label' => Yii::t('commons', 'Moneda'),
								'data' => $details->codigomoneda,
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Cuenta de Credito'),
								'data' => $details->datoadicional,
							),

							array(
								'icon' => 'reply',
								'label' => Yii::t('accountDetails', 'Forma de Pago'),
								'data' => $details->pagominimo,
							),
							null,

						),
					);
				} elseif ($details->tipocuenta === 'PT') {
					$datas = array(
						'accountDenomination' => $details->denominacion,
						'accountNumber' => $details->numerocuenta,
						'maskedAccountNumber' => $details->numerocuenta, // no enmascarar el nro de prestamo
						'header' => array(
							array(
								'icon' => 'circle-o',
								'label' => Yii::t('accountDetails', 'Tasa de Interés'),
								'data' => isset($details->tasa) ? $details->tasa . '%' : Yii::t('commons', 'No asignado'),
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Saldo Capital'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto2),
							),
							array(
								'icon' => 'sort-numeric-asc',
								'label' => Yii::t('accountDetails', 'Número de Cuota Pendiente'),
								'data' => $details->monto4,
							),
						),
						'panel' => array(
							'accountDesc' => $details->descripciontipocuenta,
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Fecha de Inicio'),
								'data' => WebServiceClient::formatDate($details->fechainicio, 'long'),
							),
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Fecha de Vencimiento'),
								'data' => WebServiceClient::formatDate($details->fechavencimiento, 'long'),
							),
							array(
								'icon' => 'usd',
								'label' => Yii::t('commons', 'Moneda'),
								'data' => $details->codigomoneda,
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Monto del Préstamo'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto1),
							),
							array(
								'icon' => 'circle',
								'label' => Yii::t('accountDetails', 'Mora (días)'),
								'data' => $details->diasmora,
							),
							//array(
							//	'icon' => 'money',
							//	'label'=>Yii::t('accountDetails', 'Saldo Interés'),
							//	'data'=>$details->codigomoneda.' '.Yii::app()->numberFormatter->formatDecimal($details->monto3),
							//),
						),
					);
				} elseif ($details->tipocuenta === 'TJ') {
					$datas = array(
						'accountDenomination' => $details->denominacion,
						'accountNumber' => $details->numerocuenta,
						'maskedCreditCardNumber' => $account['maskedCreditCardNumber'],
						'header' => array(
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Línea de Crédito'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto4),
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Saldo Disponible de la Cuenta'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto1),
							),
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Vencimiento de Pago Mínimo'),
								'data' => WebServiceClient::formatDate($details->fechavencimiento, 'long'),
							),
						),
						'panel' => array(
							'accountDesc' => $details->descripciontipocuenta,
							array(
								'icon' => 'calendar',
								'label' => Yii::t('accountDetails', 'Fecha de Apertura'),
								'data' => WebServiceClient::formatDate($details->fechainicio, 'long'),
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Deuda Acumulada'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->monto2),
							),
							array(
								'icon' => 'circle-o',
								'label' => Yii::t('accountDetails', 'Tasa de Interés'),
								'data' => isset($details->tasa) ? $details->tasa . '%' : Yii::t('commons', 'No asignado'),
							),
							array(
								'icon' => 'circle',
								'label' => Yii::t('accountDetails', 'Mora (días)'),
								'data' => $details->diasmora,
							),
							array(
								'icon' => 'usd',
								'label' => Yii::t('commons', 'Moneda'),
								'data' => $details->codigomoneda,
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Monto Pago Mínimo al último cierre'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->pagominimo),
							),

							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Deuda Periodo'),
								'data' => $details->codigomoneda . ' ' . Yii::app()->numberFormatter->formatDecimal($details->saldocierre),
							),
							array(
								'icon' => 'money',
								'label' => Yii::t('accountDetails', 'Fecha Proximo Cierre'),
								'data' => WebServiceClient::formatDate($details->fechacierre, 'long'),
							),
						),
					);
				}
				$this->render('accountDetails', array(
					'id'	=> $id,
					'datas'	=> $datas,
				));

				Yii::app()->end();
			}
		}

		$accountOptions = Yii::app()->user->accounts->getGridArray();
		$form = new TbForm($model->formConfig($accountOptions), $model);

		$this->render('accountDetailsForm', array('form' => $form));
	}

	/**
	 * Account movements action
	 */
	public function actionMovements($id = null)
	{
		$model = new MovementForm;
		
		// valores por defecto para el modelo
		$model->accountNumber = $id;
		$model->dateFrom = date('d-m-Y', strtotime('-90 days'));
		$model->dateTo = date('d-m-Y');

		$details = $this->wsClient->extractToRequirement(array(
			'toDate'		=> date("Y").'/'.date("m"),
		));
		
		if (Yii::app()->request->isPostRequest) {
			$model->attributes = $_POST['MovementForm'];
			
			if ($model->validate()) {
				$account = $this->_accountData($model->accountNumber);

				$fromDate = $this->formatoFecha($model->dateFrom);
				$toDate = $this->formatoFecha($model->dateTo);
				//var_dump($fromDate); exit;


				$wsParams = [
					'accountNumber' => $account['accountNumber'],
					'fromDate'		=> $fromDate,
					'toDate'		=> $toDate,
				];

				if (isset(Yii::app()->user->deploymentType) && Yii::app()->user->deploymentType != '9999') {

					if ($account['accountType'] == 'PT') {
						$movements = $this->wsClient->getLoanMovements($wsParams);
					} else {
						$movements = $this->wsClient->getMovements($wsParams);
					}
				} else {
					$movements = $this->wsClient->getLoanMovements($wsParams);
				}
			
				$list = isset($movements->listamovimientos->array) ? $movements->listamovimientos->array : array();
				$dataProvider = $this->_getMovementsProvider($list);
				$tjProviders = $account['accountType'] === 'TJ' ?
					$this->_getTjProviders($movements) :
					[];
				
				// datos para el grafico
				$chartDatas = array();
				$list = array_reverse($list);

				foreach ($list as $row) {
					list($h, $m, $s) = explode(':', $row->hora);

					// agregar hora y convertirlo a milisegundos para js
					$time = (WebServiceClient::formatDate($row->fechavalor, null) + mktime($h, $m, $s, 1, 1, 1970)) * 1000;
					$chartDatas[] = array($time, (int) $row->saldo);
				}

				$typeAccount = $account['accountType'];
				switch ($account['accountType']) {
					case 'AH':
						$accountType = 'Caja de Ahorro';
						break;
					case 'AP':
						$accountType = 'CDA';
						break;
					case 'TJ':
						$accountType = 'Tarjeta de Crédito';
						break;
					case 'PT':
						$accountType = 'Préstamo';
						break;
				}

				if ($model->viewOpt) {

					$form = new TbForm($model->formHiddenConfig(), $model);

					$this->render('movements', array(
						'id' => $id,
						'dataProvider' => $dataProvider,
						'account' => (Yii::app()->params['maskedAccountNumber'] == 'N') ? $account['accountNumber'] : $account['maskedAccountNumber'],
						'accountDenomination' => $account['denomination'],
						'chartDatas' => $chartDatas,
						'accountType' => $accountType,
						'form' => $form,
						'model' => $model,
						'typeAccount' => $typeAccount,
						'tjProviders' => $tjProviders,
					));

					Yii::app()->end();
				} elseif ($model->pdfOpt) {

					$dataProvider->pagination = false;
					$this->render('/commons/pdf', array(
						'class'			=> 'ExtractOnRequest2',
						'fileName'		=> 'Reporte-Movimientos',
						'config'		=> array(),
						'datas' => array(
							'dataProvider'	=> $tjProviders ?: ['MOV' => $dataProvider],
							'header' => [
								'account' => (Yii::app()->params['maskedAccountNumber'] == 'N') ? $account['accountNumber'] : $account['maskedAccountNumber'],
								'accountDenomination' => $account['denomination'],
								'date' => "Del $model->dateFrom al $model->dateTo",
								'nombrecliente'		=> $details->nombrecliente,
								'direccioncliente'	=> $details->direccioncliente,
								'codigocliente'		=> $details->codigocliente,
								'currency'			=> $account['currency'],
								'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
							],
						),
					));

					Yii::app()->end();
				}
			}
		}

		// si se recibe $id es que se accedió desde el menú contextual
		// de una cuenta en la lista del home
		$accounts = empty($id) ? Yii::app()->user->accounts->getGridArray(array(
			'conditions' => array(
				'accountType' => 'AH, CDA, CC, TJ',
			),
		), 'TA') : $id;
		
		$form = new TbForm($model->formConfig($accounts), $model);
		
		$this->render('movementsForm', array('form' => $form));
	}

	private function _getMovementsProvider($list)
	{
		return new CArrayDataProvider(
			$list,
			array(
				'keyField'		=> 'abreviaturatransaccionpadre',
				'pagination'	=> array('pageSize' => 20),
			)
		);
	}

	private function _getTjProviders($movements)
	{
		$pdfColumns = array(
			array(
				'header' => Yii::t('movements', 'Fecha movimiento'),
				'value' => 'WebServiceClient::formatDate($data->fecha)." ".$data->hora',
			),
			array(
				'header' => utf8_decode(Yii::t('movements', 'Fecha confirmación')),
				'value' => 'WebServiceClient::formatDate($data->fechavalor)." ".$data->hora',
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
			/*array(
				'header' => Yii::t('commons', 'Saldo'),
				'value' => 'Yii::app()->numberFormatter->formatDecimal($data->saldo)',
			),*/
		);

		if ($movements->cantidadtarjetas == 0) {
			return [
				[
					'title' => $movements->descripcionrespuesta,
					'dataProvider' => new CArrayDataProvider(
						[],
						array(
							'keyField'		=> 'abreviaturatransaccionpadre',
							'pagination'	=> array('pageSize' => 20),
						)
					),
					'columns' => $pdfColumns,
				],
			];
		}

		$list = $movements->listamovimientos->array;
		$tjList = [];
		$tjs = $movements->listanombretarjetas->array;

		foreach ($tjs as $tj) {
			$tjList[$tj->numerotarjeta] = $tj;
		}

		// crear un dataProvider por cada tarjeta (principal y adicionales)
		// el dataProvider del índice 0 siempre va a ser la tarjeta principal
		$dataProviders = [];
		$currentList = [];
		$currentTj = $tjs[0]->numerotarjeta;
		foreach ($list as $item) {
			if ($item->nroTarjeta !== $currentTj) {
				$dataProviders[] =  [
					'title' => $this->_getTjTitle($tjList[$currentTj]),
					'dataProvider' => new CArrayDataProvider(
						$currentList,
						array(
							'keyField'		=> 'abreviaturatransaccionpadre',
							'pagination'	=> array('pageSize' => 20),
						)
					),
					'columns' => $pdfColumns,
				];
				$currentTj = $item->nroTarjeta;
				$currentList = [];
			}

			$currentList[] = $item;
		}

		$dataProviders[] =  [
			'title' => $this->_getTjTitle($tjList[$currentTj]),
			'dataProvider' => new CArrayDataProvider(
				$currentList,
				array(
					'keyField'		=> 'abreviaturatransaccionpadre',
					'pagination'	=> array('pageSize' => 20),
				)
			),
			'columns' => $pdfColumns,
		];

		return $dataProviders;
	}

	private function _getTjTitle($tj)
	{
		return (isset($tj->nombre) ? $tj->nombre . ' - ' : '') .
			Accounts::maskAccountNumber($tj->numerotarjeta) .
			($tj->vigente === 'S' ? '' : ' (No vigente)');
	}

	/**
	 * Returned checks action
	 *
	 * Show returned checks details if account $id is setted
	 * Render a form if $id is null
	 */
	public function actionReturnedChecks($id = null)
	{
		if (isset($id) || isset($_POST['ReturnedChecksForm'])) {
			$id = isset($id) ? $id : $_POST['ReturnedChecksForm']['account'];

			$accNumber = $this->_accountData($id);
			$response = $this->wsClient->getReturnedChecks(array(
				'accountNumber' => $accNumber['accountNumber'],
			));

			$dataProvider = new CArrayDataProvider(
				isset($response->listachequesdevueltos->array) ? $response->listachequesdevueltos->array : array(),
				array(
					'keyField' => 'numerocheque',
					'pagination' => array(
						'pageSize' => 10,
					),
				)
			);

			$this->render('returnedChecks', array('dataProvider' => $dataProvider));
		} else {
			$model = new ReturnedChecksForm;
			$accounts = Yii::app()->user->accounts->getGridArray(array(
				'conditions' => array(
					'accountType' => 'CC, AH'
				)
			));
			$form = new TbForm($model->formConfig($accounts), $model);
			$this->render('returnedChecksForm', array('form' => $form));
		}
	}

	/**
	 * Deposits to confirm action
	 *
	 * Show deposits to confirm details if account $id is setted
	 * Render a form if $id is null
	 */
	public function actionDepositsToConfirm($id = null)
	{

		if (isset($id) || isset($_POST['DepositsToConfirmForm'])) {
			$id = isset($id) ? $id : $_POST['DepositsToConfirmForm']['account'];

			$accNumber = $this->_accountData($id);
			$response = $this->wsClient->getDepositsToConfirm(array(
				'accountNumber' => $accNumber['accountNumber'],
			));

			$dataProvider = new CArrayDataProvider(
				isset($response->listadepositosconfirmar->array) ? $response->listadepositosconfirmar->array : array(),
				array(
					'keyField' => 'nroboleta',
					'pagination' => array(
						'pageSize' => 10,
					),
				)
			);

			$this->render('depositsToConfirm', array('dataProvider' => $dataProvider));
		} else {
			$model = new DepositsToConfirmForm;
			$accounts = Yii::app()->user->accounts->getGridArray(array(
				'conditions' => array(
					'accountType' => 'AH'
				),
			));
			$form = new TbForm($model->formConfig($accounts), $model);
			$this->render('depositsToConfirmForm', array('form' => $form));
		}
	}

	public function actionAccountBalance()
	{
		$model = new AccountBalanceForm;

		if (isset($_POST['AccountBalanceForm'])) {
			$model->attributes = $_POST['AccountBalanceForm'];

			if ($model->validate()) {

				$account = $this->_accountData($model->account);

				$wsResponse = $this->wsClient->getHistoricalAccountBalance(array(
					'accountNumber' => $account['accountNumber'],
					'days' => $model->days,
				));

				$dataProvider = new CArrayDataProvider(
					isset($wsResponse->listasaldos->array) ? $wsResponse->listasaldos->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> array('pageSize' => 20),
					)
				);

				switch ($account['accountType']) {
					case 'AH':
						$accountType = 'Caja de Ahorro';
						break;
					case 'TJ':
						$accountType = 'Tarjeta de Crédito';
						break;
					case 'PT':
						$accountType = 'Préstamo';
						break;
				}

				$this->render('accountBalance', array(
					'accDesc'	=> $account['denomination'],
					'account'	=> (Yii::app()->params['maskedAccountNumber'] == 'N') ? $account['accountNumber'] : $account['maskedAccountNumber'],
					'dataProvider'	=> $dataProvider,
					'accountDenomination' => $account['denomination'],
					'accountType' => $accountType,
					'currency' => $wsResponse->codigomoneda,
				));

				Yii::app()->end();
			}
		}

		$accounts = Yii::app()->user->accounts->getGridArray(array(
			'conditions' => array(
				'accountType' => 'AH, CDA, CC, TJ',
			),
		), 'TA');
		$form = new TbForm($model->formConfig($accounts), $model);

		$this->render('accountBalanceForm', array('form' => $form));
	}

	public function actionInvoice($id = null, $fecha = null)
	{
		if ($id === null) {

			$invoices = $this->wsClient->getInvoicesList();

			$dataProvider = new CArrayDataProvider(
				$invoices->cantidadfacturas > 0 ? $invoices->listafacturas->array : array(),
				array(
					'keyField'		=> 'numerofactura',
					'pagination'	=> array('pageSize' => 20),
				)
			);

			$this->render('invoicesList', array(
				'dataProvider'	=> $dataProvider,
				'mode' => 'list',
			));
		} else {
			// Recuperar detalles de la factura
			$dia = substr($fecha, 0, 2);
			$mes = substr($fecha, 2, 2);
			$ann = substr($fecha, 4);
			//throw new CHttpException(404, Yii::t('invoicesList', 'FECHA '.$fecha));
			//$fecha = new DateTime();
			$fec = new DateTime();
			$fec->setDate($ann, $mes, $dia);
			$fecha_cadena = date_format($fec, 'Y/m/d H:i:s');


			$invoice = $this->wsClient->getInvoice(array(
				'invoiceNumber' => $id,
				'invoiceDate' => $fecha_cadena,
			));
			
			
			//modificaciones completas de qr
			$qr = $this->qrDatos($invoice);
			$kude = '';
			$qr->CDC == '' ? $kude = '': $kude = '<b>KuDE de Factura Electrónica</b>';
			//$path = 'C:\xampp\htdocs\www\homebanking\img'; //cambiar al mudar al server de test widows
			$path = '/var/www/html/homebanking/img';
			$content = "$qr->QRCODE";
			$nombre = $path."/facturas/".$qr->CDC.'.png';
			QRcode::png($content,$nombre,QR_ECLEVEL_L,10,2);
		
			if ($invoice->error === 'N')
				$this->render('invoicesList', array(
					'invoice'	=> $invoice,
					'mode' => 'details',
					'cdc' => $qr->CDC,
					'qrcode' => $qr->QRCODE,
					'kude' => $kude
					
				));
			else
				throw new CHttpException(500, Yii::t('error', 'No se pudo obtener la información solicitada.'));
		}
	}

	/**
	 * @autor: Higinio Samaniego
	 * @date: 
	 * Función obtener datos de qr
	 */
	public function qrDatos($datosFactura){

		error_reporting(E_ALL);

		//cambiar url a prod para publicar.
		$url = 'https://10.90.7.16/apispi/public/api/qrfactura';
		$ch = curl_init();
		
		$datos = [
			'factura' => $datosFactura->numerofactura,
			'tipocomprobante' => $datosFactura->tipocomprobante,
			'timbrado' => $datosFactura->numerotimbrado,
			'fecha' => $datosFactura->fecha
		];
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$datos);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$server_output = curl_exec($ch);
		
		curl_close($ch);
		return json_decode($server_output);

	}

	public function actionInternationalTransactions()
	{
		$transactions = $this->wsClient->getInternationalTransactions();

		//separar transacciones tipo enviadas y tipo recibidas
		$env = $rec = array();
		if ($transactions->cantidadtransferencias > 0) {
			foreach ($transactions->listatransferencias->array as $row) {
				if ($row->tipotransferencia === 'ENV') {
					$env[] = $row;
				} elseif ($row->tipotransferencia === 'REC') {
					$rec[] = $row;
				}
			}
		}
		$dpenv = new CArrayDataProvider(
			$env,
			array(
				'keyField' => 'numerooperacion',
				'pagination' => array('pageSize' => 10),
			)
		);
		$dprec = new CArrayDataProvider(
			$rec,
			array(
				'keyField' => 'numerooperacion',
				'pagination' => array('pageSize' => 10),
			)
		);

		//$dataProvider->sortData();

		$this->render('internationalTransactions', array(
			'dpenv'	=> $dpenv,
			'dprec'	=> $dprec,
		));
	}

	public function actionInternationalTransactionDetails($id)
	{
		// recuperar todas las transacciones
		$transactions	= $this->wsClient->getInternationalTransactions();
		$detailRow		= null;

		// buscar el row correspondiente al numero de operacion recibido en $id
		foreach ($transactions->listatransferencias->array as $row) {
			if ($row->numerooperacion === $id) {
				$detailRow = $row;
				break;
			}
		}

		if (empty($detailRow)) {
			$this->setFlashError('Transacción no encontrada');
			$this->redirect(array('/site/index'));
		}

		$this->render('internationalTransactionDetails', array('data' => $detailRow));
	}

	public function actionExtract($id = null)
	{

		$model = new ExtractOnRequestForm;

		if ($id) {
			$account = $this->_accountData($id);
			$details = $this->wsClient->extractToRequirement(array(
				'accountNumber' => $account['accountNumber']
			));

			if ($details->error === 'S') {
				$this->setFlashError($details->descripcionrespuesta);
				$this->redirect(array('site/index'));
			}

			// Lista de extractos de cuentas
			$dataProvider['cuentascliente'] = new CArrayDataProvider(
				isset($details->cuentascliente->array) ? $details->cuentascliente->array : array(),
				array(
					'keyField'		=> false,
					'pagination'	=> false,
				)
			);

			// detalles de los movimientos
			$dataProvider['detallesmovimiento'] = new CArrayDataProvider(
				isset($details->detallesmovimiento->array) ? $details->detallesmovimiento->array : array(),
				array(
					'keyField'		=> false,
					'pagination'	=> false,
				)
			);

			// resumen por tipo de cuenta
			$tipo = array(
				'PF'  => array('titulo' => 'Plazo Fijo'),
				'CDA' => array('titulo' => 'Certificado de depósitos de ahorro'),
				'PR'  => array('titulo' => 'Préstamos'),
				'TA'  => array('titulo' => 'Tarjetas de Crédito'),
				'LS'  => array('titulo' => 'Línea de Sobregiro'),
				'TI'  => array('titulo' => 'Títulos de Inversión'),
			);

			if (isset($details->resumenescuenta->array)) {
				foreach ($tipo as $key => $value) {
					$new_array = array_filter($details->resumenescuenta->array, function ($obj) use ($key) {
						if (isset($obj->tiporesumen) && $obj->tiporesumen == $key) {
							return true;
						} else {
							return false;
						}
					});

					if (count($new_array) > 0) {
						$new_array = array_values($new_array);
						$dataProvider[$key] = new CArrayDataProvider(
							$new_array,
							array(
								'keyField'		=> false,
								'pagination'	=> false,
							)
						);
					}
				}
			}

			// retenciones de cuentas
			$dataProvider['retencionescuenta'] = new CArrayDataProvider(
				isset($details->retencionescuenta->array) ? $details->retencionescuenta->array : array(),
				array(
					'keyField'		=> false,
					'pagination'	=> false,
				)
			);

			// bloqueo de cuentas
			$dataProvider['bloqueoscuenta'] = new CArrayDataProvider(
				isset($details->bloqueoscuenta->array) ? $details->bloqueoscuenta->array : array(),
				array(
					'keyField'		=> false,
					'pagination'	=> false,
				)
			);

			$this->render('extractOnRequest', array(
				'dataProvider'	=> $dataProvider,
				'model' => $model,
				'header'		=> array(
					'nombrecliente'		=> $details->nombrecliente,
					'direccioncliente'	=> $details->direccioncliente,
					'codigocliente'		=> $details->codigocliente,
					'date'				=> $model->month,
					'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
				),
			));

			Yii::app()->end();
		}
	}

	public function actionAditionalCards($id = null)
	{
		$model = new AditionalCardsForm;

		if (isset($_POST['AditionalCardsForm'])) {
			$model->attributes = $_POST['AditionalCardsForm'];
			if ($model->validate()) {
				$id = $model->account;
			}
		}

		if ($id) {
			$account = $this->_accountData($id);
			$response = $this->wsClient->getAditionalCards(array(
				'creditCardNumber' => $account['accountNumber'],
			));

			$dataProvider = new CArrayDataProvider(
				isset($response->tarjetasadicionales->array) ? $response->tarjetasadicionales->array : array(),
				array(
					'keyField' => 'numerotarjeta',
					'pagination' => array(
						'pageSize' => 10,
					),
				)
			);

			$this->render('aditionalCards', array('dataProvider' => $dataProvider));
			Yii::app()->end();
		}

		// renderizar formulario de consulta
		$accounts = Yii::app()->user->accounts->getGridArray(array(
			'conditions' => array(
				'accountType' => 'TJ',
			)
		), 'TA');
		$form = new TbForm($model->formConfig($accounts), $model);

		if ($form->submitted('submit')) $form->validate();

		$this->render('aditionalCardsForm', array('form' => $form));
	}

	public function actionCdaCoupons($id = null)
	{

		$model = new CdaCouponsForm;



		if (isset($_POST['CdaCouponsForm'])) {
			$model->attributes = $_POST['CdaCouponsForm'];
			if ($model->validate()) {
				$id = $model->account;
			}
		}

		if ($id) {
			$account = $this->_accountData($id);

			$response = $this->wsClient->getCdaCoupons(array(
				'cdaNumber' => $account['accountNumber'],
			));





			$dataProvider = new CArrayDataProvider(
				isset($response->listacuotas->array) ? $response->listacuotas->array : array(),
				array(
					'keyField' => false,
					'pagination' => array(
						'pageSize' => 10,
					),
				)
			);


			switch ($account['accountType']) {
				case 'AH':
					$accountType = 'Caja de Ahorro';
					break;
				case 'AP':
					$accountType = 'CDA';
					break;
				case 'TJ':
					$accountType = 'Tarjeta de Crédito';
					break;
				case 'PT':
					$accountType = 'Préstamo';
					break;
			}

			//$this->render('cdaCoupons', array('dataProvider'=>$dataProvider));
			$this->render('cdaCoupons', array(
				'dataProvider' => $dataProvider,
				'account' => (Yii::app()->params['maskedAccountNumber'] == 'N') ? $account['accountNumber'] : $account['maskedAccountNumber'],
				'accountDenomination' => $account['denomination'],
				'accountType' => $accountType,
			));
			Yii::app()->end();
		}

		// renderizar formulario de consulta
		$accounts = Yii::app()->user->accounts->getGridArray(array(
			'conditions' => array(
				'accountType' => 'AP',
			)
		), 'AH');
		$form = new TbForm($model->formConfig($accounts), $model);

		if ($form->submitted('submit')) $form->validate();

		$this->render('cdaCouponsForm', array('form' => $form));
	}

	public function actionAuthorizations()
	{
		// obtener lista de autorizaciones
		$response = $this->wsClient->getAuthorizations(array(
			'status' => 'P',
		));

		// establecer proveedor de datos
		$dataProvider = new CArrayDataProvider(
			$response->cantidadautorizaciones > 0 ? $response->listaautorizaciones->array : array(),
			array(
				'keyField' => false,
				'pagination' => array('pageSize' => 10),
			)
		);

		// renderizar lista de autorizaciones
		$this->render('authorizations', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionExtractOnRequest($id = null)
	{
		$valorExtra = Yii::app()->user->getState('initdata');

		$model = new ExtractOnRequestForm;

		if (isset($_POST['ExtractOnRequestForm'])) {

			$model->attributes = $_POST['ExtractOnRequestForm'];
			if ($model->validate()) {

				$details = $this->wsClient->extractToRequirement(array(
					'toDate' => $model->month,
				));


				if ($details->error === 'S') {
					$this->setFlashError($details->descripcionrespuesta);
					$this->redirect(array('extractOnRequest'));
				}

				// Lista de extractos de cuentas
				$dataProvider['cuentascliente'] = new CArrayDataProvider(
					isset($details->cuentascliente->array) ? $details->cuentascliente->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

				// detalles de los movimientos
				$dataProvider['detallesmovimiento'] = new CArrayDataProvider(
					isset($details->detallesmovimiento->array) ? $details->detallesmovimiento->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

				// resumen por tipo de cuenta
				$tipo = array(
					'PF'  => array('titulo' => 'Plazo Fijo'),
					'CDA' => array('titulo' => 'Certificado de depósitos de ahorro'),
					'PR'  => array('titulo' => 'Préstamos'),
					'TA'  => array('titulo' => 'Tarjetas de Crédito'),
					'LS'  => array('titulo' => 'Línea de Sobregiro'),
					'TI'  => array('titulo' => 'Títulos de Inversión'),
				);

				if (isset($details->resumenescuenta->array)) {
					foreach ($tipo as $key => $value) {
						$new_array = array_filter($details->resumenescuenta->array, function ($obj) use ($key) {
							if (isset($obj->tiporesumen) && $obj->tiporesumen == $key) {
								return true;
							} else {
								return false;
							}
						});

						if (count($new_array) > 0) {
							$new_array = array_values($new_array);
							$dataProvider[$key] = new CArrayDataProvider(
								$new_array,
								array(
									'keyField'		=> false,
									'pagination'	=> false,
								)
							);
						}
					}
				}

				// retenciones de cuentas
				$dataProvider['retencionescuenta'] = new CArrayDataProvider(
					isset($details->retencionescuenta->array) ? $details->retencionescuenta->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

				// bloqueo de cuentas
				$dataProvider['bloqueoscuenta'] = new CArrayDataProvider(
					isset($details->bloqueoscuenta->array) ? $details->bloqueoscuenta->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

				if ($model->viewOpt) {

					$form = new TbForm($model->formHiddenConfig(), $model);


					$this->render('extractOnRequest', array(
						'dataProvider'	=> $dataProvider,
						'form'			=> $form,
						'header'		=> array(
							'nombrecliente'		=> $details->nombrecliente,
							'direccioncliente'	=> $details->direccioncliente,
							'codigocliente'		=> $details->codigocliente,
							'date'				=> $model->month,
							'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
						),
					));

					Yii::app()->end();
				} elseif ($model->pdfOpt) {

					$this->render('/commons/pdf', array(
						'class'			=> 'ExtractOnRequest',
						'fileName'		=> 'Reporte',
						'config'		=> array(),
						'datas' => array(
							'dataProvider'	=> $dataProvider,
							'header'		=> array(
								'nombrecliente'		=> utf8_decode($details->nombrecliente),
								'direccioncliente'	=> utf8_decode($details->direccioncliente),
								'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
								'codigocliente'		=> $details->codigocliente,
								'date'				=> $model->month,
							),
							'footer'				=> array(
								'mensaje'		=> "El saldo que consta en este extracto será considerado correcto, si no es objetado dentro de los quince días de su remisión (Cod. Civil Art. 1402).",
							),
						),
					));

					Yii::app()->end();
				}
			}
		} else {
			$model->month = date('Y/m');
		}

		$form = new TbForm($model->formConfig(), $model);

		$this->render('extractOnRequestForm', array('form' => $form));
	}

	public function actionAccountExtract($accountHash = null)
	{
		$model = new AccountExtractForm;
		$account = null;

		if (!empty($accountHash)) {
			$model->accountHash = $accountHash;
			$account = $this->_accountData($accountHash);
			$accountsOptions = false;
		} else {
			// mostrar la grilla de opciones de cuentas en el formulario
			$accountsOptions = Yii::app()->user->accounts->getGridArray(array(
				'conditions' => array(
					'accountType' => 'AH',
				),
			), 'TA');
		}

		if (isset($_POST['AccountExtractForm'])) {
			$model->attributes = $_POST['AccountExtractForm'];

			if ($model->validate()) {
				$account = $this->_accountData($model->accountHash);
				$details = $this->wsClient->extractToRequirement(array(
					'toDate'		=> $model->month,
					'accountNumber' => $account['accountNumber'],
				));

				
				// Lista de extractos de cuentas
				$dataProvider['cuentascliente'] = new CArrayDataProvider(
					isset($details->cuentascliente->array) ? $details->cuentascliente->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

				// resumen por tipo de cuenta
				$tipo = array(
					'PF'  => array('titulo' => 'Plazo Fijo'),
					'CDA' => array('titulo' => 'Certificado de depósitos de ahorro'),
					'PR'  => array('titulo' => 'Préstamos'),
					'TA'  => array('titulo' => 'Tarjetas de Crédito'),
					'LS'  => array('titulo' => 'Línea de Sobregiro'),
					'TI'  => array('titulo' => 'Títulos de Inversión'),
				);

				if (isset($details->resumenescuenta->array)) {
					foreach ($tipo as $key => $value) {
						$new_array = array_filter($details->resumenescuenta->array, function ($obj) use ($key) {
							if (isset($obj->tiporesumen) && $obj->tiporesumen == $key) {
								return true;
							} else {
								return false;
							}
						});

						if (count($new_array) > 0) {
							$new_array = array_values($new_array);
							$dataProvider[$key] = new CArrayDataProvider(
								$new_array,
								array(
									'keyField'		=> false,
									'pagination'	=> false,
								)
							);
						}
					}
				}



				if ($details->error === 'S') {
					$this->setFlashError($details->descripcionrespuesta);
					$this->redirect(array('accountExtract'));
				}

				// detalles de los movimientos
				$dataProvider['detallesmovimiento'] = new CArrayDataProvider(
					isset($details->detallesmovimiento->array) ? $details->detallesmovimiento->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

				//Resumen de cuenta, added: Higinio Samaniego, 03-11-2022
				$dataProvider['cuentascliente'] = new CArrayDataProvider(
					isset($details->cuentascliente->array) ? $details->cuentascliente->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

				if ($model->viewOpt) {

					$form = new TbForm($model->formHiddenConfig(), $model);

					

					$this->render('accountExtract', array(
						'dataProvider'	=> $dataProvider,
						'form'			=> $form,
						'header'		=> array(
							'nombrecliente'		=> $details->nombrecliente,
							'direccioncliente'	=> $details->direccioncliente,
							'codigocliente'		=> $details->codigocliente,
							'date'				=> $model->month,
							'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
						),
					));

					Yii::app()->end();
				} elseif ($model->printOpt) {

					$this->layout = '//layouts/printPage';
					$this->render('accountExtract', array(
						'dataProvider'	=> $dataProvider,
						'header'		=> array(
							'nombrecliente'		=> $details->nombrecliente,
							'direccioncliente'	=> $details->direccioncliente,
							'codigocliente'		=> $details->codigocliente,
							'date'				=> $model->month,
							'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
						),
						
					));

					Yii::app()->end();
				} elseif ($model->pdfOpt) {

					$this->render('/commons/pdf', array(
						'class'			=> 'ExtractOnRequest_TAE',
						'fileName'		=> 'Reporte',
						'config'		=> array(),
						'datas' => array(
							'dataProvider'	=> $dataProvider,
							'header'		=> array(
								'nombrecliente'		=> utf8_decode($details->nombrecliente),
								'direccioncliente'	=> utf8_decode($details->direccioncliente),
								'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
								'codigocliente'		=> $details->codigocliente,
								'date'				=> $model->month,
							
							),
							'footer'				=> array(
								'mensaje'		=> "El saldo que consta en este extracto será considerado correcto, si no es objetado dentro de los quince días de su remisión (Cod. Civil Art. 1402).",
							),
							
						),
					));

					Yii::app()->end();
				} elseif ($model->excel) {
					//echo "Descargar en Excel";
					$datos = array(
						'dataProvider'	=> $dataProvider,
						'header'		=> array(
							'nombrecliente'		=> utf8_decode($details->nombrecliente),
							'direccioncliente'	=> utf8_decode($details->direccioncliente),
							'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
							'codigocliente'		=> $details->codigocliente,
							'date'				=> $model->month,
						
						),
						'footer'				=> array(
							'mensaje'		=> "El saldo que consta en este extracto será considerado correcto, si no es objetado dentro de los quince días de su remisión (Cod. Civil Art. 1402).",
						),
					);

					$this->excelExport($datos);

				}
			}
		} else {
			$model->month = date('Y/m');
		}

		$form = new TbForm($model->formConfig($accountsOptions, $account), $model);

		$this->render('accountExtractForm', array(
			'form'			=> $form,
			'accountTitle'	=> empty($accountHash),
		));
	}

	/**
	 * Excel export example
	 */
	public function excelExport( $datos = array() ){

		$nombreClienteEmpresa = $datos['header']['nombrecliente'].' '.$datos['dataProvider']['cuentascliente']->rawData['0']->numerocuenta;
		$img = '<img src="http://www.fic.com.py/fic/img/logo.png" alt="Logo" width="120" height="60">';
		$salida = "";
		$salida .= "$img<br>
					<b>Detalle de Movimientos</b>
					<table border='1'>";
		$salida .= "<thead>
						
						<tr>
							<th>Estado de cta. al </th>
							<th>$nombreClienteEmpresa</th>
							
						</tr>
					</thead>
					<tbody>
					<tr>
						<td>".$datos['dataProvider']['cuentascliente']->rawData['0']->fechaextracto."</td>
						<td>".$datos['header']['direccioncliente']."</td>
						
					</tr>
					<tr>
						<td>Oficial de cuentas: ".Yii::app()->user->getState('officerName')."
						<br> Teléfono: ".Yii::app()->user->getState('officerPhone')."
						<br> E-mail: ".Yii::app()->user->getState('officerEmail')."</td>
						<td>Nro. Cliente: ".$datos['header']['codigocliente']."</td>
						
					</tr>
					</tbody>
					</table>

					";
			$resumenCuenta = "<br>
								<b>Resumen de Cuenta</b>
							<table border='1'>
								<thead>
									<tr>
										<th>Descripción</th>
										<th>Cuenta</th>
										<th>Saldo Anterior</th>
										<th>Saldo Actual</th>
										<th>Saldo Promedio</th>
										<th>T.A.E.</th>
										<th>T.A.N.</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>".$datos['dataProvider']['cuentascliente']->rawData['0']->descripcion."</td>
										<td>".$datos['dataProvider']['cuentascliente']->rawData['0']->numerocuenta."</td>
										<td>".number_format($datos['dataProvider']['cuentascliente']->rawData['0']->saldoanteriordisponible,0,",",".")."</td>
										<td>".number_format($datos['dataProvider']['cuentascliente']->rawData['0']->saldoactualdisponible,0,",",".")."</td>
										<td>".number_format($datos['dataProvider']['cuentascliente']->rawData['0']->saldopromedio,0,",",".")."</td>
										<td>".number_format($datos['dataProvider']['cuentascliente']->rawData['0']->tasefectiva,2,",",".")."</td>
										<td>".number_format($datos['dataProvider']['cuentascliente']->rawData['0']->tasnominal,2,",",".")."</td>
									</tr>
								</tbody>
							</table>";

			$detalleMovimientos = "<br><b>Detalle de movimientos</b>
									<table border='1'>
										<thead>
										<tr>
											<th>Fecha Conf.</th>
											<th>Fecha Tran.</th>
											<th>Nro. Comprobante</th>
											<th>Concepto</th>
											<th>Importe Dédito.</th>
											<th>Importe Crédito</th>
											<th>Saldo Actual</th>
										</tr>
										</thead><tbody>";
			
			
			foreach ($datos['dataProvider']['detallesmovimiento']->rawData as $data ){
				
				$detalleMovimientos .= "
						<tr>
							<td>".$data->fechaconfirmacion."</td>
							<td>".$data->fechatransaccion."</td>
							<td>".(isset($data->numerocomprobante)?$data->numerocomprobante:0)."</td>
							<td>".$data->concepto."</td>
							<td>".number_format($data->montodebito,0,",",".")."</td>
							<td>".number_format($data->montocredito,0,",",".")."</td>
							<td>".number_format($data->saldo,0,",",".")."</td>
						</tr>
				";
			
			}
			$detalleMovimientos .= "</tbody></table>";
			$resumenCuenta .= $detalleMovimientos;
			$salida .= $resumenCuenta;

		
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=Extracto_cuenta.xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($salida);
		exit;
	}

	/**
	 * Reporte semestral
	 */
	public function actionSemiannualReport()
	{
		$model = new SemiannualReport();

		if (isset($_POST['SemiannualReport'])) {
			$model->attributes = $_POST['SemiannualReport'];

			if ($model->validate()) {
				$details = $this->wsClient->getMovementsPerSemester([
					'ann' => $model->year,
					'semester' => $model->semester,
				]);

				if ($details->error === 'S') {
					$this->setFlashError($details->descripcionrespuesta);
					$this->redirect(array('semiannualReport'));
				}

				$dataProvider = isset($details->listamovsemestrales->array) ?
					$details->listamovsemestrales->array : [];

				$header = [
					'codigoCliente' => Yii::app()->user->getState('clientId'),
					'nombreCliente' => Yii::app()->user->getState('clientArea')['nombrecompleto'],
					'oficial' => Yii::app()->user->getState('officerName'),
					'direccion' => Yii::app()->user->getState('clientArea')['address'],
					'oficina' => $details->oficina,
					'periodo' => (int) $model->semester === 1 ? 'Primero' : 'Segundo',
					'fecha' => (int) $model->semester === 1 ? '01/01/' . $model->year : '01/07/' . $model->year,
					'al' => (int) $model->semester === 1 ? '30/06/' . $model->year : '31/12/' . $model->year,
				];

				$cotizacion = 6500;

				$this->render('semiannualReport', compact('dataProvider', 'header', 'cotizacion'));
			}
		}

		$form = new TbForm($model->formConfig(), $model);

		$this->render('semiannualReportForm', compact('form'));
	}

	protected function semiannualReportTotalBlock($title, $amount)
	{
		return '
		<tr>
			<td colspan="2"></td>
			<th colspan="2" class="text-right">' . $title . '</th>
			<th class="text-right" style="border-top-color: black;">' .
			Yii::app()->numberFormatter->formatDecimal($amount) .
			'
			</th>
		</tr>';
	}

	protected function actionCCextract()
	{
		$details = $this->wsClient->getCreditCardMovements(array(
			"cardNumber" => '30401200',
			"month" => '2016',
			"year" => '',
			"type" => '',
		));
	}

	protected function amountContext($amount)
	{
		if ($amount > 0)
			return 'text-success';
		elseif ($amount < 0)
			return 'text-error';
		else
			return 'text-warning';
	}



	/**
	 * Added:Higinio Samaniego,21-02-2024, ListEmpresa
	 */
	public function Actionlistempresas()
	{
		$ci = $_POST['cedula']; 
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://10.90.7.16/apispi/public/api/listempresa/".$ci,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSL_VERIFYHOST => FALSE
		  ]);
		  
		  $response = curl_exec($curl);
		  $err = curl_error($curl);
		  
		  curl_close($curl);
		  
		  if($err){
			echo "cURL Error #:" . $err;
		  }else{
			echo $response;
		  }


	}
}
