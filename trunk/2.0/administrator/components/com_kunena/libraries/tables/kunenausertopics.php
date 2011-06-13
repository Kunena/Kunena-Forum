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

require_once (dirname ( __FILE__ ) . '/kunena.php');
kimport('kunena.user.helper');
kimport('kunena.forum.topic.helper');

/**
 * Kunena User Topics Table
 * Provides access to the #__kunena_user_topics table
 */
class TableKunenaUserTopics extends KunenaTable
{
	var $user_id = null;
	var $topic_id = null;
	var $category_id = null;
	var $posts = null;
	var $last_post_id = null;
	var $owner = null;
	var $favorite = null;
	var $subscribed = null;
	var $params = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_user_topics', array('user_id', 'topic_id'), $db );
	}

	function check() {
		$user = KunenaUserHelper::get($this->user_id);
		$topic = KunenaForumTopicHelper::get($this->topic_id);
		if (!$user->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_USER_INVALID', (int) $user->userid ) );
		}
		if (!$topic->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_TOPIC_INVALID', (int) $topic->id ) );
		} else {
			$this->category_id = $topic->category_id;
		}
		return ($this->getError () == '');
	}
}