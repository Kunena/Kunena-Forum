<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.model' );
kimport ( 'kunena.forum.category.helper' );

require_once KPATH_ADMIN . '/models/categories.php';

/**
 * Categories Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelCategories extends KunenaAdminModelCategories {
	protected $_items = false;

	protected function populateState() {
		$layout = $this->getCmd ( 'layout', 'default' );
		$this->setState ( 'layout', $layout );

		// Administrator state
		if ($layout == 'manage') {
			return parent::populateState();
		}

		// User state
		$catid = $this->getInt ( 'catid', 0 );
		$this->setState ( 'item.id', $catid );
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->getState ( 'item.id' ));
	}

	public function getCategories() {
		if ( $this->_items === false ) {
			$this->_items = array();
			$this->me = KunenaFactory::getUser();
			$this->config = KunenaFactory::getConfig();
			$catid = $this->getState ( 'item.id' );
			$layout = $this->getState ( 'layout' );
			$flat = false;

			if ($layout == 'user') {
				$categories[0] = KunenaForumCategoryHelper::getSubscriptions();
				$flat = true;
			} elseif ($catid) {
				$categories[0] = KunenaForumCategoryHelper::getCategories($catid);
				if (empty($categories[0]))
					return array();
			} else {
				$categories[0] = KunenaForumCategoryHelper::getChildren();
			}

		if ($flat) {
			$allsubcats = $categories[0];
		} else {
			$allsubcats = KunenaForumCategoryHelper::getChildren(array_keys($categories [0]), 1);
		}
		if (empty ( $allsubcats ))
			return array();

		if ($this->config->shownew && $this->me->userid) {
			KunenaForumCategoryHelper::getNewTopics(array_keys($allsubcats));
		}

		$modcats = array ();
		$lastpostlist = array ();
		$userlist = array();
		$topiclist = array();

		foreach ( $allsubcats as $subcat ) {
			if ($flat || isset ( $categories [0] [$subcat->parent_id] )) {

				$last = $subcat->getLastPosted ();
				if ($last->last_topic_id) {
					$topiclist[$last->last_topic_id] = $last->last_topic_id;
					// collect user ids for avatar prefetch when integrated
					$userlist [(int)$last->last_post_userid] = (int)$last->last_post_userid;
					$lastpostlist [(int)$subcat->id] = (int)$last->last_post_id;
					$last->_last_post_location = $last->last_topic_posts;
				}

				if ($this->config->listcat_show_moderators) {
					$subcat->moderators = $subcat->getModerators ( false );
					$userlist += $subcat->moderators;
				}

				if ($this->me->isModerator ( $subcat->id ))
					$modcats [] = $subcat->id;
			}
			$categories [$subcat->parent_id] [] = $subcat;
		}
		KunenaForumTopicHelper::getTopics($topiclist);

		if ($this->me->ordering != '0') {
			$topic_ordering = $this->me->ordering == '1' ? true : false;
		} else {
			$topic_ordering = $this->config->default_sort == 'asc' ? false : true;
		}

		$this->pending = array ();
		if ($this->me->userid && count ( $modcats )) {
			$catlist = implode ( ',', $modcats );
			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT catid, COUNT(*) AS count
				FROM #__kunena_messages
				WHERE catid IN ({$catlist}) AND hold='1'
				GROUP BY catid" );
			$pending = $db->loadAssocList ();
			KunenaError::checkDatabaseError();
			foreach ( $pending as $item ) {
				if ($item ['count'])
					$this->pending [$item ['catid']] = $item ['count'];
			}
		}
		// Fix last post position when user can see unapproved or deleted posts
		if ($lastpostlist && !$topic_ordering && $this->me->userid && $this->me->isModerator()) {
			$access = KunenaFactory::getAccessControl();
			$list = implode ( ',', $lastpostlist );
			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT mm.catid, mm.thread, SUM(mm.hold=1) AS unapproved, SUM(mm.hold IN (2,3)) AS deleted
				FROM #__kunena_messages AS m
				INNER JOIN #__kunena_messages AS mm ON m.thread=mm.thread
				WHERE m.id IN ({$list}) AND mm.hold>0 AND mm.id<m.id
				GROUP BY m.thread" );
			$holdtopics = $db->loadObjectList ();
			KunenaError::checkDatabaseError();
			foreach ( $holdtopics as $topic ) {
				$hold = $access->getAllowedHold($this->me->userid, $topic->catid, false);
				$category = KunenaForumCategoryHelper::get($topic->catid);
				if (isset($hold[1]))
					$category->_last_post_location += $topic->unapproved;
				if (isset($hold[2]) || isset($hold[3]))
					$category->_last_post_location += $topic->deleted;
			}
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		KunenaUserHelper::loadUsers($userlist);

		if ($flat) {
			$this->_items = $allsubcats;
		} else {
			$this->_items = $categories;
		}
		}

		return $this->_items;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is one of htmlspecialchars or htmlentities.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	function escape($var) {
		if (in_array ( $this->_escape, array ('htmlspecialchars', 'htmlentities' ) )) {
			return call_user_func ( $this->_escape, $var, ENT_COMPAT, 'UTF-8' );
		}
		return call_user_func ( $this->_escape, $var );
	}

	/**
	 * Sets the _escape() callback.
	 *
	 * @param mixed $spec The callback for _escape() to use.
	 */
	function setEscape($spec) {
		$this->_escape = $spec;
	}
}