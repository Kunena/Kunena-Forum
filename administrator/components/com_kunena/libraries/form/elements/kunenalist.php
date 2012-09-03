<?php
/**
* @copyright    Copyright (C) 2009 Open Source Matters. All rights reserved.
* @license      GNU/GPL
*
* Quick J!1.5 hack taken from: http://docs.joomla.org/Adding_a_multiple_item_select_list_parameter_type
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a multiple item select element
 *
 */

class JElementKunenaList extends JElement
{
	/**
	* Element name
	*
	* @access       protected
	* @var	  string
	*/
	var     $_name = 'KunenaList';

	function fetchElement($name, $value, &$node, $control_name)
	{
		// Base name of the HTML control.
		$ctrl   = $control_name .'['. $name .']';

		// Construct an array of the HTML OPTION statements.
		$options = array ();
		foreach ($node->children() as $option)
		{
			$val    = $option->attributes('value');
			$text   = $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		// Construct the various argument calls that are supported.
		$attribs	= ' ';
		if ($v = $node->attributes( 'size' )) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = $node->attributes( 'class' )) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
		if ($m = $node->attributes( 'multiple' ))
		{
			$attribs	.= ' multiple="multiple"';
			$ctrl	   .= '[]';
		}

		// Render the HTML SELECT list.
		return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
	}
}