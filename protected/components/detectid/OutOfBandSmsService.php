<?php
Yii::import('application.components.detectid.Service');

class OutOfBandSmsService extends Service
{
    const TAG = 'components.DetectID.OutOfBandSmsService';

    function __construct($host)
    {
        parent::__construct($host . '/detect/services/WSOutOfBandSmsService?wsdl');
    }
}
