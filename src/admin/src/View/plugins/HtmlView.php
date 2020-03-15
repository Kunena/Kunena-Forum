<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Administrator\View\Plugins;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of plugins.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @since       K1.5
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Form object for search filters
	 *
	 * @var    Form
	 * @since  4.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	public $activeFilters;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  4.0.0
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    CMSObject
	 * @since  6.0
	 */
	protected $state;

	protected $listOrdering;

	protected $listDirection;

	protected $filterElement;

	protected $filterEnabled;

	protected $filterSearch;

	protected $total;

	protected $limit;

	protected $filterName;

	protected $filterAccess;

	/**
	 * Display the view
	 *
	 * @param   null  $tpl  tpl
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$model            = $this->getModel();
		$this->items      = $this->get('Items');
		$this->state      = $this->get('state');
		$this->pagination = $this->get('Pagination');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->user = Factory::getApplication()->getIdentity();

		$this->filterForm = $model->getFilterForm();

		$this->filterSearch  = $this->escape($this->state->get('filter.search'));
		$this->filterEnabled = $this->escape($this->state->get('filter.enabled'));
		$this->filterName    = $this->escape($this->state->get('filter.name'));
		$this->filterElement = $this->escape($this->state->get('filter.element'));
		$this->filterAccess  = $this->escape($this->state->get('filter.access'));
		$this->filterActive  = $this->escape($this->state->get('filter.active'));
		$this->listOrdering  = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
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
		$sortFields   = [];
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
		$sortDirection   = [];
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void The HTML code for the select tag
	 *
	 * @since   K1.6
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_PLUGIN_MANAGER'), 'puzzle');
		ToolbarHelper::spacer();
		ToolbarHelper::publish('publish', 'JTOOLBAR_ENABLE', true);
		ToolbarHelper::unpublish('unpublish', 'JTOOLBAR_DISABLE', true);
		ToolbarHelper::divider();
		ToolbarHelper::checkin('checkin');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('resync', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/plugins';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array    The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function publishedOptions()
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}
}
