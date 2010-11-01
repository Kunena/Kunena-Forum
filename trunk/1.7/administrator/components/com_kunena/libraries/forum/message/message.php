<?php
/**
 * @version $Id: message.php 3759 2010-10-20 13:48:28Z mahagr $
 * Kunena Component - KunenaForumMessage Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.forum.category.helper');
kimport ('kunena.forum.topic.helper');
kimport ('kunena.forum.message.helper');

/**
 * Kunena Forum Message Class
 */
class KunenaForumMessage extends JObject {
	protected $_exists = false;
	protected $_db = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier = 0) {
		// Always load the message -- if message does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		$this->load ( $identifier );
	}

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access	public
	 * @param	identifier		The message to load - Can be only an integer.
	 * @return	KunenaForumMessage		The message object.
	 * @since	1.7
	 */
	static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumMessageHelper::get($identifier, $reload);
	}

	public function exists() {
		return $this->_exists;
	}

	public function newReply($quote=false, $user=null) {
		$user = KunenaUser::getInstance($user);
		$topic = $this->getTopic();
		$message = new KunenaForumMessage();
		$message->parent = $this->id;
		$message->thread = $this->thread;
		$message->catid = $topic->category_id;
		$message->name = $user->getName('');
		$message->userid = $user->userid;
		$message->subject = $this->subject;
		if ($quote) {
			$text = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', $this->message );
			$message->message = "[quote=\"{$this->name}\" post={$this->id}]" .  $text . "[/quote]";
		}
		return array($topic, $message);
	}

	public function publish($value=KunenaForum::PUBLISHED) {
		if ($this->hold == $value)
			return true;

		$this->hold = (int)$value;
		$result = $this->save();
		$this->getTopic()->update($this, $this->hold ? -1 : 1);
		return $result;
	}

	public function getTopic() {
		return KunenaForumTopicHelper::get($this->thread);
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->catid);
	}

	public function authorise($action='read', $user=null) {
		static $actions  = array(
			'read'=>array('Read'),
			'reply'=>array('Read','NotHold'),
			'edit'=>array('Read','Own','EditTime'),
			'move'=>array('Read'),
			'approve'=>array('Read'),
			'delete'=>array('Read','Own','EditTime'),
			'undelete'=>array('Read'),
			'permdelete'=>array('Read'),
		);
		$user = KunenaUser::getInstance($user);
		if (!isset($actions[$action])) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		$category = $this->getCategory();
		$auth = $category->authorise('topic.'.$action, $user);
		if (!$auth) {
			$this->setError ( $category->getError() );
			return false;
		}
		foreach ($actions[$action] as $function) {
			$authFunction = 'authorise'.$function;
			if (! method_exists($this, $authFunction) || ! $this->$authFunction($user)) {
				$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to get the messages table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The messages table name to be used
	 * @param	string	The messages table prefix to be used
	 * @return	object	The messages table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaMessages', $prefix = 'Table') {
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	public function bind($data, $ignore = array()) {
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaForumMessage object by id
	 *
	 * @access	public
	 * @param	mixed	$id The message id to be loaded
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the table object
		$table = &$this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumMessage object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new message
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		// Create the messages table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new message
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new message return
		if ($isnew && $updateOnly) {
			return true;
		}

		//Store the message data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Set the id for the KunenaForumMessage object in case we created a new message.
		if ($result && $isnew) {
			$this->load ( $table->get ( 'id' ) );
			self::$_instances [$table->get ( 'id' )] = $this;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaForumMessage object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		// Create the table object
		$table = &$this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
			return false;
		}
		$this->_exists = false;

		$db = JFactory::getDBO ();
		// Delete attacments
		require_once KPATH_SITE.'/lib/kunean.attachments.class.php';
		CKunenaAttachments::deleteMessage($this->id);
		// Delete thank yous
		$queries[] = "DELETE FROM #__kunena_thankyou WHERE postid={$db->quote($this->id)}";
		// Delete message
		$queries[] = "DELETE FROM #__kunena_messages_text WHERE mesid={$db->quote($this->id)}";

		if ($this->hold == 0) {
			$queries[] = "UPDATE #__kunena_users SET posts=posts-1 WHERE userid={$db->quote($this->userid)}";
			$queries[] = "UPDATE #__kunena_user_topics SET posts=posts-1 WHERE user_id={$db->quote($this->userid)} AND topic_id={$db->quote($this->thread)}";
			$this->getTopic()->update($this, -1);
			// TODO: event missing
		}

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		return $result;
	}

	// Internal functions

	protected function authoriseRead($user) {
		// Check that user has the right to see the post (user can see his own unapproved posts)
		if ($this->hold > 1 || ($this->hold == 1 && $this->userid != $user->userid)) {
			$access = KunenaFactory::getAccessControl();
			$hold = $access->getAllowedHold($user->userid, $this->catid, false);
			if (!in_array($this->hold, $hold)) {
				$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
	}
	protected function authoriseNotHold($user) {
		if ($this->hold) {
			// Nobody can reply to unapproved or deleted post
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}
	protected function authoriseOwn($user) {
		// Check that topic owned by the user or user is a moderator
		// TODO: check #__kunena_user_topics
		if ((!$this->userid || $this->userid != $user->userid) && !$user->isModerator($this->catid)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
			return false;
		}
		return true;
	}
	protected function authoriseEditTime($user) {
		// Do not perform rest of the checks to moderators and admins
		if ($user->isModerator($this->catid)) {
			return true;
		}
		// User is only allowed to edit post within time specified in the configuration
		if (! CKunenaTools::editTimeCheck ( $this->modified_time, $this->time )) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
			return false;
		}
		return true;
	}
}