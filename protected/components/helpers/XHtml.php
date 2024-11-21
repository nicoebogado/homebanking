<?php
/**
* XHtml class
*
* This class adds helper methods to CHtml class.
*
* @author Erik Uus <erik.uus@gmail.com>
* @modified by norotaro
* @version 1.0.1
*/

class XHtml extends CHtml
{
	public static function ajaxButton($label, $url, $ajaxOptions=[], $htmlOptions=[])
	{
		$ajaxOptions['url']=$url;
	    $htmlOptions['ajax']=$ajaxOptions;
	    $htmlOptions['type']='button';

	    self::clientChange('click', $htmlOptions);
    	return self::tag('button', $htmlOptions, $label);
	}

	public static function dateCalendarField($form, $model, $attribute=array(), $config=array())
	{
		$defaults = array(
			'mask'			=> '99/99/9999',
			'image'			=> 'small_icons/calendar.png',
			'dateFormat'	=> 'dd/mm/yy',
			'htmlOptions'	=> array('size' => 10),
			'showCal'		=> true,
		);
		$defaults['calOptions'] = array(
			'showOn'			=> 'button',
			'buttonImage'		=> HImage::url($defaults['image']),
			'buttonText'		=> 'Abrir Calendario',
			'buttonImageOnly'	=> true,
			'dateFormat'		=> $defaults['dateFormat'],
			'changeMonth'		=> true,
			'changeYear'		=> true,
		);
		
		if(is_array($config))
			$config = array_merge($defaults, $config);
		else
			throw new Exception("$config debe ser un array", 1);
			
		
		$form->widget('CMaskedTextField', array(
			'model'			=> $model,
			'attribute'		=> $attribute,
			'mask'			=> $config['mask'],
			'htmlOptions'	=> $config['htmlOptions'],
		));
		
		if($config['showCal']) {
			$form->widget('zii.widgets.jui.CJuiDatePicker', array(
				'name'			=> $attribute.'Pick',
				'language'		=> 'es',
				'options'		=> $config['calOptions'],
				'htmlOptions'	=> array(
					'class'	=> 'shadowdatepicker hide',
				),
			));
			
			Yii::app()->getClientScript()->registerScript(
				$attribute.'PickScript',
				"$('#{$attribute}Pick').change(function(){
					$('#".CHtml::activeId($model, $attribute)."').val($(this).val()).focus();
				});"
			);
		}
	}

	/**
	 * Format number
	 * @param string number
	 * @param string number format
	 * @return string formatted number
	 */
	public static function formatNumber($number, $format = '#,##0')
	{
		return $number ?
			Yii::app()->getLocale()->getNumberFormatter()->format($format, $number) : null;
	}

	/**
	 * Format date
	 * @param string date
	 * @param string time format
	 * @return string formatted datetime
	 */
	public static function formatDate($date, $format='dd-MM-yyyy', $null=null)
	{
		$date = strpos($date, '-');
		return $date && $date!=='0000-00-00' ?
			Yii::app()->getLocale()->getDateFormatter()->format($format, $date) : $null;
	}

	public static function getAttribute($get, $from, $where)
	{
		$bound = is_array($where);
		$cmd = Yii::app()->db->createCommand()
			->select($get)
			->from($from)
			->where($bound ? $where[0] : $where, $bound ? $where[1] : array())
			->queryRow();

		return $cmd[$get];
	}

	public static function getAttributes($get, $from, $where, $associative=true)
	{
		$cmd = Yii::app()->db->createCommand()
			->select($get)
			->from($from)
			->where($where)
			->queryRow($associative);

		return $cmd;
	}

	public static function dateReverse($date)
	{
		return implode('-', array_reverse(explode(strpos($date, '-') ? '-' : '/', $date)));
	}
}