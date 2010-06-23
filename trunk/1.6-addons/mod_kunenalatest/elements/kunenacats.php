<?php
/**
 * @version $Id: $
 * KunenaLatest Module
 * @package Kunena latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( '' );

class JElementKunenaCats extends JElement {
	var $_name = 'KunenaCats';

	function fetchElement($name, $value, &$node, $control_name) {
		$kunena_db = JFactory::getDBO ();
		// Detect and load Kunena 1.6+
		$kunena_api = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'api.php';
		if (! JComponentHelper::isEnabled ( 'com_kunena', true ) || ! is_file ( $kunena_api ))
			return JError::raiseError ( JText::_ ( 'Kunena Error' ), JText::_ ( 'Kunena 1.6 is not installed on your system' ) );

		require_once ($kunena_api);
		require_once (KUNENA_PATH . DS . 'class.kunena.php');
		$items = JJ_categoryArray ();

		$options = Array ();
		$options [] = JHTML::_ ( 'select.option', 'all', JText::_ ( 'MOD_KUNENALATEST_CATEGORY_ALL' ) );

		foreach ( $items as $cat ) {
			$options [] = JHTML::_ ( 'select.option', $cat->id, $cat->treename, 'value', 'text', $cat->section );
		}

		$ctrl = $control_name . '[' . $name . '][]';

		return JHTML::_ ( 'select.genericlist', $options, $ctrl, 'class=" inputbox" size="5" multiple="multiple"', 'value', 'text', $value, $control_name . $name );
	}
}
