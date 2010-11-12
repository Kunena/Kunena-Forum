<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaPosting {
	var $parent = null;
	private $message = null;
	private $options = null;
	private $errors = array ();

	function __construct($userid = null) {
		$this->_config = KunenaFactory::getConfig ();
		$this->_session = KunenaFactory::getSession ();
		$this->_db = JFactory::getDBO ();
		$this->_my = JFactory::getUser ($userid);
		$this->_myuser = KunenaFactory::getUser ($userid);
		$this->_app = JFactory::getApplication ();

		$this->setError ( '-load-', JText::_ ( 'COM_KUNENA_POSTING_NOT_LOADED' ) );
	}

	protected function checkDatabaseError() {
		kimport('error');
		return KunenaError::checkDatabaseError();
	}

	public function getErrors() {
		if (empty ( $this->errors ))
			return false;
		return $this->errors;
	}

	protected function setError($field, $message) {
		if (empty ( $message ))
			return true;
		$this->errors [$field] = $message;
		return false;
	}

	protected function loadMessage($mesid) {
		$this->errors = array ();
		$this->options = null;
		if ($mesid) {
			// Check that message and category exists and fetch some information for later use
			$query = "SELECT m.*, mm.hold AS topichold, mm.locked AS topiclocked, c.locked AS catlocked, t.message,
					c.name AS catname, c.parent AS catparent, c.pub_access,
					c.review, c.class_sfx, p.id AS poll_id, c.allow_anonymous,
					c.post_anonymous, c.allow_polls
				FROM #__kunena_messages AS m
				INNER JOIN #__kunena_categories AS c ON c.id=m.catid AND c.published
				INNER JOIN #__kunena_messages AS mm ON mm.id=m.thread AND mm.moved=0
				INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
				LEFT JOIN #__kunena_polls AS p ON m.id=p.threadid
				WHERE m.id={$this->_db->quote($mesid)} AND m.moved=0";

			$this->_db->setQuery ( $query, 0, 1 );
			$this->parent = $this->_db->loadObject ();
			$this->checkDatabaseError ();
		}
		if (! $this->parent) {
			return $this->setError ( '-load-', JText::_ ( 'COM_KUNENA_POST_INVALID' ) );
		}
		return true;
	}

	protected function loadCategory($catid) {
		$this->errors = array ();
		$this->options = null;
		if ($catid) {
			// Check that category exists and fill some information for later use
			$query = "SELECT m.*, 0 AS topichold, 0 AS topiclocked, c.locked AS catlocked, '' AS message,
					c.id AS catid, c.name AS catname, c.parent AS catparent, c.pub_access,
					c.review, c.class_sfx, 0 AS poll_id, c.allow_anonymous,
					c.post_anonymous, c.allow_polls
				FROM #__kunena_categories AS c
				LEFT JOIN #__kunena_messages AS m ON m.id=0
				WHERE c.id={$this->_db->quote($catid)} AND c.published";
			$this->_db->setQuery ( $query, 0, 1 );
			$this->parent = $this->_db->loadObject ();
			$this->checkDatabaseError ();
		}
		if (! $this->parent) {
			return $this->setError ( '-load-', JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
		}
		return true;
	}

	public function canRead($action = '-read-') {
		// Load must have been performed successfully!
		if (! $this->parent) {
			return false; // Error has already been set, either in construct() or load()
		}
		// Do not perform rest of the checks to administrators
		if (CKunenaTools::isAdmin ()) {
			return true; // ACCEPT!
		}
		// Category must be visible
		if (! $this->_session->canRead ( $this->parent->catid )) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
		}
		// Check unapproved, deleted etc messages
		$access = KunenaFactory::getAccessControl();
		$hold = $access->getAllowedHold($this->_my->id, $this->parent->catid, false);
		if ($this->parent->hold == 1 && $this->_my->id == $this->parent->userid) {
				// User can see his own post before it gets approved
		} else if (!in_array($this->parent->hold, $hold) || !in_array($this->parent->topichold, $hold)) {
			// User is not allowed to see this post
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_INVALID' ) );
		}
		return true;
	}

	function canPost($action = '-post-') {
		// Visitors are allowed to post only if public writing is enabled
		if (($this->_my->id == 0 && ! $this->_config->pubwrite)) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' ) );
		}
		// User must see category or topic in order to be able post into it
		if (! $this->canRead ($action)) {
			return false; // Error has already been set
		}
		// Posts cannot be in sections
		if (! $this->parent->catparent) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_IS_SECTION' ) );
		}
		// Do not perform rest of the checks to moderators and admins
		if (CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			return true; // ACCEPT!
		}
		// Posts cannot be written to locked topics
		if ($this->parent->topiclocked) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ) );
		}
		// Posts cannot be written to locked categories
		if ($this->parent->catlocked) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ) );
		}

		return true;
	}

	function canReply($action = '-reply-') {
		return $this->canPost ($action);
	}

	function canEdit($action = '-edit-') {
		// Visitors cannot edit posts
		if (! $this->_my->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' ) );
		}
		// User must see topic in order to edit messages in it
		if (! $this->canRead ($action)) {
			return false;
		}
		// Categories cannot be edited - verify that post exists
		if (! $this->parent->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_INVALID' ) );
		}
		// Do not perform rest of the checks to moderators and admins
		if (CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			return true; // ACCEPT!
		}
		// User must be author of the message
		if ($this->parent->userid != $this->_my->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
		}
		// User is only allowed to edit post within time specified in the configuration
		if (! CKunenaTools::editTimeCheck ( $this->parent->modified_time, $this->parent->time )) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
		}
		// Posts cannot be edited in locked topics
		if ($this->parent->topiclocked) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ) );
		}
		// Posts cannot be edited in locked categories
		if ($this->parent->catlocked) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ) );
		}

		return true;
	}

	function canDelete($action = '-delete-') {
		// Visitors cannot delete posts
		if (! $this->_my->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' ) );
		}
		// User must see topic in order to delete messages in it
		if (! $this->canRead ($action)) {
			return false;
		}
		// Categories cannot be deleted - verify that post exists
		if (! $this->parent->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_INVALID' ) );
		}
		// Do not perform rest of the checks to moderators and admins
		if (CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			return true; // ACCEPT!
		}
		// User must be author of the message
		if ($this->parent->userid != $this->_my->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_DELETE_NOT_ALLOWED' ) );
		}
		// User is only allowed to delete post within time specified in the configuration
		if (! CKunenaTools::editTimeCheck ( $this->parent->modified_time, $this->parent->time )) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_DELETE_NOT_ALLOWED' ) );
		}
		// Posts cannot be deleted in locked topics
		if ($this->parent->topiclocked) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ) );
		}
		// Posts cannot be deleted in locked categories
		if ($this->parent->catlocked) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ) );
		}
		// Post cannot be marked as deleted
		if ($this->parent->hold >= 1) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_ALREADY_DELETED' ) );
		}

		return true;
	}

	function canApprove($action = '-approve-') {
		// User must be moderator in order to approve message
		if (!CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
		}
		// User must see topic in order to approve messages in it
		if (! $this->canRead ($action)) {
			return false;
		}
		// Categories cannot be approved - verify that post exists
		if (! $this->parent->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_INVALID' ) );
		}
		// Post must be on hold
		if ($this->parent->hold != 1) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_NOT_UNAPPROVED' ) );
		}
		return true;
	}

	function canUndelete($action = '-undelete-') {
		// User must be moderator in order to restore message
		if (!CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
		}
		// User must see topic in order to restore messages in it
		if (! $this->canRead ($action)) {
			return false;
		}
		// Categories cannot be restored - verify that post exists
		if (! $this->parent->id) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_INVALID' ) );
		}
		// Post must be marked as deleted
		if ($this->parent->hold != 2 && $this->parent->hold != 3) {
			return $this->setError ( $action, JText::_ ( 'COM_KUNENA_POST_NOT_DELETED' ) );
		}
		// TODO: check that topic is not on hold, otherwise we get orphan message
		return true;
	}

	public function post($catid, $fields = array(), $options = array()) {
		if (! $this->parent || $this->parent->catid != $catid || $this->parent->id) {
			$this->loadCategory ( $catid );
		}
		if (! $this->canPost ())
			return false;

		// Load all options and fields
		$this->loadOptions ( $options );
		$this->setOption ( 'action', 'post' );
		$this->setOption ( 'allowed', array ('name', 'email', 'time', 'subject', 'message', 'topic_emoticon' ) );
		$this->setOption ( 'required', array ('subject', 'message' ) );

		$this->loadFields ( $fields );

		return empty ( $this->errors );
	}

	public function reply($mesid, $fields = array(), $options = array()) {
		if (! $this->parent || $this->parent->id != $mesid) {
			$this->loadMessage ( $mesid );
		}
		if (! $this->canReply ())
			return false;

		// Load all options and fields
		$this->loadOptions ( $options );
		$this->setOption ( 'action', 'reply' );
		$this->setOption ( 'allowed', array ('name', 'email', 'subject', 'message', 'topic_emoticon' ) );
		$this->setOption ( 'required', array ('message' ) );

		$this->loadFields ( $fields );
		// Load these default values from the previous message
		$this->loadDefaults ( array ('subject' ) );

		return empty ( $this->errors );
	}

	public function edit($mesid, $fields = array(), $options = array()) {
		if (! $this->parent || $this->parent->id != $mesid) {
			$this->loadMessage ( $mesid );
		}
		if (! $this->canEdit ())
			return false;

		// Load all options and fields
		$this->loadOptions ( $options );
		$this->setOption ( 'action', 'edit' );

		// Restrict fields that user can enter
		$this->setOption ( 'allowed', array ('name', 'email', 'subject', 'message', 'topic_emoticon', 'modified_reason' ) );
		$this->setOption ( 'required', array () );

		$this->loadFields ( $fields );
		// Load these default values from the edited message
		$this->loadDefaults ( array ('name', 'email', 'subject', 'message', 'topic_emoticon' ) );

		return empty ( $this->errors );
	}

	public function action($mesid) {
		if (! $this->parent || $this->parent->id != $mesid) {
			$this->loadMessage ( $mesid );
		}
		if (! $this->canRead ())
			return false;

		// Load all options and fields
		$this->setOption ( 'action', 'action' );

		// Restrict fields that user can enter
		$this->setOption ( 'allowed', array ('hold' ) );
		$this->setOption ( 'required', array () );

		// Load these default values from the message
		$this->loadDefaults ( array ('id', 'catid', 'parent', 'thread', 'userid', 'name', 'email', 'subject', 'message', 'topic_emoticon', 'time' ) );

		return empty ( $this->errors );
	}

	protected function savePost() {
		if (! $this->check ())
			return false;

		// Update rest of the information
		$this->setOption ( 'allowed', null );

		// Fill user related information
		if ($this->getOption ( 'anonymous' )) {
			// Anonymous post: Just in case remove userid, email and IP address - name is already anonymous
			$this->set ( 'ip', '' );
			$this->set ( 'userid', 0 );
			$this->set ( 'email', '' );
		} else {
			// Regular post: Fill missing fields
			if (! $this->get ( 'ip' ))
				$this->set ( 'ip', $_SERVER ["REMOTE_ADDR"] );
			if (! $this->get ( 'userid' ))
				$this->set ( 'userid', $this->_my->id );
			if (! $this->get ( 'name' ))
				$this->set ( 'name', $this->_myuser->getName() );
			if (! $this->get ( 'email' ))
				$this->set ( 'email', $this->_my->email );
			if (! $this->get ( 'time' ))
				$this->set ( 'time', CKunenaTimeformat::internalTime () );
		}

		// Fill thread/post related information
		$this->set ( 'parent', $this->parent->id );
		$this->set ( 'thread', $this->parent->thread );
		$this->set ( 'catid', $this->parent->catid );

		// On reviewed forum, require approval if user is not a moderator
		$this->set ( 'hold', CKunenaTools::isModerator ( $this->_my, $this->parent->catid ) ? 0 : ( int ) $this->parent->review );

		if (! empty ( $this->errors ))
			return false;

		if ($this->isAlreadyPosted ()) {
			return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_DUPLICATE_IGNORED' ) );
		}

		$meskeys = array ();
		$mesvalues = array ();
		$txtkeys = array ('mesid' );
		$txtvalues = array (0 );
		foreach ( $this->message as $field => $value ) {
			if ($field != 'message') {
				$meskeys [] = $this->_db->nameQuote ( $field );
				$mesvalues [] = $this->_db->quote ( $value );
			} else {
				$txtkeys [] = $this->_db->nameQuote ( $field );
				$txtvalues [] = $this->_db->quote ( $value );
			}
		}
		if (empty ( $mesvalues ) || count ( $txtvalues ) < 2) {
			return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );
		}

		$meskeys = implode ( ', ', $meskeys );
		$mesvalues = implode ( ', ', $mesvalues );
		$query = "INSERT INTO #__kunena_messages ({$meskeys}) VALUES({$mesvalues})";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		$dberror = $this->checkDatabaseError ();
		if ($dberror)
			return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );

		$id = ( int ) $this->_db->insertId ();
		$txtvalues [0] = $this->_db->quote ( $id );

		$txtkeys = implode ( ', ', $txtkeys );
		$txtvalues = implode ( ', ', $txtvalues );
		$query = "INSERT INTO #__kunena_messages_text ({$txtkeys}) VALUES({$txtvalues})";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		$dberror = $this->checkDatabaseError ();
		if ($dberror) {
			// Delete partial message on error
			$query = "DELETE FROM #__kunena_messages WHERE mesid={$this->_db->quote($id)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );
		}

		$this->set ( 'id', $id );
		if ($this->parent->thread == 0) {
			// For new thread, we now know to where the message belongs
			$this->set ( 'thread', $id );
			$query = "UPDATE #__kunena_messages SET thread={$this->_db->quote($id)} WHERE id={$this->_db->quote($id)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			$dberror = $this->checkDatabaseError ();
			if ($dberror)
				return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );
		}

		//update the user posts count
		$userid = ( int ) $this->get ( 'userid' );
		if ($userid) {
			$query = "UPDATE #__kunena_users SET posts=posts+1 WHERE userid={$this->_db->quote($userid)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();

		}

		// now increase the #s in categories only case approved
		if (! $this->get ( 'hold' )) {
			CKunenaTools::modifyCategoryStats ( $id, $this->get ( 'parent' ), $this->get ( 'time' ), $this->get ( 'catid' ) );
		}

		// Add attachments if there are any
		// TODO: find better way
		if ($this->getOption ( 'attachments' )) {
			require_once (KUNENA_PATH_LIB . DS . 'kunena.attachments.class.php');
			$attachments = CKunenaAttachments::getInstance ();
			$message = $this->get ( 'message' );
			$fileinfos = $attachments->multiupload ( $id, $message );
			foreach ( $fileinfos as $fileinfo ) {
				if (! $fileinfo ['status'])
					$this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo ['name'] ) . ': ' . $fileinfo ['error'], 'error' );
			}
			$this->_db->setQuery ( "UPDATE #__kunena_messages_text SET message={$this->_db->quote($message)} WHERE mesid={$this->_db->Quote($id)}" );
			$this->_db->query ();
		}

		// Mark topic read for me
		CKunenaTools::markTopicRead ( $this->get ( 'thread'), $this->_my->id );

		// Mark topic unread for others

		// First take care of old sessions to make our job easier and faster
		$lasttime = $this->get ( 'time' ) - max(intval(JFactory::getConfig()->getValue( 'config.lifetime' ))*60, intval(KunenaFactory::getConfig ()->fbsessiontimeout)) - 60;
		$query = "UPDATE #__kunena_sessions SET readtopics='0' WHERE currvisit<{$this->_db->quote($lasttime)}";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		$dberror = $this->checkDatabaseError ();
		if ($dberror)
			return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_SESSIONS' ) );

		// Then look at users who have read the thread
		$thread = $this->get ( 'thread' );
		$query = "SELECT userid, readtopics FROM #__kunena_sessions WHERE readtopics LIKE '%{$thread}%' AND userid!={$this->_db->quote($userid)}";
		$this->_db->setQuery ( $query );
		$sessions = $this->_db->loadObjectList ();
		$dberror = $this->checkDatabaseError ();
		if ($dberror)
			return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_SESSIONS' ) );

		// And clear current thread
		$errcount = 0;
		foreach ( $sessions as $session ) {
			$readtopics = $session->readtopics;
			$rt = explode ( ",", $readtopics );
			$key = array_search ( $thread, $rt );
			if ($key !== false) {
				unset ( $rt [$key] );
				$readtopics = implode ( ",", $rt );
				$query = "UPDATE #__kunena_sessions SET readtopics={$this->_db->quote($readtopics)} WHERE userid={$this->_db->quote($session->userid)}";
				$this->_db->setQuery ( $query );
				$this->_db->query ();
				$dberror = $this->checkDatabaseError ();
				if ($dberror)
					$errcount ++;
			}
		}
		if ($errcount)
			return $this->setError ( '-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_SESSIONS' ) );

		// Get the event dispatcher
		$dispatcher	= JDispatcher::getInstance();

		// Load the JXFinder plug-in group
		JPluginHelper::importPlugin('finder');

		// Fire after post save event
		$dispatcher->trigger('onAfterSaveKunenaPost', array($id));

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		if ($this->parent->thread == 0) {
			$activity->onAfterPost($this);
		} else {
			$activity->onAfterReply($this);
		}

		return $id;
	}

	protected function saveEdit() {
		if (! $this->check ())
			return false;

		// Update rest of the information
		$this->setOption ( 'allowed', null );
		$this->set ( 'hold', CKunenaTools::isModerator ( $this->_my, $this->parent->catid ) ? 0 : ( int ) $this->parent->review );
		$this->set ( 'modified_by', ( int ) $this->_my->id );
		$this->set ( 'modified_time', CKunenaTimeformat::internalTime () );

		$anonymous = $this->getOption ( 'anonymous' );
		if ($anonymous) {
			if ($this->_my->id == $this->parent->userid && $this->parent->modified_by == $this->parent->userid) {
				// I am the author and previous modification was made by me => delete modification information to hide my personality
				$this->set ( 'modified_by', 0 );
				$this->set ( 'modified_time', 0 );
				$this->set ( 'modified_reason', '' );
			} else if ($this->_my->id == $this->parent->userid) {
				// I am the author, but somebody else has modified the message => leave modification information intact
				$this->set ( 'modified_by', null );
				$this->set ( 'modified_time', null );
				$this->set ( 'modified_reason', null );
			}
			// Remove userid, email and ip address
			$this->set ( 'userid', 0 );
			$this->set ( 'ip', '' );
			$this->set ( 'email', '' );
		}

		if (! empty ( $this->errors ))
			return false;

		$mesvalues = array ();
		$txtvalues = array ();
		foreach ( $this->message as $field => $value ) {
			if ($field != 'message') {
				$mesvalues [] = "{$this->_db->nameQuote($field)}={$this->_db->quote($value)}";
			} else {
				$txtvalues [] = "{$this->_db->nameQuote($field)}={$this->_db->quote($value)}";
			}
		}
		if (! empty ( $mesvalues )) {
			$mesvalues = implode ( ', ', $mesvalues );
			$query = "UPDATE #__kunena_messages SET {$mesvalues} WHERE id={$this->_db->quote($this->parent->id)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			$dberror = $this->checkDatabaseError ();
			if ($dberror)
				return $this->setError ( '-edit-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );
		}
		if (! empty ( $txtvalues )) {
			$txtvalues = implode ( ', ', $txtvalues );
			$query = "UPDATE #__kunena_messages_text SET {$txtvalues} WHERE mesid={$this->_db->quote($this->parent->id)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			$dberror = $this->checkDatabaseError ();
			if ($dberror)
				return $this->setError ( '-edit-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );
		}
		$this->set ( 'id', $id = $this->parent->id );

		// Add/delete attachments if there are any changes
		// TODO: find better way
		if ($this->getOption ( 'attachments' )) {
			require_once (KUNENA_PATH_LIB . DS . 'kunena.attachments.class.php');
			$attachments = CKunenaAttachments::getInstance ();
			$message = $this->get ( 'message' );

			// Delete attachments which weren't checked (= not listed in here)
			jimport ( 'joomla.utilities.arrayhelper' );
			$attachkeeplist = JRequest::getVar ( 'attach-id', array (0 ), 'post', 'array' );
			JArrayHelper::toInteger ( $attachkeeplist, array (0 ) );
			$attachkeeplist = implode ( ',', $attachkeeplist );
			$query = "SELECT id FROM #__kunena_attachments WHERE mesid={$this->_db->Quote($id)} AND id NOT IN ({$attachkeeplist})";
			$this->_db->setQuery ( $query );
			$attachmentlist = $this->_db->loadResultArray ();
			if (! KunenaError::checkDatabaseError ()) {
				$attachments->deleteAttachment ( $attachmentlist );
			}

			$fileinfos = $attachments->multiupload ( $id, $message );
			foreach ( $fileinfos as $fileinfo ) {
				if (! $fileinfo ['status'])
					$this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo ['name'] ) . ': ' . $fileinfo ['error'], 'error' );
			}
			$this->_db->setQuery ( "UPDATE #__kunena_messages_text SET message={$this->_db->quote($message)} WHERE mesid={$this->_db->Quote($id)}" );
			$this->_db->query ();
		}

		// Get the event dispatcher
		$dispatcher	= JDispatcher::getInstance();

		// Load the JXFinder plug-in group
		JPluginHelper::importPlugin('finder');

		// Fire after post save event
		$dispatcher->trigger('onAfterSaveKunenaPost', array($id));

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onAfterEdit($this);

		return $id;
	}

	public function delete($mesid) {
		if (! $this->parent || $this->parent->id != $mesid) {
			$this->loadMessage ( $mesid );
		}
		if (! $this->canDelete ())
			return false;

		if (!CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			//need to check that the message is the last of the thread
			$this->_db->setQuery ( "SELECT id FROM #__kunena_messages WHERE `hold`='0' AND `thread`={$this->_db->quote($this->parent->thread)} ORDER BY id DESC LIMIT 0, 1" );
			$lastMessage = $this->_db->loadResult ();
			$dberror = $this->checkDatabaseError ();
			if ($dberror)
				return $this->setError ( '-delete-', JText::_ ( 'COM_KUNENA_POST_ERROR_DELETE' ) );

			if ($this->parent->id != $lastMessage) {
				return $this->setError ( '-delete-', JText::_ ( 'COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER' ) );
			}
		}

		// Execute delete
		$query = "UPDATE #__kunena_messages SET `hold`=2 WHERE `id`={$this->_db->quote($this->parent->id)};";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		$dberror = $this->checkDatabaseError ();
		if ($dberror)
			return $this->setError ( '-delete-', JText::_ ( 'COM_KUNENA_POST_ERROR_DELETE' ) );

		// Last but not least update forum stats
		CKunenaTools::reCountBoards();

		$this->set ( 'id', $this->parent->id );

		// Get the event dispatcher
		$dispatcher	= JDispatcher::getInstance();

		// Load the JXFinder plug-in group
		JPluginHelper::importPlugin('finder');

		// Fire after post save event
		$dispatcher->trigger('onDeleteKunenaPost', array(array($mesid)));

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onAfterDelete($this);

		return empty ( $this->errors );
	}

	public function undelete($mesid) {
		if (! $this->parent || $this->parent->id != $mesid) {
			$this->loadMessage ( $mesid );
		}
		if (! $this->canUndelete ())
			return false;

		// Execute undelete
		$query = "UPDATE #__kunena_messages SET `hold`=0 WHERE `id`={$this->_db->quote($this->parent->id)};";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		$dberror = $this->checkDatabaseError ();
		if ($dberror)
			return $this->setError ( '-undelete-', JText::_ ( 'COM_KUNENA_POST_ERROR_UNDELETE' ) );

		// Last but not least update forum stats
		CKunenaTools::reCountBoards();

		$this->set ( 'id', $this->parent->id );

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onAfterUndelete($this);

		return empty ( $this->errors );
	}

	public function save() {
		switch ($this->getOption ( 'action' )) {
			case 'post' :
			case 'reply' :
				return $this->savePost ();
			case 'edit' :
				return $this->saveEdit ();
			default :
				return $this->setError ( '-commit-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );
		}
	}

	protected function floodProtection() {
		// Flood protection
		if ($this->_config->floodprotection && ! CKunenaTools::isModerator ( $this->_my->id, $this->parent->catid )) {
			$this->_db->setQuery ( "SELECT MAX(time) FROM #__kunena_messages WHERE ip={$this->_db->quote($this->ip)}" );
			$lastPostTime = $this->_db->loadResult ();
			if (KunenaError::checkDatabaseError()) return;

			if ($lastPostTime + $this->_config->floodprotection > CKunenaTimeformat::internalTime ()) {
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD1' ) . ' ' . $this->_config->floodprotection . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD2' ) . '<br />';
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD3' );
				return true;
			}
		}
		return false;
	}

	public function loadOptions($options) {
		foreach ( $options as $option => $value ) {
			$this->setOption ( $option, $value );
		}
	}

	public function loadFields($fields) {
		foreach ( $fields as $field => $value ) {
			$this->set ( $field, $value );
		}
	}

	public function setOption($name, $value) {
		if ($name == 'anonymous' && !$this->parent->allow_anonymous) return;
		$this->options [$name] = $value;
	}

	public function getOption($name) {
		return isset ( $this->options [$name] ) ? $this->options [$name] : false;
	}

	protected function loadDefaults($fields) {
		foreach ( $fields as $name ) {
			if (! isset ( $this->message [$name] ) && $this->parent->$name !== null) {
				$this->message [$name] = $this->parent->$name;
			}
		}
	}

	public function set($name, $value = null) {
		if (! empty ( $this->options ['allowed'] ) && ! in_array ( $name, $this->options ['allowed'] )) {
			return $this->setError ( $name, JText::_ ( 'COM_KUNENA_POST_ERROR_FIELD_NOT_ALLOWED' ) );
		}
		$retval = $this->get ( $name );
		if ($value === null)
			unset ( $this->message [$name] );
		else
			$this->message [$name] = JString::trim ( ( string ) $value );
		return $retval;
	}

	public function get($name) {
		if (! isset ( $this->message [$name] ))
			return null;
		return $this->message [$name];
	}

	// Functions to check that values are legal


	protected function check() {
		if ($this->errors)
			return false;
		foreach ( $this->message as $field => $value ) {
			switch ($field) {
				case 'name' :
					$this->checkAuthorName ( $field, $value );
					break;
				case 'email' :
					$this->checkEmail ( $field, $value );
					break;
				case 'subject' :
					$this->checkNotEmpty ( $field, $value );
					break;
				case 'message' :
					$this->checkNotEmpty ( $field, $value );
					break;
				case 'topic_emoticon' :
					$this->checkTopicEmoticon ( $field, $value );
					break;
				case 'modified_reason' :
					break;
			}
		}
		foreach ( $this->getOption ( 'required' ) as $field ) {
			$value = $this->get ( $field );
			if (empty ( $value )) {
				$this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_REQUIRED' ) );
			}
		}
		return empty ( $this->errors );
	}

	protected function checkNotEmpty($field, $value) {
		if (empty ( $value )) {
			return $this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_EMPTY' ) );
		}
		return true;
	}

	protected function checkTopicEmoticon($field, $value) {
		$template = KunenaFactory::getTemplate();
		if (! isset ( $template->topicIcons [$value] )) {
			return $this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_TOPICEMOTICON_INVALID' ) );
		}
		return true;
	}
	protected function checkEmail($field, $value) {
		if ($value) {
			// Email address must be valid
			jimport ( 'joomla.mail.helper' );
			if (! JMailHelper::isEmailAddress ( $value )) {
				return $this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_EMAIL_INVALID' ) );
			}
		} else if (! $this->_my->id && $this->_config->askemail) {
			return $this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_EMAIL_EMPTY' ) );
		}
		return true;
	}

	protected function checkAuthorName($field, $value) {
		if (! $this->_my->id || $this->getOption ( 'anonymous' )) {
			// Unregistered or anonymous users: Do not allow existing username
			jimport ( 'joomla.user.helper' );
			$nicktaken = JUserHelper::getUserId ( $value );
			if (empty ( $value ) || $nicktaken || $value == $this->_my->name) {
				$this->set ( 'name', $name = JText::_ ( 'COM_KUNENA_USERNAME_ANONYMOUS' ) );
				$this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_POST_FIELD_NAME_CONFLICT_ANON', $value, $name ), 'notice' );
			}
		} else {
			// Registered users
			if (empty ( $value )) {
				return $this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_NAME_EMPTY' ) );
			}

			if (CKunenaTools::isModerator ( $this->_my->id, $this->parent->catid )) {
				// Moderators can to do whatever they want to
			} else if ($this->_config->changename) {
				// Others are not allowed to use username from other users
				jimport ( 'joomla.user.helper' );
				$nicktaken = JUserHelper::getUserId ( $value );
				if ($nicktaken && $nicktaken != $this->_my->id) {
					return $this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_NAME_CONFLICT_REG' ) );
				}
			} else if ($value != $this->_my->username && $value != $this->_my->name) {
				return $this->setError ( $field, JText::_ ( 'COM_KUNENA_POST_FIELD_NAME_CHANGED' ) );
			}
		}
		return true;
	}

	protected function isAlreadyPosted() {
		// Ignore identical messages (posted within 5 minutes)
		$duplicatetimewindow = CKunenaTimeformat::internalTime () - 5 * 60;
		$this->_db->setQuery ( "SELECT m.id FROM #__kunena_messages AS m JOIN #__kunena_messages_text AS t ON m.id=t.mesid
			WHERE m.userid={$this->_db->quote($this->message['userid'])}
			AND m.name={$this->_db->quote($this->message['name'])}
			AND m.subject={$this->_db->quote($this->message['subject'])}
			AND m.ip={$this->_db->quote($this->message['ip'])}
			AND t.message={$this->_db->quote($this->message['message'])}
			AND m.time>={$this->_db->quote($duplicatetimewindow)}" );
		$id = $this->_db->loadResult ();
		$dberror = $this->checkDatabaseError ();
		if ($dberror)
			return $this->setError ( '-duplicate-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ) );
		return ( bool ) $id;
	}

	public function emailToSubscribers($LastPostUrl = false, $mailsubs = false, $mailmods = false, $mailadmins = false) {
		if ($LastPostUrl === false) {
			jimport ( 'joomla.environment.uri' );
			$uri = JURI::getInstance ( JURI::base () );
			$LastPostUrl = $uri->toString ( array ('scheme', 'host', 'port' ) ) . CKunenaLink::GetMessageURL($this->get ( 'id' ), $this->get ( 'catid' ), 0, false);
		}
		//get all subscribers, moderators and admins who will get the email
		$emailToList = CKunenaTools::getEMailToList ( $this->get ( 'catid' ), $this->get ( 'thread' ), $mailsubs, $mailmods, $mailadmins, $this->_my->id );

		if (count ( $emailToList )) {
			jimport('joomla.mail.helper');
			if (! $this->_config->email || ! JMailHelper::isEmailAddress ( $this->_config->email )) {
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_EMAIL_INVALID' ), 'notice' );
				return false;
			} else if ($_SERVER ["REMOTE_ADDR"] == '127.0.0.1') {
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_EMAIL_DISABLED' ), 'notice' );
				return false;
			}
			// clean up the message for review
			$authorname = $this->get ( 'name' );
			$message = smile::purify ( $this->get ( 'message' ) );
			$subject = $this->get ( 'subject' );

			$mailsender = JMailHelper::cleanAddress ( $this->_config->board_title . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) );
			$mailsubject = JMailHelper::cleanSubject ( "[" . $this->_config->board_title . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . "] " . $subject . " (" . $this->parent->catname . ")" );

			foreach ( $emailToList as $emailTo ) {
				if (! $emailTo->email || ! JMailHelper::isEmailAddress ( $emailTo->email ))
					continue;

				if ($emailTo->subscription) {
					$msg1 = JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1' );
					$msg2 = JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2' );
				} else {
					$msg1 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD1' );
					$msg2 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD2' );
				}

				$msg = "$emailTo->name,\n\n";
				$msg .= $msg1 . " " . $this->_config->board_title . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . "\n\n";
				$msg .= JText::_ ( 'COM_KUNENA_MESSAGE_SUBJECT' ) . ": " . $subject . "\n";
				$msg .= JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . ": " . $this->parent->catname . "\n";
				$msg .= JText::_ ( 'COM_KUNENA_VIEW_POSTED' ) . ": " . $authorname . "\n\n";
				$msg .= $msg2 . "\n";
				$msg .= "URL: $LastPostUrl\n\n";
				if ($this->_config->mailfull == 1) {
					$msg .= JText::_ ( 'COM_KUNENA_GEN_MESSAGE' ) . ":\n-----\n";
					$msg .= $message;
					$msg .= "\n-----";
				}
				$msg .= "\n\n";
				$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION3' ) . "\n";
				$msg .= "\n\n\n\n";
				$msg .= "** Powered by Kunena! - http://www.kunena.org **";
				$msg = JMailHelper::cleanBody ( $msg );

				JUtility::sendMail ( $this->_config->email, $mailsender, $emailTo->email, $mailsubject, $msg );
			}
		}
	}
}
