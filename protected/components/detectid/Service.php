<?php

abstract class Service extends CComponent
{
    const TAG = 'components.DetectID.Service';

    protected $wsdlUrl;
    protected $client;
    protected $methods;

    function __construct($wsdlUrl)
    {
        $this->wsdlUrl = $wsdlUrl;
        $this->connect();
    }

    /**
     * Busca la funcion en la lista de funciones disponibles y lo llama
     * Si no encuentra llama al metodo __call padre
     */
    public function __call($method, $params)
    {
        if (array_search($method, $this->methods) !== false) {
            $params = isset($params[0]) ? $params[0] : array();
            return $this->callSoapMethod($method, $params);
        }

        parent::__call($method, $params);
    }

    protected function connect()
    {
        try {
            $wsdl = $this->wsdlUrl;
            $this->client    = new SoapClient($wsdl, array(
                'trace'         => true,
                'cache_wsdl'    => WSDL_CACHE_NONE,
                'exceptions'    => true,
                'features'      => SOAP_SINGLE_ELEMENT_ARRAYS,
            ));

            // establecer la lista de funciones disponibles
            $this->methods = array_map(function ($f) {
                $pattern = '/^\w+\s(\w+).*$/';
                $replacement = '$1';
                return preg_replace($pattern, $replacement, $f);
            }, $this->client->__getFunctions());
        } catch (SoapFault $e) {
            throw new CHttpException(503, Yii::t('components', 'El servicio se encuentra temporalmente inaccesible. Por favor intente más tarde.'));
            Yii::app()->end();
        }
    }

    /**
     * Invoca un servicio
     */
    protected function callSoapMethod($method, $params = array())
    {
        $paramsObj = new stdClass();
        foreach ($params as $p => $val) {
            $paramsObj->{$p} = $val;
        }

        try {
            $response = $this->client->{$method}($paramsObj);
        } catch (Exception $e) {
            Yii::log($e->getMessage(), 'error', static::TAG);
            Yii::log($this->client->__getLastRequestHeaders(), 'info', static::TAG);
            Yii::log($this->client->__getLastRequest(), 'info', static::TAG);
            Yii::log($this->client->__getLastResponseHeaders(), 'info', static::TAG);
            Yii::log($this->client->__getLastResponse(), 'info', static::TAG);
        }

        $this->_log();
        return $response;
    }

    private function _log()
    {
        Yii::log($this->client->__getLastRequest(), 'info', static::TAG);
        Yii::log($this->client->__getLastResponse(), 'info', static::TAG);
    }
}
