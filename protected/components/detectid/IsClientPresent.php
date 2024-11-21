<?php
Yii::import('application.components.detectid.Service');

class IsClientPresent extends Service
{
    const TAG = 'components.DetectID.IsClientService';

    function __construct($host)
    {
        parent::__construct($host . '/detect/services/WSClientService?wsdl');
    }
}