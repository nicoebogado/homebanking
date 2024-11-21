<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
if(!strpos($_SERVER['SCRIPT_NAME'], 'skelgen'))
require_once(dirname(__FILE__).'/WebTestCase.php');
//require_once( Yii::getPathOfAlias('system.test.CTestCase').'.php' );


Yii::createWebApplication($config);
