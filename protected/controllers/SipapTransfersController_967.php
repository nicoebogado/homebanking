<?php
Yii::import('booster.widgets.TbForm');
Yii::import('application.models.SIPAP.TransferForm');

class SipapTransfersController extends Controller
{

    private $_initialParameterization;
    private $_entidades;
    private $_listadocumentos;
    private $_listadiashabiles;
    private $_listamotivossipap;
    private $_listatiposipap;
    private $_listafrecuentes;

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

    public function actionTransfer($id = null)
    {

        $this->getInitialParameterization();
        $model = new TransferForm;
        $model->transferType = $id;
        $model->actionType = 'verificar';

        $filterOpts = array(
            'conditions' => array(
                'accountType' => 'AH, CC',
            ),
        );
        $accountOptions = Yii::app()->user->accounts->getLabelBalanceList($filterOpts);
        $gridOptions = Yii::app()->user->accounts->getGridArray($filterOpts);

        $form = new TbForm($model->formConfig(
            $accountOptions,
            $gridOptions,
            $this->_entidades,
            $this->_listadocumentos,
            $this->_listamotivossipap
        ), $model);

        $wizardOptions = array(
            array(
                'title' => Yii::t('sipapTransfer', 'Cuenta Origen'),
                'subtitle' => Yii::t('sipapTransfer', 'Seleccione la cuenta de débito'),
                'elements' => array('debitAccount', 'transferType', 'actionType'),
            ),
            array(
                'title' => Yii::t('sipapTransfer', 'Datos de la Transferencia'),
                'subtitle' => Yii::t('sipapTransfer', 'Ingrese los datos'),
                'elements' => array('currency', 'amount', 'date', 'charges', 'reason', 'concept', 'hasAgreement', 'exchangeContract', 'creditQuotation'),
            ),
            array(
                'title' => Yii::t('transfers', 'Datos del Beneficiario'),
                'subtitle' => Yii::t('transfers', 'Ingrese los datos'),
                'elements' => array('financialEntity', 'creditAccount', 'name', 'address', 'documentType', 'documentData', 'saveFrequentData'),
            ),
            array(
                'title' => Yii::t('sipapTransfer', 'Resumen'),
                'subtitle' => Yii::t('sipapTransfer', 'Verifique los datos de la transferencia'),
                'view' => 'transfers/_wizardReview',
            ),
        );

        $this->render('transfers/form', array(
            'form' => $form,
            'wizardOptions' => $wizardOptions,
            'frequentAccounts' => $this->_listafrecuentes,
            'entities' => $this->_entidades,
            'tipo' => $id,
        ));
    }

    public function actionVerify()
    {
        $model = new TransferForm;
        if (isset($_POST['TransferForm'])) {
            $model->attributes = $_POST['TransferForm'];

            if ($_POST['TransferForm']['actionType'] === 'verificar') {
                $model->debitAccount    = $_POST['TransferForm']['debitAccount'];
                $model->debitAccount    = $this->_accountData($model->debitAccount);
                $model->debitAccount    = $model->debitAccount['accountNumber'];
                $model->date            = $this->_formatDate($_POST['TransferForm']['date']);
                $this->_renderVerifyForm($model);
            } elseif ($_POST['TransferForm']['actionType'] === 'confirmar') {
                $model->scenario = 'confirm';
                if ($model->validate()) {
                    // poner otro if para que el mensaje "Error de validación"
                    // se muestre solamente si el modelo no es válido
                    if (!$this->_confirmTransaction($model)) {
                        $this->_renderVerifyForm($model);
                    }
                } else {
                    $this->setFlashError(array_values($model->getErrors())[0][0]);
                }

                $this->_renderVerifyForm($model);
            } else {
                $this->redirect(array('sipapTransfers/transfer/' . $model->transferType));
            }
        } else {
            throw new CHttpException(404, Yii::t('commons', 'Página no encontrada'));
        }
    }

    public function actionResendSms()
    {
        $response = Yii::app()->detectId->outOfBandSmsService->retrieveNewOTP([
            'sharedKey' => Yii::app()->user->getState('sharedKey'),
        ])->WSRetrieveOtpResult;

        if ($response->resultCode === 1020) {
            $this->wsClient->getSendMessage([
                'message' => 'FIC S.A. Informa: Su codigo de seguridad para confimar la operacion es ' . $response->otp,
            ]);
        }
    }

    private function _renderVerifyForm($model)
    {
        try {
            $verifyTransfer = $this->wsClient->snpTransfer(array(
                "mode"                      => 'V',
                "debitAccount"              => $model->debitAccount,
                "currencyCredit"            => $model->currency,
                "creditAmount"              => $this->_formatAmount($model->amount),
                "transferDate"              => $model->date,
                "charges"                   => 'S',
                "creditAccount"             => HCrypt::decrypt($model->creditAccount),
                "swiftCode"                 => $model->financialEntity,
                "creditDocument"            => HCrypt::decrypt($model->documentData),
                "beneficiaryName"           => $model->name,
                "beneficiaryAddress"        => $model->address,
                "typeBeneficiaryDocument"   => $model->documentType,
                "contract"                  => $model->exchangeContract,
                "concept"                   => $model->concept,
                "ipNumber"                  => $_SERVER['REMOTE_ADDR'],
                "quotation"                 => $model->creditQuotation,
                "messageType"               => $model->transferType,
                "tuition"                   => NULL,
                "reason"                    => $model->reason,
            ));
            if ($verifyTransfer->error === 'S') {
                $this->setFlashError($verifyTransfer->descripcionrespuesta);
                $this->redirect(array('sipapTransfers/transfer/' . $model->transferType));
            } else {
                if ($model->saveFrequentData === 'S') {
                    $response = $this->wsClient->frequentTransferSnp(
                        array(
                            'mode' => 'G',
                            'debitAccount' => $model->debitAccount,
                            'currencyCredit' => 'GS',
                            'creditAmount' => $this->_formatAmount($model->amount),
                            'creditAccount' => HCrypt::decrypt($model->creditAccount),
                            'swiftCode' => $model->financialEntity,
                            'creditDocument' => HCrypt::decrypt($model->documentData),
                            'beneficiaryName' => $model->name,
                            'beneficiaryAddress' => $model->address,
                            'typeBeneficiaryDocument' => $model->documentType,
                            'contractNumber' => null,
                        )
                    );
                }
                $model->scenario = 'confirm';
                $model->actionType = 'confirmar';
                $form = new TbForm($model->hiddenFormConfig(), $model);
                $this->render('transfers/confirm', array(
                    'details'   => $verifyTransfer,
                    'form'      => $form,
                ));
            }
        } catch (Exception $e) {
            $this->setFlashError('No se pudo verificar los datos de la transferencia');
            $this->redirect(array('sipapTransfers/transfer/' . $model->transferType));
        }
    }

    private function _confirmTransaction($model)
    {
        try {
            $verifyTransfer = $this->wsClient->snpTransfer(array(
                "mode"                      => 'C',
                "debitAccount"              => $model->debitAccount,
                "currencyCredit"            => $model->currency,
                "creditAmount"              => $this->_formatAmount($model->amount),
                "transferDate"              => $model->date,
                "charges"                   => $model->charges,
                "creditAccount"             => HCrypt::decrypt($model->creditAccount),
                "swiftCode"                 => $model->financialEntity,
                "creditDocument"            => HCrypt::decrypt($model->documentData),
                "beneficiaryName"           => $model->name,
                "beneficiaryAddress"        => $model->address,
                "typeBeneficiaryDocument"   => $model->documentType,
                "contract"                  => $model->exchangeContract,
                "concept"                   => $model->concept,
                "ipNumber"                  => $_SERVER['REMOTE_ADDR'],
                "quotation"                 => $model->creditQuotation,
                "messageType"               => $model->transferType,
                "tuition"                   => NULL,
                "reason"                    => $model->reason,
            ));

            if ($verifyTransfer->error === 'S') {
                $this->setFlashError($verifyTransfer->descripcionrespuesta);
            } else {
                $this->setFlashSuccess($verifyTransfer->descripcionrespuesta);
                Yii::app()->user->accounts->refresh();
                $this->redirect(array('site/index'));
                return true;
            }
        } catch (Exception $e) {
            $this->setFlashError('No se pudo confirmar la transferencia');
            $this->redirect(array('sipapTransfers/transfer/' . $model->transferType));
        }

        return false;
    }

    public function actionDeleteFrequent()
    {
        $formID = $_POST['id'];
        try {
            $response = $this->wsClient->frequentTransferSnp(
                array(
                    'mode' => 'E',
                    'debitAccount' => null,
                    'currencyCredit' => null,
                    'creditAmount' => null,
                    'creditAccount' => null,
                    'swiftCode' => null,
                    'creditDocument' => null,
                    'beneficiaryName' => null,
                    'beneficiaryAddress' => null,
                    'typeBeneficiaryDocument' => null,
                    'contractNumber' => $formID,
                )
            );
            if ($response->error === 'S') {
                echo $response->descripcionrespuesta;
            } else {
                echo 'OK*Transferencia Frecuente eliminada exitosamente!*' . $formID;
            }
        } catch (Exception $e) {
            echo 'Ha ocurrido un error, vuelva a intentarlo mas tarde';
        }
        Yii::app()->end();
    }

    public function getInitialParameterization()
    {

        $params = $this->wsClient->initialParameterizationSnp();
        $this->_initialParameterization = json_decode(json_encode($params), true);

        $entidades = isset($this->_initialParameterization['listaentidades']['array']) ? $this->_initialParameterization['listaentidades']['array'] : array();
        $this->_entidades = array_combine($this->array_column($entidades, 'codigoswift'), $this->array_column($entidades, 'participante'));

        $listadocumentos = isset($this->_initialParameterization['listadocumentos']['array']) ? $this->_initialParameterization['listadocumentos']['array'] : array();
        $this->_listadocumentos = array_combine($this->array_column($listadocumentos, 'codigodocumento'), $this->array_column($listadocumentos, 'descripcion'));

        $listamotivossipap = isset($this->_initialParameterization['listamotivossipap']['array']) ? $this->_initialParameterization['listamotivossipap']['array'] : array();
        $this->_listamotivossipap = array_combine($this->array_column($listamotivossipap, 'codigomotivo'), $this->array_column($listamotivossipap, 'motivo'));

        $listadiashabiles = isset($this->_initialParameterization['listadiashabiles']) ? $this->_initialParameterization['listadiashabiles'] : array();
        $this->_listadiashabiles = isset($listadiashabiles['array']) ? $listadiashabiles['array'] : array();

        $listatiposipap = isset($this->_initialParameterization['listatiposipap']) ? $this->_initialParameterization['listatiposipap'] : array();
        $this->_listatiposipap = isset($listatiposipap['array']) ? $listatiposipap['array'] : array();

        $listafrecuentes = isset($this->_initialParameterization['listaplanillas']) ? $this->_initialParameterization['listaplanillas'] : array();
        $this->_listafrecuentes = isset($listafrecuentes['array']) ? $listafrecuentes['array'] : array();
    }

    public function actionTransfersDetails()
    {

        ///////////  Evita que el error de cache con el backbutton de los browsers
        header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
        header("Pragma: no-cache"); //HTTP 1.0
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        ///////////

        if (isset($_POST) && !empty($_POST)) {
            $fromDate = $_POST['fromDate'];
            $toDate = $_POST['toDate'];
        } else {
            $fromDate = DateTime::createFromFormat('d/m/Y', date('d/m/Y'))->format('d/m/Y');
            $toDate = DateTime::createFromFormat('d/m/Y', date('d/m/Y'))->format('d/m/Y');
        }

        try {

            $details = $this->wsClient->snpTransfers(array(
                "fromDate" => $fromDate,
                "toDate" => $toDate,
            ));

            $transfers = isset($details->listatransferencias->array) ? $details->listatransferencias->array : array();

            $transfersSended = array_filter($transfers, function ($obj) {
                if (isset($obj->tipo) && $obj->tipo == 'E') {
                    return true;
                } else {
                    return false;
                }
            });

            $transfersReceived = array_filter($transfers, function ($obj) {
                if (isset($obj->tipo) && $obj->tipo == 'R') {
                    return true;
                } else {
                    return false;
                }
            });

            $futureTransfers = array_filter($transfers, function ($obj) {
                if (isset($obj->tipo) && $obj->tipo == 'F') {
                    return true;
                } else {
                    return false;
                }
            });

            $dataProviderTransfers = new CArrayDataProvider(
                $transfersSended,
                array(
                    'keyField' => 'numeroreferencia',
                    'pagination'    => array('pageSize' => 20),
                )
            );

            $dataProviderTransfersReceived = new CArrayDataProvider(
                $transfersReceived,
                array(
                    'keyField' => 'numeroreferencia',
                    'pagination'    => array('pageSize' => 20),
                )
            );

            $dataProviderFutureTransfers = new CArrayDataProvider(
                $futureTransfers,
                array(
                    'keyField' => 'numeroreferencia',
                    'pagination'    => array('pageSize' => 20),
                )
            );

            $this->render('transfers/details', array(
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'dataProvider1' => $dataProviderTransfers,
                'dataProvider2' => $dataProviderFutureTransfers,
                'dataProvider3' => $dataProviderTransfersReceived,
                'list' => $futureTransfers,
            ));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionCancellations()
    {
        try {
            $details = $this->wsClient->snpFutureTransfer();
            $futureTransfers = isset($details->listatransferencias->array) ? $details->listatransferencias->array : array();

            $dataProviderFutureTransfers = new CArrayDataProvider(
                $futureTransfers,
                array(
                    'keyField' => 'numeroreferencia',
                    'pagination'    => array('pageSize' => 20),
                )
            );

            $this->render('transfers/cancellations', array(
                'dataProvider2' => $dataProviderFutureTransfers,
                'list' => $futureTransfers,
                'stage' => 'list',
            ));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionCancellation($id = null)
    {

        try {
            $response = $this->wsClient->cancelationSnp(array(
                'mode' => 'V',
                'referenceNumber' => $id,
            ));

            if ($response->error === 'S') {
                $this->setFlashError($response->descripcionrespuesta);
                $this->redirect(array('sipapTransfers/cancellations/'));
            } else {
                $data = $response->listatransferencias;
                $data = $data->array[0];
                $this->render('transfers/cancellations', array(
                    'data' => $data,
                    'stage' => 'detail',
                ));
            }
        } catch (Exception $e) {
            $this->setFlashError('No se pudo cancelar la transferencia');
            $this->redirect(array('sipapTransfers/cancellations/'));
        }
    }

    public function actionConfirmCancellation($id = null)
    {
        try {
            $response = $this->wsClient->cancelationSnp(array(
                'mode' => 'C',
                'referenceNumber' => $id,
            ));
            if ($response->error === 'S') {
                $this->setFlashError($response->descripcionrespuesta);
                $this->redirect(array('sipapTransfers/cancellations/'));
            } else {
                $this->setFlashSuccess($response->descripcionrespuesta);
                $this->redirect(array('sipapTransfers/cancellations/'));
            }
        } catch (Exception $e) {
            $this->setFlashError('No se pudo cancelar la transferencia');
            $this->redirect(array('sipapTransfers/cancellations/'));
        }
    }

    public function actionDetail($id = null)
    {
        try {
            $response = $this->wsClient->detailSnp(array(
                'referenceNumber' => $id,
            ));
            if ($response->error === 'S') {
                $this->setFlashError($response->descripcionrespuesta);
                $this->redirect(array('sipapTransfers/TransfersDetails/'));
            } else {
                $data = $response->listatransferencias;
                $data = $data->array[0];
                $this->render('transfers/detail', array(
                    'data' => $data,
                ));
            }
        } catch (Exception $e) {
            $this->setFlashError('No se pudo cancelar la transferencia');
            $this->redirect(array('sipapTransfers/TransfersDetails/'));
        }
    }

    public function actionVoucher($id = null)
    {
        try {
            $response = $this->wsClient->detailSnp(array(
                'referenceNumber' => $id,
            ));
            if ($response->error === 'S') {
                $this->setFlashError($response->descripcionrespuesta);
                $this->redirect(array('sipapTransfers/TransfersDetails/'));
            } else {
                $data = $response->listatransferencias;
                $data = $data->array[0];
                $this->render('transfers/voucher', array(
                    'data' => $data,
                ));
            }
        } catch (Exception $e) {
            $this->setFlashError('No se pudo cancelar la transferencia');
            $this->redirect(array('sipapTransfers/TransfersDetails/'));
        }
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

    // cambia una fecha tipo dd/mm/yyyy a mm-dd-yyyy
    private function _formatDate($date)
    {
        list($d, $m, $y) = explode('/', $date);

        return $m . '-' . $d . '-' . $y;
    }

    private function _formatAmount($amount)
    {
        $amount = str_replace('.', '', $amount); // eliminar todos los separadores de miles
        $amount = str_replace(',', '.', $amount); // cambiar el separador decimal (,) por un punto

        return $amount;
    }

    public function encryptAccountDatas($account)
    {
        return HCrypt::encrypt(json_encode([
            'cuentabeneficiario'    => $account['cuentabeneficiario'],
            'tipodocbeneficiario' => (isset($account['tipodocbeneficiario']) ?
                $account['tipodocbeneficiario'] : ''),
            'numerodocbeneficario' => (isset($account['numerodocbeneficario']) ?
                $account['numerodocbeneficario'] : ''),
            'nombrebeneficiario' => (isset($account['nombrebeneficiario']) ?
                $account['nombrebeneficiario'] : ''),
            'direccionbeneficiario' => (isset($account['direccionbeneficiario']) ?
                $account['direccionbeneficiario'] : ''),
            'swiftbeneficiario' => (isset($account['swiftbeneficiario']) ?
                $account['swiftbeneficiario'] : ''),
        ]));
    }
}
