<?php

class TestController extends Controller
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
				'actions'=>array('test'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionTest($id=null)
	{

		//$test = $this->wsClient->cardPayment('4236224800000020','0107000024','C',4000,4000);
		//$test = $this->wsClient->getLoanFees('0166000396');
		//$test = $this->wsClient->getLoanFees('0108001059');
		//$test = $this->wsClient->requestCheckbook('0410000515', 1, 50, 1, 'V');
		$test = $this->wsClient->getOffices();
		$this->render('test', array('test'=>$test));
	}
}