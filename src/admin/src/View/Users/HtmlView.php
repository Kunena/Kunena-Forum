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

namespace Kunena\Forum\Administrator\View\Users;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Users view for Kunena backend
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The model state
	 *
	 * @var    CMSObject
	 * @since  6.0
	 */
	protected $state;

	/**
	 * DisplayDefault
	 *
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
		$this->users      = $this->get('items');
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->modCatList = $this->get('ModcatsList');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filter            = new \stdClass;
		$this->filter->Search    = $this->escape($this->state->get('filter.search'));
		$this->filter->Username  = $this->escape($this->state->get('filter.username'));
		$this->filter->Email     = $this->escape($this->state->get('filter.email'));
		$this->filter->Rank      = $this->escape($this->state->get('filter.rank'));
		$this->filter->Signature = $this->escape($this->state->get('filter.signature'));
		$this->filter->Block     = $this->escape($this->state->get('filter.block'));
		$this->filter->Banned    = $this->escape($this->state->get('filter.banned'));
		$this->filter->Moderator = $this->escape($this->state->get('filter.moderator'));
		$this->filter->Active    = $this->escape($this->state->get('filter.active'));
		$this->filter->Ip        = $this->escape($this->state->get('filter.ip'));

		$this->list            = new \stdClass;
		$this->list->Ordering  = $this->escape($this->state->get('list.ordering'));
		$this->list->Direction = $this->escape($this->state->get('list.direction'));

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortFields(): array
	{
		$sortFields   = [];
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
		// Get the toolbar object instance
		$bar = Toolbar::getInstance('toolbar');

		// Set the title bar text
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_USER_MANAGER'), 'users');
		ToolbarHelper::spacer();
		ToolbarHelper::editList('users.edit');
		ToolbarHelper::custom('users.logout', 'cancel.png', 'cancel_f2.png', 'COM_KUNENA_LOGOUT');
		ToolbarHelper::divider();
		ToolbarHelper::custom('users.move', 'move.png', 'move_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');

		HTMLHelper::_('bootstrap.renderModal', 'moderateModal');

		$title = Text::_('COM_KUNENA_VIEW_USERS_TOOLBAR_ASSIGN_MODERATORS');
		$dhtml = "<button data-bs-toggle=\"modal\" data-bs-target=\"#moderateModal\" class=\"btn btn-small\">
					<i class=\"icon-checkbox-partial\" title=\"$title\"> </i>
						$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');

		ToolbarHelper::divider();
		ToolbarHelper::custom('users.trashusermessages', 'trash.png', 'icon-32-move.png', 'COM_KUNENA_TRASH_USERMESSAGES');
		ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'users.remove');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('users.removecatsubscriptions', 'delete.png', 'delete.png', 'COM_KUNENA_REMOVE_CATSUBSCRIPTIONS');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('users.removetopicsubscriptions', 'delete.png', 'delete.png', 'COM_KUNENA_REMOVE_TOPICSUBSCRIPTIONS');
		ToolbarHelper::spacer();

		HTMLHelper::_('bootstrap.renderModal', 'subscribecatsusersModal');

		$title = Text::_('COM_KUNENA_VIEW_USERS_TOOLBAR_SUBSCRIBE_USERS_CATEGORIES');
		$dhtml = "<button data-bs-toggle=\"modal\" data-bs-target=\"#subscribecatsusersModal\" class=\"btn btn-small\">
					<i class=\"icon-checkbox-partial\" title=\"$title\"> </i>
						$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');

		$helpUrl = 'https://docs.kunena.org/en/manual/backend/users';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * Returns an array of locked filter options.
	 *
	 * @return  array    The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function signatureOptions(): array
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array    The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function blockOptions(): array
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return  array    The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function bannedOptions(): array
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array   The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function moderatorOptions(): array
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * Returns an array ranks filter options.
	 *
	 * @return  array    The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public function ranksOptions(): array
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'Administrator', Text::_('Administrator'));
		$options[] = HTMLHelper::_('select.option', 'New Member', Text::_('New Member'));

		return $options;
	}
}
