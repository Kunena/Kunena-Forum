<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena User Topics Table
 * Provides access to the #__kunena_user_topics table
 */
class TableKunenaUserTopics extends KunenaTable {
	public $user_id = null;
	public $topic_id = null;
	public $category_id = null;
	public $posts = null;
	public $last_post_id = null;
	public $owner = null;
	public $favorite = null;
	public $subscribed = null;
	public $params = null;

	public function __construct($db) {
		parent::__construct ( '#__kunena_user_topics', array('user_id', 'topic_id'), $db );
	}

	public function check() {
		$user = KunenaUserHelper::get($this->user_id);
		$topic = KunenaForumTopicHelper::get($this->topic_id);
		if (!$user->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_USER_INVALID', (int) $user->userid ) );
		}
		if (!$topic->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_TOPIC_INVALID', (int) $topic->id ) );
		}
		$this->category_id = $topic->category_id;

		return ($this->getError () == '');
	}
}
