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

    public function actionLoan($id = null)
    {

        if ($id === null) { // renderizar lista de cuentas AH
            $accounts = Yii::app()->user->accounts->getList(array(
                'conditions' => array(
                    'accountType' => 'PT',
                ),
            ));

            $dataProvider = new CArrayDataProvider(
                $accounts,
                array(
                    'keyField'      => 'accountNumber',
                    'pagination'    => array('pageSize' => 10),
                )
            );

            $this->render('loan', array(
                'mode' => 'list',
                'dataProvider'  => $dataProvider,
            ));
        } else {

            $model = new LoanPaymentForm;
            $loanDatas = $this->_accountData($id);

            if (isset($_POST['LoanPaymentForm'])) {
                $model->attributes = $_POST['LoanPaymentForm'];
                try {
                    $debitAccount = $this->_accountData($model->debitAccount);
                } catch (CHttpException $e) {
                    $model->debitAccount = null;
                }

                // Si es un método post (if de arriba) y no se recibe confirm
                // o si se recibe confirm pero _confirmLoanTransaction devuelve false
                // entonces renderizar formulario de verificación
                if (
                    (empty($_POST['LoanPaymentForm']['confirm']) && $model->validate()) ||
                    !$this->_confirmLoanTransaction($model, $loanDatas, $debitAccount)
                ) {
                    $this->_renderLoanConfirmForm($model, $loanDatas['accountNumber'], $debitAccount['accountNumber'], $id);
                }
            }

            // Obtener cuotas de prestamo
            $loanFees = $this->wsClient->getLoanFees(array(
                'loanNumber' => $loanDatas['accountNumber'],
            ));

            if ($loanFees->error === 'S') {
                $this->setFlashError($loanFees->descripcionrespuesta);
                $this->redirect(array('/payments/loan'));
            } else {
                $accountOptions = Yii::app()->user->accounts->getGridArray(array(
                    'conditions' => array(
                        '__operType__' => '&&',
                        'accountType' => 'AH',
                        'currency' => $loanDatas['currency'],
                    ),
                ));

                /* Si no se obtuvieron cuentas desde donde pagar
                 * (por ejemplo cuando no existen cuentas de la misma moneda del prestamo)
                 * redirigir con un mensaje
                 */
                if (empty($accountOptions['datas'])) {
                    $this->setFlashError(Yii::t('loanPayment', 'No posee cuentas de la misma moneda para realizar el pago'));
                    $this->redirect(array('loan'));
                }

                $form = new TbForm($model->formConfig($accountOptions), $model);

                $loanFees = $loanFees->cantidadcuotas > 0 ? $loanFees->listacuotas->array : array();
                $dataProvider = new CArrayDataProvider(
                    $loanFees,
                    array(
                        'keyField'      => 'numerocuota',
                        'pagination'    => array('pageSize' => 10),
                    )
                );

                $this->render('loan', array(
                    'mode'          => 'form',
                    'form'          => $form,
                    'dataProvider'  => $dataProvider,
                    'accDenomination' => $loanDatas['denomination'],
                    'accountNumber' => ((Yii::app()->params['maskedAccountNumber'] == 'N') ?
                        $loanDatas['accountNumber'] :
                        $loanDatas['maskedAccountNumber']),
                ));
            }
        }
    }

    private function _renderLoanConfirmForm($model, $creditAccount, $debitAccount, $id = null)
    {
        $model->scenario = 'confirm';
        $model->loanNumber = $id;
        $model->confirm = true;

        $verifPayment = $this->wsClient->loanPayment(array(
            'loanNumber'    => $creditAccount,
            'debitAccount'  => $debitAccount,
            'feesAmount'    => $model->feesAmount,
            'mode'          => 'V',
        ));

        if ($verifPayment->error === 'S') {
            if ($verifPayment->nombrerespuesta == '262') {
                // el error corresponde a nro de cuotas ingresado sobrepasa el nro de cuotas vigente
                $model->addError('feesAmount', $verifPayment->descripcionrespuesta);
            } else {
                $this->setFlashError($verifPayment->descripcionrespuesta);
                $this->redirect(array('/payments/loan', "id" => $id));
            }
        } else { // verificacion exitosa
            $model->operationAmount = $verifPayment->montooperacion;
            // configurar formulario oculto
            $form = new TbForm($model->hiddenFormConfig(), $model);
            $this->render('loan', array(
                'mode'      => 'verification',
                'details'   => $verifPayment,
                'form'      => $form,
            ));
            Yii::app()->end();
        }
    }

    private function _confirmLoanTransaction($model, $loanDatas, $debitAccount)
    {
        $model->attributes = $_POST['LoanPaymentForm'];
        $model->scenario = 'confirm';

        if (!$model->validate()) return false;

        $confirmPayment = $this->wsClient->loanPayment(array(
            'loanNumber' => $loanDatas['accountNumber'],
            'debitAccount' => $debitAccount['accountNumber'],
            'feesAmount' => $model->feesAmount,
            'mode' => 'C',
            'operationAmount' => $model->operationAmount,
        ));

        if ($confirmPayment->error === 'S') {
            $this->setFlashError($confirmPayment->descripcionrespuesta);
            return false;
        } else {
            // pago exitoso
            $this->setFlashSuccess($confirmPayment->descripcionrespuesta);
            Yii::app()->user->accounts->refresh();
            $this->render('voucher', array(
                'debitAccount' => ((Yii::app()->params['maskedAccountNumber'] == 'N') ?
                    $debitAccount['accountNumber'] :
                    $debitAccount['maskedAccountNumber']),
                'denomination' => $debitAccount['denomination'],
                'currency' => $debitAccount['currency'],
                'operationAmount' => $model->operationAmount,
                'transactionId' =>   $confirmPayment->numerodocumento,
                'creditAccount' => ((Yii::app()->params['maskedAccountNumber'] == 'N') ?
                    $loanDatas['accountNumber'] :
                    $loanDatas['maskedAccountNumber']),
                'type' => 'PT',
            ));
            Yii::app()->end();
        }
    }

    /**
     * Action for credit card payment
     * @param $id string Nro de la tarjeta a pagar
     */
    public function actionCreditCard($id = null)
    {
        if ($id === null) {
            $cards = Yii::app()->user->accounts->getList(array(
                'conditions' => array(
                    'accountType' => 'TJ'
                ),
            ));

            $dataProvider = new CArrayDataProvider(
                $cards,
                array(
                    'keyField'      => 'accountNumber',
                    'pagination'    => array('pageSize' => 10),
                )
            );

            $this->render('creditCard', array(
                'mode' => 'list',
                'dataProvider'  => $dataProvider,
            ));
        } else {
            $model = new CreditCardPaymentForm;
            try {
                $cardDatas              = $this->_accountData($id);
                $details                = $this->wsClient->getAccountDetails(array(
                    'accountNumber' => $cardDatas['accountNumber'],
                ));
                $details                = $details->listacuentas->array[0];
                $model->minPayment      = $details->pagominimo;
                $model->currency        = $details->codigomoneda;
                $model->totalDebt       = $details->monto2;
                $model->closingDebt     = $details->saldocierre;
                $model->closingDate     = $details->fechacierre;
                $model->dueDate         = $details->fechavencimiento;
            } catch (CHttpException $e) {
                $this->setFlashError('No se pudo obtener los datos de la Tarjeta');
                $this->redirect(array('/payments/creditCard'));
            }

            if (isset($_POST['CreditCardPaymentForm'])) {
                $model->attributes = $_POST['CreditCardPaymentForm'];
                if (empty($model->debitAccount)) {
                    $this->setFlashError('Debe seleccionar una cuenta para realizar el débito');
                    $this->redirect(array('/payments/creditCard', 'id' => $id));
                }
                // recuperar el nro de cuenta del debito
                try {
                    $debitAccount = $this->_accountData($model->debitAccount);
                } catch (CHttpException $e) {
                    $this->setFlashError('No se pudo obtener los datos de la cuenta para pago seleccionada');
                    $this->redirect(array('/payments/creditCard'));
                }
                $debitAccount = $this->_accountData($model->debitAccount);

                if (
                    empty($_POST['CreditCardPaymentForm']['confirm']) ||
                    !$model->validate() ||
                    !$this->_confirmCreditCardTransaction($model, $cardDatas, $debitAccount)
                ) {
                    $this->_renderCreditCardVerifyForm($model, $cardDatas['accountNumber'], $debitAccount['accountNumber'], $id);
                }

                Yii::app()->end();
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

    private function _renderCreditCardVerifyForm($model, $creditAccount, $debitAccount, $id)
    {
        $model->scenario = 'verify';
        if ($model->validate()) {
            $verifPayment = $this->wsClient->creditCardPayment(array(
                'creditCardNumber'  => $creditAccount,
                'debitAccount'      => $debitAccount,
                'mode'              => 'V',
                'debitAmount'       => $model->amount,
                'totalPayment'      => $model->amount,
            ));

            if ($verifPayment->error === 'S') {
                $this->setFlashError($verifPayment->descripcionrespuesta);
                $this->redirect(array('/payments/creditCard'));
            } else {
                // verificacion exitosa
                $model->creditAccount = $id;
                $model->confirm = true;
                // configurar formulario oculto
                $form = new TbForm($model->hiddenFormConfig(), $model);
                $this->render('creditCard', array(
                    'mode'      => 'verification',
                    'details'   => $verifPayment,
                    'form'      => $form,
                ));
            }
        }
    }

    private function _confirmCreditCardTransaction($model, $cardDatas, $debitAccount)
    {
        $model->scenario = 'confirm';
        $confirmPayment = $this->wsClient->creditCardPayment(array(
            'creditCardNumber'  => $cardDatas['accountNumber'],
            'debitAccount'      => $debitAccount['accountNumber'],
            'mode'              => 'C',
            'debitAmount'       => $model->amount,
            'totalPayment'      => $model->amount,
        ));

        if ($confirmPayment->error === 'S') {
            $this->setFlashError($confirmPayment->descripcionrespuesta);
            return false;
        } else {
            // pago exitoso
            $this->setFlashSuccess($confirmPayment->descripcionrespuesta);
            Yii::app()->user->accounts->refresh();
            $this->render('voucher', array(
                'debitAccount' => ((Yii::app()->params['maskedAccountNumber'] == 'N') ?
                    $debitAccount['accountNumber'] :
                    $debitAccount['maskedAccountNumber']),
                'denomination' => $debitAccount['denomination'],
                'currency' => $debitAccount['currency'],
                'operationAmount' => $model->amount,
                'transactionId' => $confirmPayment->numerodocumento,
                'creditAccount' => ((Yii::app()->params['maskedAccountNumber'] == 'N') ?
                    $cardDatas['accountNumber'] :
                    $cardDatas['maskedAccountNumber']),
                'type' => 'TJ',
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
            if ($model->validate()) {

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
                $_POST['SalaryPaymentForm'] : // archivo
                (isset($_POST['model']) ?
                    json_decode($_POST['model'], true) : // carga manual/checkerror
                    null);

            if ($model->controlMode === 'V') $model->scenario = 'validatePaymentWithFile';

            if ($model->validate()) {
                // establecer cantidad de cuentas credito
                $model->amountOfCreditAccounts = $model->recordsReadNumber;

                // Carga manual
                if ($model->controlMode === 'L') {
                    $parameters = '';
                    foreach ($_POST['accountNumber'] as $k => $row) {
                        // si no se cargo algun dato volver a renderizar el form
                        if (empty($row) || empty($_POST['amount'][$k])) {
                            $this->_renderSalariesManualLoadingForm($model);
                        }
                        $format = "C % 16s                                          %014d %04d\n";
                        $parameters .= sprintf($format, $row, $_POST['amount'][$k], $model->entityCode);
                    }
                    $model->parameters = $parameters;

                    // Carga por archivo
                } elseif ($model->controlMode === 'V') {

                    $file = CUploadedFile::getInstance($model, 'paymentFile');
                    // eliminar caracteres \r y dejar solamente \n en saltos de linea
                    $model->parameters = str_replace("\r", "", file_get_contents($file->tempName));
                }

                $this->_salariesDoRequest($model);
            }
        }

        $this->_renderSalariesForm($model);
    }

    public function actionSalariesCheckError()
    {
        $model = new SalaryPaymentForm;
        if (Yii::app()->request->requestType === 'POST') {
            $model->attributes = json_decode($_POST['model'], true);
            $accounts = json_decode($_POST['accounts'], true);
            if (isset($_POST['correctedAmounts']))
                $model->totalAmount = $_POST['correctedAmounts']['total'];

            if ($model->validate()) {
                $parameters = '';
                foreach ($accounts as $row) {
                    // corregir monto
                    $monto = isset($_POST['correctedAmounts']) ? $_POST['correctedAmounts'][$row['numerocuenta']] : $row['monto'];

                    // reemplazar valor corregido en el formulario
                    if ($row['error'] === 'S') {
                        if (isset($_POST['correctedAccounts'][$row['numerocuenta']])) {
                            $row['numerocuenta'] = $_POST['correctedAccounts'][$row['numerocuenta']];
                        }
                        if (empty($row['nombrecuenta'])) {
                            $row['nombrecuenta'] = '';
                        }
                    }

                    if (isset($_POST['deletedAccount'][$row['numerocuenta']])) {
                        if ($row['numerocuenta'] != $_POST['deletedAccount'][$row['numerocuenta']]) {
                            //$format = "%s % 16s % 40s %014d %04d\r\n";
                            $format = "%s %13s    %-40s %14d %4d\n";
                            $parameters .= sprintf($format, $row['tipo'], $row['numerocuenta'], $row['nombrecuenta'], $monto, $model->entityCode);
                        } else {
                            $model->totalAmount = intval($model->totalAmount) - intval($monto);
                        }
                    } else {
                        //$format = "%s % 16s % 40s %014d %04d\r\n";
                        $format = "%s %13s    %-40s %14d %4d\n";
                        $parameters .= sprintf($format, $row['tipo'], $row['numerocuenta'], $row['nombrecuenta'], $monto, $model->entityCode);
                    }
                }

                if (isset($_POST['deletedAccount'])) {
                    $count = count($_POST['deletedAccount']);
                    $model->recordsReadNumber = $model->recordsReadNumber - $count;
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
                    'keyField'      => false,
                    'pagination'    => false,
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

        for ($i = 0; $i < $model->recordsReadNumber; $i++) {
            $param = new SalaryPaymentParametersForm;
            $param->id = $i + 1;
            $rows[] = $param;
        }

        $dataProvider = new CArrayDataProvider(
            $rows,
            array(
                'pagination'    => false,
            )
        );

        $this->render('/payments/salaries/manualLoading', array(
            'model' => $model,
            'dataProvider'  => $dataProvider,
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
                'keyField'      => false,
                'pagination'    => false,
            )
        );

        // si hubo error en alguna fila renderizar pantalla de chequeo
        if ($response->error === 'S') {
            $this->setFlashError($response->descripcionrespuesta);
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
        $wsResponse = $this->wsClient->getEntities(array('typeEntity' => 'SU'));

        $entities = array();
        if (isset($wsResponse->listaentesueldo->array)) {
            $e = $wsResponse->listaentesueldo->array;
            $entities = array_combine(
                array_map(function ($row) {
                    return $row->codigoente;
                }, $e),
                array_map(function ($row) {
                    return $row->descripcion;
                }, $e)
            );
        } else {
            // redireccionar al index con mensaje de error
            $this->setFlashError(Yii::t('commons', 'Usted no está vinculado a ninguna entidad para realizar el pago'));
            $this->redirect(array('/site/index'));
        }

        // renderizar vista
        $form = new TbForm($model->formConfig($entities), $model);
        $this->render('/payments/salaries/form', array(
            'form' => $form,
        ));
    }

    //-------------- Pago a proveedores

    public function actionSuppliers()
    {
        $model = new PaymentToSuppliersForm;

        $this->_renderSuppliersForm($model);
    }

    public function actionSupplierVerification()
    {
        $model = new PaymentToSuppliersForm;

        if (isset($_POST['PaymentToSuppliersForm'])) {
            $model->attributes = $_POST['PaymentToSuppliersForm'];
            $acc = $this->_accountData($model->debitAccount);
            $model->debitAccount = $acc['accountNumber'];

            if ($model->validate()) {
                $file = CUploadedFile::getInstance($model, 'paymentFile');
                // eliminar caracteres \r y dejar solamente \n en saltos de linea
                $model->parameters = str_replace("\r", "", file_get_contents($file->tempName));
                // $model->parameters = file_get_contents($file->tempName);

                $model->controlMode = 'V';

                $this->_suppliersDoRequest($model);
            }
        }

        $this->_renderSuppliersForm($model);
    }

    public function actionSuppliersCheckError()
    {
        $model = new PaymentToSuppliersForm;
        $model->scenario = 'checkError';

        if (Yii::app()->request->requestType === 'POST') {
            $model->attributes = json_decode($_POST['model'], true);
            $model->controlMode = 'V';
            $paymentDatas = json_decode($_POST['paymentDatas'], true);

            if ($model->validate()) {
                $parameters = '';
                foreach ($paymentDatas as $row) {

                    if ($row['error'] === 'S') { // reemplazar valor corregido en el formulario
                        if (isset($_POST['correctedAccounts'][$row['numerocuentacredito']])) {
                            $row['numerocuentacredito'] = $_POST['correctedAccounts'][$row['numerocuentacredito']];
                            $row['montocredito'] = $_POST['correctedAmounts'][$row['montocredito']];
                        }
                    }

                    if (isset($_POST['deletedAccount'][$row['numerocuentacredito']])) {
                        if ($row['numerocuentacredito'] != $_POST['deletedAccount'][$row['numerocuentacredito']]) {
                            $format = "% -10s% -20s%s% -15s% -18s% -50s% -3s\n";
                            $parameters .= sprintf($format, $row['numeroorden'], $row['numerofactura'], $row['formapago'], $row['numerocuentacredito'], $row['montocredito'], $row['nombrebeneficiario'], $row['codigomoneda']);
                        }
                    } else {
                        $format = "% -10s% -20s%s% -15s% -18s% -50s% -3s\n";
                        $parameters .= sprintf($format, $row['numeroorden'], $row['numerofactura'], $row['formapago'], $row['numerocuentacredito'], $row['montocredito'], $row['nombrebeneficiario'], $row['codigomoneda']);
                    }
                }

                if (isset($_POST['deletedAccount'])) {
                    $count = count($_POST['deletedAccount']);
                    $model->invoicesNumber = $model->invoicesNumber - $count;
                }

                $model->parameters = trim($parameters, "\n"); // eliminar ultimo salto de linea

                $this->_suppliersDoRequest($model);
            }
        }

        $this->_renderSuppliersForm($model);
    }

    public function actionSuppliersConfirm()
    {
        if (isset($_POST['model'])) {
            $model = new PaymentToSuppliersForm;
            $model->attributes = json_decode($_POST['model'], true);
            $model->transactionalKey = $_POST['transactionalKey'];
            $model->controlMode = 'C';

            $response = $this->wsClient->paymentsToSuppliers($model->attributes);

            if ($response->error === 'S') {
                $this->setFlashError($response->descripcionrespuesta);
                $this->redirect(array('suppliers'));
            }

            // crear un data provider a partir de la respuesta
            $dataProvider = new CArrayDataProvider(
                $response->listaproveedores->array,
                array(
                    'keyField'      => false,
                    'pagination'    => false,
                )
            );

            $this->render('/payments/suppliers/successConfirm', array(
                'model' => $model,
                'dataProvider' => $dataProvider,
                'authNeeded' => $response->cantidadautorizadores,
            ));
        } else {
            throw new CHttpException(404, "No encontrado");
        }
    }

    private function _suppliersDoRequest($model)
    {
        $response = $this->wsClient->paymentsToSuppliers($model->attributes);

        // Si se recibe error y cantidadproveedores viene vacio
        // se redirige al formulario
        if ($response->error === 'S' && empty($response->cantidadproveedores)) {
            $this->setFlashError($response->descripcionrespuesta);
            $this->redirect(array('suppliers'));
        }

        // crear un data provider a partir de la respuesta
        $dataProvider = new CArrayDataProvider(
            $response->listaproveedores->array,
            array(
                'keyField'      => false,
                'pagination'    => false,
            )
        );

        // si hubo error en alguna fila renderizar pantalla de chequeo
        if ($response->error === 'S') {
            $this->setFlashError($response->descripcionrespuesta);
            $this->render('/payments/suppliers/checkError', array(
                'model' => $model,
                'dataProvider' => $dataProvider,
                //'errorMsg' => $response->descripcionrespuesta,
            ));
        } else {
            // Si no hubo error renderizar pantalla para confirmacion
            $this->render('/payments/suppliers/verification', array(
                'model' => $model,
                'dataProvider' => $dataProvider,
            ));
        }

        Yii::app()->end();
    }

    private function _renderSuppliersForm($model)
    {
        // Entidades para la opcion de empresas
        $wsResponse = $this->wsClient->getEntities(array('typeEntity' => 'PR'));

        $entities = array();
        if (isset($wsResponse->listaenteproveedor->array)) {
            $e = $wsResponse->listaenteproveedor->array;
            $entities = array_combine(
                array_map(function ($row) {
                    return $row->codigoente;
                }, $e),
                array_map(function ($row) {
                    return $row->descripcion . ' - #' . $row->numerocuenta;
                }, $e)
            );
        } else {
            // redireccionar al index con mensaje de error
            $this->setFlashError(Yii::t('commons', 'Usted no está vinculado a ninguna entidad para realizar el pago'));
            $this->redirect(array('/site/index'));
        }

        $validAccounts = array();
        foreach ($wsResponse->listaenteproveedor->array as $key => $value) {
            $validAccounts[] = $value->numerocuenta;
        }

        // Opciones para cuentas
        $accountOptions = Yii::app()->user->accounts->getGridArray(array('conditions' => array(
            'accountType' => 'AH',
        )), 1);

        $newArray = array();
        foreach ($accountOptions['datas'] as $key => $value) {
            foreach ($validAccounts as $account) {
                if ($value['accountNumber'] == $account) {
                    $newArray[] = $value;
                    break;
                }
            }
        }
        $accountOptions['datas'] = $newArray;

        $date = $this->wsClient->getDataBaseConnection();
        $date = explode(' ', $date);
        $date = $date[0];
        $date = explode('/', $date);
        $date = date('Y/m/d', strtotime($date[2] . '-' . $date[1] . '-' . $date[0]));

        // renderizar vista
        $form = new TbForm($model->formConfig($entities, $accountOptions, $date), $model);
        $this->render('/payments/suppliers/form', array(
            'form' => $form,
        ));
    }
}
