<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Administrator\View\Plugins;

\defined('_JEXEC') or die;

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

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $listOrdering;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $listDirection;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $filterElement;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $filterEnabled;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $filterSearch;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $total;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $limit;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $filterName;

	/**
	 * An array of items
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $filterAccess;

	/**
	 * Display the view
	 *
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
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

		$lang = Factory::getApplication()->getLanguage();

		foreach ($this->items as $item)
		{
			$source    = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
			$extension = 'plg_' . $item->folder . '_' . $item->element;
			$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
			|| $lang->load($extension . '.sys', $source, null, false, false)
			|| $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			|| $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
			$item->name = Text::_($item->name);
		}

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->user = Factory::getApplication()->getIdentity();

		$this->filterForm = $model->getFilterForm();

		$this->filter          = new \stdClass;
		$this->filter->Search  = $this->escape($this->state->get('filter.search'));
		$this->filter->Enabled = $this->escape($this->state->get('filter.enabled'));
		$this->filter->Name    = $this->escape($this->state->get('filter.name'));
		$this->filter->Element = $this->escape($this->state->get('filter.element'));
		$this->filter->Access  = $this->escape($this->state->get('filter.access'));
		$this->filter->Active  = $this->escape($this->state->get('filter.active'));

		$this->list            = new \stdClass;
		$this->list->Ordering  = $this->escape($this->state->get('list.ordering'));
		$this->list->Direction = $this->escape($this->state->get('list.direction'));

		// Check for errors.
		if (\count($errors = $this->get('Errors')))
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
	protected function getSortFields(): array
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
	protected function getSortDirectionFields(): array
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
	protected function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_PLUGIN_MANAGER'), 'puzzle');
		ToolbarHelper::spacer();
		ToolbarHelper::publish('plugins.publish', 'JTOOLBAR_ENABLE', true);
		ToolbarHelper::unpublish('plugins.unpublish', 'JTOOLBAR_DISABLE', true);
		ToolbarHelper::divider();
		ToolbarHelper::checkIn('plugins.checkIn');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('plugins.resync', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/plugins';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array    The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function publishedOptions(): array
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}
}
