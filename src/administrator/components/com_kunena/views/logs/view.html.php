<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Logs view for Kunena backend
 *
 * @since 5.0
 */
class KunenaAdminViewLogs extends KunenaView
{
	public function displayDefault($tpl = null)
	{
		$this->state = $this->get('state');
		$this->group = $this->state->get('group');
		$this->items = $this->get('items');
		$this->pagination = $this->get('Pagination');

		$this->filterUserFields = $this->getFilterUserFields();
		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterTypeFields = $this->getFilterTypeFields();
		$this->filterOperationFields = $this->getFilterOperationFields();

		$this->filterSearch = $this->escape($this->state->get('filter.search'));
		$this->filterType	= $this->escape($this->state->get('filter.type'));
		$this->filterUser	= $this->escape($this->state->get('filter.user'));
		$this->filterCategory = $this->escape($this->state->get('filter.category'));
		$this->filterTopic = $this->escape($this->state->get('filter.topic'));
		$this->filterTargetUser = $this->escape($this->state->get('filter.target_user'));
		$this->filterIp = $this->escape($this->state->get('filter.ip'));
		$this->filterTimeStart = $this->escape($this->state->get('filter.time_start'));
		$this->filterTimeStop = $this->escape($this->state->get('filter.time_stop'));
		$this->filterOperation = $this->escape($this->state->get('filter.operation'));
		$this->filterActive = $this->escape($this->state->get('filter.active'));

		$this->filterUsertypes	= $this->escape($this->state->get('filter.usertypes'));
		$this->listOrdering = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('Forum Logs'));

		$this->setToolbar();
		$this->display();
	}

	/**
	 * Set the toolbar on log manager
	 */
	protected function setToolbar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_LOG_MANAGER'), 'users' );

		JToolBarHelper::spacer();
		JToolBarHelper::custom('cleanentries', 'trash.png', 'trash_f2.png', 'COM_KUNENA_LOG_CLEAN_ENTRIES');
	}

	/**
	 *
	 */
	function displayClean()
	{
		$this->setToolBarClean();
		$this->display();
	}

	/**
	 *
	 */
	protected function setToolbarClean()
	{
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_LOG_MANAGER'), 'users' );

		JToolBarHelper::spacer();
		JToolBarHelper::custom('clean', 'delete.png', 'delete_f2.png', 'COM_KUNENA_CLEAN_LOGS_ENTRIES', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

	}

	protected function getFilterUserFields()
	{
		$filterFields = array();
		$filterFields[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_LOG_GUESTS_FILTER_USERTYPE_LABEL'));
		$filterFields[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_LOG_REGISTERED_FILTER_USERTYPE_LABEL'));
		$filterFields[] = JHtml::_('select.option', 2, JText::_('COM_KUNENA_LOG_REGULAR_FILTER_USERTYPE_LABEL'));
		$filterFields[] = JHtml::_('select.option', 3, JText::_('COM_KUNENA_LOG_MODERATORS_FILTER_USERTYPE_LABEL'));
		$filterFields[] = JHtml::_('select.option', 4, JText::_('COM_KUNENA_LOG_ADMINISTRATORS_FILTER_USERTYPE_LABEL'));
		$filterFields[] = JHtml::_('select.option', 5, JText::_('COM_KUNENA_LOG_MOD_AND_ADMIN_FILTER_USERTYPE_LABEL'));

		return $filterFields;
	}

	protected function getSortFields()
	{
		$sortFields = array();

		$sortFields[] = JHtml::_('select.option', 'id', JText::_('COM_KUNENA_LOG_ID_SORT_FIELD_LABEL'));
		$sortFields[] = JHtml::_('select.option', 'type', JText::_('COM_KUNENA_LOG_TYPE_SORT_FIELD_LABEL'));
		$sortFields[] = JHtml::_('select.option', 'user', JText::_('COM_KUNENA_LOG_USER_SORT_FIELD_LABEL'));
		$sortFields[] = JHtml::_('select.option', 'category', JText::_('COM_KUNENA_LOG_CATEGORY_SORT_FIELD_LABEL'));
		$sortFields[] = JHtml::_('select.option', 'topic', JText::_('COM_KUNENA_LOG_TOPIC_SORT_FIELD_LABEL'));
		$sortFields[] = JHtml::_('select.option', 'target_user', JText::_('COM_KUNENA_LOG_TARGET_USER_SORT_FIELD_LABEL'));
		$sortFields[] = JHtml::_('select.option', 'time', JText::_('COM_KUNENA_LOG_TIME_SORT_FIELD_LABEL'));

		return $sortFields;
	}

	protected function getSortDirectionFields()
	{
		$sortDirection = array();
//		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
//		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));
		// TODO: remove it when J2.5 support is dropped
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

		return $sortDirection;
	}

	protected function getFilterTypeFields()
	{
		$filterFields = array();
		$filterFields[] = JHtml::_('select.option', 1, 'MOD');
		$filterFields[] = JHtml::_('select.option', 2, 'ACT');
		$filterFields[] = JHtml::_('select.option', 3, 'ERR');
		$filterFields[] = JHtml::_('select.option', 4, 'REP');

		return $filterFields;
	}

	protected function getFilterOperationFields()
	{
		$filterFields = array();

		$reflection = new ReflectionClass('KunenaLog');
		$constants = $reflection->getConstants();
		ksort($constants);

		foreach ($constants as $key => $value)
		{
			if (strpos($key, 'LOG_') === 0)
			{
				$filterFields[] = JHtml::_('select.option', $key, JText::_("COM_KUNENA_{$value}"));
			}
		}

		return $filterFields;
	}

	public function getType($id)
	{
		static $types = array(1 => 'MOD', 2 => 'ACT', 3 => 'ERR', 4 => 'REP');

		return isset($types[$id]) ? $types[$id] : '???';
	}

	public function getGroupCheckbox($name)
	{
		$checked = isset($this->group[$name]) ? ' checked="checked"' : '';

		return '<input type="checkbox" name="group_'.$name.'" value="1" title="Group By" '.$checked.' class="filter" />';
	}
}
