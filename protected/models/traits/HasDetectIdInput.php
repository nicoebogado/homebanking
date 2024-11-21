<?php

trait HasDetectIdInput
{
    public $detectIdMobileToken;
    public $detectIdOobSms;

    public function detectIdValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->detectIdMobileToken) && empty($this->detectIdOobSms)) {
				
                $this->addError('detectIdMobileToken', 'No se recibió ningún código');
                $this->addError('detectIdOobSms', 'No se recibió ningún código');
            }
			
			//COLOCAR VALIDACION DE NUEVO DETECTID FIC AQUI
			$respuesta = $this->__verificarDetectFic();
            $auxiliar = json_decode($respuesta);
		
			if($auxiliar->result == 'S'){//SI TIENE DETECT DE FIC
        
					if($attribute === 'detectIdOobSms' && $this->detectIdOobSms){

                    $otp = $this->detectIdOobSms;
                    $codCanal = '999';
                    $arg0 = array('sharedKey' => Yii::app()->user->getState('sharedKey'),'codCanal' => $codCanal,'otp' => $otp,'origen' => 'HOMEBANKING');
                    
                    $response = Yii::app()->detectFic->wsotp->validarOtp([
                        'arg0' => $arg0,
                    ])->return;
                
                    if($response->resultCode != 0){                    

                        $msg =  'Error en validación de OTP';
                        $this->addError('detectIdOobSms', $msg);
                        
                    }

                }
				
			}else{
			

			    if ($attribute === 'detectIdOobSms' && $this->detectIdOobSms) {
					$response = Yii::app()->detectId->outOfBandSmsService->validateSmsOtpCode([
						'sharedKey' => Yii::app()->user->getState('sharedKey'),
						'otpCode' => $this->detectIdOobSms,
					])->validateSmsOtpCodeResult;

					if ($response->resultCode !== 901) {
						
						$response = Yii::app()->detectId->easysolToken->validate([
                            'sharedKey' => Yii::app()->user->getState('sharedKey'),
                            'otp' => $this->detectIdOobSms,
                        ])->easysolTokenValidationResult;

                        if ($response->resultCode !== 801) {
                            //$msg = $response->resultDescription . ' Te quedan ' . $response->remainingFailedAttempts . ' intentos.';
                            $msg =  'Error en validación de OTP, Te quedan ' . $response->remainingFailedAttempts . ' intentos.';
                            $this->addError('detectIdMobileToken', $msg);
                        }
					}
				}
			}
		
		}
    }

    protected function addDetectIdInputRules(array $rules)
    {
        $rules[] = ['detectIdMobileToken, detectIdOobSms', 'safe'];
        $rules[] = ['detectIdMobileToken, detectIdOobSms', 'detectIdValidation', 'on' => 'confirm'];

        return $rules;
    }

    protected function addDetectIdAttributeLabels(array $labels)
    {
        $labels['detectIdMobileToken'] = 'Mobile Token';
        $labels['detectIdOobSms'] = 'OTP TOKEN'; //ESTABA COMO OTP SMS

        return $labels;
    }

    protected function addDetectIdInputsConfig(array $formConfig, $sendSms = true)
    {
    
        $factors = Yii::app()->detectId->availableFactors;

        //EN TEORIA EL BOTON SIEMPRE VA SER DE OUT OF BAND SMS, DEBERIA VENIR LO QUE EL CLIENTE TIENE EN EL DETECT VIEJO.
        $factors = ['OUT_OF_BAND_SMS'];

        if (!in_array('OUT_OF_BAND_SMS', $factors) && !in_array('MOBILE_AUTHENTICATION', $factors)) {
            //throw new Exception('No posee ningún método de autenticación para confirmar la operación');
			
			//ACA CARGAMOS FORMULARIO DE DETECT POR QUE IGUAL ESTA EL DE FIC
			$formConfig['elements']['detectIdMobileToken'] = $this->mobileTokenInputConfig(['OUT_OF_BAND_SMS']);
			
			
			$respuesta = $this->__verificarDetectFic();
				$auxiliar = json_decode($respuesta);				
				
				if($auxiliar->result == 'S'){//SI TIENE DETECT DE FIC
        
					$codCanal = '999';
					$arg0 = array('sharedKey' => Yii::app()->user->getState('sharedKey'),'codCanal' => $codCanal);
					
					$response = Yii::app()->detectFic->wsotp->generarOtp([
						'arg0' => $arg0,
					])->return;
					 
					if($response->resultCode == '0'){
						
						$wsClient = new WebServiceClient();
						$wsClient->getSendMessage([
							'message' => 'FIC S.A. Informa: Su codigo de seguridad para confimar la operacion es ' . $response->otpCode,
						]);
						
					}else{
						
						$this->render('error', array(
                            'code' => 503,
                            'type' => 'CHttpException',
                            'message' => 'Error: ' . $response->resultDescription,
                        ));
                        Yii::app()->end();
						
					}
					
				}
			
        }

        if (in_array('MOBILE_AUTHENTICATION', $factors)) {
		
            $formConfig['elements']['detectIdMobileToken'] = $this->mobileTokenInputConfig($factors);
        }

        if (in_array('OUT_OF_BAND_SMS', $factors)) {
			//ACA ENTRA SI TIENE token
			
			
            if (!in_array('MOBILE_AUTHENTICATION', $factors) && $sendSms) {
				
				
				//Needed to include the new function to know if or not in the new otp of FIC
				$respuesta = $this->__verificarDetectFic();
				$auxiliar = json_decode($respuesta);				
				
				if($auxiliar->result == 'S'){//SI TIENE DETECT DE FIC
        
					$codCanal = '999';
					$arg0 = array('sharedKey' => Yii::app()->user->getState('sharedKey'),'codCanal' => $codCanal);
					
					$response = Yii::app()->detectFic->wsotp->generarOtp([
						'arg0' => $arg0,
					])->return;
					 
					if($response->resultCode == '0'){
						
						$wsClient = new WebServiceClient();
						$wsClient->getSendMessage([
							'message' => 'FIC S.A. Informa: Su codigo de seguridad para confimar la operacion es ' . $response->otpCode,
						]);
						
					}else{
						
						$this->render('error', array(
                            'code' => 503,
                            'type' => 'CHttpException',
                            'message' => 'Error: ' . $response->resultDescription,
                        ));
                        Yii::app()->end();
						
					}
					
				}else{//ENTRA AL VIEJO DETECT A OBTENER LA OTP
					
					$response = Yii::app()->detectId->outOfBandSmsService->retrieveNewOTP([
                    'sharedKey' => Yii::app()->user->getState('sharedKey'),
					])->WSRetrieveOtpResult;
					if ($response->resultCode === 1020) {
						
						try {
							$wsClient = new WebServiceClient();
						} catch (Exception $e) {
							$this->render('error', array(
								'code' => 503,
								'type' => 'CHttpException',
								'message' => 'Error: ' . $e->getMessage(),
							));
							Yii::app()->end();
						}
						// comentamos para probar
                        //SE COMENTO PARA QUE NO ENVIE AL INICIO DEL FORM
						/*$wsClient->getSendMessage([
							'message' => 'FIC S.A. Informa: Su codigo de seguridad para confimar la operacion es ' . $response->otp,
						]);*/
					}
				}
								
                
            }

            $formConfig['elements']['detectIdOobSms'] = $this->oobSmsInputConfig($factors);
        }


        return $formConfig;
    }

    protected function mobileTokenInputConfig($factors)
    {

        $hint = 'Ingrese el Mobile Token que aparece en su aplicación móvil';

        if (in_array('OUT_OF_BAND_SMS', $factors)) {
            $hint .= ' o <a href="#">enviar código SMS</a> en su lugar';
        }

        return [
            'type' => 'text',
            'hint' => $hint,
            'visible' => true,
            'autocomplete' => 'off',
            'widgetOptions' => [
                'htmlOptions' => [
                    'autocomplete' => 'off',
                    'class' => 'detect-id-input mobile-token-input',
                ],
            ],
        ];
    }

    protected function oobSmsInputConfig($factors)
    {
		
        /*$hint = 'Ingrese el código que le enviamos por SMS';

        if (in_array('MOBILE_AUTHENTICATION', $factors)) {
            $hint .= ' o <a href="#" class="change-input">ingresar Mobile token</a> en su lugar';
        }
	
        $hint .= '<br>No te llegó el SMS? <a href="#" class="resend-sms">Enviar uno nuevo</a>';*/
        //SE AGREGO LOS TEXTOS DE MOBILE TOKEN EN ESTA SECCION PARA QUE PUEDA TOMAR LOS VALORES SIN IMPORTAR QUE TENGA EL USUARIO

        $hint = 'Ingrese el Mobile Token que aparece en su aplicación móvil';

        if (in_array('OUT_OF_BAND_SMS', $factors)) {
            $hint .= ' o <a href="#" class="resend-sms" >enviar código SMS</a> en su lugar';
        }

        return [
            'type' => 'text',
            'hint' => $hint,
            'visible' => true,
            'widgetOptions' => [
                'htmlOptions' => [
                    'autocomplete' => 'off',
                    'class' => 'detect-id-input oob-sms-input',
                ],
            ],
        ];
    }
	
	//Function to verify in the new otp system
	private function __verificarDetectFic(){
        
        $url = Yii::app()->params->apiVerificarDetectFic;
		
		$documento = Yii::app()->user->getState('documento');
		
		if(Yii::app()->user->getState('empresa') != ''){
			$documento = Yii::app()->user->getState('documentoX');
		}
		
       
        $fields_string="documento=".$documento;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url."detecFic");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}
