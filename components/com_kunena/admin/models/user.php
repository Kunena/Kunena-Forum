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
 * User Model for Kunena
 *
 * @since 3.0
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

	public function getUser()
	{
		$userid = $this->getState($this->getName() . '.id');

		$user = KunenaUserHelper::get($userid);

		return $user;
	}

	public function getSubscriptions()
	{
		$db     = JFactory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("SELECT topic_id AS thread FROM #__kunena_user_topics WHERE user_id='$userid' AND subscribed=1");
		$subslist = (array) $db->loadObjectList();

		if (KunenaError::checkDatabaseError())
		{
			return array();
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

	public function getCatsubcriptions()
	{
		$db     = JFactory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$subscatslist = KunenaForumCategoryHelper::getSubscriptions($userid);

		return $subscatslist;
	}

	public function getIPlist()
	{
		$db     = JFactory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("SELECT ip FROM #__kunena_messages WHERE userid='$userid' GROUP BY ip");
		$iplist = implode("','", (array) $db->loadColumn());

		if (KunenaError::checkDatabaseError())
		{
			return array();
		}

		$list = array();

		if ($iplist)
		{
			$iplist = "'{$iplist}'";
			$db->setQuery("SELECT m.ip,m.userid,u.username,COUNT(*) as mescnt FROM #__kunena_messages AS m INNER JOIN #__users AS u ON m.userid=u.id WHERE m.ip IN ({$iplist}) GROUP BY m.userid,m.ip");
			$list = (array) $db->loadObjectlist();

			if (KunenaError::checkDatabaseError())
			{
				return array();
			}
		}

		$useripslist = array();

		foreach ($list as $item)
		{
			$useripslist[$item->ip][] = $item;
		}

		return $useripslist;
	}

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

	public function getListuserranks()
	{
		$db   = JFactory::getDBO();
		$user = $this->getUser();
		//grab all special ranks
		$db->setQuery("SELECT * FROM #__kunena_ranks WHERE rank_special = '1'");
		$specialRanks = (array) $db->loadObjectList();

		if (KunenaError::checkDatabaseError())
		{
			return array();
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

	public function getMovecatslist()
	{
		return JHtml::_('kunenaforum.categorylist', 'catid', 0, array(), array(), 'class="inputbox"', 'value', 'text');
	}

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
		$userids = (array) $db->loadObjectList();

		if (KunenaError::checkDatabaseError())
		{
			return array();
		}

		return $userids;
	}
}
