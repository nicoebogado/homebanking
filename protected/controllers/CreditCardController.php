<?php

Yii::import('booster.widgets.TbForm');

class CreditCardController extends Controller
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

    public function actionExtracts()
    {
        $model = new CreditCardBalanceForm;

        if (isset($_POST['CreditCardBalanceForm'])) {
            $model->attributes = $_POST['CreditCardBalanceForm'];

            if ($model->validate()) {
                $account = $this->_accountData($model->account);
                $ccNum = $account['creditCardNumber'];
                $balance = Yii::app()->creditCardClient->lastStatement($ccNum);

                if ($balance->Codretorno === '00') {
                    if (count(get_object_vars($balance->Sdtpv_lineasdetalle)) > 0){

                       $datas = $balance->Sdtpv_lineasdetalle->SDTPV_LineasDetalleItem; 

                    }else{

                        $datas = [];

                    }
                    
                    $dataProvider = new CArrayDataProvider($datas, [
                        'keyField'      => false,
                        'pagination'    => false,
                    ]);

                    // obtener periodos anteriores
                    $previousPeriods = Yii::app()->creditCardClient->previousPeriods($ccNum);

                    $this->render('lastStatement', compact('dataProvider', 'previousPeriods', 'balance'));
                    Yii::app()->end();
                } else {
                    $this->setFlashError($balance->Msgretorno);
                }
            }
        }

        $accounts = Yii::app()->user->accounts->getGridArray(array(
            'conditions' => array(
                'accountType' => 'TJ',
            ),
        ), 'TA');
        $form = new TbForm($model->formConfig($accounts), $model);

        $this->render('balanceForm', array('form' => $form));
    }

    public function actionPreviousPeriod()
    {
        if (Yii::app()->request->isPostRequest) {
            $balance = Yii::app()->creditCardClient->previousStatement($_POST['account'], $_POST['period']);

            if ($balance->Codretorno === '00') {
                $this->_renderStatement($balance);
                Yii::app()->end();
            } else {
                $this->setFlashError($balance->Msgretorno);
                $balance = Yii::app()->creditCardClient->lastStatement($_POST['account']);
                $this->_renderStatement($balance);
            }
        } else {
            throw new CHttpException(404, 'La página no existe');
        }
    }

    public function actionPreviousSummary($id = null)
    {
        $account = $this->_accountData($id);
        //generateSummary($month,$year)
    }

    public function actionCurrentSummary($id = null)
    {
        $account = $this->_accountData($id);
        $month = date('m');
        $year = date('Y');
        $this->generateSummary($month, $year, $id);
    }

    public function actionCurrentBalance($id = null)
    {
        $date = date('d-m-Y');
        $this->generateBalance($date, $id);
    }

    public function actionYesterdayBalance($id = null)
    {
        $date = date('d-m-Y', strtotime(date('Y-m-d H:i:s') . ' -1 day'));
        $this->generateBalance($date, $id);
    }

    public function generateSummary($month, $year, $id)
    {

        /*$result=$this->wsClient->BancardParameters(array(
			'operationType' => '1',
			'applicationType' => '1',
		));
		*/
        //var_dump($result);
        //exit;


        $account = $this->_accountData($id);
        $this->render('summary', array(
            'month' => $month,
            'year'    => $year,
            'account' => $account,
        ));
    }

    public function generateBalance($date, $id)
    {
        $account = $this->_accountData($id);
        $data = array(
            '1' =>  'uno',
            '2' =>  'dos'
        );

        $this->render('balance', array(
            'date' => $date,
            'data'    => $data,
            'account' => $account,
        ));

        Yii::app()->end();
    }

    private function _renderStatement($balance)
    {
        $datas = isset($balance->Sdtpv_lineasdetalle->SDTPV_LineasDetalleItem) ?
            $balance->Sdtpv_lineasdetalle->SDTPV_LineasDetalleItem : [];
        $dataProvider = new CArrayDataProvider($datas, [
            'keyField'      => false,
            'pagination'    => false,
        ]);

        // obtener periodos anteriores
        $previousPeriods = Yii::app()->creditCardClient->previousPeriods($balance->Nrotarjeta);

        $this->render('previousStatement', compact('dataProvider', 'previousPeriods', 'balance'));
    }
}
