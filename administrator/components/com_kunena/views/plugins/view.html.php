<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of plugins.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 * @since       1.5
 */
class KunenaAdminViewPlugins extends KunenaView {

	/**
	 * Display the view
	 */
	function display($tpl = null) {
		$this->setToolbar();
		$this->items = $this->get('Items');
		$this->state = $this->get('state');
		$this->pagination = $this->get('Pagination');

		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch = $this->escape($this->state->get('filter.search'));
		$this->filterEnabled = $this->escape($this->state->get('filter.enabled'));
		$this->filterTitle = $this->escape($this->state->get('filter.title'));
		$this->filterElement = $this->escape($this->state->get('filter.element'));
		$this->filterAccess = $this->escape($this->state->get('filter.access'));
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->listOrdering = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function setToolbar() {

		JToolbarHelper::title(JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_PLUGINS_MANAGER'), 'pluginsmanager');
		JToolbarHelper::spacer();
		JToolbarHelper::editList('plugin.edit');
		JToolbarHelper::divider();
		JToolbarHelper::publish('plugins.publish', 'JTOOLBAR_ENABLE', true);
		JToolbarHelper::unpublish('plugins.unpublish', 'JTOOLBAR_DISABLE', true);
		JToolbarHelper::divider();
		JToolbarHelper::checkin('plugins.checkin');
		JToolbarHelper::spacer();
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function publishedOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields() {
		$sortFields = array();
		$sortFields[] = JHtml::_('select.option', 'enable', JText::_('JSTATUS'));
		$sortFields[] = JHtml::_('select.option', 'name', JText::_('JGLOBAL_TITLE'));
		$sortFields[] = JHtml::_('select.option', 'element', JText::_('Element'));
		$sortFields[] = JHtml::_('select.option', 'access', JText::_('Acess'));
		$sortFields[] = JHtml::_('select.option', 'id', JText::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	protected function getSortDirectionFields() {
		$sortDirection = array();
//		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
//		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));
		// TODO: remove it when J2.5 support is dropped
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

		return $sortDirection;
	}
}
