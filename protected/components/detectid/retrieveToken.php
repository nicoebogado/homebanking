<?php
Yii::import('application.components.detectid.Service');

class RetrieveToken extends Service
{
    const TAG = 'components.DetectID.RetrieveToken';

    function __construct($host)
    {
        parent::__construct($host . '/detect/services/WSEasysolToken?wsdl');
    }
}