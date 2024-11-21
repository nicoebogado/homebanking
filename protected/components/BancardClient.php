<?php

use GuzzleHttp\Client;

/**
 * Bancard client class
 */
class BancardClient extends CApplicationComponent
{
    const TAG = 'components.BancardClient';

    private $_baseUri;
    private $_publicKey;
    private $_privateKey;
    private $_client;

    public function init()
    {
        $this->_client = new Client([
            'base_uri'  => $this->_baseUri,
            'timeout'   => 60.0,
            'verify'    => false,
            'auth'      => [
                $this->_publicKey,
                $this->_privateKey
            ],
            'headers'   => [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/json',
            ],
        ]);

        parent::init();
    }

    public function setSettings($settings)
    {
        $this->_baseUri = $settings['baseUri'];
        $this->_publicKey = 'apps/' . $settings['publicKey'];
        $this->_privateKey = $settings['privateKey'];
    }

    //---------- SERVICES -----------//

    public function brands()
    {
        return $this->_makeGetRequest('brands');
    }

    public function services($serviceId)
    {
        return $this->_makeGetRequest('services/' . $serviceId);
    }

    public function invoices($serviceId, $customerFields)
    {
        $query = 'service_id=' . $serviceId;
        foreach ($customerFields as $val) {
            $query .= '&customer_fields[]=' . $val;
        }
        return $this->_makeGetRequest('invoices/?' . $query);
    }

    public function commissions($serviceId, $amount)
    {
        return $this->_makeGetRequest('services/' . $serviceId . '/commissions?amount=' . $amount);
    }

    public function payment($serviceId, $payload)
    {
        return $this->_makePostRequest('services/' . $serviceId . '/payment', $payload);
    }

    public function reverse($serviceId, $payload)
    {
        return $this->_makePostRequest('services/' . $serviceId . '/reverse', $payload, false);
    }

    //---------- PRIVATE FUNCTIONS -----------//

    private function _makeGetRequest($url)
    {
        Yii::log('Making a GET request to: ' . $url, 'info', self::TAG);
        try {
            return $this->_checkResponse($this->_client->get($url));
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->_logError($e);
        }
    }

    private function _makePostRequest($url, $payload = [], $throwException = true)
    {
        Yii::log('Making a POST request to: ' . $url . ' with payload: ' . json_encode($payload), 'info', self::TAG);
        $response = null;

        try {
            $response = $this->_checkResponse($this->_client->post($url, [
                'json' => $payload,
            ]));
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->_logError($e, $throwException);
        }

        return $response;
    }

    public function _logError($e, $throwException = true)
    {
        $response = 'Error en llamada remota';

        Yii::log(\GuzzleHttp\Psr7\str($e->getRequest()), 'error', self::TAG);
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            Yii::log(\GuzzleHttp\Psr7\str($response), 'error', self::TAG);
            $response = json_decode($response->getBody());
            $response = $response->messages[0]->dsc;
        }

        if ($throwException) throw new CHttpException(500, $response);
    }

    private function _checkResponse($response)
    {
        if ($response->getStatusCode() == 200) {
            // anotar en el log la respuesta
            Yii::log($this->_responseToString($response), 'info', self::TAG);
            return json_decode($response->getBody());
        }

        throw new CHttpException(500, 'Error en llamada remota: ' . $response->getReasonPhrase());
    }

    private function _responseToString($response)
    {
        $result = '';
        foreach ($response->getHeaders() as $name => $values) {
            $result .= $name . ': ' . implode(', ', $values) . "\r\n";
        }

        $result .= $response->getBody();

        return $result;
    }
}
