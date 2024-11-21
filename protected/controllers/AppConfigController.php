<?php

class AppConfigController extends Controller
{
	private $_cookieExpire; // 20 días

	public function init()
	{
		parent::init();
		$_cookieExpire = time()+60*60*24*20; // 20 días
	}

	public function actionLanguage($id)
	{
		$cookie = new CHttpCookie('appLanguage', $id);
		$cookie->expire = $this->_cookieExpire;
		Yii::app()->request->cookies['appLanguage'] = $cookie;

		$this->redirect(Yii::app()->user->returnUrl);
	}
	public function actionTheme($id)
	{
		$cookie = new CHttpCookie('appTheme', $id);
		$cookie->expire = $this->_cookieExpire;
		Yii::app()->request->cookies['appTheme'] = $cookie;

		$this->redirect(Yii::app()->user->returnUrl);
	}
}