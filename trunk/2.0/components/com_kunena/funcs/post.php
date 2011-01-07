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
kimport('kunena.forum.message.helper');
kimport('kunena.forum.topic.helper');

class CKunenaPost {
	public $allow = 0;

	function __construct() {
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->action = JRequest::getCmd ( 'action', '' );

		$this->_app = JFactory::getApplication ();
		$this->config = KunenaFactory::getConfig ();
		$this->_session = KunenaFactory::getSession ();
		$this->_db = JFactory::getDBO ();
		$this->document = JFactory::getDocument ();
		require_once (KPATH_SITE . DS . 'lib' .DS. 'kunena.poll.class.php');
		$this->poll = CKunenaPolls::getInstance();

		$this->my = JFactory::getUser ();
		$this->me = KunenaFactory::getUser ();

		$this->id = JRequest::getInt ( 'id', 0 );
		if (! $this->id) {
			$this->id = JRequest::getInt ( 'parentid', 0 );
		}
		if (! $this->id) {
		// Support for old $replyto variable in post reply/quote
			$this->id = JRequest::getInt ( 'replyto', 0 );
		}
		$this->catid = JRequest::getInt ( 'catid', 0 );

		$this->allow = 1;

		$this->cat_default_allow = null;

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		$this->numLink = null;
		$this->replycount= null;
	}

	protected function post() {
		if ($this->tokenProtection ())
			return false;
		if ($this->floodProtection ())
			return false;
		$this->verifyCaptcha ();

		$fields = array (
			'name' => JRequest::getString ( 'authorname', $this->me->getName () ),
			'email' => JRequest::getString ( 'email', null ),
			'subject' => JRequest::getVar ( 'subject', null, 'POST', 'string', JREQUEST_ALLOWRAW ),
			'message' => JRequest::getVar ( 'message', null, 'POST', 'string', JREQUEST_ALLOWRAW ));

		if (!$this->id) {
			$category = KunenaForumCategoryHelper::get($this->catid);
			if (!$category->authorise('topic.create')) {
				$this->_app->enqueueMessage ( $category->getError(), 'notice' );
				return false;
			}
			$fields['icon_id'] = JRequest::getInt ( 'topic_emoticon', 0 );
			list ($topic, $message) = $category->newTopic($fields);
		} else {
			$parent = KunenaForumMessageHelper::get($this->id);
			if (!$parent->authorise('reply')) {
				$this->_app->enqueueMessage ( $parent->getError(), 'notice' );
				return false;
			}
			list ($topic, $message) = $parent->newReply($fields);
		}

		// If requested: Make message to be anonymous
		if (JRequest::getInt ( 'anonymous', 0 ) && $message->getCategory()->allow_anonymous) {
			$message->makeAnonymous();
		}

		// Upload new attachments
		foreach ($_FILES as $key=>$file) {
			$intkey = 0;
			if (preg_match('/\D*(\d+)/', $key, $matches))
				$intkey = (int)$matches[1];
			if ($file['error'] != UPLOAD_ERR_NO_FILE) $message->uploadAttachment($intkey, $key);
		}

		// Save message
		$success = $message->save ();
		if (! $success) {
			$this->_app->enqueueMessage ( $message->getError (), 'error' );
			$this->redirectBack ();
		}
		// Display possible warnings (upload failed etc)
		foreach ( $message->getErrors () as $warning ) {
			$this->_app->enqueueMessage ( $warning, 'notice' );
		}

		$polltitle = JRequest::getString ( 'poll_title', 0 );
		$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );

		//Insert in the database the informations for the poll and the options for the poll
		$poll_exist = null;
		if (! empty ( $optionsnumbers ) && ! empty ( $polltitle )) {
			$poll_exist = "1";
			//Begin Poll management options
			$poll_optionsID = JRequest::getVar('polloptionsID', array (), 'post', 'array');
			$optvalue = array();
			foreach($poll_optionsID as $opt) {
				if ( !empty($opt) ) $optvalue[] = $opt;
			}

			if ( !empty($optvalue) ) $this->poll->save_new_poll ( $polltimetolive, $polltitle, $topic->id, $optvalue );
		}

		// Update Tags
		$globalTags = JRequest::getString ( 'tags', null );
		$userTags = JRequest::getString ( 'mytags', null );
		$this->updateTags($message->thread, $globalTags, $userTags);

		$message->sendNotification();

		//now try adding any new subscriptions if asked for by the poster
		if (JRequest::getInt ( 'subscribeMe', 0 )) {
			if ($topic->subscribe(1)) {
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' ) );
			} else {
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' ) );
			}
		}

		if ($message->hold == 1) {
			$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUCCES_REVIEW' );
		} else {
			$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUCCESS_POSTED' );
		}
		$this->_app->redirect ( CKunenaLink::GetMessageURL ( $message->id, $this->catid, 0, false ), $redirectmsg );
	}

	protected function editpostnow() {
		if ($this->tokenProtection ())
			return false;

		$message = KunenaForumMessageHelper::get($this->id);
		if (!$message->authorise('edit')) {
			$this->_app->enqueueMessage ( $message->getError(), 'notice' );
			return false;
		}
		$fields = array (
			'name' => JRequest::getString ( 'authorname', $message->name ),
			'email' => JRequest::getString ( 'email', $message->email ),
			'subject' => JRequest::getVar ( 'subject', $message->subject, 'POST', 'string', JREQUEST_ALLOWRAW ),
			'message' => JRequest::getVar ( 'message', $message->message, 'POST', 'string', JREQUEST_ALLOWRAW ),
			'modified_reason' => JRequest::getString ( 'modified_reason', $message->modified_reason ));

		// Update message contents
		$message->edit ( $fields );
		// If requested: Make message to be anonymous
		if (JRequest::getInt ( 'anonymous', 0 ) && $message->getCategory()->allow_anonymous) {
			$message->makeAnonymous();
		}

		// Mark attachments to be deleted
		$attachments = $message->getAttachments();
		$attachkeeplist = JRequest::getVar ( 'attach-id', array (0), 'post', 'array' );
		foreach ($attachments as $attachment) {
			if (!in_array($attachment->id, $attachkeeplist)) {
				$message->removeAttachment($attachment->id);
			}
		}
		// Upload new attachments
		foreach ($_FILES as $key=>$file) {
			$intkey = 0;
			if (preg_match('/\D*(\d+)/', $key, $matches))
				$intkey = (int)$matches[1];
			if ($file['error'] != UPLOAD_ERR_NO_FILE) $message->uploadAttachment($intkey, $key);
		}

		// Check if we are editing first post and update topic if we are!
		$topic = $message->getTopic();
		if ($topic->first_post_id == $message->id) {
			$topic->icon_id = JRequest::getInt ( 'topic_emoticon', $topic->icon_id );
			$topic->subject = $fields ['subject'];
			$success = $topic->save();
			if (! $success) {
				$this->_app->enqueueMessage ( $topic->getError (), 'error' );
				$this->redirectBack ();
			}
		}

		// Save message
		$success = $message->save (true);
		if (! $success) {
			$this->_app->enqueueMessage ( $message->getError (), 'error' );
			$this->redirectBack ();
		}
		// Display possible warnings (upload failed etc)
		foreach ( $message->getErrors () as $warning ) {
			$this->_app->enqueueMessage ( $warning, 'notice' );
		}

		// Polls
		if ($this->config->pollenabled) {
			$polltitle = JRequest::getString ( 'poll_title', 0 );
			$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
			$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );
			$poll_optionsID = JRequest::getVar('polloptionsID', array (), 'post', 'array');
			$optvalue = array();
			foreach($poll_optionsID as $opt) {
				if ( !empty($opt) ) $optvalue[] = $opt;
			}

			//need to check if the poll exist, if it's not the case the poll is insered like new poll
			if (! $message->getTopic()->poll_id) {
				if ( !empty($optvalue) ) $this->poll->save_new_poll ( $polltimetolive, $polltitle, $this->id, $optvalue );
			} else {
				if (empty ( $polltitle ) && empty($poll_optionsID)) {
					//The poll is deleted because the polltitle and the options are empty
					$this->poll->delete_poll ( $this->id );
				} else {
					$this->poll->update_poll_edit ( $polltimetolive, $this->id, $polltitle, $optionsnumbers, $poll_optionsID );
				}
			}
		}

		// Update Tags
		$globalTags = JRequest::getString ( 'tags', null );
		$userTags = JRequest::getString ( 'mytags', null );
		$this->updateTags($message->getTopic()->id, $globalTags, $userTags);

		$category = $message->getCategory();
		$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_EDIT' ) );
		if ($category->review && !$category->isModerator()) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_GEN_MODERATED' ) );
		}
		$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	public function updateTags($topic, $globalTags, $userTags) {
		$topic = KunenaForumTopicHelper::get($topic);
		if ($userTags !== null) {
			$topic->setKeywords($userTags, $this->me->userid);
		}
		if ($globalTags !== null) {
			$topic->setKeywords($globalTags, false);
		}
	}

	// Show forms

	protected function newtopic($do) {
		$cat_params = array ();
		$cat_params['ordering'] = 'ordering';
		$cat_params['toplevel'] = 0;
		$cat_params['sections'] = 0;
		$cat_params['direction'] = 1;
		$cat_params['action'] = 'topic.create';

		$categories = KunenaForumCategoryHelper::getCategories();
		$arrayanynomousbox = array();
		$arraypollcatid = array();
		foreach ($categories as $category) {
			if ($category->parent_id && $category->allow_anonymous) {
				$arrayanynomousbox[] = '"'.$category->id.'":'.$item->post_anonymous;
			}
			if ($category->parent_id && $category->allow_polls) {
				$arraypollcatid[] = '"'.$category->id.'":1';
			}
		}
		$arrayanynomousbox = implode(',',$arrayanynomousbox);
		$arraypollcatid = implode(',',$arraypollcatid);

		$this->document->addScriptDeclaration('var arrayanynomousbox={'.$arrayanynomousbox.'}');
		$this->document->addScriptDeclaration('var pollcategoriesid = {'.$arraypollcatid.'};');

		$this->selectcatlist = JHTML::_('kunenaforum.categorylist', 'catid', 0, null, $cat_params, 'class="inputbox"', 'value', 'text', 0);

		$this->category = KunenaForumCategoryHelper::get($this->catid);
		if (!$this->selectcatlist || ($this->catid && !$this->category->authorise('topic.create'))) {
			$msg = JText::sprintf ( 'COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS', $this->category->getError());
			$this->_app->enqueueMessage ( $msg, 'notice' );
			return false;
		}
		list ($this->topic, $this->message) = $this->category->newTopic();
		$this->title = JText::_ ( 'COM_KUNENA_POST_NEW_TOPIC' );
		$this->action = 'post';

		CKunenaTools::loadTemplate ( '/editor/form.php' );
	}

	protected function reply($do) {
		$parent = KunenaForumMessageHelper::get($this->id);
		if (!$parent->authorise('reply')) {
			$this->_app->enqueueMessage ( $parent->getError(), 'notice' );
			return false;
		}
		list ($this->topic, $this->message) = $parent->newReply($do == 'quote');
		$this->category = $this->topic->getCategory();
		$this->title = JText::_ ( 'COM_KUNENA_POST_REPLY_TOPIC' ) . ' ' . $this->topic->subject;
		$this->action = 'post';

		CKunenaTools::loadTemplate ( '/editor/form.php' );
	}

	protected function edit() {
		$this->message = KunenaForumMessageHelper::get($this->id);
		if (!$this->message->authorise('edit')) {
			$this->_app->enqueueMessage ( $this->message->getError(), 'notice' );
			return false;
		}
		$this->topic = $this->message->getTopic();
		$this->category = $this->topic->getCategory();
		$this->title = JText::_ ( 'COM_KUNENA_POST_EDIT' ) . ' ' . $this->topic->subject;
		$this->action = 'edit';

		// Load attachments
		require_once(KUNENA_PATH_LIB.DS.'kunena.attachments.class.php');
		$attachments = CKunenaAttachments::getInstance ();
		$this->attachments = array_pop($attachments->get($this->message->id));

		//save the options for query after and load the text options, the number options is for create the fields in the form after
		if ($this->topic->poll_id) {
			$this->polldatasedit = $this->poll->get_poll_data ( $this->topic->id );
			$this->polloptionstotal = count ( $this->polldatasedit );
		}

		CKunenaTools::loadTemplate ( '/editor/form.php' );
	}

	protected function moderate($modtopic = false) {
		if ($modtopic) {
			$this->topic = KunenaForumTopicHelper::get($this->id);
			if (!$this->topic->authorise('move')) {
				$this->_app->enqueueMessage ( $this->topic->getError(), 'notice' );
			}
		} else {
			$this->message = KunenaForumMessageHelper::get($this->id);
			if (!$this->message->authorise('move')) {
				$this->_app->enqueueMessage ( $this->message->getError(), 'notice' );
			}
			$this->topic = $this->message->getTopic();
		}
		$this->category = $this->topic->getCategory();

		$options =array ();
		if ($modtopic) {
			$options [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_MODERATION_MOVE_TOPIC' ) );
		} else {
			$options [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_MODERATION_CREATE_TOPIC' ) );
		}
		$options [] = JHTML::_ ( 'select.option', -1, JText::_ ( 'COM_KUNENA_MODERATION_ENTER_TOPIC' ) );
		$params = array(
			'orderby'=>'tt.last_post_time DESC',
			'where'=>" AND tt.id != {$this->_db->Quote($this->topic->id)} ");
		list ($total, $topics) = KunenaForumTopicHelper::getLatestTopics($this->catid, 0, 30, $params);
		foreach ( $topics as $cur ) {
			$options [] = JHTML::_ ( 'select.option', $cur->id, $this->escape ( $cur->subject ) );
		}
		$this->messagelist = JHTML::_ ( 'select.genericlist', $options, 'targettopic', 'class="inputbox"', 'value', 'text', 0, 'kmod_targettopic' );

		$options=array();
		$this->categorylist = CKunenaTools::KSelectList ( 'targetcat', $options, 'class="inputbox kmove_selectbox"', false, 'kmod_categories', $this->catid );
		if (isset($this->message)) $this->user = KunenaFactory::getUser($this->message->userid);

		// Get thread and reply count from current message:
		$query = "SELECT t.id,t.subject,COUNT(mm.id) AS replies FROM #__kunena_messages AS m
			INNER JOIN #__kunena_messages AS t ON m.thread=t.id
			LEFT JOIN #__kunena_messages AS mm ON mm.thread=m.thread AND mm.id > m.id
			WHERE m.id={$this->_db->Quote($this->id)}
			GROUP BY m.thread";
		$this->_db->setQuery ( $query, 0, 1 );
		$this->threadmsg = $this->_db->loadObject ();
		if (KunenaError::checkDatabaseError()) return;

		CKunenaTools::loadTemplate ( '/moderate/moderate.php' );
	}

	function canSubscribe() {
		if (!$this->my->id || !$this->config->allowsubscriptions)
			return false;
		$usertopic = $this->topic->getUserTopic();
		return !$usertopic->subscribed;
	}

	// Simple actions

	protected function delete() {
		if ($this->tokenProtection ('get'))
			return false;

		$message = KunenaForumMessageHelper::get($this->id);
		if ($message->authorise('delete') && $message->publish(KunenaForum::DELETED)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ) );
		} else {
			$this->_app->enqueueMessage ( $message->getError(), 'notice' );
		}
		$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	protected function undelete() {
		if ($this->tokenProtection ('get'))
			return false;

		$message = KunenaForumMessageHelper::get($this->id);
		if ($message->authorise('undelete') && $message->publish(KunenaForum::PUBLISHED)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE' ) );
		} else {
			$this->_app->enqueueMessage ( $message->getError(), 'notice' );
		}
		$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	protected function permdelete() {
		if ($this->tokenProtection ('get'))
			return false;

		$message = KunenaForumMessageHelper::get($this->id);
		if ($message->authorise('permdelete') && $message->delete()) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ) );
		} else {
			$this->_app->enqueueMessage ( $message->getError(), 'notice' );
		}
		if ($this->parent)
			$this->redirectBack ();
		else
			$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false ));
	}

	protected function deletethread() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('delete') && $topic->publish(KunenaForum::DELETED)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_TOPIC_SUCCESS_DELETE' ) );
		} else {
			$this->_app->enqueueMessage ( $topic->getError(), 'notice' );
		}
		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false ) );
	}

	protected function domoderate() {
		if ($this->tokenProtection ())
			return false;

		$success = false;
		$message = KunenaForumMessageHelper::get ( $this->id );
		$targetId = JRequest::getInt ( 'targetid', 0 );
		if ($targetId) $target = KunenaForumMessageHelper::get($targetId);
		else $target = KunenaForumCategoryHelper::get(JRequest::getInt ( 'targetcat', 0 ));
		if ($message->authorise ( 'move' ) && $target->authorise ( 'read' )) {
			$mode = JRequest::getVar ( 'mode', KN_MOVE_MESSAGE );
			$subject = JRequest::getString ( 'subject', '' );
			$shadow = JRequest::getInt ( 'shadow', 0 );
			$changesubject = JRequest::getInt ( 'changesubject', 0 );

			switch ($mode) {
				case KN_MOVE_THREAD:
					$ids = false;
					break;
				case KN_MOVE_NEWER:
					$ids = new JDate($message->time);
					break;
				case KN_MOVE_MESSAGE:
				default:
					$ids = $message->id;
					break;
			}
			$topic = $message->getTopic();
			if ($subject) $topic->subject = $subject;
			// TODO: change subject from every message
			$success = $topic->move ( $target, $ids );
			// TODO: make shadow post
		}
		if (!$success) {
			$error = $message->getError();
			if (!$error) $error = $target->getError();
			if (!$error) $error = $topic->getError();
			$this->_app->enqueueMessage ( $error, 'notice' );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' ) );
		}
		$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	protected function subscribe() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->subscribe(1)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function unsubscribe() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->subscribe(0)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_UNSUBSCRIBED_TOPIC' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function favorite() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->favorite(1)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_FAVORITED_TOPIC' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_FAVORITED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function unfavorite() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->favorite(0)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_UNFAVORITED_TOPIC' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_UNFAVORITED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function sticky() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('sticky')) {
			$this->_app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->sticky(1)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_SET' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_SET' ) );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function unsticky() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('sticky')) {
			$this->_app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->sticky(0)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_UNSET' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_UNSET' ) );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function lock() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('lock')) {
			$this->_app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->lock(1)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_SET' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_SET' ) );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function unlock() {
		if ($this->tokenProtection ('get'))
			return false;

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('lock')) {
			$this->_app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->lock(0)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_UNSET' ) );
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_UNSET' ) );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->id, $this->config->messages_per_page ) );
	}

	protected function approve() {
		if ($this->tokenProtection ('get'))
			return false;

		$url = CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false );
		$message = KunenaForumMessageHelper::get($this->id);
		if ($message->authorise('approve') && $message->publish(KunenaForum::PUBLISHED)) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_APPROVE_SUCCESS' ) );

			// Update category stats
			$topic = $message->getTopic();
			$category = $topic->getCategory();
			if (!$message->parent) $category->numTopics++;
			$category->numPosts++;
			$category->last_topic_id = $topic->id;
			$category->last_topic_subject = $topic->subject;
			$category->last_post_id = $message->id;
			$category->last_post_time = $message->time;
			$category->last_post_userid = $message->userid;
			$category->last_post_message = $message->message;
			$category->last_post_guest_name = $message->name;
			$category->save();

			$message->emailToSubscribers($url, $this->config->allowsubscriptions, $this->config->mailmod, $this->config->mailadmin);
		} else {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_1APPROVE_FAIL' ), 'notice' );
		}
		$this->_app->redirect ( $url );
	}

	// Helper functions

	function hasThreadHistory() {
		if (! $this->config->showhistory || $this->id == 0)
			return false;
		return true;
	}

	function displayThreadHistory() {
		if (! $this->config->showhistory || $this->id == 0)
			return;

		//get all the messages for this thread
		$query = "SELECT m.*, t.* FROM #__kunena_messages AS m
			LEFT JOIN #__kunena_messages_text AS t ON m.id=t.mesid
			WHERE thread='{$this->message->thread}' AND hold='0'
			ORDER BY time DESC";
		$this->_db->setQuery ( $query, 0, $this->config->historylimit );
		$this->messages = $this->_db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		$this->replycount = count($this->messages);

		//get attachments
		$mesids = array();
		foreach ($this->messages as $mes) {
			$mesids[]=$mes->id;
		}
		$mesids = implode(',', $mesids);
		require_once(KUNENA_PATH_LIB.DS.'kunena.attachments.class.php');
		$attachments = CKunenaAttachments::getInstance ();
		$this->attachmentslist = $attachments->get($mesids);

		CKunenaTools::loadTemplate ( '/editor/history.php' );
	}

	public function getNumLink($mesid ,$replycnt) {
		if ($this->config->ordering_system == 'replyid') {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink( $mesid, '#' .$replycnt );
		} else {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink ( $mesid, '#' . $mesid );
		}

		return $this->numLink;
	}

	protected function tokenProtection($method='post') {
		// get the token put in the message form to check that the form has been valided successfully
		if (JRequest::checkToken ($method) == false) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return true;
		}
		return false;
	}

	public function floodProtection() {
		// Flood protection
		$ip = $_SERVER ["REMOTE_ADDR"];

		if ($this->config->floodprotection && ! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->_db->setQuery ( "SELECT MAX(time) FROM #__kunena_messages WHERE ip={$this->_db->Quote($ip)}" );
			$lastPostTime = $this->_db->loadResult ();
			if (KunenaError::checkDatabaseError()) return false;

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
				$this->moderate (false);
				break;

			case 'moderatethread' :
				$this->moderate (true);
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
		$this->document->setTitle ( $title . ' - ' . $this->config->board_title );
	}

	public function hasCaptcha() {
		if ($this->config->captcha == 1 && $this->my->id < 1)
			return true;
		return false;
	}

	public function displayCaptcha() {
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

	/**
	* Escapes a value for output in a view script.
	*
	* If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	* {@link $_encoding} setting.
	*
	* @param  mixed $var The output to escape.
	* @return mixed The escaped value.
	*/
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	public function verifyCaptcha() {
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
