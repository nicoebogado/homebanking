<?php
Yii::import('application.components.detectfic.Servicefic');

class Wsotp extends Servicefic
{
    const TAG = 'components.Detectfic.Wsotp';

    function __construct($host)
    {
        parent::__construct($host . '/WSDetectFic/detect/servicios/WSOtp?wsdl');
    }
}