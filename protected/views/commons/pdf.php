<?php
$class = isset($class) ? $class : 'EPDF';
$path=Yii::getPathOfAlias('ext.pdf.'.$class);

$this->widget('ext.pdf.'.$class, array(
	'id'		=> 'informe-pdf',
	'fileName'	=> $fileName,
	'datas'		=> $datas,
	'config'	=> empty($config) ? [] : $config,
));