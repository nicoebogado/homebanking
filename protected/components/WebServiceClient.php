<?php
class WebServiceClient extends CComponent
{
	private $_wsClient;
	private $_channel = '999';
	private $_wsdl;
	private $_error = false;
	private $_functions = [];

	public function __construct()
	{
		$this->_wsdl = Yii::app()->params['wsdlUrl'];
		$this->_connect();
	}

	private function _connect()
	{
		try {
			//Agredago: Higinio Samaniego - 01-12-21
			$context = stream_context_create([
				'ssl' => [
					// set some SSL/TLS specific options
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				]
			]);
			
			$this->_wsClient	= new SoapClient($this->_wsdl, array(
				'trace'			=> true,
				'cache_wsdl'	=> WSDL_CACHE_NONE,
				'exceptions'	=> true,
				'features'		=> SOAP_SINGLE_ELEMENT_ARRAYS,
				'stream_context' => $context
			));

			// establecer la lista de funciones disponibles
			$this->_functions = array_map(function ($f) {
				$pattern = '/^\w+\s(\w+).*$/';
				$replacement = '$1';
				return preg_replace($pattern, $replacement, $f);
			}, $this->_wsClient->__getFunctions());
		} catch (SoapFault $e) {
			throw new CHttpException(503, Yii::t('components', 'El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'));
			Yii::app()->end();
		}
	}


	//--------------------------------------------------------------------//
	//--------------------------------------------------------------------//
	//------------------------WEB SERVICE FUNCITONS-----------------------//
	//--------------------------------------------------------------------//
	//--------------------------------------------------------------------//

	/**
	 * Busca la funcion en la lista de funciones disponibles y lo llama
	 * Si no encuentra llama al metodo __call padre
	 */
	public function __call($method, $params)
	{
		if (array_search($method, $this->_functions) !== false) {
			$params = isset($params[0]) ? $params[0] : array();
			return $this->_callSoapFunct($method, $params);
		}

		parent::__call($method, $params);
	}

	/**
	 * Invoca un servicio
	 */
	private function _callSoapFunct($method, $params = array())
	{
		$return = null;
		$paramsObj = new stdClass();
		foreach ($params as $p => $val) {
			$paramsObj->{$p} = $val;
		}
		$paramsObj->channel = $this->_channel;

		if (!Yii::app()->user->isGuest) {
			$this->_wsClient->__setCookie('JSESSIONID', Yii::app()->user->id);
		}

		try {
			$return = $this->_wsClient->{$method}($paramsObj);
		} catch (Exception $e) {
			$this->_exception($e);
		}

		$this->_log();
		$this->_checkError($return);
		return $return->return;
	}

	/**
	 * Iniciar sesión
	 * Retorna un array con la respuesta del servicio y la cookie de sesion
	 * @param $params['data']
	 * @param $params['password']
	 * @param $params['dataType']
	 * @param $params['companyDocNum']
	 * @param $params['companyDocType']
	 */
	public function startSession($params)
	{
		return array(
			// respuesta del servicio
			$this->_callSoapFunct(__FUNCTION__, $params),
			// cookie de sesion
			isset($this->_wsClient->_cookies) ? $this->_wsClient->_cookies['JSESSIONID'][0] : null,
		);
	}

	public function startSessionDocument($params)
	{
		return array(
			// respuesta del servicio
			$this->_callSoapFunct(__FUNCTION__, $params),
			// cookie de sesion
			isset($this->_wsClient->_cookies) ? $this->_wsClient->_cookies['JSESSIONID'][0] : null,
		);
	}

	public static function formatDate($date, $format = 'medium', $reverse = false)
	{
		$date = trim($date);

		if ($date) {
			$day = $reverse ? substr($date, 6, 2) : substr($date, 0, 2);
			$month = $reverse ? substr($date, 4, 2) : substr($date, 2, 2);
			$year = $reverse ? substr($date, 0, 4) : substr($date, 4, 4);
			$date = strtotime($year . '/' . $month . '/' . $day);

			return empty($format) ?
				$date :
				Yii::app()->dateFormatter->formatDateTime($date, 'medium', null);
		}

		return Yii::t('commons', 'No asignado');
	}

	//--------------------------------------------------------------------//
	//--------------------------------------------------------------------//
	//--------------------------PRIVATE FUNCTIONS-------------------------//
	//--------------------------------------------------------------------//
	//--------------------------------------------------------------------//

	private function _checkError($wsResponse)
	{
		$resp = $wsResponse->return;
		WSErrorMsgs::processError($resp);
	}

	private function _exception($e)
	{
		Yii::log($e, 'error', 'App.Componnets');
		Yii::log($this->_wsClient->__getLastRequestHeaders(), 'info', 'components.WebServiceClient');
		Yii::log($this->_wsClient->__getLastRequest(), 'info', 'components.WebServiceClient');
		Yii::log($this->_wsClient->__getLastResponseHeaders(), 'info', 'components.WebServiceClient');
		Yii::log($this->_wsClient->__getLastResponse(), 'info', 'components.WebServiceClient');

		Yii::app()->user->logout();
		Yii::app()->request->redirect(Yii::app()->createUrl('site/login', array('e' => base64_encode('El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'))));
		throw new CHttpException(503, Yii::t('components', 'El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'));
	}

	private function _log()
	{
		$tag = 'application.components.WebServiceClient';
		Yii::log($this->_wsClient->__getLastRequest(), 'info', $tag);
		Yii::log($this->_wsClient->__getLastResponse(), 'info', $tag);
	}

	/*private function _searchConditionMatch($conditions, $val) {
		foreach ($conditions as $k => $v) {
			if($val->{$k} == $v) return true;
		}
		return false;
	}*/
}
