<?php

/**
 *## TbBaseMenu class file.
 *
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

Yii::import('zii.widgets.CMenu');

/**
 *## Base class for menu in Booster
 *
 * @package booster.widgets.navigation
 */
abstract class TbBaseMenu extends CMenu
{
	/**
	 *### .getDividerCssClass()
	 *
	 * Returns the divider css class.
	 * @return string the class name
	 */
	abstract public function getDividerCssClass();

	/**
	 *### .getDropdownCssClass()
	 *
	 * Returns the dropdown css class.
	 * @return string the class name
	 */
	abstract public function getDropdownCssClass();

	/**
	 *### .isVertical()
	 *
	 * Returns whether this is a vertical menu.
	 * @return boolean the result
	 */
	abstract public function isVertical();

	/**
	 *### .renderMenu()
	 *
	 * Renders the menu items.
	 *
	 * @param array $items menu items. Each menu item will be an array with at least two elements: 'label' and 'active'.
	 * It may have three other optional elements: 'items', 'linkOptions' and 'itemOptions'.
	 */
	protected function renderMenu($items)
	{

		$n = count($items);

		if ($n > 0) {
			echo CHtml::openTag('ul', $this->htmlOptions) . "\n";

			$count = 0;
			foreach ($items as $item) {
				$count++;

				if (isset($item['divider'])) {
					echo "<li class=\"{$this->getDividerCssClass()}\"></li>\n";
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

					if (isset($item['items'])) {
						$classes[] = $this->getDropdownCssClass();
					}

					if (isset($item['disabled'])) {
						$classes[] = 'disabled';
					}

					if (!empty($classes)) {
						$classes = implode(' ', $classes);
						if (!empty($options['class'])) {
							$options['class'] .= ' ' . $classes;
						} else {
							$options['class'] = $classes;
						}
					}

					echo CHtml::openTag('li', $options) . "\n";

					$menu = $this->renderMenuItem($item);

					if (isset($this->itemTemplate) || isset($item['template'])) {
						$template = isset($item['template']) ? $item['template'] : $this->itemTemplate;
						echo strtr($template, array('{menu}' => $menu));
					} else {
						echo $menu;
					}

					if (isset($item['items']) && !empty($item['items'])) {
						$dropdownOptions = array(
							'encodeLabel' => $this->encodeLabel,
							'htmlOptions' => isset($item['submenuOptions']) ? $item['submenuOptions']
								: $this->submenuHtmlOptions,
							'items' => $item['items'],
						);
						$dropdownOptions['id'] = isset($dropdownOptions['htmlOptions']['id']) ?
							$dropdownOptions['htmlOptions']['id'] : null;
						$this->controller->widget('booster.widgets.TbDropdown', $dropdownOptions);
					}

					echo "</li>\n";
				}
			}

			echo "</ul>\n";
		}
	}

	/**
	 *### .renderMenuItem()
	 *
	 * Renders the content of a menu item.
	 * Note that the container and the sub-menus are not rendered here.
	 *
	 * @param array $item the menu item to be rendered. Please see {@link items} on what data might be in the item.
	 *
	 * @return string the rendered item
	 */
	protected function renderMenuItem($item)
	{

		if (isset($item['icon'])) {
			if (strpos($item['icon'], 'icon') === false && strpos($item['icon'], 'fa') === false) {
				$item['icon'] = 'glyphicon glyphicon-' . implode(' glyphicon-', explode(' ', $item['icon']));
				$item['label'] = "<span class='" . $item['icon'] . "'></span>\r\n" . $item['label'];
			} else {
				$item['label'] = "<i class='" . $item['icon'] . "'></i>\r\n" . $item['label'];
			}
		}

		if (!isset($item['linkOptions'])) {
			$item['linkOptions'] = array();
		}

		if (isset($item['items']) && !empty($item['items'])) {
			if (empty($item['url'])) {
				$item['url'] = '#';
			}

			if (isset($item['linkOptions']['class'])) {
				$item['linkOptions']['class'] .= ' dropdown-toggle';
			} else {
				$item['linkOptions']['class'] = 'dropdown-toggle';
			}

			$item['linkOptions']['data-toggle'] = 'dropdown';
			$item['label'] .= ' <span class="caret"></span>';
		}

		if (isset($item['url'])) {
			return CHtml::link($item['label'], $item['url'], $item['linkOptions']);
		} else {
			return $item['label'];
		}
	}

	/**
	 *### .normalizeItems()
	 *
	 * Normalizes the {@link items} property so that the 'active' state is properly identified for every menu item.
	 *
	 * @param array $items the items to be normalized.
	 * @param string $route the route of the current request.
	 * @param boolean $active whether there is an active child menu item.
	 *
	 * @return array the normalized menu items
	 */
	protected function normalizeItems($items, $route, &$active)
	{
		foreach ($items as $i => $item) {
			if (!is_array($item)) {
				$item = array('divider' => true);
			} else {
				if (!isset($item['itemOptions'])) {
					$item['itemOptions'] = array();
				}

				$classes = array();

				if (!isset($item['url']) && !isset($item['items']) && $this->isVertical()) {
					$item['header'] = true;
					$classes[] = 'nav-header';
				}

				if (!empty($classes)) {
					$classes = implode(' ', $classes);
					if (isset($item['itemOptions']['class'])) {
						$item['itemOptions']['class'] .= ' ' . $classes;
					} else {
						$item['itemOptions']['class'] = $classes;
					}
				}
			}

			$items[$i] = $item;
		}

		return parent::normalizeItems($items, $route, $active);
	}
}
