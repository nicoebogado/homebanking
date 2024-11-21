<?php
Yii::import('booster.widgets.TbForm');

class TransferController extends Controller
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

    public function actionListAccounts()
    {
        $accounts = Yii::app()->user->accounts->getList(array(
            'conditions' => array(
                'accountType'=>'AH',
            ),
        ));

        $dataProvider = new CArrayDataProvider(
            $accounts,
            array(
                'keyField'      => 'accountNumber',
                'pagination'    => array('pageSize'=>10),
            )
        );

        $this->render('listAccounts', array(
            'mode' => 'list',
            'dataProvider'  => $dataProvider,
        ));
    }

    public function actionForm()
    {
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
                'title'=>Yii::t('transfers', 'Cuenta Origen'),
                'subtitle'=>Yii::t('transfers', 'Seleccione la cuenta de débito'),
                'elements'=>array('debitAccount'),
            ),
            array(
                'title'=>Yii::t('transfers', 'Cuenta Destino'),
                'subtitle'=>Yii::t('transfers', 'Complete los datos del destinatario'),
                'elements'=>array('isThird', 'creditAccount', 'thirdCreditAccount', 'thirdDocType', 'thirdDocNumber', 'beneficiaryName' ,'saveFrequent'),
            ),
            array(
                'title'=>Yii::t('transfers', 'Monto'),
                'subtitle'=>Yii::t('transfers', 'Establezca el monto y concepto'),
                'elements'=>array('amount', 'concept', 'hasAgreement', 'exchangeContract', 'creditQuotation'),
            ),
            array(
                'title'=>Yii::t('transfers', 'Resumen'),
                'subtitle'=>Yii::t('transfers', 'Verifique los datos de la transferencia'),
                'view'=>'_wizardReview',
            ),
        );

        $frequent=$this->wsClient->gettransfers();

        $this->render('form', array(
            'form' => $form,
            'wizardOptions' => $wizardOptions,
            'frequent'=>$frequent,
        ));
    }

    public function actionVerify()
    {
        if(isset($_POST['TransferForm'])) {
            $model = new TransferForm;
            $model->attributes = $_POST['TransferForm'];

            if($model->validate()) {
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

                if($verifTransfer->error === 'S') {
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

            } else {
                $this->setFlashError('Error de validación');
                $this->redirect(array('/transfer/form'));
            }

        } else
            throw new CHttpException(404, Yii::t('commons', 'Página no encontrada'));

    }

    public function actionConfirm()
    {
        if(isset($_POST['TransferForm'])) {
            $model = new TransferForm;
            $model->scenario = 'confirm';
            $model->attributes = $_POST['TransferForm'];

            if($model->validate()) {
                $creditAccount = ($model->isThird) ?
                    [
                        'accountNumber' => HCrypt::decrypt($model->thirdCreditAccount),
                        'denomination' => $model->beneficiaryName,
                        'document' => $model->thirdDocNumber,
                        'documentType' => $model->thirdDocType,
                    ] :
                    $this->_accountData($model->creditAccount);

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
                    'transactionalKey'  => $model->transactionalKey,
                    'isThird'           => $model->isThird ? 'S' : 'N',
                ));

                if($confirmTransfer->error === 'S') {
                    $this->setFlashError($confirmTransfer->descripcionrespuesta);
                    $this->redirect(array('/transfer/form'));
                } else {
                    if($model->saveFrequent=='1'){
                        $result = $this->wsClient->loadTransfers(array(
                            "mode"=>'G',
                            "debitAccount"=>$debitAccount['accountNumber'],
                            "creditCurrency"=>$debitAccount['currency'],
                            "creditAmount"=>$this->_formatAmount($model->amount),
                            "creditAccount"=>$creditAccount['accountNumber'],
                            "documentCredit"=>$creditAccount['document'],
                            "beneficiaryName"=>$model->beneficiaryName,
                            "beneficiaryAddress"=>'',
                            "typeBeneficiaryDocument"=>$creditAccount['documentType'],
                            "transferNumber"=>'',
                        ));
                    }

                    // transferencia exitosa
                    $this->setFlashSuccess($confirmTransfer->descripcionrespuesta);
                    Yii::app()->user->accounts->refresh();
                    //$this->redirect(array('/site/index'));
                    $this->render('voucher',array(
                        'debitAccount'=>$debitAccount,
                        'operationAmount'=>$model->amount,
                        'creditAccount'=>$creditAccount,
                    ));
                }
            } else {
                $this->setFlashError('Error de validación');
                $this->redirect(array('/transfer/form'));
            }

        } else
            throw new CHttpException(404, Yii::t('commons', 'Página no encontrada'));
    }

    public function actionDeleteFrequent(){
        $formID=$_POST['id'];
        try {
            $response=$this->wsClient->loadTransfers(
                array(
                    "mode"=>'E',
                    "debitAccount"=>'',
                    "creditCurrency"=>'',
                    "creditAmount"=>'',
                    "creditAccount"=>'',
                    "documentCredit"=>'',
                    "beneficiaryName"=>'',
                    "beneficiaryAddress"=>'',
                    "typeBeneficiaryDocument"=>'',
                    "transferNumber"=>$formID,
                )
            );
            if($response->error === 'S'){
                echo $response->descripcionrespuesta;
                    Yii::app()->end();
            }else{
                echo 'OK*Transferencia Frecuente eliminada exitosamente!*'.$formID;
                    Yii::app()->end();
            }
        } catch (Exception $e) {
            echo 'Ha ocurrido un error, vuelva a intentarlo mas tarde';
                Yii::app()->end();
        }
        Yii::app()->end();
    }

    public function actionGetAccountDesc(){
        $accountNumber  = HCrypt::decrypt($_POST['accountNumber']);
        $documentType   = $_POST['documentType'];
        $documentNumber = HCrypt::decrypt($_POST['documentNumber']);
        try {
            $response=$this->wsClient->getDescAccount(
                array(
                    'accountNumber'=>$accountNumber,
                    'documentType'=>$documentType,
                    'documentNumber'=>$documentNumber,
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
        $docTypes = json_decode(json_encode($docTypes),true);
        return array_combine($this->array_column($docTypes, 'codigotipodocumento'),$this->array_column($docTypes, 'descripcion'));
    }

    public function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
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
            'nombrebeneficiario' => isset($account->nombrebeneficiario)?$account->nombrebeneficiario:'',
            'numerodocbeneficario' => isset($account->numerodocbeneficario)?$account->numerodocbeneficario:'',
            'tipodocbeneficiario' => isset($account->tipodocbeneficiario)?$account->tipodocbeneficiario:'',
        ]));
    }
}
