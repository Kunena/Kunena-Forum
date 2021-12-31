<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Statistics view for Kunena backend
 * @since Kunena
 */
class KunenaAdminViewStatistics extends KunenaView
{
	/**
	 * @param   null $tpl tpl
	 *
	 * @throws ReflectionException
	 * @since Kunena
	 */
	public function displayDefault($tpl = null)
	{
		$this->state      = $this->get('state');
		$this->group      = $this->state->get('group');
		$this->items      = $this->get('items');
		$this->pagination = $this->get('Pagination');

		$this->filterUserFields    = $this->getFilterUserFields();
		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterTypeFields      = $this->getFilterTypeFields();
		$this->filterOperationFields = $this->getFilterOperationFields();

		$this->filterSearch     = $this->escape($this->state->get('filter.search'));
		$this->filterType       = $this->escape($this->state->get('filter.type'));
		$this->filterUser       = $this->escape($this->state->get('filter.user'));
		$this->filterCategory   = $this->escape($this->state->get('filter.category'));
		$this->filterTopic      = $this->escape($this->state->get('filter.topic'));
		$this->filterTargetUser = $this->escape($this->state->get('filter.target_user'));
		$this->filterIp         = $this->escape($this->state->get('filter.ip'));
		$this->filterTimeStart  = $this->escape($this->state->get('filter.time_start'));
		$this->filterTimeStop   = $this->escape($this->state->get('filter.time_stop'));
		$this->filterOperation  = $this->escape($this->state->get('filter.operation'));
		$this->filterActive     = $this->escape($this->state->get('filter.active'));

		$this->filterUsertypes = $this->escape($this->state->get('filter.usertypes'));
		$this->listOrdering    = $this->escape($this->state->get('list.ordering'));
		$this->listDirection   = $this->escape($this->state->get('list.direction'));

		$document = Factory::getDocument();
		$document->setTitle(Text::_('Forum Logs'));

		$this->setToolbar();
		$this->display();
	}

	/**
	 * @return array
	 * @since  Kunena
	 */
	protected function getFilterUserFields()
	{
		$filterFields   = array();
		$filterFields[] = HTMLHelper::_('select.option', 0, 'Guests');
		$filterFields[] = HTMLHelper::_('select.option', 1, 'Registered users');
		$filterFields[] = HTMLHelper::_('select.option', 2, 'Regular members');
		$filterFields[] = HTMLHelper::_('select.option', 3, 'Moderators');
		$filterFields[] = HTMLHelper::_('select.option', 4, 'Administrators');
		$filterFields[] = HTMLHelper::_('select.option', 5, 'Mods and Admins');

		return $filterFields;
	}

	/**
	 * @return array
	 * @since  Kunena
	 */
	protected function getSortFields()
	{
		$sortFields = array();

		// TODO: translate
		$sortFields[] = HTMLHelper::_('select.option', 'id', $this->group ? 'Count' : 'Id');
		$sortFields[] = HTMLHelper::_('select.option', 'type', 'Type (by id)');
		$sortFields[] = HTMLHelper::_('select.option', 'user', 'User (by id)');
		$sortFields[] = HTMLHelper::_('select.option', 'category', 'Category (by id)');
		$sortFields[] = HTMLHelper::_('select.option', 'topic', 'Topic (by id)');
		$sortFields[] = HTMLHelper::_('select.option', 'target_user', 'Target User (by id)');
		$sortFields[] = HTMLHelper::_('select.option', 'time', 'Time (by id)');

		return $sortFields;
	}

	/**
	 * @return array
	 * @since  Kunena
	 */
	protected function getSortDirectionFields()
	{
		$sortDirection   = array();
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

		return $sortDirection;
	}

	/**
	 * @return array
	 * @since  Kunena
	 */
	protected function getFilterTypeFields()
	{
		$filterFields   = array();
		$filterFields[] = HTMLHelper::_('select.option', 1, 'MOD');
		$filterFields[] = HTMLHelper::_('select.option', 2, 'ACT');
		$filterFields[] = HTMLHelper::_('select.option', 3, 'ERR');
		$filterFields[] = HTMLHelper::_('select.option', 4, 'REP');

		return $filterFields;
	}

	/**
	 * @return array
	 * @throws ReflectionException
	 * @since  Kunena
	 */
	protected function getFilterOperationFields()
	{
		$filterFields = array();

		$reflection = new ReflectionClass('KunenaLog');
		$constants  = $reflection->getConstants();
		ksort($constants);

		foreach ($constants as $key => $value)
		{
			if (strpos($key, 'LOG_') === 0)
			{
				$filterFields[] = HTMLHelper::_('select.option', $key, Text::_("COM_KUNENA_{$value}"));
			}
		}

		return $filterFields;
	}

	protected function setToolbar()
	{
		// Set the titlebar text
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_MENU_STATISTICS'), 'chart');
	}

	/**
	 * @param $id
	 *
	 * @return mixed|string
	 * @since Kunena
	 */
	public function getType($id)
	{
		static $types = array(1 => 'MOD', 2 => 'ACT', 3 => 'ERR', 4 => 'REP');

		return isset($types[$id]) ? $types[$id] : '???';
	}

	/**
	 * @param $name
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getGroupCheckbox($name)
	{
		$checked = isset($this->group[$name]) ? ' checked="checked"' : '';

		return '<input type="checkbox" name="group_' . $name . '" value="1" title="Group By" ' . $checked . ' class="filter" />';
	}
}
