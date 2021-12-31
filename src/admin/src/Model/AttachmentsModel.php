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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Attachments Model for Kunena
 *
 * @since   Kunena 2.0
 */
class AttachmentsModel extends ListModel
{
	/**
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'id',
				'post',
				'username',
				'size',
				'folder',
				'filetype',
				'filename',
			];
		}

		parent::__construct($config);
	}

	/**
	 * @param   null  $ordering
	 * @param   null  $direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since  Kunena 6.0
	 */
	/*
	public function getForm($data = [], $loadData = true): void
	{
		// TODO: Implement getForm() method.
	}*/

	/*
	* Method to auto-populate the model state.
	*
	* Note. Calling getState in this method will result in recursion.
	*
	* @param   string  $ordering   ordering
	* @param   string  $direction  direction
	*
	* @return  void
	*
	* @throws  Exception
	* @since   Kunena 1.6
	*
	*/
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context = 'com_kunena.admin.attachments';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$filterActive = '';

		// List state information
		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $value);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.title', 'filterTitle', '', 'string');
		$this->setState('filter.title', $value);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.type', 'filterType', '', 'string');
		$this->setState('filter.type', $value);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.size', 'filter_size', '', 'string');
		$this->setState('filter.size', $value);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.dims', 'filter_dims', '', 'string');
		$this->setState('filter.dims', $value);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.username', 'filter_username', '', 'string');
		$this->setState('filter.username', $value);

		$filterActive .= $value = $this->getUserStateFromRequest($this->context . '.filter.post', 'filter_post', '', 'string');
		$this->setState('filter.post', $value);

		$this->setState('filter.active', !empty($filterActive));

		// List state information.
		parent::populateState('filename', 'asc');
	}

	/**
	 * @param   string  $id  id
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function getStoreId($id = ''): string
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
	 * Retrieve the KunenaAttachment objects associated with user informations and message informations
	 *
	 * @param   string   $query       The query.
	 * @param   integer  $limitstart  Offset.
	 * @param   integer  $limit       The number of records.
	 *
	 * @return  KunenaAttachment[]
	 *
	 * @throws null
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$this->_db->setQuery($query, $limitstart, $limit);
		$ids     = $this->_db->loadColumn();
		$results = KunenaAttachmentHelper::getById($ids);
		$userids = [];
		$mesids  = [];

		foreach ($results as $result)
		{
			$userids[$result->userid] = $result->userid;
			$mesids[$result->mesid]   = $result->mesid;
		}

		KunenaUserHelper::loadUsers($userids);
		KunenaMessageHelper::getMessages($mesids);

		return $results;
	}

	/**
	 *
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected function getListQuery(): QueryInterface
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
			$title = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.filename LIKE ' . $title . ' OR a.filename_real LIKE ' . $title . ')');
		}

		$filter = $this->getState('filter.type');

		if (!empty($filter))
		{
			$type = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.filetype LIKE ' . $type . ')');
		}

		// TODO: support < > and ranges
		$filter = $this->getState('filter.size');

		if (!empty($filter))
		{
			$size = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.size LIKE ' . $size . ')');
		}

		$filter = $this->getState('filter.username');

		if (!empty($filter))
		{
			$username = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(m.name LIKE ' . $username . ')');
		}

		$filter = $this->getState('filter.post');

		if (!empty($filter))
		{
			$post = $db->quote('%' . $db->escape($filter, true) . '%');
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
			$post = $db->quote('%' . $db->escape($filter, true) . '%');
			$query->where('(a.filename LIKE ' . $post . ')');
		}

		$db->setQuery($query);

		return $query;
	}
}
