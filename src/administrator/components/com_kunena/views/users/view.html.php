<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Views
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Users view for Kunena backend
 *
 * @since  K1.X
 */
class KunenaAdminViewUsers extends KunenaView
{
	/**
	 * DisplayDefault
	 *
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

		$this->display();
	}

	/**
	 * setToolbar
	 *
	 * @return    string
	 */
	protected function setToolbar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_USER_MANAGER'), 'users');
		JToolBarHelper::spacer();
		JToolBarHelper::editList();
		JToolBarHelper::custom('logout', 'cancel.png', 'cancel_f2.png', 'COM_KUNENA_LOGOUT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('move', 'move.png', 'move_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');

		JHtml::_('bootstrap.modal', 'moderateModal');
		$title = JText::_('COM_KUNENA_VIEW_USERS_TOOLBAR_ASSIGN_MODERATORS');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#moderateModal\" class=\"btn btn-small\">
					<i class=\"icon-checkbox-partial\" title=\"$title\"> </i>
						$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');

		JToolBarHelper::divider();
		JToolBarHelper::custom('trashusermessages', 'trash.png', 'icon-32-move.png', 'COM_KUNENA_TRASH_USERMESSAGES');
		JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('removecatsubscriptions', 'delete.png', 'delete.png', 'COM_KUNENA_REMOVE_CATSUBSCRIPTIONS');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('removetopicsubscriptions', 'delete.png', 'delete.png', 'COM_KUNENA_REMOVE_TOPICSUBSCRIPTIONS');
		JToolBarHelper::spacer();
		$help_url  = 'https://docs.kunena.org/en/manual/backend/users';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * Returns an array of locked filter options.
	 *
	 * @return    string    The HTML code for the select tag
	 */
	public function signatureOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return    string    The HTML code for the select tag
	 */
	public function blockOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return    string    The HTML code for the select tag
	 */
	public function bannedOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return    string    The HTML code for the select tag
	 */
	public function moderatorOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return     array
	 */
	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = JHtml::_('select.option', 'username', JText::_('COM_KUNENA_USRL_USERNAME'));
		$sortFields[] = JHtml::_('select.option', 'email', JText::_('COM_KUNENA_USRL_EMAIL'));
		$sortFields[] = JHtml::_('select.option', 'rank', JText::_('COM_KUNENA_A_RANKS'));
		$sortFields[] = JHtml::_('select.option', 'signature', JText::_('COM_KUNENA_GEN_SIGNATURE'));
		$sortFields[] = JHtml::_('select.option', 'enabled', JText::_('COM_KUNENA_USRL_ENABLED'));
		$sortFields[] = JHtml::_('select.option', 'banned', JText::_('COM_KUNENA_USRL_BANNED'));
		$sortFields[] = JHtml::_('select.option', 'moderator', JText::_('COM_KUNENA_VIEW_MODERATOR'));
		$sortFields[] = JHtml::_('select.option', 'id', JText::_('JGRID_HEADING_ID'));
		$sortFields[] = JHtml::_('select.option', 'ip', JText::_('COM_KUNENA_GEN_IP'));

		return $sortFields;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return     array
	 */
	protected function getSortDirectionFields()
	{
		$sortDirection = array();
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}

	/**
	 * Returns an array ranks filter options.
	 *
	 * @return    string    The HTML code for the select tag
	 */
	public function ranksOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', 'Administrator', JText::_('Administrator'));
		$options[] = JHtml::_('select.option', 'New Member', JText::_('New Member'));

		return $options;
	}
}
