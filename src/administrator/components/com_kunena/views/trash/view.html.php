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
use Joomla\CMS\Language\Text;

/**
 * Trash view for Kunena backend
 *
 * @since  K1.0
 */
class KunenaAdminViewTrash extends KunenaView
{
	/**
	 * @since Kunena
	 */
	public function displayDefault()
	{
		$this->setLayout($this->state->get('layout'));
		$this->trash_items       = $this->get('Trashitems');
		$this->pagination        = $this->get('Navigation');
		$this->view_options_list = $this->get('ViewOptions');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch   = $this->escape($this->state->get('list.search'));
		$this->filterTitle    = $this->escape($this->state->get('filter.title'));
		$this->filterTopic    = $this->escape($this->state->get('filter.topic'));
		$this->filterCategory = $this->escape($this->state->get('filter.category'));
		$this->filterIp       = $this->escape($this->state->get('filter.ip'));
		$this->filterAuthor   = $this->escape($this->state->get('filter.author'));
		$this->filterDate     = $this->escape($this->state->get('filter.date'));
		$this->filterActive   = $this->escape($this->state->get('filter.active'));
		$this->listOrdering   = $this->escape($this->state->get('list.ordering'));
		$this->listDirection  = $this->escape($this->state->get('list.direction'));

		$this->setToolBarDefault();
		$this->display();
	}

	/**
	 * @return array
	 * @since Kunena
	 */
	protected function getSortFields()
	{
		$sortFields = array();

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
	 * @return array
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
	 * @since Kunena
	 */
	protected function setToolBarDefault()
	{
		// Set the titlebar text
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TRASH_MANAGER'), 'trash');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('restore', 'checkin.png', 'checkin_f2.png', 'COM_KUNENA_TRASH_RESTORE');
		JToolbarHelper::divider();
		JToolbarHelper::custom('purge', 'trash.png', 'trash_f2.png', 'COM_KUNENA_TRASH_PURGE');
		JToolbarHelper::spacer();

		$help_url = 'https://docs.kunena.org/en/manual/backend/trashbin';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayPurge()
	{
		$this->purgeitems    = $this->get('PurgeItems');
		$this->md5Calculated = $this->get('Md5');

		$this->setToolBarPurge();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarPurge()
	{
		// Set the titlebar text
		JToolbarHelper::title(Text::_('COM_KUNENA'), 'kunena.png');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('purge', 'delete.png', 'delete_f2.png', 'COM_KUNENA_DELETE_PERMANENTLY');
		JToolbarHelper::spacer();
		JToolbarHelper::cancel();
		JToolbarHelper::spacer();

		$help_url = 'https://docs.kunena.org/en/manual/backend/trashbin';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
