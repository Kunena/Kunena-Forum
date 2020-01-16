<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Logs;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use ReflectionClass;
use ReflectionException;
use function defined;

/**
 * Logs view for Kunena backend
 *
 * @since 5.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  ReflectionException
	 */
	public function display($tpl = null)
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

		$document = Factory::getApplication()->getDocument();
		$document->setTitle(Text::_('Forum Logs'));

		$this->setToolbar();

		return parent::display($tpl);
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getFilterUserFields()
	{
		$filterFields   = [];
		$filterFields[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_LOG_GUESTS_FILTER_USERTYPE_LABEL'));
		$filterFields[] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_LOG_REGISTERED_FILTER_USERTYPE_LABEL'));
		$filterFields[] = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_LOG_REGULAR_FILTER_USERTYPE_LABEL'));
		$filterFields[] = HTMLHelper::_('select.option', 3, Text::_('COM_KUNENA_LOG_MODERATORS_FILTER_USERTYPE_LABEL'));
		$filterFields[] = HTMLHelper::_('select.option', 4, Text::_('COM_KUNENA_LOG_ADMINISTRATORS_FILTER_USERTYPE_LABEL'));
		$filterFields[] = HTMLHelper::_('select.option', 5, Text::_('COM_KUNENA_LOG_MOD_AND_ADMIN_FILTER_USERTYPE_LABEL'));

		return $filterFields;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortFields()
	{
		$sortFields = [];

		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('COM_KUNENA_LOG_ID_SORT_FIELD_LABEL'));
		$sortFields[] = HTMLHelper::_('select.option', 'type', Text::_('COM_KUNENA_LOG_TYPE_SORT_FIELD_LABEL'));
		$sortFields[] = HTMLHelper::_('select.option', 'user', Text::_('COM_KUNENA_LOG_USER_SORT_FIELD_LABEL'));
		$sortFields[] = HTMLHelper::_('select.option', 'category', Text::_('COM_KUNENA_LOG_CATEGORY_SORT_FIELD_LABEL'));
		$sortFields[] = HTMLHelper::_('select.option', 'topic', Text::_('COM_KUNENA_LOG_TOPIC_SORT_FIELD_LABEL'));
		$sortFields[] = HTMLHelper::_('select.option', 'target_user', Text::_('COM_KUNENA_LOG_TARGET_USER_SORT_FIELD_LABEL'));
		$sortFields[] = HTMLHelper::_('select.option', 'time', Text::_('COM_KUNENA_LOG_TIME_SORT_FIELD_LABEL'));

		return $sortFields;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortDirectionFields()
	{
		$sortDirection   = [];
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

		return $sortDirection;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getFilterTypeFields()
	{
		$filterFields   = [];
		$filterFields[] = HTMLHelper::_('select.option', 1, 'MOD');
		$filterFields[] = HTMLHelper::_('select.option', 2, 'ACT');
		$filterFields[] = HTMLHelper::_('select.option', 3, 'ERR');
		$filterFields[] = HTMLHelper::_('select.option', 4, 'REP');

		return $filterFields;
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  ReflectionException
	 */
	protected function getFilterOperationFields()
	{
		$filterFields = [];

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

	/**
	 * Set the toolbar on log manager
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolbar()
	{
		// Get the toolbar object instance
		$bar = Toolbar::getInstance('toolbar');

		// Set the titlebar text
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_LOG_MANAGER'), 'users');

		ToolbarHelper::spacer();
		ToolbarHelper::custom('cleanentries', 'trash.png', 'trash_f2.png', 'COM_KUNENA_LOG_CLEAN_ENTRIES', false);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function displayClean()
	{
		$this->setToolBarClean();
		$this->display();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolbarClean()
	{
		// Set the titlebar text
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_LOG_MANAGER'), 'users');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('clean', 'delete.png', 'delete_f2.png', 'COM_KUNENA_CLEAN_LOGS_ENTRIES', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
	}

	/**
	 * @param   integer  $id id
	 *
	 * @return  mixed|string
	 *
	 * @since   Kunena 6.0
	 */
	public function getType($id)
	{
		static $types = [1 => 'MOD', 2 => 'ACT', 3 => 'ERR', 4 => 'REP'];

		return isset($types[$id]) ? $types[$id] : '???';
	}

	/**
	 * @param   string  $name name
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getGroupCheckbox($name)
	{
		$checked = isset($this->group[$name]) ? ' checked="checked"' : '';

		return '<input type="checkbox" name="group_' . $name . '" value="1" title="Group By" ' . $checked . ' class="filter form-control" />';
	}
}
