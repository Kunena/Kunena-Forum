<?php
/**
 * @version $Id$
 * Kunena Component - JElementKunenaCategoryList Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ( '' );

require_once JPATH_ADMINISTRATOR.'/components/com_kunena/api.php';

JHTML::addIncludePath(KPATH_ADMIN . '/libraries/html/html');

class JElementKunenaCategoryList extends JElement {
	var $_name = 'KunenaCategoryList';

	function fetchElement($name, $value, &$node, $control_name) {
		$none = $node->attributes ( 'none' );

		$options = Array ();
		$options [] = JHTML::_ ( 'select.option', '0', $none ? JText::_ ( $none ) : '&nbsp;' );
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
