<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'modules'=>array(
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'password'=>'enter',
				// If removed, Gii defaults to localhost only. Edit carefully to taste.
				//'ipFilters'=>array('127.0.0.1','::1'),
			),
		),
		'components'=>array(
			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'trace, info, profile, error, warning',
					),
					array(
                       'class'=>'CWebLogRoute',
                       'levels'=>'info, profile, warning, error',
                    ),
				),
			),
		),
		'params'=>array(
			'wsdlUrl'=>'http://10.192.18.21:8080/Productos/Services?wsdl',
                     'maskedAccountNumber' => 'Y',
                     'timerDBcheck' => '3600000', // 15 segundos
		),
	)
);
