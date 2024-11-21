<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $document;
	public $data;
	public $dataType;
	public $companyDocNum;
	public $companyDocType;
	public $initdata;
	public $availableUrls;

	public function __construct($document, $data, $password, $dataType, $companyDocNum, $companyDocType)
	{
		$this->document = $document;
		$this->data = $data;
		$this->password = $password;
		$this->dataType = $dataType;
		$this->companyDocNum = $companyDocNum;
		$this->companyDocType = $companyDocType;
	}

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$wsClient = new WebServiceClient;
		list($resp, $sid) = $wsClient->startSessionDocument(array(
			'document'			=> $this->document,
			'data'				=> $this->data,
			'password'			=> $this->password,
			'dataType'			=> $this->dataType,
			'companyDocNum'		=> $this->companyDocNum,
			'companyDocType'	=> $this->companyDocType,
		));

		$this->setState('initdata', $resp);

		if ($resp->error === 'S') {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			Yii::app()->user->setFlash('error', $resp->descripcionrespuesta);
		} else {
			$this->errorCode = self::ERROR_NONE;
			$this->setState('id', $sid);
			$this->setState('suid', HCrypt::random_str());
			$this->setState('accounts', new Accounts);

			$this->setState('clientId', isset($resp->codigocliente) ? $resp->codigocliente : null);

			$this->setState('clientPerfil', isset($resp->codigoperfil) ? $resp->codigoperfil : null);//Added: Higinio Samaniego, 31/05/2022

			$this->setState('codigoempresa', isset($resp->codigoempresa) ? $resp->codigoempresa : null);//Added: Higinio Samaniego, 03/06/2022


			//PARA INICIAR SESION RAPIDAMENTE
			$this->setState('password', isset($this->password) ? $this->password : null);//Added: Higinio Samaniego, 27/02/2024
			$this->setState('documento', isset($this->data) ? $this->data : null);//Added: Higinio Samaniego, 27/02/2024
			if(isset($resp->codigoempresa)){//Added: Higinio Samaniego, 12/06/2024, para recuperar datos del enemigo
				$this->setState('documentoX', isset($this->document)?$this->document:$this->data);//Added: Higinio Samaniego, 27/02/2024
			}
			
			$this->setState('empresa', isset($resp->nombreempresa) ? $resp->nombreempresa : null);//Added: Higinio Samaniego, 27/02/2024

			if(isset($resp->codigoempresa)){
				//Function getting token data
				$this->getTokenData($resp);
			}

			$this->setState('officerName', isset($resp->nombreoficial) ? $resp->nombreoficial : null);
			$this->setState('officerEmail', isset($resp->emailoficial) ? $resp->emailoficial : null);
			$this->setState('officerPhone', isset($resp->telefonooficial) ? $resp->telefonooficial : null);
			// datos de empresa
			$this->setState('entityCode', isset($resp->codigoempresa) ? $resp->codigoempresa : null);
			$this->setState('entityName', isset($resp->nombreempresa) ? $resp->nombreempresa : null);

			// Se guarda tipo de implementación para poder esconder o mostrar funcionalidades
			$this->setState('deploymentType', isset($resp->tipoimplementacion) ? $resp->tipoimplementacion : null);
			//$this->setState('deploymentType','9999');

			$this->setState('operationMode', isset($resp->tipooperativa) ? $resp->tipooperativa : null);
			$this->setState('sessionTimeout', $resp->tiempoinactividad * 60 * 1000);

			$this->setState('sharedKey', isset($resp->sharekey) ? $resp->sharekey : null);

			$this->username = $resp->nombrecompleto;

			// Recupera Opciones de menu habilitadasa
			$availableUrls = $wsClient->getMenu();
			$availableUrls = $availableUrls->listajerarquiasmenus->array;
			$this->setState('availableUrls', $availableUrls);

			// Recuperar datos del area del cliente
			$clientArea = $wsClient->clientArea();

			if ($clientArea->error === 'N') {
				$sharedKey = preg_replace("/[^a-zA-Z0-9]+/", "", $this->username);
				$sharedKey .= $clientArea->tipodocumento . $clientArea->documento;

				$this->setState('clientArea', array(
					'address' => $clientArea->direccionprincipal,
					'phoneNumber' => $clientArea->telefonoprincipal,
					'email' => isset($clientArea->email) ? $clientArea->email : null,
					'nombrecompleto' => $this->username,
					'documento' => $clientArea->documento,
					'tipodocumento' => $clientArea->tipodocumento,
					'sharedKeyFromDatas' => $sharedKey,
				));
			} else
				throw new CHttpException(503, Yii::t('commons', 'Error interno del servidor'));

			// Si la peticion retorna el cambio de clave se debe renderizar solo
			// el formulario de cambio de clave
			if ($resp->nombrerespuesta === 'CLA_ACC_CAMBIAR') {
				$this->setState('changePassword', $resp->descripcionrespuesta);
			}
		}

		return !$this->errorCode;
	}

	/***
	 * Added:15-06-2022
	 * @author:Higinio Samaniego ¯¯\('',)/¯¯
	***/
	//Get token data
	public function getTokenData($resp)
	{
		try {

			//ControlCut: If have all data necesary => get token data
			if(!is_null($resp->codigoempresa)){

				$perfil = trim($resp->codigoperfil);

				if($perfil == "COMPLETO" || $perfil == "AUTORIZA"){
					
					//verifica si tiene sharekey al acceder 
					if(isset($resp->sharekey)){
						//funciona esta funcion
						$responseToken = Yii::app()->detectId->retrieveToken->retrieveToken(["sharedKey"=>trim($resp->sharekey)]);
						//codeToken:825->Token existente, 811->Token no asignado, 97->Cliente no existe
						$tokenData = $responseToken->WSEasysolTokenAssignResult;

						$this->setState('codeToken', isset($tokenData->resultCode) ? $tokenData->resultCode : null);
						if($tokenData->resultCode == "825"){
							
							$this->setState('statusToken', isset($tokenData->status) ? $tokenData->status : null);
						}
					}
					
	
				}

			}
			

		} catch (Exception $e) {

			echo $e->getMessage();

		}

		
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setDataType($dataType)
	{
		$this->dataType = $dataType;
	}

	public function getDataType()
	{
		return $this->dataType;
	}

	public function setCompanyDocNum($companyDocNum)
	{
		$this->companyDocNum = $companyDocNum;
	}

	public function getCompanyDocNum()
	{
		return $this->companyDocNum;
	}

	public function setCompanyDocType($companyDocType)
	{
		$this->companyDocType = $companyDocType;
	}

	public function getCompanyDocType()
	{
		return $this->companyDocType;
	}

	public function verifyDetectClient()
	{
		return true;
	}

}
