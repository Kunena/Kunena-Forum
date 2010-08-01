<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

class CkunenaPathway {
	protected $_db;
	public $config;
	public $document;
	public $kunena_topic_title = null;
	public $show_online_users = null;

	function __construct($show_online_users = '') {
		$this->_db = JFactory::getDBO ();
		$this->config = CKunenaConfig::getInstance ();
		$this->document = & JFactory::getDocument ();
		$this->func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
		$this->catid = JRequest::getInt ( 'catid', 0 );
		$this->id = JRequest::getInt ( 'id', 0 );
		$this->kunena_topic_title = $this->getTopicTitle();
		$this->title_name = JText::_('COM_KUNENA_CATEGORIES');
		$this->_setTitle();
		$this->show_online_users=$show_online_users;
	}

	private function _setTitle() {
		$this->document->setTitle ( $this->kunena_topic_title ? $this->kunena_topic_title : $this->title_name . ' - ' . $this->escape($this->config->board_title) );
	}

	private function _getOnlineUsers($sfunc = '') {
		static $users = null;

		if ($users === null) {
			$queryName = $this->config->username ? "username" : "name";
			$query = "SELECT w.userid, w.func, u.$queryName AS username, k.showOnline FROM #__kunena_whoisonline AS w
				LEFT JOIN #__users AS u ON u.id=w.userid
				LEFT JOIN #__kunena_users AS k ON k.userid=w.userid
				WHERE w.link LIKE '%" . $this->_db->getEscaped ( JURI::current () ) . "%' AND w.func LIKE '%$sfunc%'
				GROUP BY w.userid ORDER BY u.{$queryName} ASC";
			$this->_db->setQuery ( $query );
			$users = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError ();
		}

		return $users;
	}

	public function getMessagesTitles($id) {
		$sql = "SELECT subject, id FROM #__kunena_messages WHERE id={$this->_db->Quote($id)}";
		$this->_db->setQuery ( $sql );
		$kunena_topic_title = $this->_db->loadResult ();
		KunenaError::checkDatabaseError();

		return $kunena_topic_title;
	}

	public function getCatsDetails($catids) {
		$query = "SELECT * FROM #__kunena_categories WHERE id={$this->_db->Quote($catids)} AND published='1'";
		$this->_db->setQuery ( $query );
		$results = $this->_db->loadObject ();
		KunenaError::checkDatabaseError();

		return $results;
	}

	public function getTotalViewing($sfunc) {
		$users = $this->_getOnlineUsers($sfunc);

		return $total_viewing = count ( $users );
	}

	public function getUsersOnlineList($sfunc) {
		$users = $this->_getOnlineUsers($sfunc);

		$onlineUsersList = '';
		$totalguest = 0;
		$divider = ', ';
		$lastone = end ( $users );
		foreach ( $users as $user ) {
			if ($user->userid != 0) {
				if ($user == $lastone && ! $totalguest) {
					$divider = '';
				}
				if ($user->showOnline > 0) {
					$onlineUsersList = CKunenaLink::GetProfileLink ( $user->userid, $user->username ) . $divider;
				}
			} else {
				$totalguest = $totalguest + 1;
			}
		}
		if ($totalguest > 0) {
			if ($totalguest == 1) {
				$onlineUsersList .= '(' . $totalguest . ') ' . JText::_('COM_KUNENA_WHO_ONLINE_GUEST');
			} else {
				$onlineUsersList .= '(' . $totalguest . ') ' . JText::_('COM_KUNENA_WHO_ONLINE_GUESTS');
			}
		}

		return $onlineUsersList;
	}

	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	function getCategoriesPath() {
		$path_menu = array ();

		while ( $this->catid > 0 ) {
			$results = $this->getCatsDetails($this->catid);
			if (! $results)
			break;

			$parent_id = $results->parent;
			$catname = $this->escape( JString::trim ( $results->name ) );
			$cat_link = CKunenaLink::GetCategoryLink ( 'showcat', $this->catid, $catname );

			if ($this->catid && $this->func != "view") {
				$this->title_name = $catname;
				$path_menu [] = $cat_link;
			} else {
				$path_menu [] = $cat_link;
			}

			// next looping
			$this->catid = $parent_id;
		}

		//reverse the array
		$path_menu = array_reverse ( $path_menu );
		return $path_menu;
	}

	function getTopicTitle() {
		if ($this->func == "view" and $this->id) {
			$this->kunena_topic_title = $this->getMessagesTitles($this->id);
			$this->kunena_topic_title = $this->escape($this->kunena_topic_title);
		}
		return $this->kunena_topic_title;
	}

	public function getPathMenu() {
		$path_menu = array ();
		$path_menu = $this->getCategoriesPath();
		$path_menu[] = $this->getTopicTitle();

		// count items in path
		if (count ( $path_menu ) == 0)
			$path_menu [] = '';

		return $path_menu;
	}

	public function getPathway() {
		$path_menu = $this->getPathMenu();

		$forum_path = '<div class="path-element-first">' . CKunenaLink::GetKunenaLink ( $this->escape( $this->config->board_title ) ) . '</div>';

		$last_item = '';
		for($i = 0; $i < count ( $path_menu ); $i ++) {
			if ($i == count ( $path_menu ) - 1) {
				if ( ($this->config->onlineusers && $this->show_online_users) || $this->show_online_users ) :
					$last_item .= '<br /><div class="path-element-last">' . $path_menu [$i] . '</div>';
				endif;
			} else {
				$forum_path .= '<div class="path-element">' . $path_menu [$i] . '</div>';
			}
		}

		//get viewing
		$users_online = '';
		if ( ($this->config->onlineusers && $this->show_online_users) || $this->show_online_users ) {
			if ($this->func == "userprofile") {
				$users_online .= JText::_('COM_KUNENA_USER_PROFILE');
				$users_online .= $this->escape($this->kunena_username);
			} else {
				$users_online .= "<div class=\"path-element-users\">(".$this->getTotalViewing($this->func). ' ' . JText::_('COM_KUNENA_PATHWAY_VIEWING') . ")&nbsp;";
				$users_online .= $this->getUsersOnlineList($this->func);
			}
			$users_online .= '</div>';
		}

		return $forum_path . $last_item . $users_online ;
	}

	public function display() {
		if ( $this->show_online_users ) {
			CKunenaTools::loadTemplate('/pathway.php');
		} else {
			echo $this->getPathway();
		}
	}
}