<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $document;
	public $data;
	public $password;
	public $dataType;
	public $companyDocNum;
	public $companyDocType;

	private $_identity;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		//comentado por cambio en tipo de login
		return array(
			// data and password are required
			array('data, password, dataType', 'required'),
			array('document, data, password, dataType', 'safe'),
			array('companyDocNum, companyDocType', 'safe'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
		/*return array(
			// data and password are required
			array(' data, password, dataType', 'required'),
			//array('companyDocNum, companyDocType', 'safe'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);*/
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'document' => Yii::t('document', 'Cédula de Identidad'),
			//'data' => Yii::t('login', 'Nro. de Cuenta'),
			'data' => Yii::t('login', 'Cédula de Identidad'),
			'password' => Yii::t('login', 'Clave de acceso'),
			'dataType' => Yii::t('login', 'Tipo de acceso'),
			'companyDocNum' => Yii::t('login', 'Dato de acceso de empresa'),
			'companyDocType' => Yii::t('login', 'Tipo de acceso de empresa'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params)
	{
		//echo $this->document."-". $this->data."-". $this->password."-". $this->dataType."-". $this->companyDocNum."-". $this->companyDocType;
		//exit;
		if (!$this->hasErrors()) {
			$this->_identity = new UserIdentity($this->document, $this->data, $this->password, $this->dataType, $this->companyDocNum, $this->companyDocType);
			//var_dump($this->_identity);exit;
			if (!$this->_identity->authenticate())
				$this->addError('password', 'Incorrect data or password.');
		}
	}

	/**
	 * Logs in the user using the given data and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if ($this->_identity === null) {
			$this->_identity = new UserIdentity($this->document, $this->data, $this->password, $this->dataType, $this->companyDocNum, $this->companyDocType);
			$this->_identity->authenticate();
		}

		if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
			Yii::app()->user->login($this->_identity);
			return true;
		} else
			return false;
	}

	/**
	 * Return array with options for $dataType attribute select
	 * @param string $accessType P:people, L:legal
	 */
	public function getDataTypeOptions($accessType = 'P')
	{
		$options = array(
			'P' => array(
				'C' => Yii::t('login', 'Nro. de Cuenta'),
				'T' => Yii::t('login', 'Nro. de Tarjeta'),
				'K' => Yii::t('login', 'Nro. de Cliente'),
			),
			'L' => array(
				'D' => Yii::t('login', 'R.U.C.'),
				'K' => Yii::t('login', 'Nro. de Cliente'),
			),
		);
		return $options[$accessType];
	}
}
