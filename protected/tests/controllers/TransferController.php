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
				'keyField'		=> 'accountNumber',
				'pagination'	=> array('pageSize'=>10),
			)
		);

		$this->render('listAccounts', array(
			'mode' => 'list',
			'dataProvider'	=> $dataProvider,
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

		$accountOptions = Yii::app()->user->accounts->getLabelList($filterOpts);
		$gridOptions = Yii::app()->user->accounts->getGridArray($filterOpts);

		$form = new TbForm($model->formConfig($accountOptions, $gridOptions), $model);

		$this->render('form', array(
			'form' => $form,
		));
	}

	public function actionVerify()
	{
		if(isset($_POST['TransferForm'])) {
			$model = new TransferForm;
			$model->attributes = $_POST['TransferForm'];

			if($model->validate()) {
				$creditAccountData = $this->_accountData($model->creditAccount);
				$creditAccount = ($model->isThird) ?
					 $model->thirdCreditAccount :
					 $creditAccountData['accountNumber'];
				
				$debitAccount = $this->_accountData($model->debitAccount);
				$verifTransfer = $this->wsClient->transfers(array(
					'debitAccount'		=> $debitAccount['accountNumber'],
					'amount'			=> $model->amount,
					'creditAccount'		=> $creditAccount,
					'mode'				=> 'V',
					'quotation'			=> $model->creditQuotation,
					'creditAmount'		=> $model->amount,
					'concept'			=> $model->concept,
					'exchangeContract'	=> $model->exchangeContract,
					'creditQuotation'	=> $model->creditQuotation,
					'isThird'			=> $model->isThird ? 'S' : 'N',
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
						'details'	=> $verifTransfer,
						'form'		=> $form,
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
				$creditAccountData = $this->_accountData($model->creditAccount);
				$creditAccount = ($model->isThird) ?
					 $model->thirdCreditAccount :
					 $creditAccountData['accountNumber'];

				$debitAccount = $this->_accountData($model->debitAccount);
				$confirmTransfer = $this->wsClient->transfers(array(
					'debitAccount'		=> $debitAccount['accountNumber'],
					'amount'			=> $model->amount,
					'creditAccount'		=> $creditAccount,
					'mode'				=> 'C',
					'quotation'			=> $model->creditQuotation,
					'creditAmount'		=> $model->amount,
					'concept'			=> $model->concept,
					'exchangeContract'	=> $model->exchangeContract,
					'creditQuotation'	=> $model->creditQuotation,
					'transactionalKey'	=> $model->transactionalKey,
					'isThird'			=> $model->isThird ? 'S' : 'N',
				));

				if($confirmTransfer->error === 'S') {
					$this->setFlashError($confirmTransfer->descripcionrespuesta);
					$this->redirect(array('/transfer/form'));
				} else {
					// transferencia exitosa
					$this->setFlashSuccess($confirmTransfer->descripcionrespuesta);
					Yii::app()->user->accounts->refresh();
					$this->redirect(array('/site/index'));
				}
			} else {
				$this->setFlashError('Error de validación');
				$this->redirect(array('/transfer/form'));
			}

		} else
			throw new CHttpException(404, Yii::t('commons', 'Página no encontrada'));
	}
}
