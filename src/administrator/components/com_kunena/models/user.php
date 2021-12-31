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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function getSubscriptions()
	{
		$db     = Factory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("SELECT topic_id AS thread FROM #__kunena_user_topics WHERE user_id='$userid' AND subscribed=1");

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
	 * @throws Exception
	 * @since Kunena
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
	 * @throws Exception
	 * @since Kunena
	 */
	public function getIPlist()
	{
		$db     = Factory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("SELECT ip FROM #__kunena_messages WHERE userid='$userid' GROUP BY ip");

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
			$db->setQuery("SELECT m.ip,m.userid,u.username,COUNT(*) as mescnt FROM #__kunena_messages AS m INNER JOIN #__users AS u ON m.userid=u.id WHERE m.ip IN ({$iplist}) GROUP BY m.userid,m.ip");

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
	 * @throws Exception
	 * @since Kunena
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
			'action'   => 'read', );
		$modCats = HTMLHelper::_('kunenaforum.categorylist', 'catid[]', 0, $categoryList, $params, 'class="inputbox" multiple="multiple" size="15"', 'value', 'text', $modCatList, 'kforums');

		return $modCats;
	}

	/**
	 * @return KunenaUser
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function getUser()
	{
		$userid = $this->getState($this->getName() . '.id');

		$user = KunenaUserHelper::get($userid);

		return $user;
	}

	/**
	 * @return array|mixed
	 * @throws Exception
	 * @since Kunena
	 */
	public function getListuserranks()
	{
		$db   = Factory::getDBO();
		$user = $this->getUser();

		// Grab all special ranks
		$db->setQuery("SELECT * FROM #__kunena_ranks WHERE rank_special = '1'");

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
		$selectRank = HTMLHelper::_('select.genericlist', $yesnoRank, 'newrank', 'class="inputbox" size="5"', 'value', 'text', $user->rank);

		return $selectRank;
	}

	/**
	 * @return mixed
	 *
	 * @since Kunena
	 */
	public function getMovecatslist()
	{
		return HTMLHelper::_('kunenaforum.categorylist', 'catid', 0, array(), array(), 'class="inputbox"', 'value', 'text');
	}

	/**
	 * @return array|string
	 * @throws Exception
	 * @since Kunena
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
		$db->setQuery("SELECT id,username FROM #__users WHERE id IN(" . $userids . ")");

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
