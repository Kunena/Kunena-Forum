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

kimport ( 'kunena.controller' );
kimport ( 'kunena.error' );
kimport ( 'kunena.forum.message.helper' );
kimport ( 'kunena.forum.topic.helper' );
kimport ( 'kunena.forum.category.helper' );

require_once KPATH_SITE . '/lib/kunena.link.class.php';

/**
 * Kunena Topic Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerTopic extends KunenaController {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->id = JRequest::getInt('id', 0);
		$this->catid = JRequest::getInt('catid', 0);
		$this->config = KunenaFactory::getConfig();
		$this->me = KunenaFactory::getUser();
	}

	public function post() {
		$this->id = JRequest::getInt('parentid', 0);
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		if (! $this->verifyCaptcha () ) {
			$this->redirectBack ();
		}

		$fields = array (
			'name' => JRequest::getString ( 'authorname', $this->me->getName () ),
			'email' => JRequest::getString ( 'email', null ),
			'subject' => JRequest::getVar ( 'subject', null, 'POST', 'string', JREQUEST_ALLOWRAW ),
			'message' => JRequest::getVar ( 'message', null, 'POST', 'string', JREQUEST_ALLOWRAW ));

		if (!$this->id) {
			$category = KunenaForumCategoryHelper::get($this->catid);
			if (!$category->authorise('topic.create')) {
				$app->enqueueMessage ( $category->getError(), 'notice' );
				$this->redirectBack ();
			}
			$fields['icon_id'] = JRequest::getInt ( 'topic_emoticon', 0 );
			list ($topic, $message) = $category->newTopic($fields);
		} else {
			$parent = KunenaForumMessageHelper::get($this->id);
			if (!$parent->authorise('reply')) {
				$app->enqueueMessage ( $parent->getError(), 'notice' );
				$this->redirectBack ();
			}
			list ($topic, $message) = $parent->newReply($fields);
			$category = $topic->getCategory();
		}

		// Flood protection
		if ($this->config->floodprotection && ! KunenaFactory::getUser()->isModerator($category->id)) {
			$timelimit = JFactory::getDate()->toUnix() - $this->config->floodprotection;
			$ip = $_SERVER ["REMOTE_ADDR"];

			$db = JFactory::getDBO();
			$db->setQuery ( "SELECT COUNT(*) FROM #__kunena_messages WHERE ip={$db->Quote($ip)} WHERE time>{$db->quote($timelimit)}" );
			$count = $db->loadResult ();
			if (KunenaError::checkDatabaseError() || $count) {
				$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_POST_TOPIC_FLOOD', $this->config->floodprotection) );
				$this->redirectBack ();
			}
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
			$app->enqueueMessage ( $message->getError (), 'error' );
			$this->redirectBack ();
		}
		// Display possible warnings (upload failed etc)
		foreach ( $message->getErrors () as $warning ) {
			$app->enqueueMessage ( $warning, 'notice' );
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

			if ( !empty($optvalue) ) {
				require_once KPATH_SITE . '/lib/kunena.poll.class.php';
				$poll = CKunenaPolls::getInstance();
				$poll->save_new_poll ( $polltimetolive, $polltitle, $topic->id, $optvalue );
				$topic->poll_id = $topic->id;
				$topic->save();
			}
		}

		// Update Tags
		$globalTags = JRequest::getString ( 'tags', null );
		$userTags = JRequest::getString ( 'mytags', null );
		$this->updateTags($message->thread, $globalTags, $userTags);

		$message->sendNotification();

		//now try adding any new subscriptions if asked for by the poster
		if (JRequest::getInt ( 'subscribeMe', 0 )) {
			if ($topic->subscribe(1)) {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' ) );
			} else {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' ) );
			}
		}

		if ($message->hold == 1) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCES_REVIEW' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_POSTED' ) );
		}
		$app->redirect ( CKunenaLink::GetMessageURL ( $message->id, $this->catid, 0, false ) );
	}

	protected function edit() {
		$this->id = JRequest::getInt('mesid', 0);
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = KunenaForumMessageHelper::get($this->id);
		if (!$message->authorise('edit')) {
			$app->enqueueMessage ( $message->getError(), 'notice' );
			$this->redirectBack ();
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
				$app->enqueueMessage ( $topic->getError (), 'error' );
				$this->redirectBack ();
			}
		}

		// Save message
		$success = $message->save (true);
		if (! $success) {
			$app->enqueueMessage ( $message->getError (), 'error' );
			$this->redirectBack ();
		}
		// Display possible warnings (upload failed etc)
		foreach ( $message->getErrors () as $warning ) {
			$app->enqueueMessage ( $warning, 'notice' );
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
			require_once KPATH_SITE . '/lib/kunena.poll.class.php';
			if (! $message->getTopic()->poll_id) {
				$poll = CKunenaPolls::getInstance();
				if ( !empty($optvalue) ) {
					$poll->save_new_poll ( $polltimetolive, $polltitle, $this->id, $optvalue );
					$topic->poll_id = $this->id;
					$topic->save();
				}
			} else {
				$poll = CKunenaPolls::getInstance();
				if (empty ( $polltitle ) && empty($poll_optionsID)) {
					//The poll is deleted because the polltitle and the options are empty
					$poll->delete_poll ( $this->id );
				} else {
					$poll->update_poll_edit ( $polltimetolive, $this->id, $polltitle, $optionsnumbers, $poll_optionsID );
				}
			}
		}

		// Update Tags
		$globalTags = JRequest::getString ( 'tags', null );
		$userTags = JRequest::getString ( 'mytags', null );
		$this->updateTags($message->getTopic()->id, $globalTags, $userTags);

		$category = $message->getCategory();
		$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_EDIT' ) );
		if ($category->review && !$category->isModerator()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_GEN_MODERATED' ) );
		}
		$app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	public function subscribe() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->subscribe(1)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->redirectBack ();
	}

	public function unsubscribe() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->subscribe(0)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_UNSUBSCRIBED_TOPIC' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->redirectBack ();
	}

	public function favorite() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->favorite(1)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_FAVORITED_TOPIC' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_FAVORITED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->redirectBack ();
	}

	public function unfavorite() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('read') && $topic->favorite(0)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_UNFAVORITED_TOPIC' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NO_UNFAVORITED_TOPIC' ) .' '. $topic->getError(), 'notice' );
		}
		$this->redirectBack ();
	}

	public function sticky() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('sticky')) {
			$app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->sticky(1)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_SET' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_SET' ) );
		}
		$this->redirectBack ();
	}

	public function unsticky() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('sticky')) {
			$app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->sticky(0)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_UNSET' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_UNSET' ) );
		}
		$this->redirectBack ();
	}

	public function lock() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('lock')) {
			$app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->lock(1)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_SET' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_SET' ) );
		}
		$this->redirectBack ();
	}

	public function unlock() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if (!$topic->authorise('lock')) {
			$app->enqueueMessage ( $topic->getError(), 'notice' );
		} elseif ($topic->lock(0)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_UNSET' ) );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_UNSET' ) );
		}
		$this->redirectBack ();
	}

	public function delete() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topic = KunenaForumTopicHelper::get($this->id);
		if ($topic->authorise('delete') && $topic->publish(KunenaForum::TOPIC_DELETED)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_TOPIC_SUCCESS_DELETE' ) );
		} else {
			$app->enqueueMessage ( $topic->getError(), 'notice' );
		}
		$app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false ) );
	}

	public function move() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topicId = JRequest::getInt('id', 0);
		$messageId = JRequest::getInt('mesid', 0);
		$targetTopic = JRequest::getInt ( 'targettopic', 0 );
		$targetCategory = JRequest::getInt ( 'targetcategory', 0 );

		if ($messageId) {
			$object = KunenaForumMessageHelper::get ( $messageId );
			$topic = $object->getTopic();
		} else {
			$object = KunenaForumTopicHelper::get ( $topicId );
			$topic = $object;
		}
		if ($targetTopic) {
			$target = KunenaForumMessageHelper::get( $targetTopic );
		} else {
			$target = KunenaForumCategoryHelper::get( $targetCategory );
		}

		$error = null;
		if (!$object->authorise ( 'move' )) {
			$error = $object->getError();
		} elseif (!$target->authorise ( 'read' )) {
			$error = $target->getError();
		} else {
			$subject = JRequest::getString ( 'subject', '' );
			$shadow = JRequest::getInt ( 'shadow', 0 );
			$changesubject = JRequest::getInt ( 'changesubject', 0 );

			if ($object instanceof KunenaForumMessage) {
				$mode = JRequest::getWord ( 'mode', 'selected' );
				switch ($mode) {
					case 'newer':
						$ids = new JDate($object->time);
						break;
					case 'selected':
					default:
						$ids = $object->id;
						break;
				}
			} else {
				$ids = false;
			}
			if ($subject) $topic->subject = $subject;
			// TODO: change subject from every message
			if (!$topic->move ( $target, $ids )) {
				$error = $topic->getError();
			}
			// TODO: make shadow post
		}
		if ($error) {
			$app->enqueueMessage ( $error, 'notice' );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' ) );
		}
		//$app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	public function approve() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = KunenaForumMessageHelper::get($this->id);
		$url = CKunenaLink::GetMessageURL ( $this->id, $message->catid, 0, false );
		if ($message->authorise('approve') && $message->publish(KunenaForum::PUBLISHED)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_APPROVE_SUCCESS' ) );

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
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_1APPROVE_FAIL' ), 'notice' );
		}
		$app->redirect ( $url );
	}

	public function deletemessage() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = KunenaForumMessageHelper::get($this->id);
		if ($message->authorise('delete') && $message->publish(KunenaForum::DELETED)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ) );
		} else {
			$app->enqueueMessage ( $message->getError(), 'notice' );
		}
		$app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	public function undeletemessage() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = KunenaForumMessageHelper::get($this->id);
		if ($message->authorise('undelete') && $message->publish(KunenaForum::PUBLISHED)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE' ) );
		} else {
			$app->enqueueMessage ( $message->getError(), 'notice' );
		}
		$app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	public function permdeletemessage() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = KunenaForumMessageHelper::get($this->id);
		if ($message->authorise('permdelete') && $message->delete()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ) );
		} else {
			$app->enqueueMessage ( $message->getError(), 'notice' );
		}
		if ($this->parent)
			$this->redirectBack ();
		else
			$app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false ));
	}

	protected function updateTags($topic, $globalTags, $userTags) {
		$topic = KunenaForumTopicHelper::get($topic);
		if ($userTags !== null) {
			$topic->setKeywords($userTags, $this->me->userid);
		}
		if ($globalTags !== null) {
			$topic->setKeywords($globalTags, false);
		}
	}

	public function hasCaptcha() {
		if ($this->config->captcha == 1 && $this->my->id < 1)
			return true;
		return false;
	}

	public function verifyCaptcha() {
		if (! $this->hasCaptcha ())
			return true;

		$app = JFactory::getApplication ();
		$dispatcher = JDispatcher::getInstance ();
		$results = $dispatcher->trigger ( 'onCaptchaRequired', array ('kunena.post' ) );

		if (! JPluginHelper::isEnabled ( 'system', 'captcha' ) || ! $results [0]) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CAPTCHA_CANNOT_CHECK_CODE' ), 'error' );
			return false;
		}

		if ($results [0]) {
			$captchaparams = array (
				JRequest::getVar ( 'captchacode', '', 'post' ),
				JRequest::getVar ( 'captchasuffix', '', 'post' ),
				JRequest::getVar ( 'captchasessionid', '', 'post' ) );
			$results = $dispatcher->trigger ( 'onCaptchaVerify', $captchaparams );
			if (! $results [0]) {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CAPTCHACODE_DO_NOT_MATCH' ), 'error' );
				return false;
			}
		}
		return true;
	}

	protected function redirectBack() {
		$httpReferer = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );
		JFactory::getApplication ()->redirect ( $httpReferer );
	}
}