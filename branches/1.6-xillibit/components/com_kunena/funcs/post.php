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

class CKunenaPost {
	public $allow = 0;

	function __construct() {
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->action = JRequest::getCmd ( 'action', '' );

		$this->_app = & JFactory::getApplication ();
		$this->config = & CKunenaConfig::getInstance ();
		$this->_session = KunenaFactory::getSession ();
		$this->_db = &JFactory::getDBO ();
		$this->document = JFactory::getDocument ();
		require_once (JPATH_COMPONENT . DS . 'lib' .DS. 'kunena.poll.class.php');
		$this->poll =& CKunenaPolls::getInstance();

		$this->my = &JFactory::getUser ();

		$this->id = JRequest::getInt ( 'id', 0 );
		if (! $this->id) {
			$this->id = JRequest::getInt ( 'parentid', 0 );
		}
		if (! $this->id) {
		// Support for old $replyto variable in post reply/quote
			$this->id = JRequest::getInt ( 'replyto', 0 );
		}
		$this->catid = JRequest::getInt ( 'catid', 0 );

		$this->msg_cat = null;

		$this->allow = 1;
	}

	// Temporary function to handle old style permission handling
	// TODO: Remove this when all functions are using new style
	protected function load() {
		if ($this->msg_cat)
			return true;

		if ($this->id) {
			// Check that message and category exists and fill some information for later use
			$query = "SELECT m.*, (mm.locked OR c.locked) AS locked, c.locked AS catlocked, t.message,
					c.name AS catname, c.parent AS catparent, c.pub_access,
					c.review, c.class_sfx, p.id AS poll_id, c.allow_anonymous,
					c.post_anonymous, c.allow_polls
				FROM #__fb_messages AS m
				INNER JOIN #__fb_messages AS mm ON mm.id=m.thread
				INNER JOIN #__fb_messages_text AS t ON t.mesid=m.id
				INNER JOIN #__fb_categories AS c ON c.id=m.catid
				LEFT JOIN #__fb_polls AS p ON m.id=p.threadid
				WHERE m.id='" . $this->id . "'";

			$this->_db->setQuery ( $query );
			$this->msg_cat = $this->_db->loadObject ();
			check_dberror ( 'Unable to check message.' );

			if (! $this->msg_cat) {
				echo JText::_ ( 'COM_KUNENA_POST_INVALID' );
				return false;
			}

			// Make sure that category id is from the message (post may have been moved)
			if ($this->do != 'domovepostnow' && $this->do != 'domergepostnow' && $this->do != 'dosplit') {
				$this->catid = $this->msg_cat->catid;
			}
		} else if ($this->catid) {
			// Check that category exists and fill some information for later use
			$this->_db->setQuery ( "SELECT 0 AS id, 0 AS thread, id AS catid, name AS catname, parent AS catparent, pub_access, locked, locked AS catlocked, review, class_sfx, allow_anonymous, post_anonymous, allow_polls FROM #__fb_categories WHERE id='{$this->catid}'" );
			$this->msg_cat = $this->_db->loadObject ();
			check_dberror ( 'Unable to load category.' );
			if (! $this->msg_cat) {
				echo JText::_ ( 'COM_KUNENA_NO_ACCESS' );
				return false;
			}
		}

		// Check if anonymous user needs to log in
		if ($this->my->id == 0 && (! $this->config->pubwrite || ($this->catid && ! $this->_session->canRead ( $this->catid )))) {
			CKunenaTools::loadTemplate ( '/login.php' );
			return false;
		}
		// Check user access rights
		if (!empty ( $this->msg_cat->catparent ) && ! $this->_session->canRead ( $this->catid ) && ! CKunenaTools::isAdmin ()) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return false;
		}

		return true;
	}

	protected function post() {
		$this->verifyCaptcha ();

		if ($this->tokenProtection ())
			return false;
		if ($this->floodProtection ())
			return false;

		$fields ['name'] = JRequest::getString ( 'authorname', $this->getAuthorName () );
		$fields ['email'] = JRequest::getString ( 'email', null );
		$fields ['subject'] = JRequest::getVar ( 'subject', null, 'POST', 'string', JREQUEST_ALLOWRAW );
		$fields ['message'] = JRequest::getVar ( 'message', null, 'POST', 'string', JREQUEST_ALLOWRAW );
		$fields ['topic_emoticon'] = JRequest::getInt ( 'topic_emoticon', null );

		$options ['anonymous'] = JRequest::getInt ( 'anonymous', 0 );
		$contentURL = JRequest::getVar ( 'contentURL', '' );

		require_once (KUNENA_PATH_LIB . DS . 'kunena.posting.class.php');
		$message = new CKunenaPosting ( );
		if (! $this->id) {
			$success = $message->post ( $this->catid, $fields, $options );
		} else {
			$success = $message->reply ( $this->id, $fields, $options );
		}

		if ($success) {
			$success = $message->save ();
		}

		// Handle errors
		if (! $success) {
			$errors = $message->getErrors ();
			foreach ( $errors as $field => $error ) {
				$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
			}
			$this->redirectBack ();
		}

		$catinfo = $message->parent;
		$userid = $message->get ( 'userid' );
		$id = $message->get ( 'id' );
		$thread = $message->get('thread');
		$subject = $message->get('subject');
		$holdPost = $message->get ( 'hold' );

		$polltitle = JRequest::getString ( 'poll_title', 0 );
		$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );

		//Insert in the database the informations for the poll and the options for the poll
		$poll_exist = null;
		if (! empty ( $optionsnumbers ) && ! empty ( $polltitle )) {
			$poll_exist = "1";
			//Begin Poll management options
			$optionvalue = array ();
			for($ioptions = 0; $ioptions < $optionsnumbers; $ioptions ++) {
				$optionvalue [] = JRequest::getString ( 'field_option' . $ioptions, null );
			}
		}

		if (! empty ( $polltitle ) && ! empty ( $optionsnumbers )) {
			$this->poll->save_new_poll ( $polltimetolive, $polltitle, $id, $optionvalue );
		}

		// TODO: replace this with better solution
		$this->_db->setQuery ( "SELECT COUNT(*) AS totalmessages FROM #__fb_messages WHERE thread='{$thread}'" );
		$result = $this->_db->loadObject ();
		check_dberror ( "Unable to load messages." );
		$threadPages = ceil ( $result->totalmessages / $this->config->messages_per_page );
		//construct a useable URL (for plaintext - so no &amp; encoding!)
		jimport ( 'joomla.environment.uri' );
		$uri = & JURI::getInstance ( JURI::base () );
		$LastPostUrl = $uri->toString ( array ('scheme', 'host', 'port' ) ) . str_replace ( '&amp;', '&', CKunenaLink::GetThreadPageURL ( 'view', $this->catid, $thread, $threadPages, $this->config->messages_per_page, $id ) );

		//Update the attachments table if an image has been attached
		require_once (KUNENA_PATH_LIB . DS . 'kunena.attachments.class.php');
		$attachments = CKunenaAttachments::getInstance ();
		//$attachments->assign ( $id );
		$fileinfos = $attachments->multiupload ( $id );
		foreach ( $fileinfos as $fileinfo ) {
			if (! $fileinfo ['status'])
				$this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo [name] ) . ': ' . $fileinfo ['error'], 'error' );
		}

		$message->emailToSubscribers($LastPostUrl, $this->config->allowsubscriptions && ! $holdPost, $this->config->mailmod || $holdPost, $this->config->mailadmin || $holdPost);

		$redirectmsg = '';

		if ( $this->config->catsautosubscribed ) {
			$subscribeMe = 1;
			if ( $this->config->catsautosubscribed != '1' ){
				if ( in_array($this->catid, explode(',',$this->config->catsautosubscribed)) ) {
					$subscribeMe = 1;
				} else {
					$subscribeMe = 0;
				}
			}
		} else {
			$subscribeMe = JRequest::getVar ( 'subscribeMe', '' );
		}

		//now try adding any new subscriptions if asked for by the poster
		if ($subscribeMe == 1) {
			$this->_db->setQuery ( "INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('$thread','{$this->my->id}')" );

			if (@$this->_db->query ()) {
				$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' ) . '<br />';
			} else {
				$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' ) . '<br />';
			}
		}

		if ($holdPost == 1) {
			$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUCCES_REVIEW' );
		} else {
			$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUCCESS_POSTED' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $id, $this->config->messages_per_page, $this->catid ), $redirectmsg );
	}

	protected function newtopic($do) {
		$this->reply($do);
	}

	protected function reply($do) {
		if (!$this->load())
			return false;
		if ($this->lockProtection ())
			return false;
		if ($this->floodProtection ())
			return false;
		if ($this->isBanned($this->my->id) )
			return false;

		$this->kunena_editmode = 0;

		$message = $this->msg_cat;
		if ($this->catid && $this->msg_cat->id > 0) {
			if ($do == 'quote') {
				$this->message_text = "[b]" . kunena_htmlspecialchars ( stripslashes ( $message->name ) ) . " " . JText::_ ( 'COM_KUNENA_POST_WROTE' ) . ":[/b]\n";
				$mestext = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', stripslashes ( $message->message ) );
				$this->message_text .= '[quote]' .  kunena_htmlspecialchars ( $mestext ) . "[/quote]";
			} else {
				$this->message_text = '';
			}
			$reprefix = JString::substr ( stripslashes ( $message->subject ), 0, JString::strlen ( JText::_ ( 'COM_KUNENA_POST_RE' ) ) ) != JText::_ ( 'COM_KUNENA_POST_RE' ) ? JText::_ ( 'COM_KUNENA_POST_RE' ) . ' ' : '';
			$this->subject = kunena_htmlspecialchars ( stripslashes ( $message->subject ) );
			$this->resubject = $reprefix . $this->subject;
			$this->parent = $message->parent;
		} else {
			$this->message_text = '';
			$this->resubject = '';
			$this->parent = 0;

			$options = array ();
			if (empty ( $this->msg_cat->allow_anonymous ))
				$this->selectcatlist = CKunenaTools::forumSelectList ( 'postcatid', $this->catid, $options, '' );
		}
		$this->authorName = kunena_htmlspecialchars ( $this->getAuthorName () );
		$this->emoid = 0;
		$this->action = 'post';

		$this->allow_anonymous = ! empty ( $this->msg_cat->allow_anonymous ) && $this->my->id;
		$this->anonymous = ($this->allow_anonymous && ! empty ( $this->msg_cat->post_anonymous ));
		$this->allow_name_change = 0;
		if (! $this->my->id || $this->config->changename || ! empty ( $this->msg_cat->allow_anonymous ) || CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->allow_name_change = 1;
		}

		// check if this user is already subscribed to this topic but only if subscriptions are allowed
		$this->cansubscribe = 0;
		if ($this->my->id && $this->config->allowsubscriptions == 1) {
			$this->cansubscribe = 1;
			if ($this->msg_cat && $this->msg_cat->thread) {
				$this->_db->setQuery ( "SELECT thread FROM #__fb_subscriptions WHERE userid='{$this->my->id}' AND thread='{$this->msg_cat->thread}'" );
				$subscribed = $this->_db->loadResult ();
				check_dberror ( "Unable to load subscriptions." );

				if ($subscribed) {
					$this->cansubscribe = 0;
				}
			}
		}

		if ($this->id)
			$this->title = JText::_ ( 'COM_KUNENA_POST_REPLY_TOPIC' ) . ' ' . $this->subject;
		else
			$this->title = JText::_ ( 'COM_KUNENA_POST_NEW_TOPIC' );

		CKunenaTools::loadTemplate ( '/editor/form.php' );
	}

	protected function edit() {
		if (!$this->load())
			return false;
		if ($this->lockProtection ())
			return false;
		if ($this->isBanned($this->my->id) )
			return false;

		$message = $this->msg_cat;

		$allowEdit = 0;
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			// Moderator can edit any message
			$allowEdit = 1;
		} else if ($this->my->id && $this->my->id == $message->userid) {
			$allowEdit = CKunenaTools::editTimeCheck ( $message->modified_time, $message->time );
		}

		if ($allowEdit == 1) {
			// Load attachments
			require_once(KUNENA_PATH_LIB.DS.'kunena.attachments.class.php');
			$attachments = CKunenaAttachments::getInstance ();
			$this->attachments = array_pop($attachments->get($message->id));

			$this->kunena_editmode = 1;

			$this->message_text = kunena_htmlspecialchars ( stripslashes ( $message->message ) );
			$this->resubject = kunena_htmlspecialchars ( stripslashes ( $message->subject ) );
			$this->authorName = kunena_htmlspecialchars ( stripslashes ( $message->name ) );
			$this->email = kunena_htmlspecialchars ( stripslashes ( $message->email ) );
			$this->id = $message->id;
			$this->catid = $message->catid;
			$this->parent = $message->parent;
			$this->emoid = $message->topic_emoticon;
			$this->action = 'edit';

			//save the options for query after and load the text options, the number options is for create the fields in the form after
			if ($message->poll_id) {
				$this->polldatasedit = $this->poll->get_poll_data ( $this->id );
				if ($this->kunena_editmode) {
					$this->polloptionstotal = count ( $this->polldatasedit );
				}
			}

			$this->allow_anonymous = ! empty ( $this->msg_cat->allow_anonymous ) && $message->userid;
			$this->anonymous = 0;
			$this->allow_name_change = 0;
			if (! $this->my->id || $this->config->changename || ! empty ( $this->msg_cat->allow_anonymous ) || CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
				$this->allow_name_change = 1;
			}
			if (!$this->allow_name_change && $message->userid == $this->my->id) $this->authorName = $this->getAuthorName ();

			$this->title = JText::_ ( 'COM_KUNENA_POST_EDIT' ) . ' ' . $this->resubject;

			CKunenaTools::loadTemplate ( '/editor/form.php' );
		} else {
			$this->_app->redirect ( CKunenaLink::GetKunenaURL ( false ), JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
		}
	}

	protected function editpostnow() {
		if ($this->tokenProtection ())
			return false;

		$fields ['name'] = JRequest::getString ( 'authorname', $this->getAuthorName () );
		$fields ['email'] = JRequest::getString ( 'email', null );
		$fields ['subject'] = JRequest::getVar ( 'subject', null, 'POST', 'string', JREQUEST_ALLOWRAW );
		$fields ['message'] = JRequest::getVar ( 'message', null, 'POST', 'string', JREQUEST_ALLOWRAW );
		$fields ['topic_emoticon'] = JRequest::getInt ( 'topic_emoticon', null );
		$fields ['modified_reason'] = JRequest::getString ( 'modified_reason', null );

		$options ['anonymous'] = JRequest::getInt ( 'anonymous', 0 );

		require_once (KUNENA_PATH_LIB . DS . 'kunena.posting.class.php');
		$message = new CKunenaPosting ( );
		$success = $message->edit ( $this->id, $fields, $options );
		if ($success) {
			$success = $message->save ();
		}

		// Handle errors
		if (! $success) {
			$errors = $message->getErrors ();
			foreach ( $errors as $field => $error ) {
				$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
			}
			$this->redirectBack ();
		}

		$mes = $message->parent;

		$polltitle = JRequest::getString ( 'poll_title', 0 );
		$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );

		//update the poll when an user edit his post
		if ($this->config->pollenabled) {
			$optvalue = array ();
			for($i = 0; $i < $optionsnumbers; $i ++) {
				$optvalue [] = JRequest::getString ( 'field_option' . $i, null );
			}
			//need to check if the poll exist, if it's not the case the poll is insered like new poll
			if (! $mes->poll_id) {
				$this->poll->save_new_poll ( $polltimetolive, $polltitle, $this->id, $optvalue );
			} else {
				if (empty ( $polltitle ) && empty ( $optionsnumbers )) {
					//The poll is deleted because the polltitle and the options are empty
					$this->poll->delete_poll ( $this->id );
				} else {
					$this->poll->update_poll_edit ( $polltimetolive, $this->id, $polltitle, $optvalue, $optionsnumbers );
				}
			}
		}

		//Update the attachments table if an file has been attached
		require_once (KUNENA_PATH_LIB . DS . 'kunena.attachments.class.php');
		$attachments = CKunenaAttachments::getInstance ();

		// Delete attachments which weren't checked (= not listed in here)
		jimport('joomla.utilities.arrayhelper');
		$attachkeeplist = JRequest::getVar('attach-id',array ( 0 ), 'post', 'array');
		JArrayHelper::toInteger($attachkeeplist, array ( 0 ));
		$attachkeeplist = implode(',', $attachkeeplist);
		$query = "SELECT id FROM #__kunena_attachments WHERE mesid={$this->id} AND id NOT IN ({$attachkeeplist})";
		$this->_db->setQuery ( $query );
		$attachmentlist = $this->_db->loadResultArray ();
		check_dberror ( "Unable to load attachments." );
		$attachments->deleteAttachment($attachmentlist);

		//$attachments->assign ( $this->id );
		$fileinfos = $attachments->multiupload ( $this->id );
		foreach ( $fileinfos as $fileinfo ) {
			if (! $fileinfo ['status'])
				$this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo [name] ) . ': ' . $fileinfo ['error'], 'error' );
		}

		$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_EDIT' ) );
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page, $this->catid ) );
	}

	protected function delete() {
		require_once (KUNENA_PATH_LIB . DS . 'kunena.posting.class.php');
		$message = new CKunenaPosting ( );
		$success = $message->delete ( $this->id );

		// Handle errors
		if (! $success) {
			$errors = $message->getErrors ();
			foreach ( $errors as $field => $error ) {
				$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
			}
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE') );
		}
		$this->redirectBack ();
	}

	protected function undelete() {
		require_once (KUNENA_PATH_LIB . DS . 'kunena.posting.class.php');
		$message = new CKunenaPosting ( );
		$success = $message->undelete ( $this->id );

		// Handle errors
		if (! $success) {
			$errors = $message->getErrors ();
			foreach ( $errors as $field => $error ) {
				$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
			}
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE') );
		}
		$this->redirectBack ();
	}

	protected function permdelete() {
		if (!$this->load())
			return false;
		// FIXME: we need better permission control
		if ($this->moderatorProtection ())
			return false;

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$delete = $kunena_mod->deleteMessagePerminantly ( $this->id, true );
		if (! $delete) {
			$this->_app->enqueueMessage( $kunena_mod->getErrorMessage ());
		} else {
			$this->_app->enqueueMessage( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ));
		}

		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false ) );
	}

	protected function deletethread() {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$delete = $kunena_mod->deleteThread ( $this->id );
		if (! $delete) {
			$message = $kunena_mod->getErrorMessage ();
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' );
		}

		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false ), $message );
	}

	protected function moderate($modchoices='',$modthread = false) {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');

		$this->moderateTopic = $modthread;
		$this->moderateMultiplesChoices = $modchoices;

		// Get list of latest messages:
		$query = "SELECT id,subject FROM #__fb_messages WHERE catid={$this->catid} AND parent=0 AND hold=0 AND moved=0 AND thread!='{$this->msg_cat->thread}' ORDER BY id DESC";
		$this->_db->setQuery ( $query, 0, 30 );
		$messagesList = $this->_db->loadObjectlist ();
		check_dberror ( "Unable to load messages." );

		// Get thread and reply count from current message:
		$query = "SELECT t.id,t.subject,COUNT(mm.id) AS replies FROM #__fb_messages AS m
			INNER JOIN #__fb_messages AS t ON m.thread=t.id
			LEFT JOIN #__fb_messages AS mm ON mm.thread=m.thread AND mm.id > m.id
			WHERE m.id={$this->id}
			GROUP BY m.thread";
		$this->_db->setQuery ( $query, 0, 1 );
		$this->threadmsg = $this->_db->loadObject ();
		check_dberror ( "Unable to load messages." );

		$messages =array ();
		if ($this->moderateTopic) {
			$messages [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_MODERATION_MOVE_TOPIC' ) );
		} else {
			$messages [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_MODERATION_CREATE_TOPIC' ) );
		}
		$messages [] = JHTML::_ ( 'select.option', -1, JText::_ ( 'COM_KUNENA_MODERATION_ENTER_TOPIC' ) );
		foreach ( $messagesList as $mes ) {
			$messages [] = JHTML::_ ( 'select.option', $mes->id, kunena_htmlspecialchars ( stripslashes ( $mes->subject ) ) );
		}
		$this->messagelist = JHTML::_ ( 'select.genericlist', $messages, 'targettopic', 'class="inputbox"', 'value', 'text', 0, 'kmod_targettopic' );

		$options=array();
		$this->categorylist = CKunenaTools::KSelectList ( 'targetcat', $options, 'class="inputbox kmove_selectbox"', false, 'kmod_categories', $this->catid );
		$this->message = $this->msg_cat;
		$this->user = KunenaFactory::getUser($this->msg_cat->userid);

		CKunenaTools::loadTemplate ( '/moderate/moderate.php' );
	}

	protected function domoderate() {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');

		$mode = JRequest::getVar ( 'mode', KN_MOVE_MESSAGE );
		$targetSubject = JRequest::getString ( 'subject', '' );
		$targetCat = JRequest::getInt ( 'targetcat', 0 );
		$targetId = JRequest::getInt ( 'targetid', 0 );
		if (!$targetId) $targetId = JRequest::getInt ( 'targettopic', 0 );
		$shadow = JRequest::getInt ( 'shadow', 0 );

		$moderation = CKunenaModeration::getInstance ();
		$success = $moderation->move($this->id, $targetCat, $targetSubject, $targetId, $mode, $shadow);
		if (! $success) {
			$this->_app->enqueueMessage( $moderation->getErrorMessage () );
		} else {
			$this->_app->enqueueMessage( JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' ));
		}
		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, true ) );
	}

	protected function subscribe() {
		if (!$this->load())
			return false;
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' );
		$this->_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('{$thread}','{$this->my->id}')" );

			if (@$this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function unsubscribe() {
		if (!$this->load())
			return false;
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC' );
		$this->_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "DELETE FROM #__fb_subscriptions WHERE thread={$thread} AND userid={$this->my->id}" );

			if ($this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_UNSUBSCRIBED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function favorite() {
		if (!$this->load())
			return false;
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_FAVORITED_TOPIC' );
		$this->_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "INSERT INTO #__fb_favorites (thread,userid) VALUES ('{$thread}','{$this->my->id}')" );

			if (@$this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_FAVORITED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function unfavorite() {
		if (!$this->load())
			return false;
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_UNFAVORITED_TOPIC' );
		$this->_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "DELETE FROM #__fb_favorites WHERE thread={$thread} AND userid={$this->my->id}" );

			if ($this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_UNFAVORITED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function sticky() {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_SET' );
		$this->_db->setQuery ( "update #__fb_messages set ordering=1 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_SET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function unsticky() {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_UNSET' );
		$this->_db->setQuery ( "update #__fb_messages set ordering=0 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_UNSET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function lock() {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_SET' );
		$this->_db->setQuery ( "update #__fb_messages set locked=1 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_SET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function unlock() {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_UNSET' );
		$this->_db->setQuery ( "update #__fb_messages set locked=0 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_UNSET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	protected function approve() {
		if (!$this->load())
			return false;
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_MODERATE_1APPROVE_FAIL' );
		$this->_db->setQuery ( "UPDATE #__fb_messages SET hold=0 WHERE id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_MODERATE_1APPROVE_SUCCESS' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ), $success_msg );
	}

	function hasThreadHistory() {
		if (! $this->config->showhistory || $this->id == 0)
			return false;
		return true;
	}

	function displayThreadHistory() {
		if (! $this->config->showhistory || $this->id == 0)
			return;

		//get all the messages for this thread
		$query = "SELECT m.*, t.* FROM #__fb_messages AS m
			LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid
			WHERE thread='{$this->msg_cat->thread}' AND hold='0'
			ORDER BY time DESC";
		$this->_db->setQuery ( $query, 0, $this->config->historylimit );
		$this->messages = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load messages." );

		//get attachments
		$mesids = array();
		foreach ($this->messages as $mes) {
			$mesids[]=$mes->id;
		}
		$mesids = implode(',', $mesids);
		require_once(KUNENA_PATH_LIB.DS.'kunena.attachments.class.php');
		$attachments = CKunenaAttachments::getInstance ();
		$this->attachmentslist = $attachments->get($mesids);

		$this->subject = stripslashes ( $this->msg_cat->subject );

		CKunenaTools::loadTemplate ( '/editor/history.php' );
	}

	protected function getAuthorName() {
		if (! $this->my->id) {
			$name = '';
		} else {
			$name = $this->config->username ? $this->my->username : $this->my->name;
		}
		return $name;
	}

	protected function moderatorProtection() {
		if (! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ), 'notice' );
			return true;
		}
		return false;
	}

	protected function tokenProtection() {
		// get the token put in the message form to check that the form has been valided successfully
		if (JRequest::checkToken () == false) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return true;
		}
		return false;
	}

	protected function lockProtection() {
		if ($this->msg_cat && $this->msg_cat->locked && ! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			if ($this->msg_cat->catlocked)
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ), 'error' );
			else
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ), 'error' );
			return true;
		}
		return false;
	}

	protected function isBanned($userid) {
		$sql = "SELECT enabled, userid, bantype FROM #__kunena_banned_users WHERE userid='$userid' AND bantype=2";
		$this->_db->setQuery ( $sql );
		$isbanned = $this->_db->loadObject ();
		check_dberror ( 'Unable to load datas from this user.' );

		if ( is_object($isbanned) ) {
			return true;
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS' ), 'error' );
			return false;
		}
	}

	protected function floodProtection() {
		// Flood protection
		$ip = $_SERVER ["REMOTE_ADDR"];

		if ($this->config->floodprotection && ! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->_db->setQuery ( "SELECT MAX(time) FROM #__fb_messages WHERE ip='{$ip}'" );
			$lastPostTime = $this->_db->loadResult ();
			check_dberror ( "Unable to load max time for current request from IP: {$ip}" );

			if ($lastPostTime + $this->config->floodprotection > CKunenaTimeformat::internalTime ()) {
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD1' ) . ' ' . $this->config->floodprotection . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD2' ) . '<br />';
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD3' );
				return true;
			}
		}
		return false;
	}

	function displayAttachments($attachments) {
		$this->attachments = $attachments;
		CKunenaTools::loadTemplate('/view/message.attachments.php');
	}

	function display() {
		if (! $this->allow)
			return;
		if ($this->action == "post") {
			$this->post ();
			return;
		} else if ($this->action == "cancel") {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_SUBMIT_CANCEL' ) );
			return;
		}

		switch ($this->do) {
			case 'new' :
				$this->newtopic ( $this->do );
				break;

			case 'reply' :
			case 'quote' :
				$this->reply ( $this->do );
				break;

			case 'edit' :
				$this->edit ();
				break;

			case 'editpostnow' :
				$this->editpostnow ();
				break;

			case 'delete' :
				$this->delete ();
				break;

			case 'undelete' :
				$this->undelete ();
				break;

			case 'deletethread' :
				$this->deletethread ();
				break;

			case 'moderate' :
				$this->moderate ();
				break;

			case 'moderatethread' :
				$this->moderate ('',true);
				break;

			case 'merge' :
				$this->moderate ('modmergemessage',false);
				break;

			case 'move' :
				$this->moderate ('modmovemessage',false);
				break;

			case 'split' :
				$this->moderate ('modsplitmultpost',false);
				break;

			case 'movetopic' :
				$this->moderate ('modmovetopic',true);
				break;

			case 'mergetopic' :
				$this->moderate ('modmergetopic',true);
				break;

			case 'domoderate' :
				$this->domoderate ();
				break;

			case 'permdelete' :
				$this->permdelete();
				break;

			case 'subscribe' :
				$this->subscribe ();
				break;

			case 'unsubscribe' :
				$this->unsubscribe ();
				break;

			case 'favorite' :
				$this->favorite ();
				break;

			case 'unfavorite' :
				$this->unfavorite ();
				break;

			case 'sticky' :
				$this->sticky ();
				break;

			case 'unsticky' :
				$this->unsticky ();
				break;

			case 'lock' :
				$this->lock ();
				break;

			case 'unlock' :
				$this->unlock ();
				break;

			case 'approve' :
				$this->approve ();
				break;
		}
	}

	function setTitle($title) {
		$this->document->setTitle ( $title . ' - ' . stripslashes ( $this->config->board_title ) );
	}

	function hasCaptcha() {
		if ($this->config->captcha == 1 && $this->my->id < 1)
			return true;
		return false;
	}

	function displayCaptcha() {
		if (! $this->hasCaptcha ())
			return;

		$dispatcher = &JDispatcher::getInstance();
        $results = $dispatcher->trigger( 'onCaptchaRequired', array( 'kunena.post' ) );

		if (! JPluginHelper::isEnabled ( 'system', 'captcha' ) || !$results[0] ) {
			echo JText::_ ( 'COM_KUNENA_CAPTCHA_NOT_CONFIGURED' );
			return;
		}

        if ($results[0]) {
        	$dispatcher->trigger( 'onCaptchaView', array( 'kunena.post', 0, '', '<br />' ) );
        }
	}

	function verifyCaptcha() {
		if (! $this->hasCaptcha ())
			return;

		$dispatcher     = &JDispatcher::getInstance();
        $results = $dispatcher->trigger( 'onCaptchaRequired', array( 'kunena.post' ) );

		if (! JPluginHelper::isEnabled ( 'system', 'captcha' ) || !$results[0]) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CAPTCHA_CANNOT_CHECK_CODE' ), 'error' );
			$this->redirectBack ();
		}

        if ( $results[0] ) {
        	$captchaparams = array( JRequest::getVar( 'captchacode', '', 'post' )
                        , JRequest::getVar( 'captchasuffix', '', 'post' )
                        , JRequest::getVar( 'captchasessionid', '', 'post' ));
        	$results = $dispatcher->trigger( 'onCaptchaVerify', $captchaparams );
            if ( ! $results[0] ) {
                $this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CAPTCHACODE_DO_NOT_MATCH' ), 'error' );
				$this->redirectBack ();
                return false;
           }
      }
	}

	function redirectBack() {
		$httpReferer = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );
		$this->_app->redirect ( $httpReferer );
	}
}
