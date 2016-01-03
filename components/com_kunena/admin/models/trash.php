<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Models
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

jimport('joomla.application.component.model');

/**
 * Trash Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelTrash extends KunenaModel
{
	protected $__state_set = false;
	protected $_items = false;
	protected $_items_order = false;
	protected $_object = false;

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		$this->context = 'com_kunena.admin.trash';

		$layout = $this->getUserStateFromRequest("com_kunena.admin.trash.layout", 'layout', 'messages', 'cmd');

		// Set default view on messages
		if ($layout != 'messages')
		{
			$layout = 'topics';
		}

		$this->setState('layout', $layout);

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		// List state information
		$value = $this->getUserStateFromRequest("com_kunena.admin.trash.list.limit", 'limit', $this->app->getCfg('list_limit'), 'int');
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

		$filter_active = '';

		$filter_active .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.search', 'filter_search', '', 'string');
		$this->setState('list.search', $value);

		$filter_active .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_title', 'filter_title', '', 'string');
		$this->setState('filter.title', $value);

		$filter_active .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_topic', 'filter_topic', '', 'string');
		$this->setState('filter.topic', $value);

		$filter_active .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_category', 'filter_category', '', 'string');
		$this->setState('filter.category', $value);

		$filter_active .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_ip', 'filter_ip', '', 'string');
		$this->setState('filter.ip', $value);

		$filter_active .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_author', 'filter_author', '', 'string');
		$this->setState('filter.author', $value);

		$filter_active .= $value = $this->getUserStateFromRequest('com_kunena.admin.trash.list.filter_date', 'filter_time', '', 'string');
		$this->setState('filter.time', $value);

		$this->setState('filter.active', !empty($filter_active));
	}

	/**
	 * Method to get all deleted messages or topics in function of user selection.
	 *
	 * @return    Array
	 * @since    1.6
	 */
	public function getTrashItems()
	{
		if ($this->state->get('layout') == 'topics')
		{
			// Get topics
			return $this->_getTopics();
		}

		// Get messages
		return $this->_getMessages();
	}

	/**
	 * Method to get all deleted messages.
	 *
	 * @return    Array
	 * @since    1.6
	 */
	protected function _getMessages()
	{
		$db   = JFactory::getDBO();
		$join = array();

		$query = $db->getQuery(true)->select('a.id')->from('#__kunena_messages AS a');
		$query->where('a.hold>=2');

		$filter = $this->getState('filter.title');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.subject LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.topic');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(tt.subject LIKE ' . $like . ')');
			$join['tt'] = true;
		}

		$filter = $this->getState('filter.category');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(c.name LIKE ' . $like . ')');
			$join['c'] = true;
		}

		$filter = $this->getState('filter.ip');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.ip LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.author');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.name LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.time');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.time LIKE ' . $like . ')');
		}

		$search = $this->getState('list.search');

		if (!empty($search))
		{
			$like = $db->Quote('%' . $db->escape($search, true) . '%');
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
			$query->innerJoin('#__kunena_topics AS tt ON tt.id=a.thread');
		}

		if (isset($join['c']))
		{
			$query->innerJoin('#__kunena_categories AS c ON c.id=a.catid');
		}

		// TODO: add authorization.

		$cquery = clone $query;
		$cquery->clear('select')->clear('order')->select('COUNT(*)');
		$db->setQuery($cquery);
		$total = (int) $db->loadResult();
		$this->setState('list.total', $total);

		if (KunenaError::checkDatabaseError() || !$total)
		{
			return array();
		}

		// If out of range, use last page
		if ($this->getState('list.limit') && $total < $this->getState('list.start'))
		{
			$this->setState('list.start', intval($total / $this->getState('list.limit')) * $this->getState('list.limit'));
		}

		$db->setQuery($query, $this->getState('list.start'), $this->getState('list.limit'));
		$ids = $db->loadColumn();

		return KunenaForumMessageHelper::getMessages($ids, 'none');
	}

	/**
	 * Method to get all deleted topics.
	 *
	 * @return    Array
	 * @since    1.6
	 */
	protected function _getTopics()
	{
		$db   = JFactory::getDBO();
		$join = array();

		$query = $db->getQuery(true)->select('a.id')->from('#__kunena_topics AS a');
		$query->where('a.hold>=2');

		$filter = $this->getState('filter.title');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.subject LIKE ' . $like . ')');
		}

		$filter = $this->getState('filter.category');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(c.name LIKE ' . $like . ')');
			$join['c'] = true;
		}

		$filter = $this->getState('filter.author');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(m.name LIKE ' . $like . ')');
			$join['m'] = true;
		}

		$filter = $this->getState('filter.time');

		if (!empty($filter))
		{
			$like = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.first_post_time LIKE ' . $like . ')');
		}

		$search = $this->getState('list.search');

		if (!empty($search))
		{
			$like = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('( a.subject LIKE ' . $like . ' OR m.name LIKE ' . $like . ' OR a.id LIKE ' . $like . ' )');
			$join['m'] = true;
		}

		// Add the list ordering clause.
		$direction = strtoupper($this->getState('list.direction'));

		switch ($this->getState('list.ordering'))
		{
			case 'title':
				$query->order('a.subject ' . $direction);
				break;
			case 'category':
				$query->order('c.name ' . $direction);
				$join['c'] = true;
				break;
			case 'author':
				$query->order('m.name ' . $direction);
				$join['m'] = true;
				break;
			case 'time':
				$query->order('a.first_post_time ' . $direction);
				break;
			default:
				$query->order('a.id ' . $direction);
				$this->setState('list.ordering', 'id');
		}

		if (isset($join['c']))
		{
			$query->innerJoin('#__kunena_categories AS c ON c.id=a.category_id');
		}

		if (isset($join['m']))
		{
			$query->innerJoin('#__kunena_messages AS m ON m.id=a.first_post_id');
		}

		// TODO: add authorization.

		$cquery = clone $query;
		$cquery->clear('select')->clear('order')->select('COUNT(*)');
		$db->setQuery($cquery);
		$total = (int) $db->loadResult();
		$this->setState('list.total', $total);

		if (KunenaError::checkDatabaseError() || !$total)
		{
			return array();
		}

		// If out of range, use last page
		if ($this->getState('list.limit') && $total < $this->getState('list.start'))
		{
			$this->setState('list.start', intval($total / $this->getState('list.limit')) * $this->getState('list.limit'));
		}

		$db->setQuery($query, $this->getState('list.start'), $this->getState('list.limit'));
		$ids = $db->loadColumn();

		return KunenaForumTopicHelper::getTopics($ids, 'none');
	}

	/**
	 * Method to get select options to choose between topics and messages.
	 *
	 * @return    Array
	 * @since    1.6
	 */
	public function getViewOptions()
	{
		$view_options   = array();
		$view_options[] = JHtml::_('select.option', 'topics', JText::_('COM_KUNENA_TRASH_TOPICS'));
		$view_options[] = JHtml::_('select.option', 'messages', JText::_('COM_KUNENA_TRASH_MESSAGES'));

		return JHtml::_('select.genericlist', $view_options, 'layout', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $this->getState('layout'));
	}

	/**
	 * Method to get details on selected items.
	 *
	 * @return    Array
	 * @since    1.6
	 */
	public function getPurgeItems()
	{
		$ids  = (array) $this->app->getUserState('com_kunena.purge');
		$type = (string) $this->app->getUserState('com_kunena.type');

		$items = array();

		if ($type == 'topics')
		{
			$items = KunenaForumTopicHelper::getTopics($ids, 'none');
		}
		elseif ($type == 'messages')
		{
			$items = KunenaForumMessageHelper::getMessages($ids, 'none');
		}

		return $items;
	}

	/**
	 * Method to hash datas.
	 *
	 * @return    string Hashed value.
	 * @since    1.6
	 */
	public function getMd5()
	{
		$ids = (array) $this->app->getUserState('com_kunena.purge');

		return md5(serialize($ids));
	}

	public function getNavigation()
	{
		jimport('joomla.html.pagination');
		$navigation = new JPagination ($this->getState('list.total'), $this->getState('list.start'), $this->getState('list.limit'));

		return $navigation;
	}
}
