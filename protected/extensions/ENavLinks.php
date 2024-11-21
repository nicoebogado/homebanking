<?php
/**
* Widget para links
*/
class ENavLinks extends CWidget
{
	public $links=array();
	public $class;

	function init()
	{
		if (empty($this->links)) {
			throw new CException('El parámetro link no puede estar vacío');
		}
		if (!is_array($this->links)) {
			throw new CException('El parámetro link debe ser un array');
		}
		if (empty($this->class)) {
			$this->class = 'nav nav-pills pull-right';
		} else
			$this->class .= ' nav nav-pills';

		parent::init();
	}

	public function run()
	{
		$code = CHtml::openTag('div', array('class'=>'row'));
		$code .= CHtml::openTag('div', array('class'=>'col-md-12'));
		$code .= CHtml::openTag('ul', array('class'=>$this->class));
		$pipe = false;
		foreach ($this->links as $key => $value) {
			if($pipe) $code .= CHtml::tag('li', array('style'=>'padding-top:5px; color:#DDD'), '|'); //'<li style="vertical-align:center">|</li>';
			else $pipe = true;
			$code .= CHtml::openTag('li');
			$code .= CHtml::link($key, $value);
			$code .= '</li>';
		}
		$code .= '</ul></div></div>';

		echo $code;
	}
}