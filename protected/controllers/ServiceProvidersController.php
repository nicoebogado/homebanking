<?php

Yii::import('booster.widgets.TbForm');

class ServiceProvidersController extends Controller{

  public function actionSelectAccount()
  {

    $model = new ServiceProvidersForm();

    if(isset($_POST['ServiceProvidersForm'])){
      $model->attributes=$_POST['ServiceProvidersForm'];
	    if(!$model->validate()){
        Yii::app()->user->setFlash('error', 'Debe seleccionar una cuenta');
      }else{
        $debitAccount = $this->_accountData($model->debitAccount);
        $debitAccount = $debitAccount['accountNumber'];
        $paymentServices = $this->wsClient->paymentServices(array(
          'controlMode' => 'C',
          'debitAccount' => $debitAccount,
          'transactionalKey' => $model->transactionalKey,
          'ipNumber' => $_SERVER['REMOTE_ADDR'],
        ));
        if ($paymentServices->error === 'S') {
          $this->setFlashError($paymentServices->descripcionrespuesta);
          $this->redirect(array('/serviceProviders/selectAccount'));
        } else {
          Yii::app()->user->setState('pronet','S');
          Yii::app()->user->setState('dbnumerosesion',$paymentServices->numerosesion);
          $this->redirect(array('/serviceProviders/payment/'.$model->debitAccount));
        }
      }
    }

    $accountOptions = Yii::app()->user->accounts->getGridArray(array(
      'conditions' => array(
        '__operType__' => '&&',
        'accountType' => 'AH',
      ),
    ));

    $form = new TbForm($model->formConfig($accountOptions), $model);
    $this->render('account', array(
      'mode' => 'form',
      'form' => $form,
    ));

  }

  public function actionPayment($id = null){
    if($id === null){
      $this->redirect(array('/serviceProviders/selectAccount'));
    }
    $autenticated=Yii::app()->user->getState('pronet');
    if($autenticated=='S'){
      Yii::app()->user->setState('pronet','N');
      $debitAccount = $this->_accountData($id);
      $debitAccount = ((Yii::app()->params['maskedAccountNumber']=='N')?
                                    $debitAccount['accountNumber']:
                                    $debitAccount['maskedAccountNumber']
                                );
      $this->render('payment',array('debitAccount'=>$debitAccount));
    }else{
      $this->redirect(array('/serviceProviders/selectAccount'));
    }
  }

}

?>
