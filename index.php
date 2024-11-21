<?php
date_default_timezone_set('America/Asuncion');
setlocale(LC_TIME, 'es_PY');

require __DIR__ . '/vendor/autoload.php';

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

function fb($what, $categorie='vardump'){
  echo Yii::log(CVarDumper::dumpAsString($what),'info',$categorie);
}

require_once($yii);
Yii::createWebApplication($config)->run();
