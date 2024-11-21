<?php
class HScript {
	/**
	 * Makes the given URL relative to the /js directory
	 * @param string $filename the js filename
	 * @return string js relative url
	 */
	public static function url($filename)
	{
		return Yii::app()->baseUrl.'/js/'.$filename.'.js';
	}

	public static function register($path)
	{
		if(!is_array($path))
			Yii::app()->getClientScript()->registerScriptFile(self::url($path), CClientScript::POS_END);
		else
			foreach ($path as $p) {
				Yii::app()->getClientScript()->registerScriptFile(self::url($p), CClientScript::POS_END);
			}
	}
	public static function registerExternal($paths)
	{
		$paths = is_array($paths) ? $paths : [$paths];

		foreach ($paths as $path) {
			Yii::app()->getClientScript()->registerScriptFile($path, CClientScript::POS_END);
		}
	}

	public static function registerCode($name, $code)
	{
		Yii::app()->getClientScript()->registerScript($name, $code, CClientScript::POS_END);
	}

	public static function registerPlugin($name)
	{
		self::registerScript('plugins/jquery.'.$name);
	}
}