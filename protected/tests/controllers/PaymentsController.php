<?php
Yii::import('booster.widgets.TbForm');

/**
 * Controlador de pagos
 */
class PaymentsController extends Controller
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

	public function actionLoan($id = null)
	{
		if($id === null) { // renderizar lista de cuentas AH
	    	$accounts = Yii::app()->user->accounts->getList(array(
	    		'conditions' => array(
		    		'accountType'=>'PT',
		    	),
		    ));

			$dataProvider = new CArrayDataProvider(
				$accounts,
				array(
					'keyField'		=> 'accountNumber',
					'pagination'	=> array('pageSize'=>10),
				)
			);

			$this->render('loan', array(
				'mode' => 'list',
				'dataProvider'	=> $dataProvider,
			));

		} else {

			$model = new LoanPaymentForm;
			$loanDatas = $this->_accountData($id);

			if(isset($_POST['LoanPaymentForm'])) {
				$model->attributes = $_POST['LoanPaymentForm'];
				$debitAccount = $this->_accountData($model->debitAccount);

				// renderizar vista de confirmacion
				if(empty($_POST['LoanPaymentForm']['confirm'])) {
					if($model->validate()) {
						$verifPayment = $this->wsClient->loanPayment(array(
							'loanNumber'	=> $loanDatas['accountNumber'],
							'debitAccount'	=> $debitAccount['accountNumber'],
							'feesAmount'	=> $model->feesAmount,
							'mode'			=>'V',
						));

						if($verifPayment->error === 'S') {
							$this->setFlashError($verifPayment->descripcionrespuesta);
							$this->redirect(array('/payments/loan', "id"=>$id));
						} else {
							// verificacion exitosa
							$model->scenario = 'confirm';
							$model->loanNumber = $id;
							$model->confirm = true;
							// configurar formulario oculto
							$form = new TbForm($model->hiddenFormConfig(), $model);
							$this->render('loan', array(
								'mode'		=> 'verification',
								'details'	=> $verifPayment,
								'form'		=> $form,
							));
							Yii::app()->end();
						}
					}

				} else  // procesar confirmacion de pago

					$model->scenario = 'confirm';
					$model->attributes = $_POST['LoanPaymentForm'];
					if($model->validate()) {
						$confirmPayment = $this->wsClient->loanPayment(array(
							'loanNumber' => $loanDatas['accountNumber'],
							'debitAccount' => $debitAccount['accountNumber'],
							'feesAmount' => $model->feesAmount,
							'mode'=>'C',
							'transactionalKey' => $model->transactionalKey,
						));

						if($confirmPayment->error === 'S') {
							$this->setFlashError($confirmPayment->descripcionrespuesta);
							$this->redirect(array('/payments/loan', "id"=>$id));
						} else {
							// pago exitoso
							$this->setFlashSuccess($confirmPayment->descripcionrespuesta);
							Yii::app()->user->accounts->refresh();
							$this->redirect(array('/site/index'));
							// TODO imprimir respuesta de pago
						}
					}

			}

			// Obtener cuotas de prestamo
			$loanFees = $this->wsClient->getLoanFees(array(
				'loanNumber' => $loanDatas['accountNumber'],
			));

			if ($loanFees->error === 'S') {
				$this->setFlashError($loanFees->descripcionrespuesta);
				$this->redirect(array('/payments/loan', "id"=>$id));
			} else {
				$accountOptions = Yii::app()->user->accounts->getGridArray(array(
					'conditions' => array(
						'__operType__' => '&&',
						'accountType' => 'AH',
						'currency' => $loanDatas['currency'],
					),
				));

				$form = new TbForm($model->formConfig($accountOptions), $model);

				$loanFees = $loanFees->cantidadcuotas > 0 ? $loanFees->listacuotas->array : array();
				$dataProvider = new CArrayDataProvider(
					$loanFees,
					array(
						'keyField'		=> 'nrocuota',
						'pagination'	=> array('pageSize'=>10),
					)
				);

				$this->render('loan', array(
					'mode'			=> 'form',
					'form'			=> $form,
					'dataProvider'	=> $dataProvider,
					'accDenomination' => $loanDatas['denomination'],
				));
			}
		}
    }

    /**
     * Action for credit card payment
     * @param $id string Nro de la tarjeta a pagar
     */
    public function actionCreditCard($id=null)
    {
    	if($id === null) {
	    	$cards = Yii::app()->user->accounts->getList(array(
	    		'conditions' => array(
		    		'accountType'=>'TJ'
		    	),
		    ));

			$dataProvider = new CArrayDataProvider(
				$cards,
				array(
					'keyField'		=> 'accountNumber',
					'pagination'	=> array('pageSize'=>10),
				)
			);

			$this->render('creditCard', array(
				'mode' => 'list',
				'dataProvider'	=> $dataProvider,
			));
		} else {
			$model = new CreditCardPaymentForm;
			$cardDatas = $this->_accountData($id);

			if(isset($_POST['CreditCardPaymentForm'])) {
				$model->attributes = $_POST['CreditCardPaymentForm'];
				// recuperar el nro de cuenta del debito
				$debitAccount = $this->_accountData($model->debitAccount);

				// renderizar vista de confirmacion
				if(empty($_POST['CreditCardPaymentForm']['confirm'])) {

					$model->scenario = 'verify';
					if($model->validate()) {
						$verifPayment = $this->wsClient->creditCardPayment(array(
							'creditCardNumber'	=> $cardDatas['accountNumber'],
							'debitAccount'		=> $debitAccount['accountNumber'],
							'mode'				=>'V',
							'debitAmount'		=> $model->amount,
							'totalPayment'		=> $model->amount,
						));

						if($verifPayment->error === 'S') {
							$this->setFlashError($verifPayment->descripcionrespuesta);
							$this->redirect(array('/payments/creditCard'));
						} else {
							// verificacion exitosa
							$model->creditAccount = $id;
							$model->confirm = true;
							// configurar formulario oculto
							$form = new TbForm($model->hiddenFormConfig(), $model);
							$this->render('creditCard', array(
								'mode'		=> 'verification',
								'details'	=> $verifPayment,
								'form'		=> $form,
							));
							Yii::app()->end();
						}
					}

				} else { // procesar confirmacion de pago

					$model->scenario = 'confirm';
					$model->attributes = $_POST['CreditCardPaymentForm'];
					if($model->validate()) {
						$confirmPayment = $this->wsClient->creditCardPayment(array(
							'creditCardNumber'	=> $cardDatas['accountNumber'],
							'debitAccount'		=> $debitAccount['accountNumber'],
							'mode'				=>'C',
							'debitAmount'		=> $model->amount,
							'totalPayment'		=> $model->amount,
							'transactionalKey'	=> $model->transactionalKey,
						));

						if($confirmPayment->error === 'S') {
							$this->setFlashError($confirmPayment->descripcionrespuesta);
							$this->redirect(array('/payments/creditCard'));
						} else {
							// pago exitoso
							$this->setFlashSuccess($confirmPayment->descripcionrespuesta);
							Yii::app()->user->accounts->refresh();
							$this->redirect(array('/site/index'));
							// TODO imprimir respuesta de pago
						}
					}
				}
			}

			$accountOptions = Yii::app()->user->accounts->getGridArray(array(
				'conditions' => array(
					'__operType__' => '&&',
					'accountType' => 'AH',
					'currency' => $cardDatas['currency'],
				),
			));

			$form = new TbForm($model->formConfig($cardDatas['denomination'], $accountOptions), $model);

			$this->render('creditCard', array(
				'mode' => 'form',
				'form' => $form,
			));
		}
    }

    public function actionSalaries()
    {
    	$model = new SalaryPaymentForm;

    	$this->_renderSalariesForm($model);
    }

    public function actionSalariesManualLoading()
    {
    	$model = new SalaryPaymentForm;

    	if (isset($_POST['SalaryPaymentForm'])) {
    		$model->attributes = $_POST['SalaryPaymentForm'];
    		if($model->validate()) {

    			$this->_renderSalariesManualLoadingForm($model);
    		}
    	}

    	$this->_renderSalariesForm($model);
    }

    public function actionSalariesVerification()
    {
    	$model = new SalaryPaymentForm;
    	if (Yii::app()->request->requestType === 'POST') {
    		// establecer valores del modelo segun el formulario que se reciba (manual/archivo/checkError)
    		$model->attributes = isset($_POST['SalaryPaymentForm']) ?
    			$_POST['SalaryPaymentForm']: // archivo
    			(isset($_POST['model']) ?
    				json_decode($_POST['model'], true): // carga manual/checkerror
    				null
    			);

    		if($model->validate()) {
    			// establecer cantidad de cuentas credito
    			$model->amountOfCreditAccounts = $model->recordsReadNumber;

    			// Carga manual
    			if ($model->controlMode === 'L') {
    				$parameters = '';
    				foreach ($_POST['accountNumber'] as $k=>$row) {
    					// si no se cargo algun dato volver a renderizar el form
    					if (empty($row) || empty($_POST['amount'][$k])) {
    						$this->_renderSalariesManualLoadingForm($model);
    					}
    					$format = "C % 16s                                          %014d %04d\r\n";
    					$parameters .= sprintf($format, $row, $_POST['amount'][$k], $model->entityCode);
    				}
    				$model->parameters = $parameters;

				// Carga por archivo
    			} elseif($model->controlMode === 'V') {

    				$file=CUploadedFile::getInstance($model,'paymentFile');
    				$model->parameters = file_get_contents($file->tempName);
				}

				$this->_salariesDoRequest($model);
    		}
    	}

    	$this->_renderSalariesForm($model);
    }

    public function actionSalariesCheckError()
    {
    	if (Yii::app()->request->requestType === 'POST') {
    		$model = new SalaryPaymentForm;
    		$model->attributes = json_decode($_POST['model'], true);
    		$accounts = json_decode($_POST['accounts'], true);
    		if (isset($_POST['correctedAmounts']))
    			$model->totalAmount = $_POST['correctedAmounts']['total'];

    		if ($model->validate()) {
    			$parameters = '';
    			foreach ($accounts as $row) {
    				// corregir monto
    				$monto = isset($_POST['correctedAmounts']) ?
    					$_POST['correctedAmounts'][$row['numerocuenta']] :
    					$row['monto'];

    				// reemplazar valor corregido en el formulario
    				if ($row['error'] === 'S') {
    					$row['numerocuenta'] = $_POST['correctedAccounts'][$row['numerocuenta']];
    					if(empty($row['nombrecuenta']))
    						$row['nombrecuenta'] = '';
    				}

    				$format = "%s % 16s % 40s %014d %04d\r\n";
    				$parameters .= sprintf($format, $row['tipo'], $row['numerocuenta'], $row['nombrecuenta'], $monto, $model->entityCode);
    			}
    			$model->parameters = $parameters;

    			$this->_salariesDoRequest($model);
    		}
    	}

    	$this->_renderSalariesForm($model);
    }

    public function actionSalariesConfirm()
    {
    	if (isset($_POST['model'])) {
    		$model = new SalaryPaymentForm;
    		$model->attributes = json_decode($_POST['model'], true);
    		$model->transactionalKey = $_POST['transactionalKey'];
    		$model->controlMode = 'C';

    		$response = $this->wsClient->salaryPayments($model->attributes);

    		if ($response->error === 'S') {
    			$this->setFlashError($response->descripcionrespuesta);
    			$this->redirect(array('salaries'));
    		}

			// crear un data provider a partir de la respuesta
			$dataProvider = new CArrayDataProvider(
				$response->listacuentas->array,
				array(
					'keyField'		=> false,
					'pagination'	=> false,
				)
			);

    		$this->render('/payments/salaries/successConfirm', array(
				'model' => $model,
				'dataProvider' => $dataProvider,
				'authNeeded' => $response->cantidadautorizadores,
			));
    	} else {
    		throw new CHttpException(404, "No encontrado");
    	}
    }

    private function _renderSalariesManualLoadingForm($model)
    {
    	// Forzar modo de carga manual
		$model->controlMode = 'L';

		for ($i=0; $i < $model->recordsReadNumber; $i++) { 
			$param = new SalaryPaymentParametersForm;
			$param->id = $i+1;
			$rows[] = $param;
		}

		$dataProvider = new CArrayDataProvider(
			$rows,
			array(
				'pagination'	=> false,
			)
		);

		$this->render('/payments/salaries/manualLoading', array(
			'model'	=> $model,
			'dataProvider'	=> $dataProvider,
		));

		Yii::app()->end();
    }

    private function _salariesDoRequest($model)
    {
    	$response = $this->wsClient->salaryPayments($model->attributes);

		// Si se recibe error y no está seteado cantidadcuentas
		// se redirige al formulario
		if ($response->error === 'S' && !isset($response->cantidadcuentas)) {
			$this->setFlashError($response->descripcionrespuesta);
			$this->redirect(array('salaries'));
		}

		// crear un data provider a partir de la respuesta
		$dataProvider = new CArrayDataProvider(
			$response->listacuentas->array,
			array(
				'keyField'		=> false,
				'pagination'	=> false,
			)
		);

		// si hubo error en alguna fila renderizar pantalla de chequeo
		if ($response->error === 'S') {
			$this->render('/payments/salaries/checkError', array(
				'model' => $model,
				'dataProvider' => $dataProvider,
				'amountError' => $response->nombrerespuesta === 'SAL_MTO_TOT_INV',
				'amountErrorMsg' => $response->descripcionrespuesta,
			));
		} else {
			// Si no hubo error renderizar pantalla para confirmacion
			$this->render('/payments/salaries/verification', array(
				'model' => $model,
				'dataProvider' => $dataProvider,
			));
		}

		Yii::app()->end();
    }

    private function _renderSalariesForm($model)
    {
    	// Entidades para la opcion de empresas
    	//$entities = array(Yii::app()->user->getState('entityCode')=>Yii::app()->user->getState('entityName'));
    	// renderizar vista
    	$form = new TbForm($model->formConfig(), $model);
		$this->render('/payments/salaries/form', array(
			'form' => $form,
		));
    }
}
