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

		//03-11-2022 added:Higinio Samaniego, se extrae nombres de autorizadores
		$nombreAutorizadores = "";
		$aux = 0;
		if(isset($response->detalles->listaautorizadores)){
			foreach($response->detalles->listaautorizadores as $datos){
				for ($i=0; $i < count($datos); $i++) { 	
					if($datos[$i]->estado == "Autorizado"){
						$nombreAutorizadores .= ", ".$datos[$i]->nombre;
					}
				}				
			}

			$nombreAutorizadores = substr($nombreAutorizadores,1);
			$response->detalles->cantidadautorizadores =  $response->detalles->cantidadautorizadores." - ".$nombreAutorizadores;	
		}
		$this->_renderDetail($response, 1);
	}

	public function actionDecline($id)
	{

		$response = $this->wsClient->authorizeOperation(array(
			'mode' => 'R',
			'documentNumber' => $id,
		));

		if($response->error == 'N'){
			$this->setFlashError($response->descripcionrespuesta);
			$this->redirect(array('listWithAction'));
		}else{
			$this->setFlashError($response->descripcionrespuesta);
			$this->redirect(array('listWithAction'));
		}
		
	}

	public function actionSalaryDetailWithActions($id)
	{
		$perfil = Yii::app()->user->getState('clientPerfil');
		
		$model = new AuthorizationConfirmForm;
			
		if (isset($_POST['AuthorizationConfirmForm'])) {

			$model->attributes = $_POST['AuthorizationConfirmForm'];
			

			if ($model->validate() || $_POST['AuthorizationConfirmForm']['isToken']) {
				if ($model->authorize || $_POST['AuthorizationConfirmForm']['isToken']) {
					$response = $this->wsClient->authorizeOperation(array(
						'mode' => 'A',
						'documentNumber' => $id,
						'transactionalKey' => $model->transactionalKey,
					));

					//03-11-2022 added:Higinio Samaniego, se extrae nombres de autorizadores
					$nombreAutorizadores = "";
					$aux = 0;
					if(isset($response->detalles->listaautorizadores)){
						foreach($response->detalles->listaautorizadores as $datos){
							for ($i=0; $i < count($datos); $i++) { 	
								if($datos[$i]->estado == "Autorizado"){
									$nombreAutorizadores .= ", ".$datos[$i]->nombre;
								}
							}				
						}
				
						$nombreAutorizadores = substr($nombreAutorizadores,1);
						$response->detalles->cantidadautorizadores =  $response->detalles->cantidadautorizadores." - ".$nombreAutorizadores;	
					}

					
          			if ($response->error === 'N' && $response->detalles->autorizacionesfaltantes == 0) {
						
						Yii::log('entro en el perfil autorizaciones faltantes');

						$this->_renderDetail($response, 3);
						Yii::app()->user->accounts->refresh();

						$email = Yii::app()->user->getState('clientArea')['email'];
						
						
						if ($email) {

							$this->_enviarCorreos($response, $email, $model);

						}
						Yii::app()->end();

					}else if($response->error === 'N' && $perfil == 'COMPLETO'){ 

						Yii::log('entro en el perfil completo'); 

						$this->_renderDetail($response, 3);
						Yii::app()->user->accounts->refresh();

						$email = Yii::app()->user->getState('clientArea')['email'];
						
						
						if ($email) {

							$this->_enviarCorreos($response, $email, $model);

						}
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

		//03-11-2022 added:Higinio Samaniego, se extrae nombres de autorizadores
		$nombreAutorizadores = "";
		$aux = 0;
		if(isset($response->detalles->listaautorizadores)){
			foreach($response->detalles->listaautorizadores as $datos){
				for ($i=0; $i < count($datos); $i++) { 	
					if($datos[$i]->estado == "Autorizado"){
						$nombreAutorizadores .= ", ".$datos[$i]->nombre;
					}
				}				
			}
	
			$nombreAutorizadores = substr($nombreAutorizadores,1);
			$response->detalles->cantidadautorizadores =  $response->detalles->cantidadautorizadores." - ".$nombreAutorizadores;	
		}
		

		$this->_renderDetail($response, 2, $model);
	}

	/**
	* Envio de Correos 
	**/
	private function _enviarCorreos($response, $email, $model = null){

		if(isset($response->detalles->estadosalario)){


			$name = Yii::app()->user->getState('clientArea')['nombrecompleto'];
			$date = date('d/m/Y H:i:s');

			$cuenta_debito = explode('-',$response->detalles->cuentaprincipal);


				$montoprincipal = ($response->detalles->codigomonedaprincipal=='GS'?number_format($response->detalles->montoprincipal, 0, ',', '.'):number_format($response->detalles->montoprincipal, 2, ',', '.'));
				$msgBody = <<<EOT
				<img src="https://www.fic.com.py/fic/img/logo.png" alt="logo" width="100" height="50">
				<h1>Aviso de Autorización de {$response->detalles->descripcionmovimiento}</h1>
				<p>Estimado $name</p>
				<p>Informamos que usted ha realizado un {$response->detalles->descripcionmovimiento}</p>

				<table>
				<tbody>
				<tr>
				<td><b>Fecha y Hora: </b></td>
				<td> {$date}</td>
				</tr>
				<tr>
				<td><b>Número de Cuenta de Débito: </b></td>
				<td> {$cuenta_debito[0]}</td>
				</tr>
				<tr>
				<td><b>Nombre de la cuenta de Débito: </b></td>
				<td> {$cuenta_debito[1]}</td>
				</tr>
				<tr>
				<tr>
				<td><b>Importe transferido: </b></td>
				<td> {$response->detalles->codigomonedaprincipal} {$montoprincipal}</td>
				</tr>
				
				</tbody>
				</table>
				<p></p>
				EOT;

				//$msgBody = base64_encode($msgBody);
				$subject = $response->detalles->descripcionmovimiento;
				$mailer = Yii::app()->MultiMailer->to($email, $name);
				$mailer->subject($subject);
				$mailer->body($msgBody);
				
				
				Yii::log("Enviando email a $email. Asunto: $subject");
				Yii::log($msgBody);

				if ($mailer->send()) {
					Yii::log('enviado');
				} else {
					Yii::log('no enviado');
				}


		}else{
		

			$name = Yii::app()->user->getState('clientArea')['nombrecompleto'];
			$date = date('d/m/Y H:i:s');

			$cuenta_debito = explode('-',$response->detalles->cuentaprincipal);

			$referencia = "";
			if (isset($response->detalles->cuentacontra)){
						$cuenta_credito = explode('-',$response->detalles->cuentacontra);
			}else{
				$cuenta_credito = [$response->detalles->listatransfsnp->array[0]->numeroctabeneficiario,
				$response->detalles->listatransfsnp->array[0]->nombrebeneficiario];
				$referencia = "<tr>
				<td><b>Nro de referencia</b></td>
				<td>".$response->detalles->listatransfsnp->array[0]->numeroreferencia."</td>
				</tr>";               
			}

				$montoprincipal = ($response->detalles->codigomonedaprincipal=='GS'?number_format($response->detalles->montoprincipal, 0, ',', '.'):number_format($response->detalles->montoprincipal, 2, ',', '.'));
				$msgBody = <<<EOT
				<img src="https://www.fic.com.py/fic/img/logo.png" alt="logo" width="100" height="50">
				<h1>Aviso de Autorización de Transferencia a través del Homebanking</h1>
				<p>Estimado $name</p>
				<p>Informamos que usted ha realizado una transferencia con los siguientes detalles</p>

				<table>
				<tbody>
				<tr>
				<td><b>Fecha y Hora: </b></td>
				<td> {$date}</td>
				</tr>
				<tr>
				<td><b>Número de Cuenta de Débito: </b></td>
				<td> {$cuenta_debito[0]}</td>
				</tr>
				<tr>
				<td><b>Nombre de la cuenta de Débito: </b></td>
				<td> {$cuenta_debito[1]}</td>
				</tr>
				<tr>
				<td><b>Número cuenta destino: </b></td>
				<td> {$cuenta_credito[0]}</td>
				</tr>
				<tr>
				<td><b>Nombre de la Cuenta Crédito: </b></td>
				<td> {$cuenta_credito[1]}</td>
				</tr>
				<tr>
				<td><b>Importe transferido: </b></td>
				<td> {$response->detalles->codigomonedaprincipal} {$montoprincipal}</td>
				</tr>
				{$referencia}
				</tbody>
				</table>
				<p></p>
				EOT;

				//$msgBody = base64_encode($msgBody);
				$subject = $response->detalles->descripcionmensaje=='TRANSFERENCIA INTERBANCARIA (SNP)'?'Aviso de transferencia SIPAP':'Aviso de transferencia';
				$mailer = Yii::app()->MultiMailer->to($email, $name);
				$mailer->subject($subject);
				$mailer->body($msgBody);
				
				
				Yii::log("Enviando email a $email. Asunto: $subject");
				Yii::log($msgBody);

				if ($mailer->send()) {
					Yii::log('enviado');
				} else {
					Yii::log('no enviado');
				}
		}




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

		$details = array();
		$authType = null;
		if(isset($response->detalles->cantidaddetallessalario)){
			$details = $response->detalles->cantidaddetallessalario > 0 ?
				$response->detalles->listadetallesalario->array :
				array();
			$authType = 'salary';
		} elseif (isset($response->detalles->cantidaddetallesproveedor)) {
			$details = $response->detalles->cantidaddetallesproveedor > 0 ?
				$response->detalles->listadetalleproveedor->array :
				array();
			$authType = 'supplier';
		} elseif(isset($response->detalles->cantidaddetallessnp)){
			$details = $response->detalles->cantidaddetallessnp > 0 ?
				$response->detalles->listatransfsnp->array :
				array();
			$authType = 'snp';
		}

		$dataProvider = new CArrayDataProvider(
			$details,
			array(
				'keyField'=>false,
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
			'authType'		=> $authType,
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


		if ($response->error === "S" && empty($response->cantidadautorizaciones)) {
			$this->setFlashError($response->descripcionrespuesta);
			$this->redirect(array('/site/index'));
		}

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
