<?php
Yii::import('booster.widgets.TbForm');

class UserController extends Controller
{
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
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
				'actions'=>array('changeAccessPassword', 'changeTransactionalKey'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionChangeAccessPassword()
	{
		$model = new ChangePasswordForm();

		if(isset($_POST['ChangePasswordForm']))
		{
			$model->attributes=$_POST['ChangePasswordForm'];
			if($model->validate()) {
				$wsrs = $this->wsClient->changeAccessPassword(array(
					'currentPassword' => $model->currentPassword,
					'newPassword' => $model->newPassword,
				));
				if($wsrs->error === 'N') {
					if(Yii::app()->user->getState('changePassword'))
						Yii::app()->user->setState('changePassword', null);
					$this->setFlashSuccess(Yii::t('login', 'La clave de acceso se ha cambiado correctamente'));
					$this->redirect(Yii::app()->homeUrl);
				} else {
					$this->setFlashError($wsrs->descripcionrespuesta);
					$this->redirect(array('user/changeAccessPassword'));
				}
			}
		}

		$form = new TbForm($model->formConfig(), $model);
		$this->render('changeAccessPassword', array('form'=>$form));
	}

	public function actionChangeTransactionalKey()
	{
		$model = new ChangePasswordForm();

		if(isset($_POST['ChangePasswordForm']))
		{
			$model->attributes=$_POST['ChangePasswordForm'];
			if($model->validate()) {
				$wsrs = $this->wsClient->changeTransactionalKey(array(
					'currentPassword' => $model->currentPassword,
					'newPassword' => $model->newPassword,
				));
				if($wsrs->error === 'N') {
					$this->setFlashSuccess('La clave ha cambiado correctamente');
					$this->redirect(Yii::app()->homeUrl);
				} else {
					$this->setFlashError($wsrs->descripcionrespuesta);
					$this->redirect(array('changeTransactionalKey'));
				}
			}
		}

		$form = new TbForm($model->formConfig(), $model);
		$this->render('changeTransactionalKey', array('form'=>$form));
	}
}