<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutMessageList extends KunenaLayout
{
	function displayTimeFilter($id = 'kfilter-select-time', $attrib = 'class="kinputbox" onchange="this.form.submit()" size="1"') {
		// make the select list for time selection
		$timesel[] = JHtml::_('select.option', -1, JText::_('COM_KUNENA_SHOW_ALL'));
		$timesel[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
		$timesel[] = JHtml::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
		$timesel[] = JHtml::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
		$timesel[] = JHtml::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
		$timesel[] = JHtml::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
		$timesel[] = JHtml::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
		$timesel[] = JHtml::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
		$timesel[] = JHtml::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
		$timesel[] = JHtml::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
		echo JHtml::_('select.genericlist', $timesel, 'sel', $attrib, 'value', 'text', $this->state->get('list.time'), $id);
	}
}
