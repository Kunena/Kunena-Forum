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

jimport('joomla.application.component.model');

/**
 * User Model for Kunena
 *
 * @since  3.0
 */
class KunenaAdminModelUser extends KunenaModel
{
	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		$this->context = 'com_kunena.admin.user';

		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout        = $app->input->get('layout');
		$this->context = 'com_kunena.admin.user';

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$value = JFactory::getApplication()->input->getInt('userid');
		$this->setState($this->getName() . '.id', $value);
	}

	/**
	 * @return KunenaUser
	 *
	 * @throws Exception
	 */
	public function getUser()
	{
		$userid = $this->getState($this->getName() . '.id');

		$user = KunenaUserHelper::get($userid);

		return $user;
	}

	/**
	 * @return array|KunenaForumTopic[]
	 *
	 * @throws Exception
	 */
	public function getSubscriptions()
	{
		$db     = JFactory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("SELECT topic_id AS thread FROM #__kunena_user_topics WHERE user_id='$userid' AND subscribed=1");

		try
		{
			$subslist = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());

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
	 */
	public function getCatsubcriptions()
	{
		$userid = $this->getState($this->getName() . '.id');

		$subscatslist = KunenaForumCategoryHelper::getSubscriptions($userid);

		return $subscatslist;
	}

	/**
	 * @return array
	 *
	 * @throws Exception
	 */
	public function getIPlist()
	{
		$db     = JFactory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("SELECT ip FROM #__kunena_messages WHERE userid='$userid' GROUP BY ip");

		try
		{
			$iplist = implode("','", (array) $db->loadColumn());
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());

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
				JFactory::getApplication()->enqueueMessage($e->getMessage());

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
			$categoryList[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_GLOBAL_MODERATOR'));
		}

		$params  = array(
			'sections' => false,
			'action'   => 'read');
		$modCats = JHtml::_('kunenaforum.categorylist', 'catid[]', 0, $categoryList, $params, 'class="inputbox" multiple="multiple" size="15"', 'value', 'text', $modCatList, 'kforums');

		return $modCats;
	}

	/**
	 * @return array|mixed
	 *
	 */
	public function getListuserranks()
	{
		$db   = JFactory::getDBO();
		$user = $this->getUser();
		//grab all special ranks
		$db->setQuery("SELECT * FROM #__kunena_ranks WHERE rank_special = '1'");

		try
		{
			$specialRanks = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$yesnoRank [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_RANK_NO_ASSIGNED'));

		foreach ($specialRanks as $ranks)
		{
			$yesnoRank [] = JHtml::_('select.option', $ranks->rank_id, $ranks->rank_title);
		}

		//build special ranks select list
		$selectRank = JHtml::_('select.genericlist', $yesnoRank, 'newrank', 'class="inputbox" size="5"', 'value', 'text', $user->rank);

		return $selectRank;
	}

	/**
	 * @return mixed
	 *
	 */
	public function getMovecatslist()
	{
		return JHtml::_('kunenaforum.categorylist', 'catid', 0, array(), array(), 'class="inputbox"', 'value', 'text');
	}

	/**
	 * @return array|string
	 *
	 */
	public function getMoveuser()
	{
		$db = JFactory::getDBO();

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
			JFactory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		return $userids;
	}
}
