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

kimport ( 'kunena.view' );
kimport ( 'kunena.forum.message.attachment.helper' );
kimport ( 'kunena.forum.topic.poll.helper' );
kimport ( 'kunena.captcha' );
kimport ( 'kunena.html.parser' );

/**
 * Topic View
 */
class KunenaViewTopic extends KunenaView {
	var $topic_subscribe = null;
	var $topic_favorite = null;
	var $topic_reply = null;
	var $topic_new = null;
	var $topic_sticky = null;
	var $topic_lock = null;
	var $topic_delete = null;
	var $topic_moderate = null;
	var $mmm = 0;

	function displayDefault($tpl = null) {
		$this->layout = $this->state->get('layout');
		$this->setLayout($this->layout);
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		$this->assignRef ( 'topic', $this->get ( 'Topic' ) );
		$channels = $this->category->getChannels();
		if (! $this->category->authorise('read')) {
			// User is not allowed to see the category
			$this->setError($this->category->getError());
		} elseif (! $this->topic) {
			// Moved topic loop detected (1 -> 2 -> 3 -> 2)
			$this->setError(JText::_('COM_KUNENA_VIEW_TOPIC_ERROR_LOOP'));
		} elseif (! $this->topic->authorise('read')) {
			// User is not allowed to see the topic
			$this->setError($this->topic->getError());
		} elseif ($this->state->get('item.id') != $this->topic->id || ($this->category->id != $this->topic->category_id && !isset($channels[$this->topic->category_id]))) {
			// Topic has been moved or it doesn't belong to the current category
			$db = JFactory::getDBO();
			$query = "SELECT COUNT(*) FROM #__kunena_messages WHERE thread={$db->Quote($this->topic->id)} AND hold IN ({$this->state->get('hold')}) AND id<={$db->Quote($this->state->get('item.id'))}";
			$db->setQuery ( $query );
			$replyCount = $db->loadResult ();
			if (KunenaError::checkDatabaseError()) return;
			$app = JFactory::getApplication();
			$app->redirect(CKunenaLink::GetThreadPageURL ( 'view', $this->topic->category_id, $this->topic->id, $replyCount, $this->state->get('list.limit'), $this->state->get('item.id'), false ));
		}

		$errors = $this->getErrors();
		if ($errors) {
			return $this->displayNoAccess($errors);
		}

		$this->assignRef ( 'messages', $this->get ( 'Messages' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );

		// If page does not exist, redirect to the last page
/*		if ($this->total <= $this->state->get('list.start')) {
			$app = JFactory::getApplication();
			$app->redirect(CKunenaLink::GetThreadPageURL('view', $this->topic->category_id, $this->topic->id, $this->total, $this->state->get('list.start'), '', false));
		}
*/
		$this->assignRef ( 'moderators', $this->get ( 'Moderators' ) );
		$this->assignRef ( 'usertopic',$this->topic->getUserTopic());
		$this->headerText =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->pagination = $this->getPagination ( 7 );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		// Mark topic read
		$this->topic->markRead ();
		$this->topic->hits++;
		$this->topic->save();

		$this->keywords = $this->topic->getKeywords(false, ', ');

		$this->buttons();

		//meta description and keywords
		// TODO: use real keywords, too
		$metaKeys = $this->escape ( "{$this->topic->subject}, {$this->category->getParent()->name}, {$this->config->board_title}, " . JText::_('COM_KUNENA_GEN_FORUM') . ', ' . JFactory::getapplication()->getCfg ( 'sitename' ) );

		// Create Meta Description form the content of the first message
		// better for search results display but NOT for search ranking!
		$metaDesc = KunenaHtmlParser::stripBBCode($this->topic->first_post_message);
		$metaDesc = preg_replace('/\s+/', ' ', $metaDesc); // remove newlines
		$metaDesc = preg_replace('/^[^\w0-9]+/', '', $metaDesc); // remove characters at the beginning that are not letters or numbers
		$metaDesc = trim($metaDesc); // Remove trailing spaces and beginning

		// remove multiple spaces
		while (strpos($metaDesc, '  ') !== false){
			$metaDesc = str_replace('  ', ' ', $metaDesc);
		}

		// limit to 185 characters - google will cut off at ~150
		if (strlen($metaDesc) > 185){
			$metaDesc = rtrim(JString::substr($metaDesc, 0, 182)).'...';
		}

		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $this->escape($metaDesc) );

		$this->setTitle(JText::sprintf('COM_KUNENA_VIEW_TOPICS_DEFAULT', $this->topic->subject));

		$this->display($tpl);
	}

	function displayFlat($tpl = null) {
		$this->state->set('layout', 'default');
		JFactory::getApplication()->setUserState( 'com_kunena.topic_layout', 'default' );
		$this->displayDefault($tpl);
	}

	function displayThreaded($tpl = null) {
		$this->state->set('layout', 'threaded');
		JFactory::getApplication()->setUserState( 'com_kunena.topic_layout', 'threaded' );
		$this->displayDefault($tpl);
	}

	function displayIndented($tpl = null) {
		$this->state->set('layout', 'indented');
		JFactory::getApplication()->setUserState( 'com_kunena.topic_layout', 'indented' );
		$this->displayDefault($tpl);
	}

	protected function DisplayCreate($tpl = null) {
		$this->setLayout('edit');
		$this->catid = $this->state->get('item.catid');
		$this->my = JFactory::getUser();
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$categories = KunenaForumCategoryHelper::getCategories();
		$arrayanynomousbox = array();
		$arraypollcatid = array();
		foreach ($categories as $category) {
			if ($category->parent_id && $category->allow_anonymous) {
				$arrayanynomousbox[] = '"'.$category->id.'":'.$category->post_anonymous;
			}
			if ($category->parent_id && $category->allow_polls) {
				$arraypollcatid[] = '"'.$category->id.'":1';
			}
		}
		$arrayanynomousbox = implode(',',$arrayanynomousbox);
		$arraypollcatid = implode(',',$arraypollcatid);
		$this->document->addScriptDeclaration('var arrayanynomousbox={'.$arrayanynomousbox.'}');
		$this->document->addScriptDeclaration('var pollcategoriesid = {'.$arraypollcatid.'};');

		$cat_params = array ();
		$cat_params['ordering'] = 'ordering';
		$cat_params['toplevel'] = 0;
		$cat_params['sections'] = 0;
		$cat_params['direction'] = 1;
		$cat_params['action'] = 'topic.create';

		$selected = $this->catid ? $this->catid : null;
		$this->selectcatlist = JHTML::_('kunenaforum.categorylist', 'catid', 0, null, $cat_params, 'class="inputbox"', 'value', 'text', $selected, 'postcatid');

		$this->category = KunenaForumCategoryHelper::get($this->catid);
		if (!$this->selectcatlist || ($this->catid && !$this->category->authorise('topic.create'))) {
			$msg = JText::sprintf ( 'COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS', $this->category->getError());
			$app = JFactory::getApplication();
			$app->enqueueMessage ( $msg, 'notice' );
			return false;
		}
		list ($this->topic, $this->message) = $this->category->newTopic();
		$this->title = JText::_ ( 'COM_KUNENA_POST_NEW_TOPIC' );
		$this->action = 'post';

		$this->document->addScriptDeclaration('// <![CDATA[
   			var number_field = 1;
			// ]]>');

		$this->polldatasedit = array();

		$this->display($tpl);
	}

	protected function DisplayReply($tpl = null) {
		$this->setLayout('edit');
		$this->catid = $this->state->get('item.catid');
		$this->my = JFactory::getUser();
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();
		$mesid = $this->state->get('item.mesid');
		if (!$mesid) {
			$this->topic = KunenaForumTopicHelper::get($this->state->get('item.id'));
			$mesid = $this->topic->first_post_id;
		}

		$parent = KunenaForumMessageHelper::get($mesid);
		if (!$parent->authorise('reply')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage ( $parent->getError(), 'notice' );
			return false;
		}
		$quote = JRequest::getBool ( 'quote', false );
		list ($this->topic, $this->message) = $parent->newReply($quote);
		$this->category = $this->topic->getCategory();
		$this->title = JText::_ ( 'COM_KUNENA_POST_REPLY_TOPIC' ) . ' ' . $this->topic->subject;
		$this->action = 'post';

		$this->display($tpl);
	}

	protected function displayEdit($tpl = null) {
		$this->catid = $this->state->get('item.catid');
		$this->my = JFactory::getUser();
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();
		$mesid = $this->state->get('item.mesid');
		$document = JFactory::getDocument();

		$this->message = KunenaForumMessageHelper::get($mesid);
		if (!$this->message->authorise('edit')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage ( $this->message->getError(), 'notice' );
			return false;
		}
		$this->topic = $this->message->getTopic();
		$this->category = $this->topic->getCategory();
		$this->title = JText::_ ( 'COM_KUNENA_POST_EDIT' ) . ' ' . $this->topic->subject;
		$this->action = 'edit';

		// Get attachments
		$this->attachments = $this->message->getAttachments();

		//save the options for query after and load the text options, the number options is for create the fields in the form after
		if ($this->topic->poll_id) {
			if ($this->config->pollenabled) {
				if ($this->message->id == 0 || $this->message->parent == 0) $this->display_poll=1;
				else $this->display_poll=0;

				$this->polldatasedit = KunenaForumTopicPollHelper::getPollData ( $this->topic->id );

				require_once (KPATH_SITE . '/lib/kunena.poll.class.php');
				$kunena_poll = CKunenaPolls::getInstance();
				$kunena_poll->call_js_poll_edit($this->message->exists(), $this->message->id);

				$this->polloptionstotal = count ( $this->polldatasedit );

				$document->addScriptDeclaration('// <![CDATA[
	   		var number_field = "'.$this->polloptionstotal .'";
			// ]]>');
			}
		} /* put it in reply or create to check if poll need to be displayed
		else {
			if ($this->config->pollenabled) {
				//if ( empty($this->category->allow_polls) ) $this->category->allow_polls = '';
				$this->display_poll = $kunena_poll->get_poll_allowed($this->message->id, $this->message->parent, $this->message->exists(), $this->category->allow_polls);
			}
		}*/

		$this->display($tpl);
	}

	function displayVote($tpl = null) {
		// need to check if poll is allowed in this category
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		$this->assignRef ( 'topic', $this->get ( 'Topic' ) );
		if (!$this->config->pollenabled || !$this->topic->poll_id || !$this->category->allow_polls) {
			return '';
		}

		$this->polldata = $this->get('Polldata');
		$this->nbvoters = $this->get('Voters');
		$this->usersvoted = $this->get('Usersvoted');
		$this->voted = false;

		$this->display($tpl);
	}

	protected function displayReport($tpl = null) {
		$this->catid = $this->state->get('item.catid');
		$this->id = $this->state->get('item.id');
		$this->mesid = $this->state->get('item.mesid');
		$app = JFactory::getApplication();
		$config = KunenaFactory::getConfig ();
		$me = KunenaFactory::getUser ();

		if (!$me->exists() || $config->reportmsg == 0) {
			// Deny access if report feature has been disabled or user is guest
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ), 'notice' );
			return;
		}
		if (!$this->mesid) {
			$this->topic = KunenaForumTopicHelper::get($this->id);
			if (!$this->topic->authorise('read')) {
				$app->enqueueMessage ( $this->topic->getError(), 'notice' );
				return;
			}
		} else {
			$this->message = KunenaForumMessageHelper::get($this->mesid);
			if (!$this->message->authorise('read')) {
				$app->enqueueMessage ( $this->message->getError(), 'notice' );
				return;
			}
			$this->topic = $this->message->getTopic();
		}
		$this->display($tpl);
	}

	protected function displayModerate($tpl = null) {
		$this->mesid = JRequest::getInt('mesid', 0);
		$this->id = $this->state->get('item.id');
		$this->catid = $this->state->get('item.catid');
		$this->config = KunenaFactory::getConfig();
		$app = JFactory::getApplication();

		if (!$this->mesid) {
			$this->topic = KunenaForumTopicHelper::get($this->id);
			if (!$this->topic->authorise('move')) {
				$app->enqueueMessage ( $this->topic->getError(), 'notice' );
				return;
			}
		} else {
			$this->message = KunenaForumMessageHelper::get($this->mesid);
			if (!$this->message->authorise('move')) {
				$app->enqueueMessage ( $this->message->getError(), 'notice' );
				return;
			}
			$this->topic = $this->message->getTopic();
		}
		$this->category = $this->topic->getCategory();

		$options =array ();
		if (!$this->mesid) {
			$options [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_MODERATION_MOVE_TOPIC' ) );
		} else {
			$options [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_MODERATION_CREATE_TOPIC' ) );
		}
		$options [] = JHTML::_ ( 'select.option', -1, JText::_ ( 'COM_KUNENA_MODERATION_ENTER_TOPIC' ) );

		$db = JFactory::getDBO();
		$params = array(
			'orderby'=>'tt.last_post_time DESC',
			'where'=>" AND tt.id != {$db->Quote($this->topic->id)} ");
		list ($total, $topics) = KunenaForumTopicHelper::getLatestTopics($this->catid, 0, 30, $params);
		foreach ( $topics as $cur ) {
			$options [] = JHTML::_ ( 'select.option', $cur->id, $this->escape ( $cur->subject ) );
		}
		$this->topiclist = JHTML::_ ( 'select.genericlist', $options, 'targettopic', 'class="inputbox"', 'value', 'text', 0, 'kmod_topics' );

		$options = array ();
		$cat_params = array ('sections'=>0, 'catid'=>0);
		$this->assignRef ( 'categorylist', JHTML::_('kunenaforum.categorylist', 'targetcategory', 0, $options, $cat_params, 'class="inputbox kmove_selectbox"', 'value', 'text', $this->catid));
		if (isset($this->message)) $this->user = KunenaFactory::getUser($this->message->userid);

		if ($this->mesid) {
			// Get thread and reply count from current message:
			$query = "SELECT COUNT(mm.id) AS replies FROM #__kunena_messages AS m
				INNER JOIN #__kunena_messages AS t ON m.thread=t.id
				LEFT JOIN #__kunena_messages AS mm ON mm.thread=m.thread AND mm.id > m.id
				WHERE m.id={$db->Quote($this->mesid)}";
			$db->setQuery ( $query, 0, 1 );
			$this->replies = $db->loadResult ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$this->display($tpl);
	}

	function buttons() {
		$catid = $this->state->get('item.catid');
		$id = $this->state->get('item.id');

		// Subscribe topic
		if ($this->usertopic->subscribed) {
			// this user is allowed to unsubscribe
			$this->topic_subscribe = CKunenaLink::GetTopicPostLink ( 'unsubscribe', $catid, $id, $this->getButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_TOPIC_LONG') );
		} elseif ($this->topic->authorise('subscribe')) {
			// this user is allowed to subscribe
			$this->topic_subscribe = CKunenaLink::GetTopicPostLink ( 'subscribe', $catid, $id, $this->getButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_TOPIC_LONG') );
		}

		// Favorite topic
		if ($this->usertopic->favorite) {
			// this user is allowed to unfavorite
			$this->topic_favorite = CKunenaLink::GetTopicPostLink ( 'unfavorite', $catid, $id, $this->getButton ( 'favorite', JText::_('COM_KUNENA_BUTTON_UNFAVORITE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNFAVORITE_TOPIC_LONG') );
		} elseif ($this->topic->authorise('favorite')) {
			// this user is allowed to add a favorite
			$this->topic_favorite = CKunenaLink::GetTopicPostLink ( 'favorite', $catid, $id, $this->getButton ( 'favorite', JText::_('COM_KUNENA_BUTTON_FAVORITE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_FAVORITE_TOPIC_LONG') );
		}

		// Reply topic
		if ($this->topic->authorise('reply')) {
			// this user is allowed to reply to this topic
			$this->topic_reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $catid, $this->topic->id, $this->getButton ( 'reply', JText::_('COM_KUNENA_BUTTON_REPLY_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_REPLY_TOPIC_LONG') );
		}

		// New topic
		if ($this->category->authorise('topic.create')) {
			//this user is allowed to post a new topic
			$this->topic_new = CKunenaLink::GetPostNewTopicLink ( $catid, $this->getButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}

		// Moderator specific stuff
		if ($this->category->authorise('moderate')) {
			if (!$this->topic->ordering) {
				$this->topic_sticky = CKunenaLink::GetTopicPostLink ( 'sticky', $catid, $id, $this->getButton ( 'sticky', JText::_('COM_KUNENA_BUTTON_STICKY_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_STICKY_TOPIC_LONG') );
			} else {
				$this->topic_sticky = CKunenaLink::GetTopicPostLink ( 'unsticky', $catid, $id, $this->getButton ( 'sticky', JText::_('COM_KUNENA_BUTTON_UNSTICKY_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_UNSTICKY_TOPIC_LONG') );
			}

			if (!$this->topic->locked) {
				$this->topic_lock = CKunenaLink::GetTopicPostLink ( 'lock', $catid, $id, $this->getButton ( 'lock', JText::_('COM_KUNENA_BUTTON_LOCK_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_LOCK_TOPIC_LONG') );
			} else {
				$this->topic_lock = CKunenaLink::GetTopicPostLink ( 'unlock', $catid, $id, $this->getButton ( 'lock', JText::_('COM_KUNENA_BUTTON_UNLOCK_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_UNLOCK_TOPIC_LONG') );
			}
			$this->topic_delete = CKunenaLink::GetTopicPostLink ( 'deletethread', $catid, $id, $this->getButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_TOPIC_LONG') );
			$this->topic_moderate = CKunenaLink::GetTopicPostReplyLink ( 'moderatethread', $catid, $id, $this->getButton ( 'moderate', JText::_('COM_KUNENA_BUTTON_MODERATE_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MODERATE') );
		}
	}

	function displayPoll() {
		// need to check if poll is allowed in this category
		if (!$this->config->pollenabled || !$this->topic->poll_id || !$this->category->allow_polls) {
			return '';
		}
		if ($this->getLayout() == 'poll') {
			$this->assignRef ( 'category', $this->get ( 'Category' ) );
			$this->assignRef ( 'topic', $this->get ( 'Topic' ) );
		}
		$this->polldata = $this->get('Polldata');
		$this->nbvoters = $this->get('Voters');
		$this->usersvoted = $this->get('Usersvoted');
		$this->voted = $this->get('UsersHasVoted');

		if ($this->voted) echo $this->loadTemplate("pollresults");
		else echo $this->loadTemplate("poll");
	}

	function displayTopicActions($location=0) {
		static $locations = array('top', 'bottom');

		$catid = $this->state->get('item.catid');
		$id = $this->state->get('item.id');
		$mesid = $this->state->get('item.mesid');
		$limitstart = $this->state->get('list.start');
		$limit =  $this->state->get('list.limit');

		$this->layout_buttons = array();
		if ($this->config->enable_threaded_layouts) {
			if ($this->layout != 'default') {
				$this->layout_buttons[] = CKunenaLink::GetThreadLayoutLink('flat', $catid, $id, $mesid,  $this->getButton ( 'layout-flat', JText::_('COM_KUNENA_BUTTON_LAYOUT_FLAT') ), $limitstart, $limit, JText::_('COM_KUNENA_BUTTON_LAYOUT_FLAT_LONG'), 'nofollow', 'kicon-button kbuttonuser btn-left');
			}
			if ($this->layout != 'threaded') {
				$this->layout_buttons[] = CKunenaLink::GetThreadLayoutLink('threaded', $catid, $id, $mesid,  $this->getButton ( 'layout-threaded', JText::_('COM_KUNENA_BUTTON_LAYOUT_THREADED') ), $limitstart, $limit, JText::_('COM_KUNENA_BUTTON_LAYOUT_THREADED_LONG'), 'nofollow', 'kicon-button kbuttonuser btn-left');
			}
			if ($this->layout != 'indented') {
				$this->layout_buttons[] = CKunenaLink::GetThreadLayoutLink('indented', $catid, $id, $mesid,  $this->getButton ( 'layout-indented', JText::_('COM_KUNENA_BUTTON_LAYOUT_INDENTED') ), $limitstart, $limit, JText::_('COM_KUNENA_BUTTON_LAYOUT_INDENTED_LONG'), 'nofollow', 'kicon-button kbuttonuser btn-left');
			}
		}
		$location ^= 1;
		$this->goto = '<a name="forum'.$locations[$location].'"></a>';
		$this->goto .= CKunenaLink::GetSamePageAnkerLink ( 'forum'.$locations[$location], $this->getIcon ( 'kforum'.$locations[$location], JText::_('COM_KUNENA_GEN_GOTO'.$locations[$location] ) ), 'nofollow', 'kbuttongoto');
		echo $this->loadTemplate('actions');
	}

	function displayMessageProfile() {
		static $profiles = array ();

		if (! isset ( $profiles [$this->profile->userid] )) {
			$direction = $this->state->get('profile.direction');
			// Modify profile values by integration
			$triggerParams = array ('userid' => $this->profile->userid, 'userinfo' => &$this->profile );
			$integration = KunenaFactory::getProfile();
			$integration->trigger ( 'profileIntegration', $triggerParams );

			//karma points and buttons
			$me = KunenaFactory::getUser ();
			$this->userkarma_title = $this->userkarma_minus = $this->userkarma_plus = '';
			if ($this->config->showkarma && $this->profile->userid) {
				$this->userkarma_title = JText::_ ( 'COM_KUNENA_KARMA' ) . ": " . $this->profile->karma;
				if ($me->userid && $me->userid != $this->profile->userid) {
					$this->userkarma_minus = ' ' . CKunenaLink::GetKarmaLink ( 'decrease', $this->topic->category_id, $this->message->id, $this->profile->userid, '<span class="kkarma-minus" alt="Karma-" border="0" title="' . JText::_ ( 'COM_KUNENA_KARMA_SMITE' ) . '"> </span>' );
					$this->userkarma_plus = ' ' . CKunenaLink::GetKarmaLink ( 'increase', $this->topic->category_id, $this->message->id, $this->profile->userid, '<span class="kkarma-plus" alt="Karma+" border="0" title="' . JText::_ ( 'COM_KUNENA_KARMA_APPLAUD' ) . '"> </span>' );
				}
			}

			// FIXME: we need to change how profilebox integration works
			/*
			$integration = KunenaFactory::getProfile();
			$triggerParams = array(
				'username' => &$this->username,
				'messageobject' => &$this->msg,
				'subject' => &$this->subjectHtml,
				'messagetext' => &$this->messageHtml,
				'signature' => &$this->signatureHtml,
				'karma' => &$this->userkarma_title,
				'karmaplus' => &$this->userkarma_plus,
				'karmaminus' => &$this->userkarma_minus,
				'layout' => $direction
			);

			$profileHtml = $integration->showProfile($this->msg->userid, $triggerParams);
			*/
			$profileHtml = '';
			if ($profileHtml) {
				// Use integration
				$profiles [$this->profile->userid] = $profileHtml;
			} else {
				$this->userkarma = "{$this->userkarma_title} {$this->userkarma_minus} {$this->userkarma_plus}";
				// Use kunena profile
				if ($this->config->showuserstats) {
					if ($this->config->userlist_usertype)
						$this->usertype = $this->profile->getType ( $this->topic->category_id );
					$this->userrankimage = $this->profile->getRank ( $this->topic->category_id, 'image' );
					$this->userranktitle = $this->profile->getRank ( $this->topic->category_id, 'title' );
					$this->userposts = $this->profile->posts;
					$activityIntegration = KunenaFactory::getActivityIntegration ();
					$this->userpoints = $activityIntegration->getUserPoints ( $this->profile->userid );
					$this->usermedals = $activityIntegration->getUserMedals ( $this->profile->userid );
				}
				$this->personalText = KunenaHtmlParser::parseText ( $this->profile->personalText );

				$profiles [$this->profile->userid] = $this->loadTemplate ( "profile_{$direction}" );
			}
		}
		echo $profiles [$this->profile->userid];
	}

	function displayMessageContents() {
		//Show admins the IP address of the user:
		if ($this->message->ip && ($this->category->authorise('admin') || ($this->category->authorise('moderate') && !$this->config->hide_ip))) {
			$this->ipLink = CKunenaLink::GetMessageIPLink ( $this->message->ip );
		}
		$this->signatureHtml = KunenaHtmlParser::parseBBCode ( $this->profile->signature );
		$this->attachments = $this->message->getAttachments();
		echo $this->loadTemplate("message");
	}

	function displayMessageActions() {
		$me = KunenaFactory::getUser();

		//Thankyou info and buttons
		$this->message_thankyou = '';
		if ($this->config->showthankyou && $this->profile->userid) {
			require_once(KPATH_SITE .DS. 'lib'.DS.'kunena.thankyou.php');
			$thankyou = new CKunenaThankyou();
			$this->thankyou = $thankyou->getThankYouUser($this->message->id);

			if($me->userid && !isset($this->thankyou[$me->userid]) && $me->userid != $this->profile->userid) {
				$this->message_thankyou = CKunenaLink::GetThankYouLink ( $this->topic->category_id, $this->message->id, $this->profile->userid , $this->getButton ( 'thankyou', JText::_('COM_KUNENA_BUTTON_THANKYOU') ), JText::_('COM_KUNENA_BUTTON_THANKYOU_LONG'), 'kicon-button kbuttonuser btn-left');
			}
		}

		$this->message_quickreply = $this->message_reply = $this->message_quote = '';
		if ($this->topic->authorise('reply')) {
			//user is allowed to reply/quote
			if ($me->userid) {
				$this->message_quickreply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->topic->category_id, $this->message->id, $this->getButton ( 'reply', JText::_('COM_KUNENA_BUTTON_QUICKREPLY') ), 'nofollow', 'kicon-button kbuttoncomm btn-left kqreply', JText::_('COM_KUNENA_BUTTON_QUICKREPLY_LONG'), ' id="kreply'.$this->message->id.'"' );
			}
			$this->message_reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->topic->category_id, $this->message->id, $this->getButton ( 'reply', JText::_('COM_KUNENA_BUTTON_REPLY') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_REPLY_LONG') );
			$this->message_quote = CKunenaLink::GetTopicPostReplyLink ( 'quote', $this->topic->category_id, $this->message->id, $this->getButton ( 'quote', JText::_('COM_KUNENA_BUTTON_QUOTE') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_QUOTE_LONG') );
		} else {
			//user is not allowed to write a post
			if ($this->topic->locked) {
				$this->message_closed = JText::_('COM_KUNENA_POST_LOCK_SET');
			} else {
				$this->message_closed = JText::_('COM_KUNENA_VIEW_DISABLED');
			}
		}

		//Offer an moderator a few tools
		$this->message_edit = $this->message_moderate = '';
		$this->message_delete = $this->message_undelete = $this->message_permdelete = $this->message_publish = '';
		if ($me->isModerator ( $this->topic->category_id )) {
			unset($this->message_closed);
			$this->message_edit = CKunenaLink::GetTopicPostReplyLink ( 'edit', $this->topic->category_id, $this->message->id, $this->getButton ( 'edit', JText::_('COM_KUNENA_BUTTON_EDIT') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_EDIT_LONG') );
			$this->message_moderate = CKunenaLink::GetTopicPostReplyLink ( 'moderate', $this->topic->category_id, $this->message->id, $this->getButton ( 'moderate', JText::_('COM_KUNENA_BUTTON_MODERATE') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MODERATE_LONG') );
			if ($this->message->hold == 1) {
				$this->message_publish = CKunenaLink::GetTopicPostLink ( 'approve', $this->topic->category_id, $this->message->id, $this->getButton ( 'approve', JText::_('COM_KUNENA_BUTTON_APPROVE') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_APPROVE_LONG') );
			}
			if ($this->message->hold == 2 || $this->message->hold == 3) {
				$this->message_undelete = CKunenaLink::GetTopicPostLink ( 'undelete', $this->topic->category_id, $this->message->id, $this->getButton ( 'undelete', JText::_('COM_KUNENA_BUTTON_UNDELETE') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG') );
				$this->message_permdelete = CKunenaLink::GetTopicPostLink ( 'permdelete', $this->topic->category_id, $this->message->id, $this->getButton ( 'permdelete', JText::_('COM_KUNENA_BUTTON_PERMDELETE') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG') );
			} else {
				$this->message_delete = CKunenaLink::GetTopicPostLink ( 'delete', $this->topic->category_id, $this->message->id, $this->getButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_LONG') );
			}
		}
		else if ($this->config->useredit && $me->userid && $me->userid == $this->profile->userid) {
			if ($this->message->authorise('edit')) {
				$this->message_edit = CKunenaLink::GetTopicPostReplyLink ( 'edit', $this->topic->category_id, $this->message->id, $this->getButton ( 'edit', JText::_('COM_KUNENA_BUTTON_EDIT') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_EDIT_LONG') );
				if ( $this->config->userdeletetmessage == '1' ) {
					if ($this->replynum == $this->replycnt) $this->message_delete = CKunenaLink::GetTopicPostLink ( 'delete', $this->topic->category_id, $this->message->id, $this->getButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_LONG') );
				} else if ( $this->config->userdeletetmessage == '2' ) {
					$this->message_delete = CKunenaLink::GetTopicPostLink ( 'delete', $this->topic->category_id, $this->message->id, $this->getButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_LONG') );
				}
			}
		}
		echo $this->loadTemplate("message_actions");
	}

	function displayMessage($id, $message, $template=null) {
		$layout = $this->getLayout();
		if (!$template) {
			$template = $this->state->get('profile.location');
			$this->setLayout('default');
		}
		$this->mmm ++;
		$this->message = $message;
		$this->profile = KunenaFactory::getUser($this->message->userid);
		$this->replynum = $id;

		// Link to individual message
		if ($this->config->ordering_system == 'replyid') {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink( $message->id, '#' . $id );
		} else {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink ( $message->id, '#' . $message->id );
		}

		if ($this->message->hold == 0) {
			$this->class = 'kmsg';
		} elseif ($this->message->hold == 1) {
			$this->class = 'kmsg kunapproved';
		} else if ($this->message->hold == 2 || $this->message->hold == 3) {
			$this->class = 'kmsg kunapproved kdeleted';
		}

		// New post suffix for class
		$this->msgsuffix = '';
		if ($this->message->isNew()) {
			$this->msgsuffix = '-new';
		}

		echo $this->loadtemplate($template);
		$this->setLayout($layout);
	}

	function displayMessages() {
		foreach ( $this->messages as $id=>$message ) {
			$this->displayMessage($id, $message);
		}
	}

	function getPagination($maxpages) {
		$catid = $this->state->get('item.catid');
		$threadid = $this->state->get('item.id');
		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');
		$page = intval($start/$limit)+1;
		$totalpages = intval(($this->total-1)/$limit)+1;

		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<ul class="kpagination">';
		$output .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';

		if ($startpage > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, 0, $limit, 1, '', $rel = 'follow' ) . '</li>';
			if ($startpage > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, ($i-1)*$limit, $limit, $i, '', $rel = 'follow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, ($totalpages-1)*$limit, $limit, $totalpages, '', $rel = 'follow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}

	// Helper functions


	function hasThreadHistory() {
		if (! $this->config->showhistory || !$this->topic->exists())
			return false;
		return true;
	}

	function displayThreadHistory() {
		if (! $this->config->showhistory || !$this->topic->exists())
			return;

		$db = JFactory::getDBO();
		$this->history = KunenaForumMessageHelper::getMessagesByTopic($this->topic, 0, 10, $ordering='DESC');
		$this->historycount = count ( $this->history );
		KunenaForumMessageAttachmentHelper::getByMessage($this->history);

		echo $this->loadTemplate ( 'history' );
	}

	public function hasCaptcha() {
		return KunenaCaptcha::enabled();
	}

	public function displayCaptcha() {
		return KunenaCaptcha::display();
	}

	function redirectBack() {
		$httpReferer = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );
		$app = JFactory::getApplication();
		$app->redirect ( $httpReferer );
	}

	public function getNumLink($mesid, $replycnt) {
		if ($this->config->ordering_system == 'replyid') {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink ( $mesid, '#' . $replycnt );
		} else {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink ( $mesid, '#' . $mesid );
		}

		return $this->numLink;
	}

	function displayAttachments($message=null) {
		if ($message instanceof KunenaForumMessage) {
			$this->attachments = $message->getAttachments();
			if (!empty($this->attachments)) echo $this->loadTemplate ( 'attachments' );
		} else {
			// FIXME:
			echo 'ERROR: no message provided (attachments)';
		}
	}

	function canSubscribe() {
		if (! $this->my->id || ! $this->config->allowsubscriptions)
			return false;
		$usertopic = $this->topic->getUserTopic ();
		return ! $usertopic->subscribed;
	}
}