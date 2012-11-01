<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Form
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.html.html');
jimport('joomla.html.parameter.element');

JHTML::addIncludePath(KPATH_ADMIN . '/libraries/html/html');

class JElementKunenaCategoryList extends JElement {
	var $_name = 'KunenaCategoryList';

	function fetchElement($name, $value, &$node, $control_name) {
		if (!class_exists('KunenaForum') || !KunenaForum::installed()) {
			echo '<a href="index.php?option=com_kunena">PLEASE COMPLETE KUNENA INSTALLATION</a>';
			return;
		}
		KunenaFactory::loadLanguage('com_kunena');

		$none = $node->attributes ( 'none' );

		$options = Array ();
		foreach ($node->children() as $option) {
			$options[] = JHTML::_('select.option', $option->attributes('value'), JText::_($option->data()));
		}
		$ctrl = $control_name . '[' . $name . ']';

		$size = $node->attributes ( 'size' );
		$class = $node->attributes ( 'class' );

		$attribs = ' ';
		if ($size) {
			$attribs .= 'size="' . $size . '"';
		}
		if ($class) {
			$attribs .= 'class="' . $class . '"';
		} else {
			$attribs .= 'class="inputbox"';
		}
		if ($node->attributes ( 'multiple' )) {
			$attribs .= ' multiple="multiple"';
			$ctrl .= '[]';
		}

		return JHTML::_('kunenaforum.categorylist', $ctrl, 0, $options, $node->attributes(), $attribs, 'value', 'text', $value);
	}
}
