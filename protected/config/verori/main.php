<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Home Banking',
	'language'=>'es',
	'sourceLanguage'=>'es',
	'theme'=>'itgf_hb',
	'charset'=>'utf-8',

	// preloading 'log' component
	'preload'=>array('log', 'bootstrap', 'less'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.helpers.*',
	),

	// application components
	'components'=>array(
		'user'=>array(
			'allowAutoLogin'=>false,
			'authTimeout'=>20*60,
		),
		'session'=>array(
			'sessionName'=>'hbsess-test',
			'cookieParams'=>[
				'lifetime' => 0,
				//'secure' => true,
				'httponly' => true,
			],
			'timeout'=>20*60,
		),
		'request' => array(
            'enableCsrfValidation'=>true,
            'enableCookieValidation'=>false,
        ),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

				'<controller:\w+>/<action:\w+>/<id:\w*>'=>'<controller>/<action>',

				'report/invoice/<id:.*>'=>'report/invoice',
			),
		),
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'info, error, warning',
				),
			),
		),
		'bootstrap'=>array(
			'class'=>'ext.bootstrap.components.Booster',
	        'coreCss'=>false,
		),
		'less'=>array(
			'class'=>'ext.less.components.LessCompiler',
			//'forceCompile'=>true, // indicates whether to force compiling
			'paths'=>array(
				'protected/extensions/bootstrap/less/cyrulean/bootstrap.less'=>'themes/default/css/bootstrap.css',
				'protected/extensions/bootstrap/less/cosmo/bootstrap.less'=>'themes/cosmo/css/bootstrap.css',
				//'protected/extensions/bootstrap/assets/less_cyrulean/bootstrap.less'=>'themes/default/css/bootstrap.css',
			),
		),
		'bancardClient'=>array(
			'class'=>'application.components.BancardClient',
			'settings'=>array(
				'baseUri'=>'https://10.0.200.38:4477/billing-public-proxy/api/0.1/',
				'publicKey'=>'2Ojm2L6JDb7vgvUi1xDGKOpytsia0ljo',
				'privateKey'=>'7UziszDsIJjTSwGmm2pu$d5q8NFiqsS$j5R2+jTg',
			),
		),
		'creditCardClient'=>array(
			'class'=>'application.components.CreditCardClient',
			'settings'=>array(
				'baseUri'=>'https://10.0.110.122/accesotst/servlet/switchprd.apvp',
                                 //'baseUri' => 'https://10.251.2.122/acceso/servlet/switchprd.apvp',
				'user'=>'E137PRUEBA',
				'password'=>'E137PRUEBA',
			),
		),
		'detectId' => array(
			'class' => 'application.components.DetectId',
			'settings' => array(
				'host' => 'https://secure.fic.com.py',
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'wsdlUrl'=>'http://ws-int.hml.fic.com.py:80/Productos/Services?wsdl',
		'maskedAccountNumber' => 'N',
        'timerDBcheck' => '3600000', // 15 segundos
	),
);
