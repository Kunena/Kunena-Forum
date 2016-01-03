<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template.Joomla30
 * @subpackage Layouts.Pagination
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$app = JFactory::getApplication();
$limits = array();

// Make the option list.
for ($i = 5; $i <= 30; $i += 5) {
	$limits[] = JHtml::_('select.option', "$i");
}
$limits[] = JHtml::_('select.option', '50', JText::_('J50'));
$limits[] = JHtml::_('select.option', '100', JText::_('J100'));
$limits[] = JHtml::_('select.option', '0', JText::_('JALL'));

$selected = $this->pagination->limit == $this->pagination->total ? 0 : $this->pagination->limit;

// Build the select list.
echo JHtml::_(
	'select.genericlist',
	$limits,
	$this->pagination->prefix . 'limit',
	'class="inputbox input-mini" size="1" onchange="Joomla.submitform();"',
	'value',
	'text',
	$selected
);
