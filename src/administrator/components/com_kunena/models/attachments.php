<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Models
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

/**
 * Attachments Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelAttachments extends JModelList
{

	/**
	 * @param   array $config
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'post',
				'username',
				'size',
				'folder',
				'filetype',
				'filename',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string $ordering
	 * @param   string $direction
	 *
	 * @return    void
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context = 'com_kunena.admin.attachments';

		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$filter_active = '';

		// List state information
		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.title', 'filter_title', '', 'string');
		$this->setState('filter.title', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'string');
		$this->setState('filter.type', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.size', 'filter_size', '', 'string');
		$this->setState('filter.size', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.dims', 'filter_dims', '', 'string');
		$this->setState('filter.dims', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.username', 'filter_username', '', 'string');
		$this->setState('filter.username', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.post', 'filter_post', '', 'string');
		$this->setState('filter.post', $value);

		$this->setState('filter.active', !empty($filter_active));

		// List state information.
		parent::populateState('filename', 'asc');
	}

	/**
	 * @param   string $id
	 *
	 * @return string
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.title');
		$id .= ':' . $this->getState('filter.type');
		$id .= ':' . $this->getState('filter.size');
		$id .= ':' . $this->getState('filter.dims');
		$id .= ':' . $this->getState('filter.username');
		$id .= ':' . $this->getState('filter.post');

		return parent::getStoreId($id);
	}

	/**
	 * @param   string $query
	 * @param   int    $limitstart
	 * @param   int    $limit
	 *
	 * @return KunenaAttachment[]
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$this->_db->setQuery($query, $limitstart, $limit);
		$ids     = $this->_db->loadColumn();
		$results = KunenaAttachmentHelper::getById($ids);
		$userids = array();
		$mesids  = array();

		foreach ($results as $result)
		{
			$userids[$result->userid] = $result->userid;
			$mesids[$result->mesid]   = $result->mesid;
		}

		KunenaUserHelper::loadUsers($userids);
		KunenaForumMessageHelper::getMessages($mesids);

		return $results;
	}

	/**
	 * @return JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'a.id'
			)
		);

		$query->from('#__kunena_attachments AS a');

		$query->select('m.subject AS post_title');
		$query->select('m.name AS user_title');
		$query->join('LEFT', '#__kunena_messages AS m ON m.id = a.mesid');

		// $query->join('LEFT', '#__kunena_messages AS u ON u.userid = a.userid');

		$filter = $this->getState('filter.title');

		if (!empty($filter))
		{
			$title = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.filename LIKE ' . $title . ' OR a.filename_real LIKE ' . $title . ')');
		}

		$filter = $this->getState('filter.type');

		if (!empty($filter))
		{
			$type = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.filetype LIKE ' . $type . ')');
		}

		// TODO: support < > and ranges
		$filter = $this->getState('filter.size');

		if (!empty($filter))
		{
			$size = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.size LIKE ' . $size . ')');
		}

		$filter = $this->getState('filter.username');

		if (!empty($filter))
		{
			$username = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(m.name LIKE ' . $username . ')');
		}

		$filter = $this->getState('filter.post');

		if (!empty($filter))
		{
			$post = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(m.subject LIKE ' . $post . ')');
		}

		// Add the list ordering clause.
		$direction = strtoupper($this->state->get('list.direction'));

		switch ($this->state->get('list.ordering'))
		{
			case 'title':
				$query->order('a.filename ' . $direction);
				break;
			case 'type':
				$query->order('a.filetype ' . $direction);
				break;
			case 'size':
				$query->order('a.size ' . $direction);
				break;
			case 'username':
				$query->order('m.name ' . $direction);
				break;
			case 'post':
				$query->order('m.subject ' . $direction);
				break;
			default:
				$query->order('a.id ' . $direction);
		}
		
		$filter = $this->getState('filter.search');
		
		if (!empty($filter))
		{
			$post = $db->Quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.filename LIKE ' . $post . ')');
		}

		return $query;
	}
}
