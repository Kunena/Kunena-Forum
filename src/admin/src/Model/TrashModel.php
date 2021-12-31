<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageFinder;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Model\KunenaModel;

/**
 * Trash Model for Kunena
 *
 * @since   Kunena 2.0
 */
class TrashModel extends KunenaModel
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $internalItems = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $internalItemsOrder = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $internalObject = false;

	/**
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load data
	 *
	 * @return void
	 *
	 * @since  Kunena 6.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// TODO: Implement getForm() method.
	}

	/**
	 * Method to get all deleted messages or topics in function of user selection.
	 *
	 * @return  array
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 1.6
	 */
	public function getTrashItems()
	{
		if ($this->state->get('layout') == 'topics')
		{
			// Get topics
			return $this->internalGetTopics();
		}

		// Get messages
		return $this->internalGetMessages();
	}

	/**
	 * Method to get all deleted topics.
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 1.6
	 */
	protected function internalGetTopics(): array
	{
		$finder = new KunenaMessageFinder;
		$finder->filterByHold([2, 3]);

		$direction = strtoupper($this->getState('list.direction'));

		switch ($this->getState('list.ordering'))
		{
			case 'title':
				$finder->order('subject', $direction);
				break;

			/*
			case 'category':
				$query->order('c.name ' . $direction);
			case 'author':
				$query->order('m.name ' . $direction);
				break; */
			case 'time':
				$finder->order('time', $direction);
				break;
			default:
				$finder->order('id', $direction);
				$this->setState('list.ordering', 'id');
		}

		$filter = $this->getState('filter.title');

		/*
		if (!empty($filter))
		{
		$like = $db->quote('%' . $db->escape($filter, true) . '%');
		$query->where('(a.subject LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.category');

		if (!empty($filter))
		{
		$like = $db->quote('%' . $db->escape($filter, true) . '%');
		$query->where('(c.name LIKE ' . $like . ')');

		}

		$filter = $this->getState('filter.author');

		if (!empty($filter))
		{
		$like = $db->quote('%' . $db->escape($filter, true) . '%');
		$query->where('(m.name LIKE ' . $like . ')');

		} */

		$filter = $this->getState('filter.time');

		if (!empty($filter))
		{
			$finder->filterByTime($filter);
		}

		$search = $this->getState('list.search');

		if (!empty($search))
		{
			$finder->where('a.subject', 'LIKE', '%' . $search . '%');
		}

		$finder->where('a.parent', '=', 0);

		$total = $finder->count();

		$this->setState('list.total', $total);

		if ($this->getState('list.limit') && $total < $this->getState('list.start'))
		{
			$this->setState('list.start', \intval($total / $this->getState('list.limit')) * $this->getState('list.limit'));
		}

		return $finder
			->start($this->getState('list.start'))
			->limit($this->getState('list.limit'))
			->find();
	}

	/**
	 * Method to get all deleted messages.
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 1.6
	 */
	protected function internalGetMessages(): array
	{
		$db   = Factory::getContainer()->get('DatabaseDriver');
		$join = [];

		$query = $db->getQuery(true)
			->select('a.id')
			->from($db->quoteName('#__kunena_messages', 'a'));
		$query->where('a.hold >= 2');

		$filter = $this->getState('filter.title');

		if (!empty($filter))
		{
			$like = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.subject LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.topic');

		if (!empty($filter))
		{
			$like = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(tt.subject LIKE ' . $like . ')');
			$join['tt'] = true;
		}

		$filter = $this->getState('filter.category');

		if (!empty($filter))
		{
			$like = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(c.name LIKE ' . $like . ')');
			$join['c'] = true;
		}

		$filter = $this->getState('filter.ip');

		if (!empty($filter))
		{
			$like = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.ip LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.author');

		if (!empty($filter))
		{
			$like = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.name LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.time');

		if (!empty($filter))
		{
			$like = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.time LIKE ' . $like . ')');
		}

		$search = $this->getState('list.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('( a.subject LIKE ' . $like . ' OR a.name LIKE ' . $like . ' OR a.id LIKE ' . $like . ' )');
		}

		// Add the list ordering clause.
		$direction = strtoupper($this->getState('list.direction'));

		switch ($this->getState('list.ordering'))
		{
			case 'title':
				$query->order('a.subject ' . $direction);
				break;
			case 'topic':
				$query->order('tt.subject ' . $direction);
				$join['tt'] = true;
				break;
			case 'category':
				$query->order('c.name ' . $direction);
				$join['c'] = true;
				break;
			case 'ip':
				$query->order('a.ip ' . $direction);
				break;
			case 'author':
				$query->order('a.name ' . $direction);
				break;
			case 'time':
				$query->order('a.time ' . $direction);
				break;
			default:
				$query->order('a.id ' . $direction);
				$this->setState('list.ordering', 'id');
		}

		if (isset($join['tt']))
		{
			$query->innerJoin($db->quoteName('#__kunena_topics', 'tt') . ' ON tt.id=a.thread');
		}

		if (isset($join['c']))
		{
			$query->innerJoin($db->quoteName('#__kunena_categories', 'c') . ' ON c.id=a.catid');
		}

		// TODO: add authorization.

		$cquery = clone $query;
		$cquery->clear('SELECT')->clear('order')->select('COUNT(*)');
		$db->setQuery($cquery);

		try
		{
			$total = (int) $db->loadResult();
			$this->setState('list.total', $total);
		}
		catch (ExecutionFailureException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return [];
		}

		if (!$total)
		{
			return [];
		}

		// If out of range, use last page
		if ($this->getState('list.limit') && $total < $this->getState('list.start'))
		{
			$this->setState('list.start', \intval($total / $this->getState('list.limit')) * $this->getState('list.limit'));
		}

		$query->setLimit($this->getState('list.limit'), $this->getState('list.start'));

		$db->setQuery($query);
		$ids = $db->loadColumn();

		return KunenaMessageHelper::getMessages($ids, 'none');
	}

	/**
	 * Method to get select options to choose between topics and messages.
	 *
	 * @return  string
	 *
	 * @since   Kunena 1.6
	 */
	public function getViewOptions(): string
	{
		$viewOptions   = [];
		$viewOptions[] = HTMLHelper::_('select.option', 'topics', Text::_('COM_KUNENA_TRASH_TOPICS'));
		$viewOptions[] = HTMLHelper::_('select.option', 'messages', Text::_('COM_KUNENA_TRASH_MESSAGES'));

		return HTMLHelper::_(
			'select.genericlist',
			$viewOptions,
			'layout',
			'class="inputbox form-control" size="1" onchange="this.form.submit()"',
			'value',
			'text',
			$this->getState('layout')
		);
	}

	/**
	 * Method to get details on selected items.
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 1.6
	 */
	public function getPurgeItems(): array
	{
		$ids  = (array) $this->app->getUserState('com_kunena.purge');
		$type = (string) $this->app->getUserState('com_kunena.type');

		$items = [];

		if ($type == 'topics')
		{
			$items = KunenaTopicHelper::getTopics($ids, 'none');
		}
		elseif ($type == 'messages')
		{
			$items = KunenaMessageHelper::getMessages($ids, 'none');
		}

		return $items;
	}

	/**
	 * Method to hash data's.
	 *
	 * @return  string Hashed value.
	 *
	 * @since   Kunena 1.6
	 */
	public function getMd5(): string
	{
		$ids = (array) $this->app->getUserState('com_kunena.purge');

		return md5(serialize($ids));
	}

	/**
	 * @return  Pagination
	 *
	 * @since   Kunena 6.0
	 */
	public function getNavigation(): Pagination
	{
		return new Pagination(
			$this->getState('list.total'),
			$this->getState('list.start'),
			$this->getState('list.limit')
		);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function populateState(): void
	{
		$context = 'com_kunena.admin.trash';

		$layout = $this->getUserStateFromRequest("com_kunena.admin.trash.layout", 'layout', 'messages', 'cmd');

		// Set default view on messages
		if ($layout != 'messages')
		{
			$layout = 'topics';
		}

		$this->setState('layout', $layout);

		if ($layout)
		{
			$context .= '.' . $layout;
		}

		// List state information
		$value = $this->getUserStateFromRequest("com_kunena.admin.trash.list.limit", 'limit', $this->app->get('list_limit'), 'int');
		$this->setState('list.limit', $value);

		$value = $this->getUserStateFromRequest("com_kunena.admin.trash.list.start", 'limitstart', 0, 'int');
		$this->setState('list.start', $value);

		$value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.ordering', 'filter_order', 'id', 'cmd');
		$this->setState('list.ordering', $value);

		$value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.direction', 'filter_order_Dir', 'asc', 'word');

		if ($value != 'asc')
		{
			$value = 'desc';
		}

		$this->setState('list.direction', $value);

		$filterActive = '';

		$filterActive .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.search', 'filter_search', '', 'string');
		$this->setState('list.search', $value);

		$filterActive .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filterTitle', 'filterTitle', '', 'string');
		$this->setState('filter.title', $value);

		$filterActive .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_topic', 'filter_topic', '', 'string');
		$this->setState('filter.topic', $value);

		$filterActive .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_category', 'filter_category', '', 'string');
		$this->setState('filter.category', $value);

		$filterActive .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_ip', 'filter_ip', '', 'string');
		$this->setState('filter.ip', $value);

		$filterActive .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_author', 'filter_author', '', 'string');
		$this->setState('filter.author', $value);

		$filterActive .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_date', 'filter_time', '', 'string');
		$this->setState('filter.time', $value);

		$this->setState('filter.active', !empty($filterActive));
	}
}
