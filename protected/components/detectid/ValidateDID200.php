<?php
Yii::import('application.components.detectid.Service');

class ValidateDID200 extends Service
{
    const TAG = 'components.DetectID.ValidateDID200';

    function __construct($host)
    {
        parent::__construct($host . '/detect/services/WSEasysolToken?wsdl');
    }
}