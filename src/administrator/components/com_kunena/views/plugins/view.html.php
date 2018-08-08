<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * View class for a list of plugins.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @since       K1.5
 */
class KunenaAdminViewPlugins extends KunenaView
{
	/**
	 * Display the view
	 *
	 * @param   null $tpl tpl
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function displayDefault($tpl = null)
	{
		$this->setToolbar();
		$this->items      = $this->get('Items');
		$this->state      = $this->get('state');
		$this->pagination = $this->get('Pagination');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch  = $this->escape($this->state->get('filter.search'));
		$this->filterEnabled = $this->escape($this->state->get('filter.enabled'));
		$this->filterName    = $this->escape($this->state->get('filter.name'));
		$this->filterElement = $this->escape($this->state->get('filter.element'));
		$this->filterAccess  = $this->escape($this->state->get('filter.access'));
		$this->filterActive  = $this->escape($this->state->get('filter.active'));
		$this->listOrdering  = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));

		return $this->display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   K1.6
	 *
	 * @return void The HTML code for the select tag
	 */
	protected function setToolbar()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_PLUGIN_MANAGER'), 'puzzle');
		JToolbarHelper::spacer();
		JToolbarHelper::publish('publish', 'JTOOLBAR_ENABLE', true);
		JToolbarHelper::unpublish('unpublish', 'JTOOLBAR_DISABLE', true);
		JToolbarHelper::divider();
		JToolbarHelper::checkin('checkin');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('resync', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		JToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/plugins';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = HTMLHelper::_('select.option', 'enable', Text::_('JSTATUS'));
		$sortFields[] = HTMLHelper::_('select.option', 'name', Text::_('COM_PLUGINS_NAME_HEADING'));
		$sortFields[] = HTMLHelper::_('select.option', 'element', Text::_('COM_PLUGINS_ELEMENT_HEADING'));
		$sortFields[] = HTMLHelper::_('select.option', 'access', Text::_('JGRID_HEADING_ACCESS'));
		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortDirectionFields()
	{
		$sortDirection   = array();
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public function publishedOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}
}
