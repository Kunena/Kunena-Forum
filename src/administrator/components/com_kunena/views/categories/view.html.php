<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * About view for Kunena backend
 * @since Kunena
 */
class KunenaAdminViewCategories extends KunenaView
{
	/**
	 * @var array|KunenaForumCategory[]
	 * @since Kunena
	 */
	public $categories = array();

	/**
	 * @since Kunena
	 */
	public function displayCreate()
	{
		$this->displayEdit();
	}

	/**
	 * @since Kunena
	 */
	public function displayEdit()
	{
		$this->category = $this->get('AdminCategory');

		// FIXME: better access control and gracefully handle no rights
		// Prevent fatal error if no rights:
		if (!$this->category)
		{
			return;
		}

		$this->options    = $this->get('AdminOptions');
		$this->moderators = $this->get('AdminModerators');
		$this->setToolBarEdit();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarEdit()
	{
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');
		JToolbarHelper::spacer();
		JToolbarHelper::apply('apply');
		JToolbarHelper::save('save');
		JToolbarHelper::save2new('save2new');

		// If an existing item, can save to a copy.
		if ($this->category->exists())
		{
			JToolbarHelper::save2copy('save2copy');
		}

		JToolbarHelper::cancel();
		JToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/categories/new-section-category';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayDefault()
	{
		$this->categories = $this->get('AdminCategories');
		$this->pagination = $this->get('AdminNavigation');

		$this->batch_categories = $this->get('BatchCategories');

		// Preprocess the list of items to find ordering divisions.
		$this->ordering = array();

		foreach ($this->categories as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		$this->setToolBarDefault();
		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->user              = Factory::getUser();
		$this->me                = KunenaUserHelper::getMyself();
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
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarDefault()
	{
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->pagination   = $this->get('AdminNavigation');

		// Get the toolbar object instance
		$bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');

		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');
		JToolbarHelper::spacer();
		JToolbarHelper::addNew('add', 'COM_KUNENA_NEW_CATEGORY');

		JToolbarHelper::editList();
		JToolbarHelper::divider();
		JToolbarHelper::publish();
		JToolbarHelper::unpublish();
		JToolbarHelper::divider();

		if (version_compare(JVERSION, '4.0', '>'))
		{
			HTMLHelper::_('bootstrap.renderModal', 'moderateModal');
		}
		else
		{
			HTMLHelper::_('bootstrap.modal', 'moderateModal');
		}

		$title = Text::_('COM_KUNENA_VIEW_CATEGORIES_CONFIRM_BEFORE_DELETE');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#catconfirmdelete\" class=\"btn btn-small button-trash\">
					<i class=\"icon-trash\" title=\"$title\"> </i>
						$title</button>";
						$bar->appendButton('Custom', $dhtml, 'confirmdelete');

		JToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/setup/sections-categories';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);

		$title = Text::_('JTOOLBAR_BATCH');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
		<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
		$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return    array
	 * @since Kunena
	 */
	protected function getSortFields()
	{
		$sortFields   = array();
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
	 * @return    array
	 * @since Kunena
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

	/**
	 * Returns an array of locked filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public function lockOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return    array   The HTML code for the select tag
	 * @since Kunena
	 */
	public function reviewOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return    array
	 * @since Kunena
	 */
	public function allowpollsOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public function anonymousOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}
}
