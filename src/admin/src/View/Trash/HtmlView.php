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

namespace Kunena\Forum\Administrator\View\Trash;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Trash view for Kunena backend
 *
 * @since  K1.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Filter search
	 *
	 * @since  6.0
	 */
	public $filterSearch;

	/**
	 * Filter title
	 *
	 * @since  6.0
	 */
	public $filterTitle;

	/**
	 * Filter topic
	 *
	 * @since  6.0
	 */
	public $filterTopic;

	/**
	 * Filter category
	 *
	 * @since  6.0
	 */
	public $filterCategory;

	/**
	 * Filter ip
	 *
	 * @since  6.0
	 */
	public $filterIp;

	/**
	 * Filter author
	 *
	 * @since  6.0
	 */
	public $filterAuthor;

	/**
	 * Filter active
	 *
	 * @since  6.0
	 */
	public $filterActive;

	/**
	 * The model state
	 *
	 * @var    CMSObject
	 * @since  6.0
	 */
	protected $state;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayPurge(): void
	{
		$this->purgeItems    = $this->get('PurgeItems');
		$this->md5Calculated = $this->get('Md5');

		$this->setToolBarPurge();
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarPurge(): void
	{
		// Set the title bar text
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'kunena.png');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('trash.purge', 'delete.png', 'delete_f2.png', 'COM_KUNENA_DELETE_PERMANENTLY', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();

		$helpUrl = 'https://docs.kunena.org/en/manual/backend/trashbin';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state              = $this->get('State');
		$this->trashInternalItems = $this->get('Trashitems');
		$this->setLayout($this->state->get('layout'));
		$this->pagination      = $this->get('Navigation');
		$this->viewOptionsList = $this->get('ViewOptions');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filter             = new \stdClass;
		$this->filter->Search     = $this->escape($this->state->get('list.search'));
		$this->filter->Title      = $this->escape($this->state->get('filter.title'));
		$this->filter->Category   = $this->escape($this->state->get('filter.category'));
		$this->filter->Topic      = $this->escape($this->state->get('filter.topic'));
		$this->filter->Active     = $this->escape($this->state->get('filter.active'));
		$this->filter->TargetUser = $this->escape($this->state->get('filter.target_user'));
		$this->filter->Ip         = $this->escape($this->state->get('filter.ip'));
		$this->filter->Author     = $this->escape($this->state->get('filter.author'));
		$this->filter->Date       = $this->escape($this->state->get('filter.date'));

		$this->list            = new \stdClass;
		$this->list->Ordering  = $this->escape($this->state->get('list.ordering'));
		$this->list->Direction = $this->escape($this->state->get('list.direction'));

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortFields(): array
	{
		$sortFields = [];

		if ($this->state->get('layout') == 'topics')
		{
			$sortFields[] = HTMLHelper::_('select.option', 'title', Text::_('COM_KUNENA_TRASH_TITLE'));
			$sortFields[] = HTMLHelper::_('select.option', 'category', Text::_('COM_KUNENA_TRASH_CATEGORY'));
			$sortFields[] = HTMLHelper::_('select.option', 'author', Text::_('COM_KUNENA_TRASH_AUTHOR'));
			$sortFields[] = HTMLHelper::_('select.option', 'time', Text::_('COM_KUNENA_TRASH_DATE'));
		}
		else
		{
			$sortFields[] = HTMLHelper::_('select.option', 'title', Text::_('COM_KUNENA_TRASH_TITLE'));
			$sortFields[] = HTMLHelper::_('select.option', 'topic', Text::_('COM_KUNENA_MENU_TOPIC'));
			$sortFields[] = HTMLHelper::_('select.option', 'category', Text::_('COM_KUNENA_TRASH_CATEGORY'));
			$sortFields[] = HTMLHelper::_('select.option', 'ip', Text::_('COM_KUNENA_TRASH_IP'));
			$sortFields[] = HTMLHelper::_('select.option', 'author', Text::_('COM_KUNENA_TRASH_AUTHOR'));
			$sortFields[] = HTMLHelper::_('select.option', 'time', Text::_('COM_KUNENA_TRASH_DATE'));
		}

		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
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
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar(): void
	{
		// Set the title bar text
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TRASH_MANAGER'), 'trash');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('trash.restore', 'checkin.png', 'checkin_f2.png', 'COM_KUNENA_TRASH_RESTORE');
		ToolbarHelper::divider();
		ToolbarHelper::custom('trash.purge', 'trash.png', 'trash_f2.png', 'COM_KUNENA_TRASH_PURGE');
		ToolbarHelper::spacer();

		$helpUrl = 'https://docs.kunena.org/en/manual/backend/trashbin';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}
}
