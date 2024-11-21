<?php
Yii::import('booster.widgets.TbForm');

class ReportController extends Controller
{
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
			array('allow',
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionExchanges()
	{
		$details = $this->wsClient->getExchanges();
		$dataProvider = new CArrayDataProvider(
			isset($details->listadetallecotizacion->array) ? $details->listadetallecotizacion->array : array(),
			array(
				'keyField'=>'moneda',
				'pagination'=>array(
					'pageSize'=>10,
				),
			)
		);
		
		$this->render('exchanges', array('dataProvider'=>$dataProvider));
	}

	/**
	 * Account details action
	 * 
	 * Show account details if $id is setted
	 * Render a form if $id is null
	 */
	public function actionAccountDetails($id=null)
	{
		fb($_POST);
		if(isset($id) || isset($_POST['AccountDetailsForm'])) {

			$id = isset($id) ? $id : $_POST['AccountDetailsForm']['account'];
			$account = $this->_accountData($id);
				
			$details = $this->wsClient->getAccountDetails(array(
				'accountNumber' => $account['accountNumber'],
			));
			if(!((int)$details->cantcuentas > 0))
				throw new CHttpException(404, Yii::t('accountDetails', 'No se encontraron detalles'));

			$labels = $this->wsClient->getAccDetailLabel(array(
				'accountTypeCode' => $account['accountType'],
				'lang' => Yii::app()->language,
			));
			$this->render('accountDetails', array(
				'id'		=> $id,
				'details'	=> $details->listacuentas->array[0],
				'labels'	=> $labels,
			));

		} else {

			$model=new AccountDetailsForm;
			$accountOptions = Yii::app()->user->accounts->getGridArray();
			$form = new TbForm($model->formConfig($accountOptions), $model);

			$this->render('accountDetailsForm',array('form'=>$form));
		}
	}

	/**
	 * Account movements action
	 * 
	 * Show account movements if $id is setted
	 * Render a form if $id is null
	 */
	public function actionMovements($id=null)
	{
		if(isset($id) || isset($_POST['MovementForm'])) {

			$id = isset($id) ? $id : $_POST['MovementForm']['account'];

			$account = $this->_accountData($id);

			$movements = $this->wsClient->getMovements(array(
				'accountNumber' => $account['accountNumber'],
			));

			
			$dataProvider = new CArrayDataProvider(
				isset($movements->listamovimientos->array) ? $movements->listamovimientos->array : array(),
				array(
					'keyField'		=> 'abreviaturatransaccionpadre',
					'pagination'	=> array('pageSize'=>20),
				)
			);
			$this->render('movements', array(
				'id'			=> $id,
				'dataProvider'	=> $dataProvider,
				'accountName'	=> $account['denomination'],
			));

		} else {

			$model=new MovementForm;
			$accountOptions = Yii::app()->user->accounts->getGridArray();
			$form = new TbForm($model->formConfig($accountOptions), $model);
			
			$this->render('movementsForm',array('form'=>$form));

		}
	}

	/**
	 * Returned checks action
	 * 
	 * Show returned checks details if account $id is setted
	 * Render a form if $id is null
	 */
	public function actionReturnedChecks($id=null)
	{
		if(isset($id) || isset($_POST['ReturnedChecksForm']))
		{
			$id = isset($id) ? $id : $_POST['ReturnedChecksForm']['account'];

			$accNumber=$this->_accountData($id);
			$response = $this->wsClient->getReturnedChecks(array(
				'accountNumber' => $accNumber['accountNumber'],
			));

			$dataProvider = new CArrayDataProvider(
				isset($response->listachequesdevueltos->array) ? $response->listachequesdevueltos->array : array(),
				array(
					'keyField'=>'nrocheque',
					'pagination'=>array(
						'pageSize'=>10,
					),
				)
			);

			$this->render('returnedChecks',array('dataProvider'=>$dataProvider));
		} else {
			$model = new ReturnedChecksForm;
			$accounts = Yii::app()->user->accounts->getGridArray(array(
				'conditions' => array(
					'accountType'=>'CC, AH'
				)
			));
			$form = new TbForm($model->formConfig($accounts), $model);
			$this->render('returnedChecksForm',array('form'=>$form));
		}
		
	}

	/**
	 * Deposits to confirm action
	 * 
	 * Show deposits to confirm details if account $id is setted
	 * Render a form if $id is null
	 */
	public function actionDepositsToConfirm($id=null) {

		if(isset($id) || isset($_POST['DepositsToConfirmForm']))
		{
			$id = isset($id) ? $id : $_POST['DepositsToConfirmForm']['account'];

			$accNumber=$this->_accountData($id);
			$response = $this->wsClient->getDepositsToConfirm(array(
				'accountNumber' => $accNumber['accountNumber'],
			));

			$dataProvider = new CArrayDataProvider(
				isset($response->listadepositosconfirmar->array) ? $response->listadepositosconfirmar->array : array(),
				array(
					'keyField'=>'nroboleta',
					'pagination'=>array(
						'pageSize'=>10,
					),
				)
			);

			$this->render('depositsToConfirm',array('dataProvider'=>$dataProvider));
		} else {
			$model = new DepositsToConfirmForm;
			$accounts = Yii::app()->user->accounts->getGridArray(array(
				'conditions' => array(
					'accountType'=>'AH'
				),
			));
			$form = new TbForm($model->formConfig($accounts), $model);
			$this->render('depositsToConfirmForm',array('form'=>$form));
		}
	}

	public function actionAccountBalance()
	{
		$model = new AccountBalanceForm;

		if (isset($_POST['AccountBalanceForm'])) {
			$model->attributes = $_POST['AccountBalanceForm'];

			if ($model->validate()) {
				$account = $this->_accountData($model->account);

				$wsResponse = $this->wsClient->getAccountBalance(array(
					'accountNumber'=>$account['accountNumber'],
					'days'=>$model->days,
				));

				$dataProvider = new CArrayDataProvider(
					isset($wsResponse->saldoscuentas->array) ? $wsResponse->saldoscuentas->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> array('pageSize'=>20),
					)
				);

				$this->render('accountBalance', array(
					'accDesc'	=> $account['denomination'],
					'dataProvider'	=> $dataProvider,
				));

				Yii::app()->end();
			}
		}

		$accounts = Yii::app()->user->accounts->getGridArray(array(
			'conditions' => array(
				'accountType'=>'AH, CDA, CC, TJ',
			),
		));
		$form = new TbForm($model->formConfig($accounts), $model);

		$this->render('accountBalanceForm',array('form'=>$form));
	}

	public function actionInvoice($id = null)
	{
		if($id === null) {

			$invoices = $this->wsClient->getInvoicesList();
	
			$dataProvider = new CArrayDataProvider(
				$invoices->cantfacturas > 0 ? $invoices->listafacturas->array : array(),
				array(
					'keyField'		=> 'nrofactura',
					'pagination'	=> array('pageSize'=>20),
				)
			);
	
			$this->render('invoicesList', array(
				'dataProvider'	=> $dataProvider,
				'mode' => 'list',
			));

		} else {
			// Recuperar detalles de la factura
			$invoice = $this->wsClient->getInvoice(array(
				'invoiceNumber' => $id,
			));
	
			$this->render('invoicesList', array(
				'invoice'	=> $invoice,
				'mode' => 'details',
			));
		}
	}

	public function actionInternationalTransactions()
	{
		$transactions = $this->wsClient->getInternationalTransactions();

		//separar transacciones tipo enviadas y tipo recibidas
		$env = $rec = array();
		foreach ($transactions->listatransferencias->array as $row) {
			if ($row->tipotransferencia === 'ENV') {
				$env[] = $row;
			} elseif($row->tipotransferencia === 'REC') {
				$rec[] = $row;
			}
		}
		$dpenv = new CArrayDataProvider(
			$env,
			array(
				'keyField'=>'numerooperacion',
				'pagination'=>array('pageSize'=>10),
			)
		);
		$dprec = new CArrayDataProvider(
			$rec,
			array(
				'keyField'=>'numerooperacion',
				'pagination'=>array('pageSize'=>10),
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
			$this->setFlashWarning('Transacción no encontrada');
			$this->redirect(array('/site/index'));
		}

		$this->render('internationalTransactionDetails', array('data'=>$detailRow));
	}

	public function actionAditionalCards($id = null)
	{
		$model = new AditionalCardsForm;

		if(isset($_POST['AditionalCardsForm'])) {
			$model->attributes = $_POST['AditionalCardsForm'];
			if($model->validate()) {
				$id = $model->account;
			}
		}

		if($id) {
			$account = $this->_accountData($id);
			$response = $this->wsClient->getAditionalCards(array(
				'creditCardNumber' => $account['accountNumber'],
			));

			$dataProvider = new CArrayDataProvider(
				isset($response->tarjetasadicionales->array) ? $response->tarjetasadicionales->array : array(),
				array(
					'keyField'=>'numerotarjeta',
					'pagination'=>array(
						'pageSize'=>10,
					),
				)
			);
			
			$this->render('aditionalCards', array('dataProvider'=>$dataProvider));
			Yii::app()->end();
		}

		// renderizar formulario de consulta
		$accounts = Yii::app()->user->accounts->getGridArray(array(
			'conditions' => array(
				'accountType'=>'TJ',
			)
		));
		$form = new TbForm($model->formConfig($accounts), $model);

		if ($form->submitted('submit')) $form->validate();
		
		$this->render('aditionalCardsForm',array('form'=>$form));
	}

	public function actionAuthorizations()
	{
		// obtener lista de autorizaciones
		$response = $this->wsClient->getAuthorizations(array(
			'status' => 'P',
		));
//var_dump($response); exit;
		// establecer proveedor de datos
		$dataProvider = new CArrayDataProvider(
			$response->cantidadautorizaciones > 0 ? $response->listaautorizaciones->array : array(),
			array(
				'keyField'=>false,
				'pagination'=>array('pageSize'=>10),
			)
		);

		// renderizar lista de autorizaciones
		$this->render('authorizations', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionExtractOnRequest($id = null)
	{
		$model=new ExtractOnRequestForm;

		if(isset($_POST['ExtractOnRequestForm'])) {

			$model->attributes = $_POST['ExtractOnRequestForm'];
			if ($model->validate()) {
					
				$details = $this->wsClient->extractToRequirement(array(
					'toDate'		=> $model->year.'/'.$model->month,
				));

				if ($details->error === 'S') {
					throw new CHttpException(404, $details->descripcionrespuesta);
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
				$dataProvider['resumenescuenta'] = new CArrayDataProvider(
					isset($details->resumenescuenta->array) ? $details->resumenescuenta->array : array(),
					array(
						'keyField'		=> false,
						'pagination'	=> false,
					)
				);

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

				if($model->viewOpt) {

					$this->render('extractOnRequest', array(
						'dataProvider'	=> $dataProvider,
						'header'		=> array(
							'nombrecliente'		=> $details->nombrecliente,
							'direccioncliente'	=> $details->direccioncliente,
							'codigocliente'		=> $details->codigocliente,
							'date'				=> $model->month.'/'.$model->year,
							'telefonocliente'	=> isset($details->telefonocliente) ? $details->telefonocliente : '',
						),
					));

					Yii::app()->end();

				}elseif($model->printOpt) {

					$this->layout = '//layouts/printPage';
					$this->render('extractOnRequest', array(
						'dataProvider'	=> $dataProvider,
						'header'		=> array(
							'nombrecliente'		=> $details->nombrecliente,
							'direccioncliente'	=> $details->direccioncliente,
							'telefonocliente'	=> $details->telefonocliente,
							'codigocliente'		=> $details->codigocliente,
							'date'				=> $model->month.'/'.$model->year,
						),
					));

					Yii::app()->end();

				} elseif ($model->pdfOpt) {

					$this->render('/commons/pdf', array(
						'class'			=> 'ExtractOnRequest',
						'fileName'		=> 'Reporte',
						'config'		=> array(
							
						),
						'datas' => array(
							'dataProvider'	=> $dataProvider,
							'header'		=> array(
								'nombrecliente'		=> utf8_decode($details->nombrecliente),
								'direccioncliente'	=> utf8_decode($details->direccioncliente),
								'telefonocliente'	=> $details->telefonocliente,
								'codigocliente'		=> $details->codigocliente,
								'date'				=> $model->month.'/'.$model->year,
							),
						),
					));

					Yii::app()->end();
				}
			}
		}

		$form = new TbForm($model->formConfig(), $model);
		
		$this->render('extractOnRequestForm',array('form'=>$form));
	}
}
