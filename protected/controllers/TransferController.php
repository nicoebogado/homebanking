<?php
Yii::import('booster.widgets.TbForm');

class TransferController extends Controller
{
    private $_cantidadAutorizadores = 0;//Cantidad de autorizadores

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

    public function actionListAccounts()
    {
        $accounts = Yii::app()->user->accounts->getList(array(
            'conditions' => array(
                'accountType' => 'AH',
            ),
        ));

        $dataProvider = new CArrayDataProvider(
            $accounts,
            array(
                'keyField'      => 'accountNumber',
                'pagination'    => array('pageSize' => 10),
            )
        );

        $this->render('listAccounts', array(
            'mode' => 'list',
            'dataProvider'  => $dataProvider,
        ));
    }

    public function actionForm()
    {
        //VALIDACION POR EMPRESA PARA ACCESO DE PERFILES
         //Added:Higinio Samaniego,21/07/2022 ** Validación de empresas 
         $perfil = Yii::app()->user->getState('clientPerfil');
         $codigoempresa = Yii::app()->user->getState('codigoempresa') ? Yii::app()->user->getState('codigoempresa') : null;
         
         if(isset($codigoempresa)){

            if($perfil == 'CONSULTA'){//No posee permisos cuando es perfil consulta en banca empresas
				$this->setFlashError("El perfil no posee priviliegios para la operación requerida.");
                $this->redirect(array('site/index'));
			}
         }       
        
        $model = new TransferForm;

        $filterOpts = array(
            'conditions' => array(
                'accountType' => 'AH',
            ),
        );

        $accountOptions = Yii::app()->user->accounts->getLabelBalanceList($filterOpts);
        $gridOptions = Yii::app()->user->accounts->getGridArray($filterOpts);
        $docTypeOptions = $this->_getDocTypeOptions();

        $form = new TbForm($model->formConfig($accountOptions, $gridOptions, $docTypeOptions), $model);
        $wizardOptions = array(
            array(
                'title' => Yii::t('transfers', 'Cuenta Origen'),
                'subtitle' => Yii::t('transfers', 'Seleccione la cuenta de débito'),
                'elements' => array('debitAccount'),
            ),
            array(
                'title' => Yii::t('transfers', 'Cuenta Destino'),
                'subtitle' => Yii::t('transfers', 'Complete los datos del destinatario'),
                'elements' => array('isThird', 'creditAccount', 'thirdCreditAccount', 'thirdDocType', 'thirdDocNumber', 'beneficiaryName', 'saveFrequent'),
            ),
            array(
                'title' => Yii::t('transfers', 'Monto'),
                'subtitle' => Yii::t('transfers', 'Establezca el monto y concepto'),
                'elements' => array('amount', 'concept', 'hasAgreement', 'exchangeContract', 'creditQuotation'),
            ),
            array(
                'title' => Yii::t('transfers', 'Resumen'),
                'subtitle' => Yii::t('transfers', 'Verifique los datos de la transferencia'),
                'view' => '_wizardReview',
            ),
        );

        $frequent = $this->wsClient->gettransfers();

        $this->render('form', array(
            'form' => $form,
            'wizardOptions' => $wizardOptions,
            'frequent' => $frequent,
        ));
    }

    public function actionVerify()
    {
        if (isset($_POST['TransferForm'])) {
            $model = new TransferForm;
            $model->attributes = $_POST['TransferForm'];

            if ($model->validate()) {
                $this->_renderVerifyForm($model);
            }
        } else {
            throw new CHttpException(404, Yii::t('commons', 'Página no encontrada'));
        }
    }

    public function actionConfirm()
    {
        //Function used for ajax response for token validation
        if(isset($_POST['tokenF']) && !isset($_POST['TransferForm'])){
			
            $resultConfirm = $this->_tokenConfirm($_POST['tokenF']);
            //ResultCode: 801 token valido, 802 token invalido
            echo $resultConfirm;
            exit;
        }
		
        
        if(isset($_POST['TransferForm'])){
			
            $model = new TransferForm;
            $model->attributes = $_POST['TransferForm'];

            if ($model->isThird) {
                $model->scenario = 'confirm';
            }
			
            if ($model->validate() || isset($_POST['TransferForm']['isToken'])) {// If was validate by opt or token.
			
                // poner otro if para que el mensaje "Error de validación"
                // se muestre solamente si el modelo no es válido
                if ($this->_confirmTransaction($model)) {
                    $this->_renderVoucher($model);
                    Yii::app()->end();
                }
            } else {
                $this->setFlashError('Error de validación');
            }

            $this->_renderVerifyForm($model);
        } else {
            throw new CHttpException(404, Yii::t('commons', 'Página no encontrada'));
        }
    }

    public function actionResendSms()
    {
        
        $respuesta = $this->__verificarDetectFic();
        $auxiliar = json_decode($respuesta);
        
        if($auxiliar->result != 'S'){

            $response = Yii::app()->detectId->outOfBandSmsService->retrieveNewOTP([
                'sharedKey' => Yii::app()->user->getState('sharedKey'),
            ])->WSRetrieveOtpResult;

        }else if($auxiliar->result == 'S'){
        
            $codCanal = '999';
            $arg0 = array('sharedKey' => Yii::app()->user->getState('sharedKey'),'codCanal' => $codCanal);
            
            $response = Yii::app()->detectFic->wsotp->generarOtp([
                'arg0' => $arg0,
            ])->return;
        }
        
        $otp = isset($response->otp) ? $response->otp : $response->otpCode;
       
        if ($response->resultCode === 1020 || $response->resultCode == '0') {

            $this->wsClient->getSendMessage([
                'message' => 'FIC S.A. Informa: Su codigo de seguridad para confimar la operacion es ' . $otp,
            ]);

        }
    }

    private function __verificarDetectFic(){
        
        $url = Yii::app()->params->apiVerificarDetectFic;
       
        $documento = Yii::app()->user->getState('documento');
		
		if(Yii::app()->user->getState('empresa') != ''){
			$documento = Yii::app()->user->getState('documentoX');
		}
       
        $fields_string="documento=".$documento;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url."detecFic");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    private function _renderVerifyForm($model)
    {
        //Added:Higinio Samaniego,31/05/2022 ** Validación de empresas 
        $perfil = Yii::app()->user->getState('clientPerfil');
        $codigoempresa = Yii::app()->user->getState('codigoempresa') ? Yii::app()->user->getState('codigoempresa') : null;
        
        if(isset($codigoempresa)){

            if(trim($perfil) == 'AUTORIZA' || trim($perfil) == 'COMPLETO'){

                /***trabajo actual***/
                
                $creditAccount = ($model->isThird) ?
                    HCrypt::decrypt($model->thirdCreditAccount) :
                    $this->_accountData($model->creditAccount)['accountNumber'];
                $debitAccount = $this->_accountData($model->debitAccount);

				
                $verifTransfer = $this->wsClient->transfers(array(
                    'debitAccount'      => $debitAccount['accountNumber'],
                    'amount'            => $this->_formatAmount($model->amount),
                    'creditAccount'     => $creditAccount,
                    'mode'              => 'V',
                    'quotation'         => $model->creditQuotation,
                    'creditAmount'      => $this->_formatAmount($model->amount),
                    'concept'           => $model->concept,
                    'exchangeContract'  => $model->exchangeContract,
                    'creditQuotation'   => $model->creditQuotation,
                    'isThird'           => $model->isThird ? 'S' : 'N',
                ));

				$model->isToken = 'H';
				
                if($verifTransfer->error === 'S'){
                    $this->setFlashError($verifTransfer->descripcionrespuesta);
                    $this->redirect(array('/transfer/form'));
                }else{
                    // verificacion exitosa
                    $model->scenario = 'confirm';
                    $model->confirm = true;
                    $arrayTest = (array)$model;
                    // configurar formulario oculto
                    $form = new TbForm($model->hiddenFormConfig(), $model);
                    
                    $this->render('verifyToken', array(
                        'details'   => $verifTransfer,
                        'form'      => $form,
                        'datos'     => $arrayTest,
                    ));
                }
                /***trabajo actual***/

            }else if($perfil == 'REGISTRA'){
                
                $banderaVaucher = "R";//bandera que indica solo mensaje sin impresion de vaucher
                
                $creditAccount = ($model->isThird) ?
                    HCrypt::decrypt($model->thirdCreditAccount) :
                    $this->_accountData($model->creditAccount)['accountNumber'];
        
                
                $debitAccount = $this->_accountData($model->debitAccount);

                $verifTransfer = $this->wsClient->transfers(array(
                    'debitAccount'      => $debitAccount['accountNumber'],
                    'amount'            => $this->_formatAmount($model->amount),
                    'creditAccount'     => $creditAccount,
                    'mode'              => 'V',
                    'quotation'         => $model->creditQuotation,
                    'creditAmount'      => $this->_formatAmount($model->amount),
                    'concept'           => $model->concept,
                    'exchangeContract'  => $model->exchangeContract,
                    'creditQuotation'   => $model->creditQuotation,
                    'isThird'           => $model->isThird ? 'S' : 'N',
                ));
				
                
                // verificacion exitosa
                $model->scenario = 'confirm';
                $model->confirm = true;

                if ($this->_confirmTransaction($model)) {
                    $this->_renderVoucher($model, $banderaVaucher);
                    Yii::app()->end();
                }

            }else if($perfil == 'CONSULTA'){//No posee permisos cuando es perfil consulta en banca empresas
				$this->setFlashError("El perfil no posee priviliegios para la operación requerida.");
                $this->redirect(array('/transfer/form'));
			}
        }else{//Si es cuenta persona
        
            $creditAccount = ($model->isThird) ?
                HCrypt::decrypt($model->thirdCreditAccount) :
                $this->_accountData($model->creditAccount)['accountNumber'];
            $debitAccount = $this->_accountData($model->debitAccount);

        $verifTransfer = $this->wsClient->transfers(array(
            'debitAccount'      => $debitAccount['accountNumber'],
            'amount'            => $this->_formatAmount($model->amount),
            'creditAccount'     => $creditAccount,
            'mode'              => 'V',
            'quotation'         => $model->creditQuotation,
            'creditAmount'      => $this->_formatAmount($model->amount),
            'concept'           => $model->concept,
            'exchangeContract'  => $model->exchangeContract,
            'creditQuotation'   => $model->creditQuotation,
            'isThird'           => $model->isThird ? 'S' : 'N',
        ));

        if ($verifTransfer->error === 'S') {
            $this->setFlashError($verifTransfer->descripcionrespuesta);
            $this->redirect(array('/transfer/form'));
        } else {
            // verificacion exitosa
            $model->scenario = 'confirm';
            $model->confirm = true;

                // configurar formulario oculto
                $form = new TbForm($model->hiddenFormConfig(), $model);
                
                $this->render('verify', array(
                    'details'   => $verifTransfer,
                    'form'      => $form,
                ));
            }
        }
    }

    private function _confirmTransaction($model)
    {
        $creditAccount = $this->_getCreditAccount($model);
        $debitAccount = $this->_accountData($model->debitAccount);

        $confirmTransfer = $this->wsClient->transfers(array(
            'debitAccount'      => $debitAccount['accountNumber'],
            'amount'            => $this->_formatAmount($model->amount),
            'creditAccount'     => $creditAccount['accountNumber'],
            'mode'              => 'C',
            'quotation'         => $model->creditQuotation,
            'creditAmount'      => $this->_formatAmount($model->amount),
            'concept'           => $model->concept,
            'exchangeContract'  => $model->exchangeContract,
            'creditQuotation'   => $model->creditQuotation,
            'isThird'           => $model->isThird ? 'S' : 'N',
        ));

        if(isset($confirmTransfer->cantidadautorizadores)){
			$this->_cantidadAutorizadores = $confirmTransfer->cantidadautorizadores;
		}
        
        if ($confirmTransfer->error === 'S') {
            $this->setFlashError($confirmTransfer->descripcionrespuesta);
            $this->redirect(array('/transfer/form'));
        } else {

            if ($model->saveFrequent == '1') {
                $this->wsClient->loadTransfers(array(
                    "mode" => 'G',
                    "debitAccount" => $debitAccount['accountNumber'],
                    "creditCurrency" => $debitAccount['currency'],
                    "creditAmount" => $this->_formatAmount($model->amount),
                    "creditAccount" => $creditAccount['accountNumber'],
                    "documentCredit" => $creditAccount['document'],
                    "beneficiaryName" => $model->beneficiaryName,
                    "beneficiaryAddress" => '',
                    "typeBeneficiaryDocument" => $creditAccount['documentType'],
                    "transferNumber" => '',
                ));
            }
            return true;
        }
    }

    private function _renderVoucher($model, $bandera = null)
    {
        $creditAccount = $this->_getCreditAccount($model);
        $debitAccount = $this->_accountData($model->debitAccount);
        $perfil = Yii::app()->user->getState('clientPerfil');

        if(isset($bandera)){
			
            $this->setFlashSuccess('Transferencia registrada exitosamente');
            Yii::app()->user->accounts->refresh();
            $this->render('voucher', array(
                'debitAccount' => $debitAccount,
                'operationAmount' => $model->amount,
                'creditAccount' => $creditAccount,
                'bandera' => $bandera,
            ));
        }else if($this->_cantidadAutorizadores != 0 && $perfil == 'AUTORIZA'){

            $this->setFlashSuccess('Transferencia registrada exitosamente, autorizaciones faltante(s): '.$this->_cantidadAutorizadores);
            Yii::app()->user->accounts->refresh();
            $this->render('voucher', array(
                'debitAccount' => $debitAccount,
                'operationAmount' => $model->amount,
                'creditAccount' => $creditAccount,
                'bandera' => 'R',
            ));

        }else{
			
            $this->setFlashSuccess('La transferencia ha sido exitosa');
            Yii::app()->user->accounts->refresh();
			$this->render('voucher', array(
                'debitAccount' => $debitAccount,
                'operationAmount' => $model->amount,
                'creditAccount' => $creditAccount,
                'bandera' => $bandera,
            ));
            
        }


    }

    public function actionDeleteFrequent()
    {
        $formID = $_POST['id'];
        try {
            $response = $this->wsClient->loadTransfers(
                array(
                    "mode" => 'E',
                    "debitAccount" => '',
                    "creditCurrency" => '',
                    "creditAmount" => '',
                    "creditAccount" => '',
                    "documentCredit" => '',
                    "beneficiaryName" => '',
                    "beneficiaryAddress" => '',
                    "typeBeneficiaryDocument" => '',
                    "transferNumber" => $formID,
                )
            );
            if ($response->error === 'S') {
                echo $response->descripcionrespuesta;
                Yii::app()->end();
            } else {
                echo 'OK*Transferencia Frecuente eliminada exitosamente!*' . $formID;
                Yii::app()->end();
            }
        } catch (Exception $e) {
            echo 'Ha ocurrido un error, vuelva a intentarlo mas tarde';
            Yii::app()->end();
        }
        Yii::app()->end();
    }

    public function actionGetAccountDesc()
    {
        $accountNumber  = HCrypt::decrypt($_POST['accountNumber']);
        $documentType   = $_POST['documentType'];
        $documentNumber = HCrypt::decrypt($_POST['documentNumber']);
        try {
            $response = $this->wsClient->getDescAccount(
                array(
                    'accountNumber' => $accountNumber,
                    'documentType' => $documentType,
                    'documentNumber' => $documentNumber,
                )
            );
            echo $response;
            Yii::app()->end();
        } catch (Exception $e) {
            echo '';
            Yii::app()->end();
        }
        Yii::app()->end();
    }

    private function _formatAmount($amount)
    {
        $amount = str_replace('.', '', $amount); // eliminar todos los separadores de miles
        $amount = str_replace(',', '.', $amount); // cambiar el separador decimal (,) por un punto

        return $amount;
    }

    private function _getDocTypeOptions()
    {
        $docTypes = $this->wsClient->getDocumenttyp();
        $docTypes = $docTypes->informaciondocumentos->array;
        $docTypes = json_decode(json_encode($docTypes), true);
        return array_combine($this->array_column($docTypes, 'codigotipodocumento'), $this->array_column($docTypes, 'descripcion'));
    }

    private function _getCreditAccount($model)
    {
        return $model->isThird ?
            [
                'accountNumber' => HCrypt::decrypt($model->thirdCreditAccount),
                'denomination' => $model->beneficiaryName,
                'document' => HCrypt::decrypt($model->thirdDocNumber),
                'documentType' => $model->thirdDocType,
            ] :
            $this->_accountData($model->creditAccount);
    }

    public function array_column(array $input, $columnKey, $indexKey = null)
    {
        $array = array();
        foreach ($input as $value) {
            if (!isset($value[$columnKey])) {
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!isset($value[$indexKey])) {
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

    public function encryptAccountDatas($account)
    {
        return HCrypt::encrypt(json_encode([
            'cuentabeneficiario' => $account->cuentabeneficiario,
            'nombrebeneficiario' => isset($account->nombrebeneficiario) ? $account->nombrebeneficiario : '',
            'numerodocbeneficario' => isset($account->numerodocbeneficario) ? $account->numerodocbeneficario : '',
            'tipodocbeneficiario' => isset($account->tipodocbeneficiario) ? $account->tipodocbeneficiario : '',
        ]));
    }

    public function _tokenConfirm($token = null){
		
		//COLOCAR VALIDACION DE NUEVO DETECTID FIC AQUI
		$respuesta = $this->__verificarDetectFic();
		$auxiliar = json_decode($respuesta);

			if($auxiliar->result == 'S'){//SI TIENE DETECT DE FIC

				

				$otp = trim($token);
				$codCanal = '999';
				$arg0 = array('sharedKey' => Yii::app()->user->getState('sharedKey'),'codCanal' => $codCanal,'otp' => $otp,'origen' => 'HOMEBANKING');
				
				$response = Yii::app()->detectFic->wsotp->validarOtp([
					'arg0' => $arg0,
				])->return;
			
				return $response->resultCode;

			}else{
				
				$sharekey = Yii::app()->user->getState('sharedKey');
				$responseToken = Yii::app()->detectId->validateDID200->validateDID200(["sharedKey"=>trim($sharekey), "otp"=>trim($token)]);
				
				return $responseToken->easysolTokenValidationResult->resultCode;
			}
        
        
    }
	
	
}
