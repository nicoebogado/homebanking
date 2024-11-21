<?php
class FormModel extends CFormModel
{
	/**
	 * Cliente del webservice
	 * @var WebServiceClient Clase alojada en components
	 * @access public
	 */
	/*protected $wsClient=null;

	public function init()
	{
		parent::init();

		try {
			$this->wsClient = new WebServiceClient();
		} catch (Exception $e) {
			$this->render('error',array(
				'code' => 503,
				'type' => 'CHttpException',
				'message' => $e->getMessage(),
			));
			Yii::app()->end();
		}
	}*/
}