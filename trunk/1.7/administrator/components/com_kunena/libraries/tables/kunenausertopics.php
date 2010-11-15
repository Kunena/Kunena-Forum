<?php
/**
 * @version $Id$
 * Kunena Component - TableKunenaUserTopics class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');

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
		kimport('kunena.user');
		kimport('kunena.forum.topic.helper');
		$user = KunenaUser::getInstance($this->user_id);
		$topic = KunenaForumTopicHelper::get($this->topic_id);
		if (!$user->exists()) return false;
		if (!$topic->exists()) return false;
		$this->category_id = $topic->category_id;
		return true;
	}
}