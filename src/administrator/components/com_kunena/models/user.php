<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.model');

/**
 * User Model for Kunena
 *
 * @since  3.0
 */
class KunenaAdminModelUser extends KunenaModel
{
	/**
	 * @return array|KunenaForumTopic[]|void
	 *
	 * @since Kunena
	 * @throws null
	 * @throws Exception
	 */
	public function getSubscriptions()
	{
		$db     = Factory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$query = $db->getQuery(true);
		$query->select('topic_id AS thread')
			->from($db->quoteName('#__kunena_user_topics'))
			->where('user_id=' . $userid . ' AND subscribed=1');
		$db->setQuery($query);

		try
		{
			$subslist = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$topic_list = array();

		if (!empty($subslist))
		{
			foreach ($subslist as $sub)
			{
				$topic_list[] = $sub->thread;
			}

			$topic_list = KunenaForumTopicHelper::getTopics($topic_list);
		}

		return $topic_list;
	}

	/**
	 * @return KunenaForumCategory[]
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function getCatsubcriptions()
	{
		$userid = $this->getState($this->getName() . '.id');

		$subscatslist = KunenaForumCategoryHelper::getSubscriptions($userid);

		return $subscatslist;
	}

	/**
	 * @return array|void
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function getIPlist()
	{
		$db     = Factory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$query = $db->getQuery(true);
		$query->select('ip')
			->from($db->quoteName('#__kunena_messages'))
			->where('userid=' . $userid)
			->group('ip');
		$db->setQuery($query);

		try
		{
			$iplist = implode("','", (array) $db->loadColumn());
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$list = array();

		if ($iplist)
		{
			$iplist = "'{$iplist}'";
			$query  = $db->getQuery(true);
			$query->select('m.ip,m.userid,u.username,COUNT(*) as mescnt')
				->from($db->quoteName('#__kunena_messages', 'm'))
				->innerJoin($db->quoteName('#__users', 'u') . ' ON m.userid=u.id')
				->where('m.ip IN (' . $iplist . ')')
				->group('m.userid,m.ip');
			$db->setQuery($query);

			try
			{
				$list = (array) $db->loadObjectlist();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}
		}

		$useripslist = array();

		foreach ($list as $item)
		{
			$useripslist[$item->ip][] = $item;
		}

		return $useripslist;
	}

	/**
	 * @return mixed
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function getListmodcats()
	{
		$user = $this->getUser();

		$modCatList = array_keys(KunenaAccess::getInstance()->getModeratorStatus($user));

		if (empty($modCatList))
		{
			$modCatList[] = 0;
		}

		$categoryList = array();

		if ($this->me->isAdmin())
		{
			$categoryList[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_GLOBAL_MODERATOR'));
		}

		$params  = array(
			'sections' => false,
			'action'   => 'read',);
		$modCats = HTMLHelper::_('kunenaforum.categorylist', 'catid[]', 0, $categoryList, $params, 'class="inputbox form-control" multiple="multiple" size="15"', 'value', 'text', $modCatList, 'kforums');

		return $modCats;
	}

	/**
	 * @return KunenaUser
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function getUser()
	{
		$userid = $this->getState($this->getName() . '.id');

		$user = KunenaUserHelper::get($userid);

		return $user;
	}

	/**
	 * @return array|mixed|void
	 * @since Kunena
	 * @throws Exception
	 */
	public function getListuserranks()
	{
		$db   = Factory::getDBO();
		$user = $this->getUser();

		// Grab all special ranks
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_ranks'))
			->where('rank_special = \'1\'');
		$db->setQuery($query);

		try
		{
			$specialRanks = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$yesnoRank [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_RANK_NO_ASSIGNED'));

		foreach ($specialRanks as $ranks)
		{
			$yesnoRank [] = HTMLHelper::_('select.option', $ranks->rank_id, $ranks->rank_title);
		}

		// Build special ranks select list
		$selectRank = HTMLHelper::_('select.genericlist', $yesnoRank, 'newrank', 'class="inputbox form-control" size="5"', 'value', 'text', $user->rank);

		return $selectRank;
	}

	/**
	 * @return mixed
	 *
	 * @since Kunena
	 */
	public function getMovecatslist()
	{
		return HTMLHelper::_('kunenaforum.categorylist', 'catid', 0, array(), array(), 'class="inputbox form-control"', 'value', 'text');
	}

	/**
	 * @return array|string|void
	 * @since Kunena
	 * @throws Exception
	 */
	public function getMoveuser()
	{
		$db = Factory::getDBO();

		$userids = (array) $this->app->getUserState('kunena.usermove.userids');

		if (!$userids)
		{
			return $userids;
		}

		$userids = implode(',', $userids);
		$query   = $db->getQuery(true);
		$query->select('id,username')
			->from($db->quoteName('#__users'))
			->where('id IN(' . $userids . ')');
		$db->setQuery($query);

		try
		{
			$userids = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		return $userids;
	}

	/**
	 * Method to auto-populate the model state.
	 * @since Kunena
	 * @throws Exception
	 */
	protected function populateState()
	{
		$this->context = 'com_kunena.admin.user';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout        = $app->input->get('layout');
		$this->context = 'com_kunena.admin.user';

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$value = Factory::getApplication()->input->getInt('userid');
		$this->setState($this->getName() . '.id', $value);
	}
}
