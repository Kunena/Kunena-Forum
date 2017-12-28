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
 * Statistics view for Kunena backend
 */
class KunenaAdminViewStatistics extends KunenaView
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


	protected function setToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_MENU_STATISTICS'), 'chart' );
	}

	protected function getFilterUserFields()
	{
		$filterFields = array();
		$filterFields[] = JHtml::_('select.option', 0, 'Guests');
		$filterFields[] = JHtml::_('select.option', 1, 'Registered users');
		$filterFields[] = JHtml::_('select.option', 2, 'Regular members');
		$filterFields[] = JHtml::_('select.option', 3, 'Moderators');
		$filterFields[] = JHtml::_('select.option', 4, 'Administrators');
		$filterFields[] = JHtml::_('select.option', 5, 'Mods and Admins');

		return $filterFields;
	}
	protected function getSortFields()
	{
		$sortFields = array();
		// TODO: translate
		$sortFields[] = JHtml::_('select.option', 'id', $this->group ? 'Count' : 'Id');
		$sortFields[] = JHtml::_('select.option', 'type', 'Type (by id)');
		$sortFields[] = JHtml::_('select.option', 'user', 'User (by id)');
		$sortFields[] = JHtml::_('select.option', 'category', 'Category (by id)');
		$sortFields[] = JHtml::_('select.option', 'topic', 'Topic (by id)');
		$sortFields[] = JHtml::_('select.option', 'target_user', 'Target User (by id)');
		$sortFields[] = JHtml::_('select.option', 'time', 'Time (by id)');

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
