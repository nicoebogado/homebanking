<?php
Yii::import('booster.widgets.TbForm');

class AuthorizationController extends Controller
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

	public function actionListWithDetail()
	{
		$this->_list(1);
	}

	public function actionListWithAction()
	{
		$this->_list(2);
	}

	public function actionSalaryDetail($id)
	{
		$response = $this->wsClient->authorizeOperation(array(
			'mode' => 'D',
			'documentNumber' => $id,
		));
		$this->_renderDetail($response, 1);
	}

	public function actionSalaryDetailWithActions($id)
	{
		$model = new AuthorizationConfirmForm;
		if (isset($_POST['AuthorizationConfirmForm'])) {
			$model->attributes = $_POST['AuthorizationConfirmForm'];
			if ($model->validate()) {
				if ($model->authorize) {
					$response = $this->wsClient->authorizeOperation(array(
						'mode' => 'A',
						'documentNumber' => $id,
						'transactionalKey' => $model->transactionalKey,
					));

					if($response->error === 'N'){
						$this->_renderDetail($response, 3);
						Yii::app()->end();
					} else {
						$this->setFlashError($response->descripcionrespuesta);
						$this->redirect(array('salaryDetailWithActions', 'id'=>$id));
					}
				} elseif ($model->reject) {
					$response = $this->wsClient->authorizeOperation(array(
						'mode' => 'R',
						'documentNumber' => $id,
						'transactionalKey' => $model->transactionalKey,
					));

					if($response->error === 'N'){
						$this->_renderDetail($response, 4);
						Yii::app()->end();
					}
				}
			}
		}

		$response = $this->wsClient->authorizeOperation(array(
			'mode' => 'D',
			'documentNumber' => $id,
		));
		$this->_renderDetail($response, 2, $model);
	}

	/**
	 * Renderiza detalle de autorizacion
	 * 
	 * @param $response respuesta del WS
	 * @param $type int 1:detalles, 2:detalles con acciones (rechazar, autorizar), 3 respuesta de autorizar, 4 respuesta de rechazar
	 */
	private function _renderDetail($response, $type, $model=null) {

		if ($response->error === 'S' || $response->detalles->error === 'S') {
			$this->setFlashError($response->descripcionrespuesta);
			$this->redirect(array($type===1 ? 'listWithDetail' : 'listWithAction'));
		}

		$c = $response->detalles->cantidaddetallessalario;
		$dataProvider = new CArrayDataProvider(
			$c > 0 ? $response->detalles->listadetallesalario->array : array(),
			array(
				'keyField'=>'numerocuenta',
				'pagination'=>array('pageSize'=>10),
			)
		);

		$form = null;

		if($type === 2) {
			$form = new TbForm($model->formConfig(), $model);
		}

		$this->render('detail', array(
			'details'		=> $response->detalles,
			'dataProvider'	=> $dataProvider,
			'type'			=> $type,
			'form'			=> $form,
		));
	}

	/**
	 * Renderiza una pantalla con lista de autorizaciones pendientes
	 * @param int $type Tipo de lista 1: con opciones de detalles, 2: con opciones de autorizacion
	 */
	private function _list($type)
	{
		$p = array('status' => 'P');
		// obtener lista de autorizaciones
		$response = $type === 1 ?
			$this->wsClient->getAuthorizations($p) :
			$this->wsClient->getUserAuthorizations($p);

		// establecer proveedor de datos
		$dataProvider = new CArrayDataProvider(
			$response->cantidadautorizaciones > 0 ? $response->listaautorizaciones->array : array(),
			array(
				'keyField'=>false,
				'pagination'=>array('pageSize'=>10),
			)
		);

		// renderizar lista de autorizaciones
		$this->render('list', array(
			'dataProvider' => $dataProvider,
			'type' => $type,
		));
	}
}
