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

kimport ( 'kunena.controller' );
kimport ( 'kunena.error' );
kimport ( 'kunena.forum.message.helper' );
kimport ( 'kunena.forum.message.thankyou.helper' );
kimport ( 'kunena.forum.topic.helper' );
kimport ( 'kunena.forum.category.helper' );
kimport ( 'kunena.forum.topic.poll.helper' );
kimport ( 'kunena.captcha' );

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
		$this->catid = JRequest::getInt('catid', 0);
		$this->id = JRequest::getInt('id', 0);
		$this->mesid = JRequest::getInt('mesid', 0);
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

		if (! KunenaCaptcha::verify () ) {
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

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		if ( $this->message->hold == 0 ) {
			if (!$topic->exists()) {
				$activity->onBeforePost($message);
			} else {
				$activity->onBeforeReply($message);
			}
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

		// Create Poll
		$polltitle = JRequest::getString ( 'poll_title', '' );
		$polloptions = JRequest::getVar('polloptionsID', array (), 'post', 'array');
		//$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		if (! empty ( $polloptions ) && ! empty ( $polltitle )) {
			if ($topic->authorise('poll.create', null, false)) {
				$poll = $topic->getPoll();
				$poll->title = $polltitle;
				$poll->polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );
				$poll->setOptions($polloptions);
				if (!$poll->save()) {
					$app->enqueueMessage ( $poll->getError(), 'notice' );
				} else {
					$topic->poll_id = $poll->id;
					$topic->save();
					$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POLL_CREATED' ) );
				}
			} else {
				$app->enqueueMessage ( $topic->getError(), 'notice' );
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

				// Activity integration
				$activity = KunenaFactory::getActivityIntegration();
				$activity->onAfterSubscribe($topic, 1);
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

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onBeforeEdit($message);

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

		// Poll
		$polltitle = JRequest::getString ( 'poll_title', '' );
		$polloptions = JRequest::getVar('polloptionsID', array (), 'post', 'array');
		//$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );

		// Save changes into poll
		$poll = $topic->getPoll();
		if (! empty ( $polloptions ) && ! empty ( $polltitle )) {
			$poll->title = $polltitle;
			$poll->polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );
			$poll->setOptions($polloptions);
			if (!$topic->poll_id) {
				// Create a new poll
				if (!$topic->authorise('poll.create')) {
					$app->enqueueMessage ( $topic->getError(), 'notice' );
				} elseif (!$poll->save()) {
					$app->enqueueMessage ( $poll->getError(), 'notice' );
				} else {
					$topic->poll_id = $poll->id;
					$topic->save();
					$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POLL_CREATED' ) );
				}
			} else {
				// Edit existing poll
				if (!$topic->authorise('poll.edit')) {
					$app->enqueueMessage ( $topic->getError(), 'notice' );
				} elseif (!$poll->save()) {
					$app->enqueueMessage ( $poll->getError(), 'notice' );
				} else {
					$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POLL_EDITED' ) );
				}
			}
		} elseif ($poll->exists()) {
			// Delete poll
			if (!$topic->authorise('poll.delete')) {
				// Error: No permissions to delete poll
				$app->enqueueMessage ( $topic->getError(), 'notice' );
			} elseif (!$poll->delete()) {
				$app->enqueueMessage ( $poll->getError(), 'notice' );
			} else {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POLL_DELETED' ) );
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

	function thankyou(){
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = KunenaForumMessageHelper::get($this->mesid);
		if (!$message->authorise('thankyou')) {
			$app->enqueueMessage ( $message->getError() );
			$this->redirectBack ();
		}

		$thankyou = KunenaForumMessageThankyouHelper::get($this->mesid);
		if (!$thankyou->save ( KunenaFactory::getUser() )) {
			$app->enqueueMessage ( $thankyou->getError() );
			$this->redirectBack ();
		}

		$activityIntegration = KunenaFactory::getActivityIntegration();
		$activityIntegration->onAfterThankyou($message->userid, $this->my->id, $message);

		$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_THANKYOU_SUCCESS' ) );
		$this->redirectBack ();
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSubscribe($topic, 1);
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSubscribe($topic, 0);
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterFavorite($topic, 1);
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterFavorite($topic, 0);
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSticky($topic, 1);
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterSticky($topic, 0);
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterLock($topic, 1);
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

			// Activity integration
			$activity = KunenaFactory::getActivityIntegration();
			$activity->onAfterLock($topic, 0);
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

		if ($this->mesid) {
			// Delete message
			$target = KunenaForumMessageHelper::get($this->mesid);
			$hold = KunenaForum::DELETED;
			$msg = JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' );
			$url = CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false );
		} else {
			// Delete topic
			$target = KunenaForumTopicHelper::get($this->id);
			$hold = KunenaForum::TOPIC_DELETED;
			$msg = JText::_ ( 'COM_KUNENA_TOPIC_SUCCESS_DELETE' );
			$url = CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false );
		}
		if ($target->authorise('delete') && $target->publish($hold)) {
			$app->enqueueMessage ( $msg );
		} else {
			$app->enqueueMessage ( $target->getError(), 'notice' );
		}
		$app->redirect ( $url );
	}

	public function undelete() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		if ($this->mesid) {
			// Undelete message
			$target = KunenaForumMessageHelper::get($this->mesid);
			$msg = JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE' );
		} else {
			// Undelete topic
			$target = KunenaForumTopicHelper::get($this->id);
			$msg = JText::_ ( 'COM_KUNENA_TOPIC_SUCCESS_UNDELETE' );
		}
		if ($target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED)) {
			$app->enqueueMessage ( $msg );
		} else {
			$app->enqueueMessage ( $target->getError(), 'notice' );
		}
		$app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	public function permdelete() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		if ($this->mesid) {
			// Delete message
			$target = KunenaForumMessageHelper::get($this->mesid);
			$msg = JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' );
			$url = CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false );
		} else {
			// Delete topic
			$target = KunenaForumTopicHelper::get($this->id);
			$msg = JText::_ ( 'COM_KUNENA_TOPIC_SUCCESS_DELETE' );
			$url = CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, false );
		}
		if ($target->authorise('permdelete') && $target->delete()) {
			$app->enqueueMessage ( $msg );
		} else {
			$app->enqueueMessage ( $target->getError(), 'notice' );
		}
		$app->redirect ( $url );
	}

	public function approve() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		if ($this->mesid) {
			// Approve message
			$target = KunenaForumMessageHelper::get($this->mesid);
		} else {
			// Approve topic
			$target = KunenaForumTopicHelper::get($this->id);
		}
		if ($target->authorise('approve') && $target->publish(KunenaForum::PUBLISHED)) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_APPROVE_SUCCESS' ) );
			// FIXME: $topic->sendNotification() doesn't exist
			//$target->sendNotification();
		} else {
			$app->enqueueMessage ( $target->getError(), 'notice' );
		}
		$app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
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
			$target = KunenaForumTopicHelper::get( $targetTopic );
		} else {
			$target = KunenaForumCategoryHelper::get( $targetCategory );
		}

		$error = null;
		if (!$object->authorise ( 'move' )) {
			$error = $object->getError();
		} elseif (!$target->authorise ( 'read' )) {
			$error = $target->getError();
		} else {
			$changesubject = JRequest::getBool ( 'changesubject', false );
			$subject = JRequest::getString ( 'subject', '' );
			$shadow = JRequest::getBool ( 'shadow', false );

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
			if (!$topic->move ( $target, $ids, $shadow, $subject, $changesubject )) {
				$error = $topic->getError();
			}
		}
		if ($error) {
			$app->enqueueMessage ( $error, 'notice' );
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' ) );
		}
		$app->redirect ( CKunenaLink::GetMessageURL ( $this->id, $this->catid, 0, false ) );
	}

	function report() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$config = KunenaFactory::getConfig ();
		$me = KunenaFactory::getUser ();

		if (!$me->exists() || $config->reportmsg == 0) {
			// Deny access if report feature has been disabled or user is guest
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ), 'notice' );
			$this->redirectBack ();
		}

		jimport ( 'joomla.mail.helper' );
		if (! $config->email || ! JMailHelper::isEmailAddress ( $config->email )) {
			// Error: email address is invalid
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_EMAIL_INVALID' ), 'error' );
			$this->redirectBack ();
		}

		// Get target object for the report
		if ($this->mesid) {
			$message = $target = KunenaForumMessageHelper::get($this->mesid);
			$topic = $target->getTopic();
			$messagetext = $message->message;
			$baduser = KunenaFactory::getUser($message->userid);
		} else {
			$topic = $target = KunenaForumTopicHelper::get($this->id);
			$messagetext = $topic->first_post_message;
			$baduser = KunenaFactory::getUser($topic->first_post_userid);
		}
		if (!$target->authorise('read')) {
			// Deny access if user cannot read target
			$app->enqueueMessage ( $target->getError(), 'notice' );
			$this->redirectBack ();
		}
		$category = $topic->getCategory();

		$reason = JRequest::getString ( 'reason' );
		$text = JRequest::getString ( 'text' );

		if (empty ( $reason ) && empty ( $text )) {
			// Do nothing: empty subject or reason is empty
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_REPORT_FORG0T_SUB_MES' ) );
			$this->redirectBack ();
		} else {
			$acl = KunenaFactory::getAccessControl();
			$emailToList = $acl->getSubscribers($topic->category_id, $topic->id, false, true, false, $me->userid);

			if (!empty ( $emailToList )) {
				$mailsender = JMailHelper::cleanAddress ( $config->board_title . ' ' . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . ': ' . $me->getName() );
				$mailsubject = "[" . $config->board_title . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . "] " . JText::_ ( 'COM_KUNENA_REPORT_MSG' ) . ": ";
				if ($reason) {
					$mailsubject .= $reason;
				} else {
					$mailsubject .= $topic->subject;
				}

				jimport ( 'joomla.environment.uri' );
				$uri = & JURI::getInstance ( JURI::base () );
				$msglink = $uri->toString ( array ('scheme', 'host', 'port' ) ) . str_replace ( '&amp;', '&', CKunenaLink::GetThreadPageURL ( 'view', $topic->category_id, $topic->id, 0, NULL, $target->id ) );

				$mailmessage = "" . JText::_ ( 'COM_KUNENA_REPORT_RSENDER' ) . " {$me->username} ($me->name)";
				$mailmessage .= "\n";
				$mailmessage .= "" . JText::_ ( 'COM_KUNENA_REPORT_RREASON' ) . " " . $reason;
				$mailmessage .= "\n";
				$mailmessage .= "" . JText::_ ( 'COM_KUNENA_REPORT_RMESSAGE' ) . " " . $text;
				$mailmessage .= "\n\n";
				$mailmessage .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_POSTER' ) . " {$baduser->username} ($baduser->name)";
				$mailmessage .= "\n";
				$mailmessage .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_SUBJECT' ) . ": " . $topic->subject;
				$mailmessage .= "\n";
				$mailmessage .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_MESSAGE' ) . "\n-----\n" . KunenaHtmlParser::stripBBCode($messagetext);
				$mailmessage .= "\n-----\n\n";
				$mailmessage .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_LINK' ) . " " . $msglink;
				$mailmessage .= "\n\n\n\n** Powered by Kunena! - http://www.kunena.org **";
				$mailmessage = JMailHelper::cleanBody ( strtr ( $mailmessage, array ('&#32;' => '' ) ) );

				foreach ( $emailToList as $emailTo ) {
					if (! $emailTo->email || ! JMailHelper::isEmailAddress ( $emailTo->email ))
						continue;

					JUtility::sendMail ( $config->email, $mailsender, $emailTo->email, $mailsubject, $mailmessage );
				}

				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_REPORT_SUCCESS' ) );
				$app->redirect ( CKunenaLink::GetThreadPageURL ( 'view', $this->catid, $this->id, 0, NULL, $this->id, false ) );
			}
		}
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

	public function vote() {
		$app = JFactory::getApplication ();
		if (!JRequest::checkToken()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$config = KunenaFactory::getConfig();
		$vote = JRequest::getInt('kpollradio', '');
		$id = JRequest::getInt ( 'id', 0 );

		$topic = KunenaForumTopicHelper::get($id);
		$poll = $topic->getPoll();
		if (!$topic->authorise('poll.vote')) {
			$app->enqueueMessage ( $topic->getError(), 'error' );
		} elseif (!$config->pollallowvoteone || !$poll->getMyVotes()) {
			// Give a new vote
			$success = $poll->vote($vote);
			if ( !$success ) {
				$app->enqueueMessage ( $topic->getError(), 'error' );
			} else {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_TOPIC_VOTE_SUCCESS' ) );
			}
		} else {
			// Change existing vote
			$success = $poll->vote($vote, true);
			if ( !$success ) {
				$app->enqueueMessage ( $topic->getError(), 'error' );
			} else {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_TOPIC_VOTE_CHANGED_SUCCESS' ) );
			}
		}
		$app->redirect ( CKunenaLink::GetThreadPageURL ( 'view', $this->catid, $this->id, 0, NULL, $this->id, false ) );
	}
}