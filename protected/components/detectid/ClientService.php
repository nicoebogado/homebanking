<?php
Yii::import('application.components.detectid.Service');

class ClientService extends Service
{
    const TAG = 'components.DetectID.ClientService';

    function __construct($host)
    {
        parent::__construct($host . '/detect/services/WSClientService?wsdl');
    }
}
