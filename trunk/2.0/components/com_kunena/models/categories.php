<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
kimport('kunena.forum.category.helper');

/**
 * Categories Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelCategories extends JModel {
	protected $__state_set = false;
	protected $_items = false;
	protected $_items_order = false;
	protected $_object = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication ();

		$catid = JRequest::getInt ( 'catid', 0 );
		$this->setState ( 'item.id', $catid );
	}

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	Optional default value.
	 * @return	mixed	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null) {
		if (! $this->__state_set) {
			$this->populateState ();
			$this->__state_set = true;
		}
		return parent::getState ( $property );
	}

	public function getItems() {
		if ( $this->_items === false ) {
			$this->me = KunenaFactory::getUser();
			$this->config = KunenaFactory::getConfig();
			$catid = $this->getState ( 'item.id' );

			if ($catid) {
				$this->categories[0] = KunenaForumCategoryHelper::getCategories($catid);
				if (empty($this->categories[0]))
					return;
			} else {
				$this->categories[0] = KunenaForumCategoryHelper::getChildren();
			}

			$allsubcats = KunenaForumCategoryHelper::getChildren(array_keys($this->categories [0]), 1);
		if (empty ( $allsubcats ))
			return;

		if ($this->config->shownew && $this->me->userid) {
			$this->new = KunenaForumCategoryHelper::getNewTopics(array_keys($allsubcats));
		}

		$modcats = array ();
		$topiclist = array ();
		$lastpostlist = array ();
		$userlist = array();

		foreach ( $allsubcats as $subcat ) {
			if (isset ( $this->categories [0] [$subcat->parent_id] )) {

				$last = $subcat->getLastPosted ();
				if ($last->last_topic_id) {
					// collect user ids for avatar prefetch when integrated
					$userlist [(int)$last->last_post_userid] = (int)$last->last_post_userid;
					$topiclist [(int)$last->last_topic_id] = $last->last_topic_subject;
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
			$this->categories [$subcat->parent_id] [] = $subcat;
		}

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
		if (!$topic_ordering && $this->me->userid && $this->me->isModerator()) {
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

		require_once (KUNENA_PATH . DS . 'router.php');
		KunenaRouter::loadMessages ( $topiclist );

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		KunenaUserHelper::loadUsers($userlist);

		$this->_items = $this->categories;
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