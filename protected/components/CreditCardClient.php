<?php
Yii::import('application.components.creditcardclient.BalanceRequest');
Yii::import('application.components.creditcardclient.LastStatementRequest');
Yii::import('application.components.creditcardclient.PreviousPeriodsRequest');
Yii::import('application.components.creditcardclient.PreviousStatementRequest');

/**
 * CreditCardClient application component
 */ 
class CreditCardClient extends CApplicationComponent
{
    private static $tag = 'components.CreditCardClient';
    private $_testingMode = true;
    private $_credentials = [];

    public function setSettings($settings)
    {
        $this->_testingMode = $settings['testingMode'];
        $this->_credentials = [
            'user' => $settings['user'],
            'password' => $settings['password'],
        ];
    }

    /**
     * Consulta de saldo
     */
    public function balance($ccNumber)
    {
        $client = $this->_getClient('001');
        $params = new BalanceRequest($ccNumber, $this->_credentials);

        return $this->_execute($client, $params);
    }

    /**
     * Extracto al día
     */
    public function lastStatement($ccNumber)
    {
        $client = $this->_getClient('002');
        $params = new LastStatementRequest($ccNumber, $this->_credentials);

        return $this->_execute($client, $params);
    }

    /**
     * Ciclos anteriores
     */
    public function previousPeriods($ccNumber)
    {
        $client = $this->_getClient('003');
        $params = new PreviousPeriodsRequest($ccNumber, $this->_credentials);

        return $this->_execute($client, $params);
    }

    /**
     * Extractos de Ciclos anteriores
     */
    public function previousStatement($ccNumber, $period)
    {
        $client = $this->_getClient('004');
        $params = new PreviousStatementRequest($ccNumber, $period, $this->_credentials);

        return $this->_execute($client, $params);
    }

    private function _getClient($wsdlId)
    {
        try {

            $baseUri = $this->_testingMode ?
                dirname(__FILE__) . '/creditcardclient/accesoprt/switchprd.apvp' :
				'https://sbc.bancardnet.dom/acceso/servlet/switchprd.apvp';
                //'https://10.251.2.122/acceso/servlet/switchprd.apvp';
			
            $context = stream_context_create([
                'ssl' => [
                    'ciphers' => 'SHA256',
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
			
            return new SoapClient($baseUri . $wsdlId . '?wsdl', [
                'cache_wsdl'    => WSDL_CACHE_NONE,
                'exceptions'    => true,
                'trace'         => true,
                'stream_context' => $context,
                'features'      => SOAP_SINGLE_ELEMENT_ARRAYS,
            ]);
        } catch (SoapFault $e) {
            throw new CHttpException(503, Yii::t('components', 'El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'));
            Yii::app()->end();
        } catch (Exception $e) {
            Yii::log('Exception message: ' . $e->getMessage(), 'error', self::$tag);

            throw new CHttpException(503, Yii::t('components', 'El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'));
        }
    }

    private function _execute($client, $params)
    {
        try {
            return $client->Execute($params);
        } catch (SoapFault $e) {
            Yii::log('Exception message: ' . $e->faultcode . ' - ' . $e->faultstring, 'error', self::$tag);
            Yii::log('Last request headers: ' . $client->__getLastRequestHeaders(), 'error', self::$tag);
            Yii::log('Last request body: ' . $client->__getLastRequest(), 'error', self::$tag);
            Yii::log('Last response headers: ' . $client->__getLastResponseHeaders(), 'error', self::$tag);
            Yii::log('Last reponse body: ' . $client->__getLastResponse(), 'error', self::$tag);

			Yii::log('Exception message: ' . $e->getMessage(), 'error', self::$tag);
            throw new CHttpException(503, Yii::t('components', 'El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'));
        } catch (Exception $e) {
            Yii::log('Exception message: ' . $e->getMessage(), 'error', self::$tag);

            throw new CHttpException(503, Yii::t('components', 'El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'));
        }
    }
}
