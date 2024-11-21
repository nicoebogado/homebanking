<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'page'=>array(
				'class'=>'CViewAction',
			),
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
				'foreColor'=>0x000000,
				'testLimit'=>1,
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$dataProvider = null;

		if(!Yii::app()->user->isGuest) {
			$accounts = Yii::app()->user->accounts->getList();
			$dataProvider = new CArrayDataProvider($accounts, array(
				'keyField'=>'hash',
				'pagination'=>array(
					'pageSize'=>10,
				),
			));
		}

		$this->render('index', array('dataProvider' => $dataProvider));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if(!Yii::app()->user->isGuest)
			$this->redirect(array('/site/index'));

		$model=new LoginForm;

		// collect user input data
		if(isset($_POST['form'], $_POST['LoginForm'][$_POST['form']]))
		{
			$model->attributes=$_POST['LoginForm'][$_POST['form']];
			
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()) {

				Yii::app()->user->accounts->set();
				$this->redirect(Yii::app()->user->returnUrl);

			} else {
				Yii::app()->user->setFlash('error', Yii::t('login', 'Código de validación errónea'));
			}
		}

		if(Yii::app()->user->hasFlash('error'))
			HScript::registerCode('lunchFormModal', '$(function(){$("#btn_loginForm").click()})');
		
		$this->render('index');
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		$wsMsg = $this->wsClient->endSession();
		if($wsMsg->error === 'S')
			Yii::log('Error al cerrar sesion en el WebService', 'error', 'app.SiteController');

		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}