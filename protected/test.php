<?php

$wsClient =new SoapClient('http://10.192.18.21:8080/Productos/Services?wsdl', array(
                                'trace'                 => true,
                                'cache_wsdl'    => WSDL_CACHE_NONE,
                                'exceptions'    => true,
                                'features'              => SOAP_SINGLE_ELEMENT_ARRAYS,
                        ));
$params = new stdClass;
$params->document = '1128702';
$params->data = '97086';
$params->password = '0a7d6c98d575e6a32eb0135f4b65c6715472c87435ea9a067c78c31388680689';
$params->dataType = 'K';
$params->companyDocNum = null;
$params->companyDocType = null;
$params->channel = '999';
$response = $wsClient->startSessionDocument($params);

var_dump($response);

$params = new stdClass;
$params->channel = '999';
$response = $wsClient->getMenu($params);

var_dump($response);
