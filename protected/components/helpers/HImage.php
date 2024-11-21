<?php
/**
 * Image helper functions
 * 
 * @author Chris
 * @link http://con.cept.me
 * @modified by norotaro
 */
class HImage {

	static $baseDir = 'img';
	
	public static function baseUrl($themePath = true)
	{
		return $themePath ?
			Yii::app()->theme->baseUrl.'/'.self::$baseDir.'/' :
			Yii::app()->baseUrl.'/'.self::$baseDir.'/';
	}

	/**
	 * Makes the given URL relative to the self::baseDir directory
	 * @param string $filename the image filename
	 * @return string image relative url
	 */
	public static function url($filename, $themePath = true)
	{
		return self::baseUrl($themePath).$filename;
	}

	public static function html($image, $alt, $htmlOptions=array())
	{
		return CHtml::image(self::url($image), $alt, $htmlOptions);
	}

	/**
	 * Makes the image tag inside link tag
	 * @param string $image the image filename
	 * @param string $linkUrl the url of the link
	 * @param array $linkHtmlOptions the link html options
	 * @return string image tag inside link tag
	 */
	public static function link($image, $linkUrl='#', $htmlOptions=array())
	{
		return CHtml::link(
			CHtml::image(self::url($image),'image',array('align'=>'top'))
			, $linkUrl, $htmlOptions);
	}

	/**
	 * Makes image tag followed by text
	 * @param string $image the image filename
	 * @param string $label the url of the link
	 * @param boolean $reverse whether text should appear before image
	 * @param string align image to text
	 * @return string image tag followed by text
	 */
	public static function label($image, $text='', $reverse=false)
	{
		$image=CHtml::image(self::url($image),'');
		$label=trim($text);
		return $reverse ? $label.' '.$image : $image.' '.$label;
	}

	public static function labelLink($image, $text='', $linkUrl, $htmlOptions=array(), $reverse=false)
	{
		return CHtml::link( self::label($image, $text, $reverse), $linkUrl, $htmlOptions );
	}
}