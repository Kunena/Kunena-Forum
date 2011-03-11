<?php
/**
 * @version $Id: users.php 4387 2011-02-08 16:19:37Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
kimport('kunena.model');
kimport ( 'kunena.error' );
kimport ( 'kunena.html.pagination' );

/**
 * Users Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelUsers extends KunenaModel {
	protected $__state_set = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication ();

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.users.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.users.list.ordering', 'filter_order', 'username', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.users.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.users.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.users.list.search', 'search', '', 'string' );
		$this->setState ( 'list.search', $value );
	}

	public function getUsers() {
		$db = JFactory::getDBO ();

		$order = '';
		if ($this->getState('list.ordering') == 'id') {
			$order = ' ORDER BY u.id '. $this->getState('list.direction');
		} else if ($this->getState('list.ordering') == 'username') {
			$order = ' ORDER BY u.username '. $this->getState('list.direction');
		} else if ($this->getState('list.ordering') == 'name') {
			$order = ' ORDER BY u.name '. $this->getState('list.direction');
		} else if ($this->getState('list.ordering') == 'moderator') {
			$order = ' ORDER BY ku.moderator '. $this->getState('list.direction');
		}

		$where = '';
		if ( $this->getState('list.search') ) {
		  $where = ' WHERE LOWER( u.username ) LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search' ), true ).'%', false ).' OR LOWER( u.email ) LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search' ), true ).'%', false ).' OR LOWER( u.name ) LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search' ), true ).'%', false );

		}

		$db->setQuery ( "SELECT COUNT(*) FROM #__kunena_users AS ku
		INNER JOIN #__users AS u ON ku.userid=u.id {$where}
		");
		$total = $db->loadResult ();
		KunenaError::checkDatabaseError();

		$this->setState ( 'list.total', $total );

		$db->setQuery ( "SELECT u.id, u.username, u.name, ku.moderator
		FROM #__kunena_users AS ku
		INNER JOIN #__users AS u ON ku.userid=u.id {$where}
		{$order}
		", $this->getState ( 'list.start'), $this->getState ( 'list.limit') );

		$users = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		return $users;
	}

	public function getUser() {
		$app = JFactory::getApplication ();
		kimport('kunena.user.helper');

		$userid = $app->getUserState ( 'kunena.user.userid');

		$user = KunenaUserHelper::get($userid);

		return $user;
	}

	public function getSubscriptions() {
		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();
		$userid = $app->getUserState ( 'kunena.user.userid');

		$db->setQuery ( "SELECT topic_id AS thread FROM #__kunena_user_topics WHERE user_id='$userid' AND subscribed=1" );
		$subslist = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		return $subslist;
	}

	public function getCatsubcriptions() {
		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();
		$userid = $app->getUserState ( 'kunena.user.userid');

		$db->setQuery ( "SELECT category_id FROM #__kunena_user_categories WHERE user_id={$userid}" );
		$subscatslist = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		return $subscatslist;
	}

	public function getIPlist() {
		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();
		$userid = $app->getUserState ( 'kunena.user.userid');

		$db->setQuery ( "SELECT ip FROM #__kunena_messages WHERE userid='$userid' GROUP BY ip" );
		$iplist = implode("','", $db->loadResultArray ());
		if (KunenaError::checkDatabaseError()) return;

		$list = array();
		if ($iplist) {
			$iplist = "'{$iplist}'";
			$db->setQuery ( "SELECT m.ip,m.userid,u.username,COUNT(*) as mescnt FROM #__kunena_messages AS m INNER JOIN #__users AS u ON m.userid=u.id WHERE m.ip IN ({$iplist}) GROUP BY m.userid,m.ip" );
			$list = $db->loadObjectlist ();
		if (KunenaError::checkDatabaseError()) return;
		}
		$useripslist = array();
		foreach ($list as $item) {
			$useripslist[$item->ip][] = $item;
		}

		return $useripslist;
	}

	public function getListmodcats() {
		$db = JFactory::getDBO ();
		$user = $this->getUser();

		$db->setQuery ( "SELECT catid FROM #__kunena_moderation WHERE userid='$user->userid'" );
		$modCatList = $db->loadResultArray ();
		if (KunenaError::checkDatabaseError()) return;
		if ($user->moderator && empty($modCatList)) $modCatList[] = 0;

		$categoryList = array(JHTML::_('select.option', 0, JText::_('COM_KUNENA_GLOBAL_MODERATOR')));
		$params = array (
			'sections' => false,
			'action' => 'read');
		$modCats = JHTML::_('kunenaforum.categorylist', 'catid[]', 0, $categoryList, $params, 'class="inputbox" multiple="multiple"', 'value', 'text', $modCatList, 'kforums');

		return $modCats;
	}

	public function getListuserranks() {
		$db = JFactory::getDBO ();
		$user = $this->getUser();
		//grab all special ranks
		$db->setQuery ( "SELECT * FROM #__kunena_ranks WHERE rank_special = '1'" );
		$specialRanks = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		$yesnoRank [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_RANK_NO_ASSIGNED') );
		foreach ( $specialRanks as $ranks ) {
			$yesnoRank [] = JHTML::_ ( 'select.option', $ranks->rank_id, $ranks->rank_title );
		}
		//build special ranks select list
		$selectRank = JHTML::_ ( 'select.genericlist', $yesnoRank, 'newrank', 'class="inputbox" size="5"', 'value', 'text', $user->rank );
		return $selectRank;
	}

	public function getMovecatslist() {
		$db = JFactory::getDBO ();

		$db->setQuery ( "SELECT id,parent_id,name FROM #__kunena_categories" );
		$catsList = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		$category = array();
		foreach ($catsList as $cat) {
			if ($cat->parent_id) {
				$category[] = JHTML::_('select.option', $cat->id, '...'.$cat->name);
			} else {
				$category[] = JHTML::_('select.option', $cat->id, $cat->name);
			}
		}
		$catslist = JHTML::_('select.genericlist', $category, 'cid[]', 'class="inputbox" multiple="multiple" size="5"', 'value', 'text');
		return $catslist;
	}

	public function getMoveuser() {
		kimport('kunena.user.helper');
		$db = JFactory::getDBO ();

		$app = JFactory::getApplication ();
		$userid = $app->getUserState ( 'kunena.usermove.userid');

		$userid = implode(',', $userid);
		$db->setQuery ( "SELECT id,username FROM #__users WHERE id IN(".$userid.")" );
		$userids = $db->loadObjectList ();

		return $userids;
	}

	public function getAdminNavigation() {
		$navigation = new KunenaHtmlPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
