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
 * Users view for Kunena backend
 *
 * @since  K1.X
 */
class KunenaAdminViewUsers extends KunenaView
{
	/**
	 * DisplayDefault
	 * @since Kunena
	 */
	public function displayDefault()
	{
		$this->setToolbar();
		$this->users      = $this->get('items');
		$this->pagination = $this->get('Pagination');
		$this->modcatlist = $this->get('Modcatslist');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch    = $this->escape($this->state->get('filter.search'));
		$this->filterUsername  = $this->escape($this->state->get('filter.username'));
		$this->filterEmail     = $this->escape($this->state->get('filter.email'));
		$this->filterRank      = $this->escape($this->state->get('filter.rank'));
		$this->filterSignature = $this->escape($this->state->get('filter.signature'));
		$this->filterBlock     = $this->escape($this->state->get('filter.block'));
		$this->filterBanned    = $this->escape($this->state->get('filter.banned'));
		$this->filterModerator = $this->escape($this->state->get('filter.moderator'));
		$this->filterActive    = $this->escape($this->state->get('filter.active'));
		$this->listOrdering    = $this->escape($this->state->get('list.ordering'));
		$this->listDirection   = $this->escape($this->state->get('list.direction'));
		$this->filterIp        = $this->escape($this->state->get('filter.ip'));

		KunenaFactory::getTemplate()->loadFontawesome();

		$this->display();
	}

	/**
	 * setToolbar
	 *
	 * @return void
	 * @since Kunena
	 */
	protected function setToolbar()
	{
		// Get the toolbar object instance
		$bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');

		// Set the titlebar text
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_USER_MANAGER'), 'users');
		JToolbarHelper::spacer();
		JToolbarHelper::editList();
		JToolbarHelper::custom('logout', 'cancel.png', 'cancel_f2.png', 'COM_KUNENA_LOGOUT');
		JToolbarHelper::divider();
		JToolbarHelper::custom('move', 'move.png', 'move_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');

		if (version_compare(JVERSION, '4.0', '>'))
		{
			HTMLHelper::_('bootstrap.renderModal', 'moderateModal');
		}
		else
		{
			HTMLHelper::_('bootstrap.modal', 'moderateModal');
		}

		$title = Text::_('COM_KUNENA_VIEW_USERS_TOOLBAR_ASSIGN_MODERATORS');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#moderateModal\" class=\"btn btn-small\">
					<i class=\"icon-checkbox-partial\" title=\"$title\"> </i>
						$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');

		JToolbarHelper::divider();
		JToolbarHelper::custom('trashusermessages', 'trash.png', 'icon-32-move.png', 'COM_KUNENA_TRASH_USERMESSAGES');
		JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('removecatsubscriptions', 'delete.png', 'delete.png', 'COM_KUNENA_REMOVE_CATSUBSCRIPTIONS');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('removetopicsubscriptions', 'delete.png', 'delete.png', 'COM_KUNENA_REMOVE_TOPICSUBSCRIPTIONS');
		JToolbarHelper::spacer();

		if (version_compare(JVERSION, '4.0', '>'))
		{
			HTMLHelper::_('bootstrap.renderModal', 'subscribecatsusersModal');
		}
		else
		{
			HTMLHelper::_('bootstrap.modal', 'subscribecatsusersModal');
		}

		$title = Text::_('COM_KUNENA_VIEW_USERS_TOOLBAR_SUBSCRIBE_USERS_CATEGORIES');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#subscribecatsusersModal\" class=\"btn btn-small\">
					<i class=\"icon-checkbox-partial\" title=\"$title\"> </i>
						$title</button>";
						$bar->appendButton('Custom', $dhtml, 'batch');

		$help_url = 'https://docs.kunena.org/en/manual/backend/users';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return     array
	 * @since Kunena
	 */
	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = HTMLHelper::_('select.option', 'username', Text::_('COM_KUNENA_USRL_USERNAME'));
		$sortFields[] = HTMLHelper::_('select.option', 'email', Text::_('COM_KUNENA_USRL_EMAIL'));
		$sortFields[] = HTMLHelper::_('select.option', 'rank', Text::_('COM_KUNENA_A_RANKS'));
		$sortFields[] = HTMLHelper::_('select.option', 'signature', Text::_('COM_KUNENA_GEN_SIGNATURE'));
		$sortFields[] = HTMLHelper::_('select.option', 'enabled', Text::_('COM_KUNENA_USRL_ENABLED'));
		$sortFields[] = HTMLHelper::_('select.option', 'banned', Text::_('COM_KUNENA_USRL_BANNED'));
		$sortFields[] = HTMLHelper::_('select.option', 'moderator', Text::_('COM_KUNENA_VIEW_MODERATOR'));
		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('JGRID_HEADING_ID'));
		$sortFields[] = HTMLHelper::_('select.option', 'ip', Text::_('COM_KUNENA_GEN_IP'));

		return $sortFields;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return     array
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
	 * Returns an array of locked filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public function signatureOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public function blockOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public function bannedOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return    array   The HTML code for the select tag
	 * @since Kunena
	 */
	public function moderatorOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * Returns an array ranks filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public function ranksOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', 'Administrator', Text::_('Administrator'));
		$options[] = HTMLHelper::_('select.option', 'New Member', Text::_('New Member'));

		return $options;
	}
}
