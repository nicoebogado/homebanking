<?php
class HCss {
	/**
	 * Makes the given filename relative to the /css directory
	 * @param string $filename the css filename
	 * @return string css relative url
	 */
	public static function url($filename, $themePath=true)
	{
		return $themePath ?
			Yii::app()->theme->baseUrl.'/css/'.$filename :
			Yii::app()->baseUrl.'/css/'.$filename;
	}

	public static function register($path, $themePath = true)
	{
		if(!is_array($path))
			Yii::app()->getClientScript()->registerCssFile(self::url($path, $themePath));
		else
			foreach ($path as $p)
				Yii::app()->getClientScript()->registerCssFile(self::url($p, $themePath));
	}
}