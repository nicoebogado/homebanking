<?php
Yii::import('application.components.detectid.Service');

class EasysolToken extends Service
{
    const TAG = 'components.DetectID.EasysolToken';

    function __construct($host)
    {
        parent::__construct($host . '/detect/services/WSEasysolToken?wsdl');
    }
}
