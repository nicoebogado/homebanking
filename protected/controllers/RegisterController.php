<?php

Yii::import('booster.widgets.TbForm');

class RegisterController extends Controller
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
			array(
				'allow', //allow no authenticated user
				'users'=>array('*'),
			),
			array(
				'deny',  // deny all users authenticated
        'users'=>array('@'),
      ),
		);
	}

	public function actionForm(){

		$this->layout = 'base';

		$model = new RegisterForm;

		if(isset($_POST['RegisterForm'])){

			$model->attributes=$_POST['RegisterForm'];
			$attributes=array();

			if($model->validate()) {

				$attributes=$_POST['RegisterForm'];

				$attributes['documentType']='1';
				$attributes['countryCode']='1';
				$attributes['type']='F';
				$attributes['gender']='M';
				$attributes['telephoneType']='C';
				$attributes['pinStatus']='A';
				$attributes['pinType']='N';

				$result = $this->wsClient->createPerson(
					array(
						"document"=>$attributes['document'],
						"documentType"=>$attributes['documentType'],
						"countryCode"=>$attributes['countryCode'],
						"name"=>$attributes['name'],
						"lastName"=>$attributes['lastName'],
						"type"=>$attributes['type'],
						"gender"=>$attributes['gender'],
						"birthdate"=>$attributes['birthdate'],
						"address"=>$attributes['address'],
						"cityCode"=>$attributes['cityCode'],
						"districtCode"=>$attributes['districtCode'],
						"houseNumber"=>$attributes['houseNumber'],
						"telephone"=>$attributes['telephone'],
						"areaCode"=>$attributes['areaCode'],
						"telephoneType"=>$attributes['telephoneType'],
						"latitude"=>$attributes['latitude'],
						"longitude"=>$attributes['longitude'],
						"pinStatus"=>$attributes['pinStatus'],
						"pinType"=>$attributes['pinType'],
						"email"=>$attributes['email']
					)
				);

				if($result->error === 'S') {
					$this->setFlashError($result->descripcionrespuesta);
					$form = new TbForm($model->formConfig($this->getCities()), $model);
					$this->render('form',array('form'=>$form));
				} else {

					$model = new RegisterForm;
					$form = new TbForm($model->formConfig($this->getCities()), $model);

					$result = $this->wsClient->createActivationKey(
						array(
							"document"=>$attributes['document'],
							'type'=>'A',
							'area'=>$attributes['areaCode'],
							'telephone'=>$attributes['telephone']
						));

					if($result->error === 'S') {
						$this->setFlashError($result->descripcionrespuesta);
						$form = new TbForm($model->formConfig($this->getCities()), $model);
						$this->render('form',array('form'=>$form));
					} else {
						$to='+595'.substr($attributes['areaCode'],1,strlen($attributes['areaCode'])).$attributes['telephone'];
						$message="Su clave de acceso es: ".$result->claveusuario.' Active su clave en :'.Yii::app()->createUrl('register/activate');
						$sms=$this->sendSMS($to,$message);
						if($sms->error==='N'){
							$this->setFlashSuccess($result->descripcionrespuesta);
						}
					}

					$this->render('form',array('form'=>$form));

				}

			}else{
				$errors=$model->errors;

				if(isset($errors['latitude'][0])){
					$this->setFlashError('Debe ubicar su dirección en el Mapa');
				}

				$form = new TbForm($model->formConfig($this->getCities()), $model);
				$this->render('form',array('form'=>$form));
			}
		}else{
			$form = new TbForm($model->formConfig($this->getCities()), $model);
			$this->render('form',array('form'=>$form));
		}
		Yii::app()->end();

	}

	public function actionMap(){
		$this->layout = 'base';
		$this->render('map');
	}

	public function getCities(){

		$file = fopen(dirname(Yii::app()->request->scriptFile).'/js/register/ciudades.csv', 'r');
		$i=1;
		while (($line = fgetcsv($file)) !== FALSE) {
  		if($i>1){
				$aux=explode(";",$line[0]);
				$codigo[]=trim($aux[0], '"');
				$descripcion[]=trim($aux[1], '"');
			}
			$i++;
		}
		fclose($file);
		array_walk_recursive($descripcion, function(&$value, $key) {
	    if (is_string($value)) {
	        $value = iconv('windows-1252', 'utf-8', $value);
	    }
		});
		$ciudades=array_combine($codigo,$descripcion);
    return $ciudades;

  }

	public function actionActivate(){

		$this->layout = 'base';

		$model = new ActivateKeyForm;

		if(isset($_POST['ActivateKeyForm'])){

			$model->attributes=$_POST['ActivateKeyForm'];
			$attributes=array();

			if($model->validate()) {

				$attributes=$_POST['ActivateKeyForm'];

					$result = $this->wsClient->activateActivationKey(
					array(
						'document'	=> $attributes['document'],
						'key'		=> $attributes['key'],
					));

					if($result->error === 'S') {
						$this->setFlashError($result->descripcionrespuesta);
						$form = new TbForm($model->formConfig(), $model);
						$this->render('activate',array('form'=>$form));
					} else {

						$model=new LoginForm;
						$model->dataType='D';
						$model->data=$attributes['document'];
						$model->password=$attributes['key'];

						if($model->login()) {

							Yii::app()->user->accounts->set();
							$this->redirect(Yii::app()->user->returnUrl);

						}

					}

			}else{

				$form = new TbForm($model->formConfig(), $model);
				$this->render('activate',array('form'=>$form));

			}

		}else{
			$form = new TbForm($model->formConfig(), $model);
			$this->render('activate',array('form'=>$form));
		}
		Yii::app()->end();

	}

	public function actionCreateKey(){

		$this->layout = 'base';
		$model = new CreateKeyForm;

		if(isset($_POST['CreateKeyForm'])){
			$model->attributes=$_POST['CreateKeyForm'];
			$attributes=array();

			if($model->validate()) {

				$attributes=$_POST['CreateKeyForm'];

				$result = $this->wsClient->createActivationKey(
					array(
						"document"=>$attributes['document'],
						'type'=>'A',
						'area'=>$attributes['area'],
						'telephone'=>$attributes['telephone']
					));

				if($result->error === 'S') {
					$this->setFlashError($result->descripcionrespuesta);
					$form = new TbForm($model->formConfig(), $model);
					$this->render('createkey',array('form'=>$form));
				} else {
					$this->setFlashSuccess($result->descripcionrespuesta);
					$model = new CreateKeyForm;
					$form = new TbForm($model->formConfig(), $model);

					$to='+595'.substr($attributes['area'],1,strlen($attributes['area'])).$attributes['telephone'];
					$message="Su clave de acceso es: ".$result->claveusuario.' Active su clave en :'.Yii::app()->createUrl('register/activate');

					$sms=$this->sendSMS($to,$message);
					if($sms->error==='N'){
						$this->setFlashSuccess($result->descripcionrespuesta);
					}

					$this->render('createkey',array('form'=>$form));
				}

			}else{
				$form = new TbForm($model->formConfig(), $model);
				$this->render('createkey',array('form'=>$form));
			}

		}else{
			$form = new TbForm($model->formConfig(), $model);
			$this->render('createkey',array('form'=>$form));
		}
		Yii::app()->end();

	}

	protected function beforeAction($action)
	{
		/* Hace el overwrite de la funcion que controla
		 que la URL este dada de alta en la estructura de menu */
		return true;
	}

	public function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
  }

	public function sendSMS($to,$message){

		$url="http://10.192.14.15/api-sst/v1/index.php/sendsms";
		$params=array("to"=>$to,"message"=>$message);
		$result=$this->callRest($url,$params,'POST');
		return $result;

	}

	public function actionGetDistrict($id){

		$file = fopen(dirname(Yii::app()->request->scriptFile).'/js/register/barrios.csv', 'r');
		$i=1;
		while (($line = fgetcsv($file)) !== FALSE) {
  		if($i>1){
				$aux=explode(";",$line[0]);
				if($aux[0]==$id){
					$codigo[]=trim($aux[1], '"');
					$descripcion[]=trim($aux[2], '"');
				}
			}
			$i++;
		}
		fclose($file);
		array_walk_recursive($descripcion, function(&$value, $key) {
	    if (is_string($value)) {
	        $value = iconv('windows-1252', 'utf-8', $value);
	    }
		});
		$barrios=array_combine($codigo,$descripcion);
		echo json_encode(array('barrios'=>$barrios));
		exit;

	}

	public function callRest($url, $params = null, $verb = 'GET', $format = 'json')
	{
	  $cparams = array(
	    'http' => array(
	      'method' => $verb,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" ,
	      'ignore_errors' => true
	    )
	  );
	  if ($params !== null) {
	    $params = http_build_query($params);
	    if ($verb == 'POST') {
	      $cparams['http']['content'] = $params;
	    } else {
	      $url .= '?' . $params;
	    }
	  }

	  $context = stream_context_create($cparams);
	  $fp = fopen($url, 'rb', false, $context);
	  if (!$fp) {
	    $res = false;
	  } else {
	    // If you're trying to troubleshoot problems, try uncommenting the
	    // next two lines; it will show you the HTTP response headers across
	    // all the redirects:
	    // $meta = stream_get_meta_data($fp);
	    // var_dump($meta['wrapper_data']);
	    $res = stream_get_contents($fp);
	  }

	  if ($res === false) {
	    throw new Exception("$verb $url failed: $php_errormsg");
	  }

	  switch ($format) {
	    case 'json':
	      $r = json_decode($res);
	      if ($r === null) {
	        throw new Exception("failed to decode $res as json");
	      }
	      return $r;

	    case 'xml':
	      $r = simplexml_load_string($res);
	      if ($r === null) {
	        throw new Exception("failed to decode $res as xml");
	      }
	      return $r;
	  }
	  return $res;
	}

}
