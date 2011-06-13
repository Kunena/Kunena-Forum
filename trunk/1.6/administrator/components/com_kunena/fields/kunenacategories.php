<?php
/**
 * @version $Id: api.php 3864 2010-11-05 16:23:40Z fxstein $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ( '' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldKunenaCategories extends JFormField {
	protected $type = 'KunenaCategories';

	protected function getInput() {
		$kunena_db = JFactory::getDBO ();

		$kunena_api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		require_once ($kunena_api);
		require_once (KUNENA_PATH . '/class.kunena.php');
		$items = JJ_categoryArray ();

		$sections = $this->element['sections'];
		$none = $this->element['none'];
		$options = Array ();
		$options [] = JHTML::_ ( 'select.option', '0', $none ? JText::_ ( $none ) : '0' );
		foreach ( $items as $cat ) {
			$options [] = JHTML::_ ( 'select.option', $cat->id, $cat->treename, 'value', 'text', ! $sections && $cat->section );
		}
		$size = $this->element['size'];
		$class = $this->element['class'];

		$attribs = ' ';
		if ($size) {
			$attribs .= 'size="' . $size . '"';
		}
		if ($class) {
			$attribs .= 'class="' . $class . '"';
		} else {
			$attribs .= 'class="inputbox"';
		}
		if (!empty($this->element['multiple'])) {
			$attribs .= ' multiple="multiple"';
		}

		return JHTML::_ ( 'select.genericlist', $options, $this->name, $attribs, 'value', 'text', $this->value );
	}
}
