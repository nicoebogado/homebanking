<?php

Yii::import('zii.widgets.CMenu');

class SidebarMenu extends CMenu
{
	public $encodeLabel = false;

	public function init() {
		
		parent::init();

		$classes = array('nav');

		$classes = implode(' ', $classes);
		if (isset($this->htmlOptions['class'])) {
			$this->htmlOptions['class'] .= ' ' . $classes;
		} else {
			$this->htmlOptions['class'] = $classes;
		}

		$this->submenuHtmlOptions = array(
          'class' => 'sidebar-subnav collapse',
        );

	}
	protected function renderMenu($items) {
		
		$n = count($items);
		$htmlCode = '';

		if ($n > 0) {
			$htmlCode .= CHtml::openTag('ul', $this->htmlOptions) . "\n";

			$count = 0;
			foreach ($items as $item) {
				$count++;
				if (isset($item['divider'])) {
					$htmlCode .= "<li class=\"{$this->getDividerCssClass()}\"></li>\n";
				} else {
					$options = isset($item['itemOptions']) ? $item['itemOptions'] : array();
					$classes = array();

					if ($item['active'] && $this->activeCssClass != '') {
						$classes[] = $this->activeCssClass;
					}

					if ($count === 1 && $this->firstItemCssClass !== null) {
						$classes[] = $this->firstItemCssClass;
					}

					if ($count === $n && $this->lastItemCssClass !== null) {
						$classes[] = $this->lastItemCssClass;
					}

					if ($this->itemCssClass !== null) {
						$classes[] = $this->itemCssClass;
					}

					if (isset($item['disabled'])) {
						$classes[] = 'disabled';
					}

					if(empty($item['icon'])) {
						$item['icon'] = false;
					}

					if (!empty($classes)) {
						$classes = implode(' ', $classes);
						if (!empty($options['class'])) {
							$options['class'] .= ' ' . $classes;
						} else {
							$options['class'] = $classes;
						}
					}

					$htmlCode .= CHtml::openTag('li', $options) . "\n";

					$menu = $this->renderMenuItem($item);

					if (isset($this->itemTemplate) || isset($item['template'])) {
						$template = isset($item['template']) ? $item['template'] : $this->itemTemplate;
						$htmlCode .= strtr($template, array('{menu}' => $menu));
					} else {
						$htmlCode .= $menu;
					}

					if (isset($item['items']) && !empty($item['items'])) {

						$htmlOptions = isset($item['submenuOptions']) ? $item['submenuOptions']
								: $this->submenuHtmlOptions;
						if(empty($htmlOptions['id']) && isset($item['submenuId']))
							$htmlOptions['id'] = $item['submenuId'];

						array_unshift($item['items'], array(
							'label' => $item['label'],
							'itemOptions' => array(
								'class' => 'sidebar-subnav-header',
							),
						));

						$submenuOptions = array(
							'encodeLabel' => $this->encodeLabel,
							'htmlOptions' => $htmlOptions,
							'items' => $item['items'],
						);
						$submenuOptions['id'] = isset($submenuOptions['htmlOptions']['id']) ? 
							$submenuOptions['htmlOptions']['id'] : null;
						$htmlCode .= $this->controller->widget('application.components.widgets.SidebarMenu', $submenuOptions, true);
					}

					$htmlCode .= "</li>\n";
				}
			}

			$htmlCode .= "</ul>\n";
		}

		echo $htmlCode;
	}

	protected function renderMenuItem($item) {

		$item['label'] = (!empty($item['icon']) ? '<em class="' . $item['icon'] . '"></em>' : '') .
			'<span>' . $item['label'] . '</span>';

		if (!isset($item['linkOptions'])) {
			$item['linkOptions'] = array();
		}

		if (isset($item['linkTitle'])) {
			$item['linkOptions']['title'] = $item['linkTitle'];
		}

		if (isset($item['items']) && !empty($item['items'])) {
			if (empty($item['url'])) {
				$item['url'] = isset($item['submenuId']) ? '#'.$item['submenuId'] : '#';
			}

			if (isset($item['linkOptions']['class'])) {
				$item['linkOptions']['class'] .= ' collapsed';
			} else {
				$item['linkOptions']['class'] = 'collapsed';
			}

			$item['linkOptions']['data-toggle'] = 'collapse';
		}

		if (isset($item['url'])) {
			return CHtml::link($item['label'], $item['url'], $item['linkOptions']);
		} else {
			return $item['label'];
		}
	}

	public function getDividerCssClass() {
		
		return 'nav-divider';
	}
}