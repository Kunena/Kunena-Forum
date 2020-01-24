<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Categories;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\GenericDataException;

/**
 * About view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var     array|Category[]
	 * @since   Kunena 6.0
	 */
	public $categories = [];

	/**
	 * The model state
	 *
	 * @var    \Joomla\CMS\Object\CMSObject
	 * @since  6.0
	 */
	protected $state;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function displayCreate()
	{
		$this->displayEdit();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$this->categories = $this->get('AdminCategories');
		$this->pagination = $this->get('AdminNavigation');
		$this->state      = $this->get('State');

		$this->batch_categories = $this->get('BatchCategories');

		// Preprocess the list of items to find ordering divisions.
		$this->ordering = [];

		foreach ($this->categories as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
		    throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->user              = Factory::getApplication()->getIdentity();
		$this->me                = \KunenaUserHelper::getMyself();
		$this->userId            = $this->user->get('id');
		$this->filterSearch      = $this->escape($this->state->get('filter.search'));
		$this->filterPublished   = $this->escape($this->state->get('filter.published'));
		$this->filterTitle       = $this->escape($this->state->get('filter.title'));
		$this->filterType        = $this->escape($this->state->get('filter.type'));
		$this->filterAccess      = $this->escape($this->state->get('filter.access'));
		$this->filterLocked      = $this->escape($this->state->get('filter.locked'));
		$this->filterReview      = $this->escape($this->state->get('filter.review'));
		$this->filterAllow_polls = $this->escape($this->state->get('filter.allow_polls'));
		$this->filterAnonymous   = $this->escape($this->state->get('filter.anonymous'));
		$this->filterActive      = $this->escape($this->state->get('filter.active'));
		$this->listOrdering      = $this->escape($this->state->get('list.ordering'));
		$this->listDirection     = $this->escape($this->state->get('list.direction'));
		$this->saveOrder         = ($this->listOrdering == 'a.ordering' && $this->listDirection == 'asc');
		$this->saveOrderingUrl   = 'index.php?option=com_kunena&view=categories&task=saveorderajax&tmpl=component';
		$this->filterLevels      = $this->escape($this->state->get('filter.levels'));

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 * 
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar()
	{
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->pagination   = $this->get('AdminNavigation');

		// Get the toolbar object instance
		$bar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');
		ToolbarHelper::spacer();
		ToolbarHelper::addNew('categories.add', 'COM_KUNENA_NEW_CATEGORY');

		ToolbarHelper::editList('categories.edit');
		ToolbarHelper::divider();
		ToolbarHelper::publish('categories.publish');
		ToolbarHelper::unpublish('categories.unpublish');
		ToolbarHelper::divider();

		HTMLHelper::_('bootstrap.renderModal', 'moderateModal');

		$title = Text::_('COM_KUNENA_VIEW_CATEGORIES_CONFIRM_BEFORE_DELETE');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#catconfirmdelete\" class=\"btn btn-small button-trash\">
					<i class=\"icon-trash\" title=\"$title\"> </i>
						$title</button>";
		$bar->appendButton('Custom', $dhtml, 'confirmdelete');

		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/setup/sections-categories';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);

		$title = Text::_('JTOOLBAR_BATCH');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
		<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
		$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortFields()
	{
		$sortFields   = [];
		$sortFields[] = HTMLHelper::_('select.option', 'ordering', Text::_('COM_KUNENA_REORDER'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.published', Text::_('JSTATUS'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.title', Text::_('JGLOBAL_TITLE'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.access', Text::_('COM_KUNENA_CATEGORIES_LABEL_ACCESS'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.locked', Text::_('COM_KUNENA_LOCKED'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.review', Text::_('COM_KUNENA_REVIEW'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.allow_polls', Text::_('COM_KUNENA_CATEGORIES_LABEL_POLL'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.anonymous', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS'));
		$sortFields[] = HTMLHelper::_('select.option', 'p.id', Text::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortDirectionFields()
	{
		$sortDirection   = [];
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array The HTML code for the select tag
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

	/**
	 * Returns an array of locked filter options.
	 *
	 * @return  array  The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function lockOptions()
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return  array The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function reviewOptions()
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function allowpollsOptions()
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return  array  The HTML code for the select tag
	 * @since   Kunena 6.0
	 */
	public function anonymousOptions()
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}
}
