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
defined ( '_JEXEC' ) or die ();

class CKunenaPosting {
	var $parent = null;
	private $message = null;
	private $options = null;
	private $errors = array();

	function __construct() {
		$this->_config = CKunenaConfig::getInstance ();
		$this->_session = CKunenaSession::getInstance ();
		$this->_db = JFactory::getDBO ();
		$this->_my = JFactory::getUser ();
		$this->_app = JFactory::getApplication ();

		$this->setError('-load-', JText::_ ( 'COM_KUNENA_POSTING_NOT_LOADED' ));
	}

	protected function checkDatabaseError() {
		if ($this->_db->getErrorNum())
		{
			if (CKunenaTools::isAdmin()) {
				$this->_app->enqueueMessage(JText::sprintf ( 'COM_KUNENA_INTERNAL_ERROR_ADMIN', '<a href="http:://www.kunena.com/">ww.kunena.com</a>' ), 'error');
			} else {
				$this->_app->enqueueMessage(JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' ), 'error');
			}
			return true;
		}
		return false;
	}

	public function getErrors() {
		if (empty($this->errors)) return false;
		return $this->errors;
	}

	protected function setError($field, $message) {
		if (empty($message)) return true;
		$this->errors[$field] = $message;
		return false;
	}

	protected function loadMessage($mesid) {
		$this->errors = array();
		$this->options = null;
		if ($mesid) {
			// Check that message and category exists and fetch some information for later use
			$query = "SELECT m.*, mm.hold AS topichold, mm.locked AS locked, c.locked AS catlocked, t.message,
					c.name AS catname, c.parent AS catparent, c.pub_access,
					c.review, c.class_sfx, p.id AS poll_id, c.allow_anonymous,
					c.post_anonymous, c.allow_polls
				FROM #__fb_messages AS m
				INNER JOIN #__fb_categories AS c ON c.id=m.catid AND c.published
				INNER JOIN #__fb_messages AS mm ON mm.id=m.thread AND mm.moved=0
				INNER JOIN #__fb_messages_text AS t ON t.mesid=m.id
				LEFT JOIN #__fb_polls AS p ON m.id=p.threadid
				WHERE m.id=" . intval ( $mesid ) . " AND m.moved=0";

			$this->_db->setQuery ( $query, 0, 1 );
			$this->parent = $this->_db->loadObject ();
			$this->checkDatabaseError();
		}
		if (! $this->parent) {
			return $this->setError('-load-', JText::_ ( 'COM_KUNENA_POST_INVALID' ));
		}
		return true;
	}

	protected function loadCategory($catid) {
		$this->errors = array();
		$this->options = null;
		if ($catid) {
			// Check that category exists and fill some information for later use
			$query = "SELECT 0 AS id, 0 AS parent, 0 AS thread, 0 AS hold, 0 AS topichold,
					id AS catid, 0 AS locked, locked AS catlocked, '' AS message,
					name AS catname, parent AS catparent, pub_access,
					review, class_sfx, 0 AS poll_id, allow_anonymous,
					post_anonymous, allow_polls
				FROM #__fb_categories WHERE id=" . intval ( $catid ) . " AND published";
			$this->_db->setQuery ( $query, 0, 1 );
			$this->message = $this->_db->loadObject ();
			$this->checkDatabaseError();
		}
		if (! $this->parent) {
			return $this->setError('-load-', JText::_ ( 'COM_KUNENA_NO_ACCESS' ));
		}
		return true;
	}

	public function load($catid, $mesid = 0) {
		if ($mesid) {
			return $this->loadMessage ( $mesid );
		}
		return $this->loadCategory ( $catid );
	}

	public function canRead() {
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
			return $this->setError('-read-', JText::_ ( 'COM_KUNENA_NO_ACCESS' ));
		}
		// Check unapproved, deleted etc messages
		if (CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			if ($this->parent->hold > 1 || $this->parent->topichold > 1) {
				// Moderators cannot see deleted posts
				return $this->setError('-read-', JText::_ ( 'COM_KUNENA_POST_INVALID' ));
			}
		} else {
			if ($this->parent->hold == 1 && $this->_my->id == $this->message->userid) {
				// User can see his own post before it gets approved
			} else if ($this->parent->hold > 0 || $this->parent->topichold > 0) {
				// Otherwise users can only see visible topics/posts
				return $this->setError('-read-', JText::_ ( 'COM_KUNENA_POST_INVALID' ));
			}
		}

		return true;
	}

	function canPost() {
		// Visitors are allowed to post only if public writing is enabled
		if (($this->_my->id == 0 && ! $this->_config->pubwrite)) {
			return $this->setError('-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' ));
		}
		// User must see category or topic in order to be able post into it
		if (! $this->canRead ()) {
			return false; // Error has already been set
		}
		// Posts cannot be in sections
		if (! $this->parent->catparent) {
			return $this->setError('-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_IS_SECTION' ));
		}
		// Do not perform rest of the checks to moderators and admins
		if (CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			return true; // ACCEPT!
		}
		// Posts cannot be written to locked topics
		if ($this->parent->locked) {
			return $this->setError('-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ));
		}
		// Posts cannot be written to locked categories
		if ($this->parent->catlocked) {
			return $this->setError('-post-', JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ));
		}

		return true;
	}

	function canEdit() {
		// Visitors cannot edit posts
		if (! $this->_my->id) {
			return $this->setError('-edit-', JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' ));
		}
		// User must see topic in order to edit messages in it
		if (! $this->canRead ()) {
			return false;
		}
		// Categories cannot be edited - verify that post exist
		if (! $this->parent->id) {
			return $this->setError('-edit-', JText::_ ( 'COM_KUNENA_POST_INVALID' ));
		}
		// Do not perform rest of the checks to moderators and admins
		if (CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			return true; // ACCEPT!
		}
		// User must be author of the message
		if ($this->parent->userid != $this->_my->id) {
			return $this->setError('-edit-', JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ));
		}
		// User is only allowed to edit post within time specified in the configuration
		if (! CKunenaTools::editTimeCheck ( $this->parent->modified_time, $this->parent->time )) {
			return $this->setError('-edit-', JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ));
		}
		// Posts cannot be edited in locked topics
		if ($this->parent->locked) {
			return $this->setError('-edit-', JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ));
		}
		// Posts cannot be edited in locked categories
		if ($this->parent->catlocked) {
			return $this->setError('-edit-', JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ));
		}

		return true;
	}

	protected function post() {
		if (!$this->canPost()) return false;
		if ($this->floodProtection ())
			return false;

		$subject = JRequest::getVar ( 'subject', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$message = JRequest::getVar ( 'message', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$anonymous = JRequest::getInt ( 'anonymous', 0 );
		$email = JRequest::getVar ( 'email', '' );
		$contentURL = JRequest::getVar ( 'contentURL', '' );
		$subscribeMe = JRequest::getVar ( 'subscribeMe', '' );
		$topic_emoticon = JRequest::getInt ( 'topic_emoticon', 0 );
		$polltitle = JRequest::getString ( 'poll_title', 0 );
		$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );

		$parent = ( int ) $this->parentid;
		$my_name = $this->getAuthorName ( '', $options->anonymous );
		jimport ( 'joomla.mail.helper' );
		if ($this->parent->catid == 0 || empty ( $this->msg_cat )) {
			echo JText::_ ( 'COM_KUNENA_POST_ERROR_NO_CATEGORY' );
		} else if ($this->msg_cat->catparent == 0) {
			echo JText::_ ( 'COM_KUNENA_POST_ERROR_IS_SECTION' );
		} else if ($options->anonymous && ! $this->msg_cat->allow_anonymous) {
			echo JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' );
		} else if (empty ( $my_name )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_NAME' );
		} else if (! $this->_my->id && $this->_config->askemail && empty ( $this->email )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_EMAIL' );
		} else if ($this->_config->askemail && ! JMailHelper::isEmailAddress ( $this->email )) {
			echo JText::_ ( 'COM_KUNENA_MY_EMAIL_INVALID' );
		} else if (empty ( $subject )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_SUBJECT' );
		} else if (empty ( $message )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_MESSAGE' );
		} else {
			if ($parent == 0) {
				$thread = 0;
			}

			if ($this->msg_cat->id == 0) {
				// bad parent, create a new post
				$parent = 0;
				$thread = 0;
			} else {

				$thread = $this->msg_cat->parent == 0 ? $this->msg_cat->id : $this->msg_cat->thread;
			}

			$messagesubject = $subject; //before we add slashes and all... used later in mail


			$userid = $this->_my->id;
			if ($options->anonymous) {
				// Anonymous post: remove all user information from the post
				$userid = 0;
				$this->email = '';
				$this->ip = '';
			}

			$authorname = addslashes ( JString::trim ( $my_name ) );
			$subject = addslashes ( JString::trim ( $subject ) );
			$message = addslashes ( JString::trim ( $message ) );
			$email = addslashes ( JString::trim ( $this->email ) );

			global $topic_emoticons;
			$topic_emoticon = (! isset ( $topic_emoticons [$options->topic_emoticon] )) ? 0 : $options->topic_emoticon;
			$posttime = CKunenaTimeformat::internalTime ();
			if ($contentURL) {
				$message = $contentURL . "\n\n" . $message;
			}

			//check if the post must be reviewed by a moderator prior to showing
			$holdPost = 0;
			if (! CKunenaTools::isModerator ( $this->_my->id, $this->parent->catid )) {
				$holdPost = $this->msg_cat->review;
			}

			// DO NOT PROCEED if there is an exact copy of the message already in the db
			$duplicatetimewindow = $posttime - $this->_config->fbsessiontimeout;
			$this->_db->setQuery ( "SELECT m.id FROM #__fb_messages AS m JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.userid='{$userid}' AND m.name='{$authorname}' AND m.email='{$email}' AND m.subject='{$subject}' AND m.ip='{$this->ip}' AND t.message='{$message}' AND m.time>='{$duplicatetimewindow}'" );
			$pid = ( int ) $this->_db->loadResult ();
			check_dberror ( 'Unable to load post.' );

			if ($pid) {
				// We get here in case we have detected a double post
				// We did not do any further processing and just display the failure message
				echo '<br /><br /><div align="center">' . JText::_ ( 'COM_KUNENA_POST_DUPLICATE_IGNORED' ) . '</div><br /><br />';
				echo CKunenaLink::GetLatestPostAutoRedirectHTML ( $this->_config, $pid, $this->_config->messages_per_page, $this->parent->catid );
			} else {
				$this->_db->setQuery ( "INSERT INTO #__fb_messages
						(parent,thread,catid,name,userid,email,subject,time,ip,topic_emoticon,hold)
						VALUES('$parent','$thread','$this->parent->catid'," . $this->_db->quote ( $authorname ) . ",'{$userid}'," . $this->_db->quote ( $email ) . "," . $this->_db->quote ( $subject ) . ",'$posttime','{$this->ip}','$topic_emoticon','$holdPost')" );

				if (! $this->_db->query ()) {
					echo JText::_ ( 'COM_KUNENA_POST_ERROR_MESSAGE' );
				} else {
					$pid = $this->_db->insertId ();

					$this->_db->setQuery ( "INSERT INTO #__fb_messages_text (mesid,message) VALUES('$pid'," . $this->_db->quote ( $message ) . ")" );
					$this->_db->query ();

					// A couple more tasks required...
					if ($thread == 0) {
						//if thread was zero, we now know to which id it belongs, so we can determine the thread and update it
						$this->_db->setQuery ( "UPDATE #__fb_messages SET thread='$pid' WHERE id='$pid'" );
						$this->_db->query ();
					}

					CKunenaTools::markTopicRead ( $pid, $this->_my->id );

					//update the user posts count
					if ($userid) {
						$this->_db->setQuery ( "UPDATE #__fb_users SET posts=posts+1 WHERE userid={$userid}" );
						$this->_db->query ();
					}

					// Perform proper page pagination for better SEO support
					// used in subscriptions and auto redirect back to latest post
					if ($thread == 0) {
						$querythread = $pid;
					} else {
						$querythread = $thread;
					}

					$this->_db->setQuery ( "SELECT * FROM #__fb_sessions WHERE readtopics LIKE '%$thread%' AND userid!={$this->_my->id}" );
					$sessions = $this->_db->loadObjectList ();
					check_dberror ( "Unable to load sessions." );
					foreach ( $sessions as $session ) {
						$readtopics = $session->readtopics;
						$userid = $session->userid;
						$rt = explode ( ",", $readtopics );
						$key = array_search ( $thread, $rt );
						if ($key !== FALSE) {
							unset ( $rt [$key] );
							$readtopics = implode ( ",", $rt );
							$this->_db->setQuery ( "UPDATE #__fb_sessions SET readtopics='$readtopics' WHERE userid=$userid" );
							$this->_db->query ();
							check_dberror ( "Unable to update sessions." );
						}
					}
				}
			}
		}
	}

	public function edit($mesid, $fields = array(), $options = array()) {
		if (! $this->message || $this->message->id != $mesid) {
			$this->loadMessage ( $mesid );
		}
		if (! $this->canEdit ())
			return false;

		// Load all options and fields
		$this->loadOptions($options);
		$this->setOption('action', 'edit');
		$this->setOption('allowed', array ('name', 'email', 'subject', 'message', 'topic_emoticon', 'modified_reason'));
		$this->setOption('required', array ());

		$this->loadFields($fields);

		return empty($this->errors);
	}

	public function delete() {
		$delete = CKunenaTools::userOwnDelete ( $this->id );
		if (! $delete) {
			$message = JText::_ ( 'COM_KUNENA_POST_OWN_DELETE_ERROR' );
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' );
		}

		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->parent->catid, true ), $message );

		return empty($this->errors);
	}

	public function save() {
		switch ($this->getOption('action')) {
			case 'edit':
				return $this->saveEdit();
			default:
				return $this->setError('-commit-', JText::_ ( 'COM_KUNENA_POST_ERROR_SAVE' ));
		}
	}

	protected function floodProtection() {
		// Flood protection
		if ($this->_config->floodprotection && ! CKunenaTools::isModerator ( $this->_my->id, $this->parent->catid )) {
			$this->_db->setQuery ( "SELECT MAX(time) FROM #__fb_messages WHERE ip='{$this->ip}'" );
			$lastPostTime = $this->_db->loadResult ();
			check_dberror ( "Unable to load max time for current request from IP: {$this->ip}" );

			if ($lastPostTime + $this->_config->floodprotection > CKunenaTimeformat::internalTime ()) {
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD1' ) . ' ' . $this->_config->floodprotection . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD2' ) . '<br />';
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD3' );
				return true;
			}
		}
		return false;
	}

	public function loadOptions($options) {
		foreach ($options as $option=>$value) {
			$this->setOption($option, $value);
		}
	}

	public function loadFields($fields) {
		foreach ($fields as $field=>$value) {
			$this->set($field, $value);
		}
	}

	public function setOption($name, $value) {
		$this->options[$name] = $value;
	}

	public function getOption($name) {
		return isset($this->options[$name]) ? $this->options[$name] : false;
	}

	public function set($name, $value = null) {
		if (!in_array($name, $this->options['allowed'])) {
			return $this->setError($name, JText::_ ( 'COM_KUNENA_POST_ERROR_FIELD_NOT_ALLOWED' ));
		}
		$retval = $this->get($name);
		if ($value === null) unset($this->message[$name]);
		else $this->message[$name] = addslashes ( JString::trim ( (string) $value ) );
		return $retval;
	}

	public function get($name) {
		if (!isset($this->message[$name])) return null;
		return stripslashes( $this->message[$name] );
	}

	protected function saveEdit() {
		$anonymous = $this->getOption('anonymous');

		// Moderators do not have to fill anonymous name
		if ($anonymous && CKunenaTools::isModerator ( $this->_my, $this->parent->catid )) {
			jimport ( 'joomla.user.helper' );
			$nickname = $this->get('name');
			if (!$nickname) $nickname = $this->parent->name;
			$nicktaken = JUserHelper::getUserId ( $nickname );
			if ($nicktaken || $nickname == $this->_my->name) {
				$this->set('name', JText::_('COM_KUNENA_USERNAME_ANONYMOUS'));
			}
		}

		if (!$this->check()) return false;

		// Allow this function to change a few more fields without raising an error
		$allowed = array ('userid', 'ip', 'hold', 'modified_by', 'modified_time');
		$this->setOption('allowed', array_unique(array_merge($this->getOption('allowed'), $allowed)));

		$this->set('hold', CKunenaTools::isModerator ( $this->_my, $this->parent->catid ) ? 0 : ( int ) $this->parent->review);
		$this->set('modified_by', ( int ) $this->_my->id);
		$this->set('modified_time', CKunenaTimeformat::internalTime ());

		if ($anonymous) {
			if ($this->_my->id == $this->parent->userid && $this->parent->modified_by == $this->parent->userid) {
				// I am the author and previous modification was made by me => delete modification information to hide my personality
				$this->set('modified_by', 0);
				$this->set('modified_time', 0);
				$this->set('modified_reason', '');
			} else if ($this->_my->id == $this->parent->userid) {
				// I am the author, but somebody else has modified the message => leave modification information intact
				$this->set('modified_by', null);
				$this->set('modified_time', null);
				$this->set('modified_reason', null);
			}
			// Remove userid, email and ip address
			$this->set('userid', 0);
			$this->set('ip', '');
			$this->set('email', '');
		}

		if (!empty($this->errors)) return false;

		$mesvalues = array();
		$txtvalues = array();
		foreach ($this->message as $field => $value)
		{
			if ($field != 'message') {
				$mesvalues[] = "{$this->_db->nameQuote($field)}={$this->_db->quote($value)}";
			} else {
				$txtvalues[] = "{$this->_db->nameQuote($field)}={$this->_db->quote($value)}";
			}
		}
		if (!empty($mesvalues)) {
			$mesvalues = implode(', ', $mesvalues);
			$query = "UPDATE #__fb_messages SET {$mesvalues} WHERE id={$this->_db->quote($this->parent->id)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			$dberror = $this->checkDatabaseError();
			if ($dberror) return $this->setError('-edit-', JText::_('COM_KUNENA_POST_ERROR_SAVE'));
		}
		if (!empty($txtvalues)) {
			$txtvalues = implode(', ', $txtvalues);
			$query = "UPDATE #__fb_messages_text SET {$txtvalues} WHERE mesid={$this->_db->quote($this->parent->id)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			$dberror = $this->checkDatabaseError();
			if ($dberror) return $this->setError('-edit-', JText::_('COM_KUNENA_POST_ERROR_SAVE'));
		}
		return true;
	}

	// Functions to check that values are legal

	protected function check() {
		if ($this->errors) return false;
		foreach ($this->message as $field => $value) {
			switch ($field) {
				case 'name':
					$this->checkAuthorName($field, $value);
					break;
				case 'email':
					$this->checkEmail($field, $value);
					break;
				case 'subject':
					$this->checkNotEmpty($field, $value);
					break;
				case 'message':
					$this->checkNotEmpty($field, $value);
					break;
				case 'topic_emoticon':
					break;
				case 'modified_reason':
					break;
			}
		}
		foreach ($this->getOption('required') as $field) {
			$value = $this->get($field);
			if (empty($value)) {
				$this->setError($field, JText::_('COM_KUNENA_POST_FIELD_REQUIRED'));
			}
		}
		return empty($this->errors);
	}

	protected function checkNotEmpty($field, $value) {
		if (empty($value)) {
			return $this->setError($field, JText::_('COM_KUNENA_POST_FIELD_EMPTY'));
		}
		return true;
	}

	protected function checkEmail($field, $value) {
		if ($value) {
			// Email address must be valid
			jimport ( 'joomla.mail.helper' );
			if ( ! JMailHelper::isEmailAddress ( $value )) {
				return $this->setError($field, JText::_('COM_KUNENA_POST_FIELD_EMAIL_INVALID'));
			}
		} else if (!$this->_my->id && $this->_config->askemail) {
			return $this->setError($field, JText::_('COM_KUNENA_POST_FIELD_EMAIL_EMPTY'));
		}
		return true;
	}

	protected function checkAuthorName($field, $value) {
		if (empty($value)) {
			return $this->setError($field, JText::_('COM_KUNENA_POST_FIELD_NAME_EMPTY'));
		}
		if (! $this->_my->id || $this->getOption('anonymous')) {
			// Unregistered or anonymous users

			// Do not allow existing username
			jimport ( 'joomla.user.helper' );
			$nicktaken = JUserHelper::getUserId ( $value );
			if ($nicktaken || $value == $this->_my->name) {
				return $this->setError($field, JText::_('COM_KUNENA_POST_FIELD_NAME_CONFLICT_ANON'));
			}
		} else {
			// Registered users

			if (CKunenaTools::isModerator ( $this->_my->id, $this->parent->catid )) {
				// Moderators can to do whatever they want to
			} else if ($this->_config->changename) {
				// Others are not allowed to use username from other users
				jimport ( 'joomla.user.helper' );
				$nicktaken = JUserHelper::getUserId ( $value );
				if ($nicktaken && $nicktaken != $this->_my->id) {
					return $this->setError($field, JText::_('COM_KUNENA_POST_FIELD_NAME_CONFLICT_REG'));
				}
			} else {
				return $this->setError($field, JText::_('COM_KUNENA_POST_FIELD_NAME_CHANGED'));
			}
		}
		return true;
	}
}
