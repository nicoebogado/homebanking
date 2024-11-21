<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'Home Banking',
	'language' => 'es',
	'sourceLanguage' => 'es',
	'theme' => 'itgf_hb',
	'charset' => 'utf-8',

	// preloading 'log' component
	'preload' => array('log', 'bootstrap', 'less'),

	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.components.helpers.*',
	), 

	// application components
	'components' => array(
		'user' => array(
			'allowAutoLogin' => false,
			'authTimeout' => 20 * 60,
		),
		'session' => array(
			'sessionName' => 'hbsess-test',
			'cookieParams' => [
				'lifetime' => 0,
				'secure' => true,
				'httponly' => true,
				'samesite' => CHttpCookie::SAME_SITE_STRICT,
			],
			'timeout' => 20 * 60,
		),
		'request' => array(
			'enableCsrfValidation' => true,
			'enableCookieValidation' => false,
			'csrfCookie' => array(
				'secure' => true,
				'httpOnly' => true,
				'sameSite' => CHttpCookie::SAME_SITE_STRICT,
			),
		),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',

				'<controller:\w+>/<action:\w+>/<id:\w*>' => '<controller>/<action>',

				'report/invoice/<id:.*>' => 'report/invoice',
			),
		),
		'db' => array(
			'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../data/testdrive.db',
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
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'site/error',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'info, error, warning',
				),
			),
		),
		'bootstrap' => array(
			'class' => 'ext.bootstrap.components.Booster',
			'coreCss' => false,
		),
		'less' => array(
			'class' => 'ext.less.components.LessCompiler',
			//'forceCompile'=>true, // indicates whether to force compiling
			'paths' => array(
				'protected/extensions/bootstrap/less/cyrulean/bootstrap.less' => 'themes/default/css/bootstrap.css',
				'protected/extensions/bootstrap/less/cosmo/bootstrap.less' => 'themes/cosmo/css/bootstrap.css',
				//'protected/extensions/bootstrap/assets/less_cyrulean/bootstrap.less'=>'themes/default/css/bootstrap.css',
			),
		),
		'bancardClient' => array(
			'class' => 'application.components.BancardClient',
			'settings' => array(
				//'baseUri' => 'https://10.0.200.38/billing-public-proxy/api/0.1/',
				//'publicKey' => 'PP7jwJX9uvP8jvLhC1f2BjWkWmPNQbfA', 
				//'privateKey' => '9xZq3$gq7cMSn2NtKdzEsoVR3fZc00CapC,uuLSl',
				'baseUri' => 'https://10.0.200.38:4477/billing-public-proxy/api/0.1/',
				'publicKey' => 'Q8plRwawue2b40fgr10ws9k0OIIQnRLZ',
				'privateKey' => 'C6RFzFmcTHuLTSYa.lTLm9QxaqRPvB10XQhIKsVC',

			),
		),
		'creditCardClient' => array(
			'class' => 'application.components.CreditCardClient',
			'settings' => array(
				'testingMode' => false,
				'user' => 'E180PRUEBA',
				'password' => 'E180PRUEBA',
			),
		),

		'MultiMailer' => array(
			'class' => 'ext.MultiMailer.MultiMailer',
			'setFromAddress' => 'sistemas@fic.com.py',
			'setFromName' => 'Fic de Finanzas',
			'setMethod'      => 'SMTP',
			'setOptions'     => array(
				'Host'     => 'smtp.office365.com',
				'Username' => 'sistemas@fic.com.py',
				'Password' => 'Rum99602',
				'Port'     => 587,
				'SMTPSecure' => 'tls',
			),
		),
		'detectId' => array(
			'class' => 'application.components.DetectId',
			'settings' => array(
				'host' => 'http://apghmll01.fic.com.py:8080',
			),
		),
		'detectFic' => array(
			'class' => 'application.components.DetectFic',
			'settings' => array(
				'host' => 'http://10.90.20.171:8080',
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
		//'wsdlUrl' => 'http://10.90.20.132:8080/Productos/Services?wsdl',
		'wsdlUrl' => 'http://10.90.20.171:8080/Productos/Services?wsdl',
		'maskedAccountNumber' => 'N',
		//'timerDBcheck' => '3600000', // 15 segundos 
		'timerDBcheck' => '7200000', // Cambiado al doble
		//'apiVerificarDetectFic' => 'https://10.90.7.16/apispi/public/api/',
		'apiVerificarDetectFic' => 'https://secure.hml.fic.com.py/test/apispi/public/api/',
	),
);
