<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

JHTML::addIncludePath(KPATH_ADMIN . '/libraries/html/html');

class JFormFieldKunenaCategoryList extends JFormField {
	protected $type = 'KunenaCategoryList';

	protected function getInput() {
		$none = $this->element['none'];

		// Get the field options.
		$options = array();
		//$options = (array) $this->getOptions();

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

		return JHTML::_('kunenaforum.categorylist', $this->name, 0, $options, $this->element, $attribs, 'value', 'text', $this->value);
	}
}
