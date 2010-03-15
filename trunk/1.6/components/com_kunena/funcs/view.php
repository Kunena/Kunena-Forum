<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined ( '_JEXEC' ) or die ();

class CKunenaView {
	public $allow = 0;

	function __construct($func, $catid, $id) {
		$this->func = $func;
		$this->catid = $catid;
		$this->id = $id;

		$this->db = JFactory::getDBO ();
		$this->session = CKunenaSession::getInstance ();

		$this->allow_forum = ($this->session->allowed != '') ? explode ( ',', $this->session->allowed ) : array ();

		$this->getView();
	}

	function getView() {
		// Is user allowed to see the forum specified in URL?
		if (! in_array ( $this->catid, $this->allow_forum )) {
			return;
		}
		$this->allow = 1;

		$this->my = JFactory::getUser ();
		if (!CKunenaTools::isModerator ( $this->my->id, $this->catid )) $where[] = "a.hold=0";
		else $where[] = "a.hold<=1";
		$where = implode(' AND ',$where); // always contains at least 1 item

		$query = "SELECT a.*, b.*, p.id AS poll_id, modified.name AS modified_name, modified.username AS modified_username
			FROM #__fb_messages AS a
			LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid
			LEFT JOIN #__users AS modified ON a.modified_by = modified.id
			LEFT JOIN #__fb_polls AS p ON a.id=p.threadid
			WHERE a.id='{$this->id}' AND {$where}";
		$this->db->setQuery ( $query );
		$this->first_message = $this->db->loadObject ();
		check_dberror ( 'Unable to load current message.' );

		// Invalid message id (deleted, on hold?)
		if (! $this->first_message)
			return;

		// Is user allowed to see the forum specified in the message?
		$this->catid = $this->first_message->catid;
		if (! in_array ( $this->catid, $this->allow_forum )) {
			$this->allow = 0;
			return;
		}

		$this->app = & JFactory::getApplication ();
		$this->config = CKunenaConfig::getInstance ();
		// Test if this is a valid URL. If not, redirect browser to the right location
		$this->thread = $this->first_message->parent == 0 ? $this->id : $this->first_message->thread;
		if ($this->first_message->moved || $this->thread != $this->id || $this->catid != $this->first_message->catid) {
			if ($this->first_message->moved) {
				$newurl = array();
				parse_str ( $this->first_message->message, $newloc );
				$this->id = $newloc ['id'];
				$query = "SELECT catid, thread FROM #__fb_messages AS a WHERE a.id='{$this->id}'";
				$this->db->setQuery ( $query );
				$newpos = $this->db->loadObject ();
				check_dberror ( 'Unable to calculate location of current message.' );
				$this->thread = $newpos->thread;
				$this->catid = $newpos->catid;
			}

			// This query to calculate the page this reply is sitting on within this thread
			$query = "SELECT COUNT(*) FROM #__fb_messages AS a WHERE a.thread='{$this->thread}' AND {$where} AND a.id<='{$this->id}'";
			$this->db->setQuery ( $query );
			$replyCount = $this->db->loadResult ();
			check_dberror ( 'Unable to calculate location of current message.' );

			$replyPage = $replyCount > $this->config->messages_per_page ? ceil ( $replyCount / $this->config->messages_per_page ) : 1;

			header ( "HTTP/1.1 301 Moved Permanently" );
			header ( "Location: " . htmlspecialchars_decode ( CKunenaLink::GetThreadPageURL ( $this->config, 'view', $this->catid, $this->thread, $replyPage, $this->config->messages_per_page, $this->first_message->id ) ) );

			$this->app->close ();
		}

		//Get the category name for breadcrumb
		$this->db->setQuery ( "SELECT * FROM #__fb_categories WHERE id='{$this->catid}'" );
		$this->catinfo = $this->db->loadObject ();
		check_dberror ( 'Unable to load category info' );
		//Get Parent's cat.name for breadcrumb
		$this->db->setQuery ( "SELECT id, name FROM #__fb_categories WHERE id='{$this->catinfo->parent}'" );
		$objCatParentInfo = $this->db->loadObject ();
		check_dberror ( 'Unable to load parent category info' );

		// START
		$this->emoticons = smile::getEmoticons ( 0 );
		$this->prevCheck = $this->session->lasttime;
		$this->read_topics = explode ( ',', $this->session->readtopics );

		$showedEdit = 0;
		$this->kunena_forum_locked = $this->catinfo->locked;

		//check if topic is locked
		$this->topicLocked = $this->first_message->locked;
		if (! $this->topicLocked) {
			//topic not locked; check if forum is locked
			$this->topicLocked = $this->catinfo->locked;
		}
		$this->topicSticky = $this->first_message->ordering;

		CKunenaTools::markTopicRead ( $this->thread, $this->my->id );

		//update the hits counter for this topic & exclude the owner
		if ($this->my->id == 0 || $this->first_message->userid != $this->my->id) {
			$this->db->setQuery ( "UPDATE #__fb_messages SET hits=hits+1 WHERE id='{$this->thread}' AND parent='0'" );
			$this->db->query ();
			check_dberror ( 'Unable to update message hits.' );
		}

		$query = "SELECT COUNT(*) FROM #__fb_messages AS a WHERE a.thread='{$this->thread}' AND {$where}";
		$this->db->setQuery ( $query );
		$this->total_messages = $this->db->loadResult ();
		check_dberror ( 'Unable to calculate message count.' );

		//prepare paging
		$limit = JRequest::getInt ( 'limit', 0 );
		if ($limit < 1)
			$limit = $this->config->messages_per_page;
		$limitstart = JRequest::getInt ( 'limitstart', 0 );
		if ($limitstart < 0)
			$limitstart = 0;
		if ($limitstart > $this->total_messages)
			$limitstart = intval ( $this->total_messages / $limit ) * $limit;
		$ordering = ($this->config->default_sort == 'desc' ? 'desc' : 'asc'); // Just to make sure only valid options make it
		$maxpages = 9 - 2; // odd number here (show - 2)
		$totalpages = ceil ( $this->total_messages / $limit );
		$page = floor ( $limitstart / $limit ) + 1;
		$firstpage = 1;
		if ($ordering == 'desc')
			$firstpage = $totalpages;

		$replylimit = $page == $firstpage ? $limit - 1 : $limit; // If page contains first message, load $limit-1 messages
		$replystart = $limitstart && $ordering == 'asc' ? $limitstart - 1 : $limitstart; // If not first page and order=asc, start on $limitstart-1
		// Get replies of current thread
		$query = "SELECT a.*, b.*, modified.name AS modified_name, modified.username AS modified_username
					FROM #__fb_messages AS a
					LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid
					LEFT JOIN #__users AS modified ON a.modified_by = modified.id
					WHERE a.thread='{$this->thread}' AND a.id!='{$this->id}' AND {$where}
					ORDER BY id {$ordering}";
		$this->db->setQuery ( $query, $replystart, $replylimit );
		$replies = $this->db->loadObjectList ();
		check_dberror ( 'Unable to load replies' );

		// Load attachments

		// First collect the message ids of the first message and all replies
		$messageids = array();
		$messageids[] = $this->id;
		foreach($replies AS $reply){
			$messageids[] = $reply->id;
		}

		// create a list of ids we can use for our sql
		$idstr = @join ( ",", $messageids );

		// now grab all attchments for these messages
		$query = "SELECT * FROM #__kunena_attachments
					WHERE mesid IN ($idstr)";
		$this->db->setQuery ( $query );
		$attachments = $this->db->loadObjectList ();
		check_dberror ( 'Unable to load attachments' );

		// arrange attachments by message
		$message_attachments = array();
		foreach ($attachments as $attachment) $message_attachments[$attachment->mesid][] = $attachment;

		$this->flat_messages = array ();
		if ($page == 1 && $ordering == 'asc')
			$this->flat_messages [] = $this->first_message; // ASC: first message is the first one
		foreach ( $replies as $message )
			$this->flat_messages [] = $message;
		if ($page == $totalpages && $ordering == 'desc')
			$this->flat_messages [] = $this->first_message; // DESC: first message is the last one
		unset ( $replies );

		// Now that we have all relevant messages in flat_messages, asign any matching attachments
		foreach ( $this->flat_messages as $message ){
			if (isset($message_attachments[$message->id]))
				$message->attachments = $message_attachments[$message->id];
		}
		// Done with attachments

		$this->pagination = $this->getPagination ( $this->catid, $this->thread, $page, $totalpages, $maxpages );

		//meta description and keywords
		$metaKeys = kunena_htmlspecialchars ( stripslashes ( "{$this->first_message->subject}, {$objCatParentInfo->name}, {$this->config->board_title}, " . JText::_('COM_KUNENA_GEN_FORUM') . ', ' . $this->app->getCfg ( 'sitename' ) ) );
		$metaDesc = kunena_htmlspecialchars ( stripslashes ( "{$this->first_message->subject} ({$page}/{$totalpages}) - {$objCatParentInfo->name} - {$this->catinfo->name} - {$this->config->board_title} " . JText::_('COM_KUNENA_GEN_FORUM') ) );

		$document = & JFactory::getDocument ();
		$cur = $document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );

		//Perform subscriptions check only once
		$fb_cansubscribe = 0;
		if ($this->config->allowsubscriptions && $this->my->id) {
			$this->db->setQuery ( "SELECT thread FROM #__fb_subscriptions WHERE userid='{$this->my->id}' AND thread='{$this->thread}'" );
			$fb_subscribed = $this->db->loadResult ();
			check_dberror ( 'Unable to load subscription' );

			if ($fb_subscribed == "") {
				$fb_cansubscribe = 1;
			}
		}
		//Perform favorites check only once
		$fb_canfavorite = 0;
		$this->db->setQuery ( "SELECT MAX(userid={$this->my->id}) AS favorited, COUNT(*) AS totalfavorited FROM #__fb_favorites WHERE thread='{$this->thread}'" );
		list ( $this->favorited, $this->totalfavorited ) = $this->db->loadRow ();
		check_dberror ( 'Unable to load favorite' );
		if ($this->config->allowfavorites && $this->my->id) {
			if (! $this->favorited) {
				$fb_canfavorite = 1;
			}
		}

		//get the Moderator list for display
		$this->db->setQuery ( "SELECT m.*, u.* FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid={$this->catid}" );
		$this->modslist = $this->db->loadObjectList ();
		check_dberror ( "Unable to load moderators." );
		$this->catModerators = array();
		foreach ($this->modslist as $mod) {
			$this->catModerators[] = $mod->userid;
		}

		//data ready display now
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || ($this->topicLocked == 0)) {
			//this user is allowed to reply to this topic
			$this->thread_reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->thread, CKunenaTools::showButton ( 'reply', JText::_('COM_KUNENA_BUTTON_REPLY_TOPIC') ), 'nofollow', 'buttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_REPLY_TOPIC_LONG') );
		}

		// Thread Subscription
		if ($fb_cansubscribe == 1) {
			// this user is allowed to subscribe - check performed further up to eliminate duplicate checks
			// for top and bottom navigation
			$this->thread_subscribe = CKunenaLink::GetTopicPostLink ( 'subscribe', $this->catid, $this->id, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_TOPIC') ), 'nofollow', 'buttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_TOPIC_LONG') );
		}

		if ($this->my->id != 0 && $this->config->allowsubscriptions && $fb_cansubscribe == 0) {
			// this user is allowed to unsubscribe
			$this->thread_subscribe = CKunenaLink::GetTopicPostLink ( 'unsubscribe', $this->catid, $this->id, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_TOPIC') ), 'nofollow', 'buttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_TOPIC_LONG') );
		}

		//START: FAVORITES
		if ($fb_canfavorite == 1) {
			// this user is allowed to add a favorite - check performed further up to eliminate duplicate checks
			// for top and bottom navigation
			$this->thread_favorite = CKunenaLink::GetTopicPostLink ( 'favorite', $this->catid, $this->id, CKunenaTools::showButton ( 'favorite', JText::_('COM_KUNENA_BUTTON_FAVORITE_TOPIC') ), 'nofollow', 'buttonuser btn-left', JText::_('COM_KUNENA_BUTTON_FAVORITE_TOPIC_LONG') );
		}

		if ($this->my->id != 0 && $this->config->allowfavorites && $fb_canfavorite == 0) {
			// this user is allowed to unfavorite
			$this->thread_favorite = CKunenaLink::GetTopicPostLink ( 'unfavorite', $this->catid, $this->id, CKunenaTools::showButton ( 'favorite', JText::_('COM_KUNENA_BUTTON_UNFAVORITE_TOPIC') ), 'nofollow', 'buttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNFAVORITE_TOPIC_LONG') );
		}
		// FINISH: FAVORITES


		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || !$this->kunena_forum_locked) {
			//this user is allowed to post a new topic
			$this->thread_new = CKunenaLink::GetPostNewTopicLink ( $this->catid, CKunenaTools::showButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'buttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}

		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			// offer the moderator always the move link to relocate a topic to another forum
			// and the (un)sticky bit links
			// and the (un)lock links
			$this->thread_move = CKunenaLink::GetTopicPostLink ( 'move', $this->catid, $this->id, CKunenaTools::showButton ( 'move', JText::_('COM_KUNENA_BUTTON_MOVE_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MOVE_TOPIC_LONG') );

			if ($this->topicSticky == 0) {
				$this->thread_sticky = CKunenaLink::GetTopicPostLink ( 'sticky', $this->catid, $this->id, CKunenaTools::showButton ( 'sticky', JText::_('COM_KUNENA_BUTTON_STICKY_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_STICKY_TOPIC_LONG') );
			} else {
				$this->thread_sticky = CKunenaLink::GetTopicPostLink ( 'unsticky', $this->catid, $this->id, CKunenaTools::showButton ( 'sticky', JText::_('COM_KUNENA_BUTTON_UNSTICKY_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_UNSTICKY_TOPIC_LONG') );
			}

			if ($this->topicLocked == 0) {
				$this->thread_lock = CKunenaLink::GetTopicPostLink ( 'lock', $this->catid, $this->id, CKunenaTools::showButton ( 'lock', JText::_('COM_KUNENA_BUTTON_LOCK_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_LOCK_TOPIC_LONG') );
			} else {
				$this->thread_lock = CKunenaLink::GetTopicPostLink ( 'unlock', $this->catid, $this->id, CKunenaTools::showButton ( 'lock', JText::_('COM_KUNENA_BUTTON_UNLOCK_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_UNLOCK_TOPIC_LONG') );
			}
			$this->thread_delete = CKunenaLink::GetTopicPostLink ( 'deletethread', $this->catid, $this->id, CKunenaTools::showButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_TOPIC_LONG') );
			$this->thread_merge = CKunenaLink::GetTopicPostLink ( 'mergethread', $this->catid, $this->id, CKunenaTools::showButton ( 'merge', JText::_('COM_KUNENA_BUTTON_MERGE_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MERGE_TOPIC_LONG') );
		}

		$this->headerdesc = nl2br ( stripslashes ( smile::smileReplace ( $this->catinfo->headerdesc, 0, $this->config->disemoticons, $this->emoticons ) ) );

		$tabclass = array ("sectiontableentry1", "sectiontableentry2" );

		$this->mmm = 0;

		$this->myname = $this->config->username ? $this->my->username : $this->my->name;
		$this->allow_anonymous = !empty($this->catinfo->allow_anonymous) && $this->my->id;
		$this->anonymous = ($this->allow_anonymous && !empty($this->catinfo->post_anonymous));
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayPoll() {
		if ($this->config->pollenabled == "1" && $this->first_message->poll_id) {
			CKunenaTools::loadTemplate('/plugin/poll/pollbox.php');
		}
	}

	function displayThreadActions() {
		CKunenaTools::loadTemplate('/view/thread.actions.php');
	}

	function displayMessageActions() {
		CKunenaTools::loadTemplate('/view/message.actions.php');
	}

	function displayMessageContents() {
		CKunenaTools::loadTemplate('/view/message.contents.php');
	}

	function displayProfileBox() {
		CKunenaTools::loadTemplate('/view/message.profilebox.php');
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
	}

	function displayMessage($message) {
		global $kunena_icons;
		$this->kunena_message = $message;
		$this->mmm ++;

		if ($this->kunena_message->parent == 0) {
			$fb_thread = $this->kunena_message->id;
		} else {
			$fb_thread = $this->kunena_message->thread;
		}

		//filter out clear html
		$this->kunena_message->name = kunena_htmlspecialchars ( $this->kunena_message->name );
		$this->kunena_message->email = kunena_htmlspecialchars ( $this->kunena_message->email );
		$this->kunena_message->subject = kunena_htmlspecialchars ( $this->kunena_message->subject );

		//Get userinfo needed later on, this limits the amount of queries
		static $uinfocache = array ();
		if (! isset ( $uinfocache [$this->kunena_message->userid] )) {
			$this->db->setQuery ( "SELECT  a.*, b.id, b.name, b.username, b.gid FROM #__fb_users AS a INNER JOIN #__users AS b ON b.id=a.userid WHERE a.userid='{$this->kunena_message->userid}'" );
			$userinfo = $this->db->loadObject ();
			check_dberror ( 'Unable to load user info' );
			if ($userinfo == NULL) {
				$userinfo = new stdClass ( );
				$userinfo->userid = 0;
				$userinfo->name = '';
				$userinfo->username = '';
				$userinfo->avatar = '';
				$userinfo->gid = 0;
				$userinfo->rank = 0;
				$userinfo->posts = 0;
				$userinfo->karma = 0;
				$userinfo->gender = JText::_('COM_KUNENA_NOGENDER');
				$userinfo->personalText = '';
				$userinfo->ICQ = '';
				$userinfo->location = '';
				$userinfo->birthdate = '';
				$userinfo->AIM = '';
				$userinfo->MSN = '';
				$userinfo->YIM = '';
				$userinfo->SKYPE = '';
				$userinfo->TWITTER = '';
				$userinfo->FACEBOOK = '';
				$userinfo->GTALK = '';
				$userinfo->MYSPACE = '';
				$userinfo->LINKEDIN = '';
				$userinfo->DELICIOUS = '';
				$userinfo->FRIENDFEED = '';
				$userinfo->DIGG = '';
				$userinfo->BLOGSPOT = '';
				$userinfo->FLICKR = '';
				$userinfo->BEBO = '';
				$userinfo->websiteurl = '';
				$userinfo->signature = '';
			}
			$uinfocache [$this->kunena_message->userid] = $userinfo;
		} else
			$userinfo = $uinfocache [$this->kunena_message->userid];

		$this->userinfo = $userinfo;

		// FIXME: reduce number of queries by preload:
		$this->profile = CKunenaUserprofile::getInstance ( $this->kunena_message->userid );

		if ($this->config->fb_profile == 'cb') {
			$triggerParams = array ('userid' => $this->kunena_message->userid, 'userinfo' => &$this->userinfo );
			$kunenaProfile = & CKunenaCBProfile::getInstance ();
			$kunenaProfile->trigger ( 'profileIntegration', $triggerParams );
		}

		//get the username:
		if ($this->config->username) {
			$fb_queryName = "username";
		} else {
			$fb_queryName = "name";
		}

		$fb_username = $this->userinfo->$fb_queryName;

		if ($fb_username == "" || $this->config->changename) {
			$fb_username = stripslashes ( $this->kunena_message->name );
		}
		$fb_username = kunena_htmlspecialchars ( $fb_username );

		$this->msg_html = new StdClass ( );
		$this->msg_html->id = $this->kunena_message->id;
		$lists ["userid"] = $this->userinfo->userid;
		$this->msg_html->username = $this->kunena_message->email != "" && $this->my->id > 0 && $this->config->showemail ? CKunenaLink::GetEmailLink ( kunena_htmlspecialchars ( stripslashes ( $this->kunena_message->email ) ), $fb_username ) : $fb_username;

		if ($this->config->allowavatar) {
			$Avatarname = $this->userinfo->username;
			$kunena_config = & CKunenaConfig::getInstance ();

			if ($kunena_config->avposition == 'left' || $kunena_config->avposition == 'right') {
				$avwidth = $kunena_config->avatarwidth;
				$avheight = $kunena_config->avatarwidth;
			} else {
				$avwidth = $kunena_config->avatarsmallwidth;
				$avheight = $kunena_config->avatarsmallwidth;
			}

			if ($this->config->avatar_src == "jomsocial") {
				// Get CUser object
				$jsuser = & CFactory::getUser ( $this->userinfo->userid );
				$this->msg_html->avatar = '<span class="kavatar"><img src="' . $jsuser->getThumbAvatar () . '" alt=" " /></span>';
			} else if ($this->config->avatar_src == "cb") {
				$kunenaProfile = & CkunenaCBProfile::getInstance ();
				$this->msg_html->avatar = '<span class="kavatar">' . $kunenaProfile->showAvatar ( $this->userinfo->userid, '', false ) . '</span>';
			} else if ($this->config->avatar_src == "aup") {
				$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
				if (file_exists ( $api_AUP )) {
					($this->config->fb_profile == 'aup') ? $showlink = 1 : $showlink = 0;
					$this->msg_html->avatar = '<span class="kavatar">' . AlphaUserPointsHelper::getAupAvatar ( $this->userinfo->userid, $showlink ) . '</span>';
				}
			} else {
				$avatar = $this->userinfo->avatar;

				if (! empty ( $avatar )) {
					if (! file_exists ( KUNENA_PATH_UPLOADED . DS . 'avatars/s_' . $avatar )) {
						$this->msg_html->avatar = '<span class="kavatar"><img border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" style="max-width: ' . $avwidth . 'px; max-height: ' . $avheight . 'px;" /></span>';
					} else {
						$this->msg_html->avatar = '<span class="kavatar"><img border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" style="max-width: ' . $avwidth . 'px; max-height: ' . $avheight . 'px;" /></span>';
					}
				} else {
					if ($kunena_config->avposition == 'left' || $kunena_config->avposition == 'right') {
						$this->msg_html->avatar = '<span class="kavatar"><img  border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/nophoto.jpg" alt="" style="max-width: ' . $avwidth . 'px; max-height: ' . $avheight . 'px;" /></span>';
					} else {
						$this->msg_html->avatar = '<span class="kavatar"><img  border="0" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" style="max-width: ' . $avwidth . 'px; max-height: ' . $avheight . 'px;" /></span>';
					}
				}
			}
		} else {
			$this->msg_html->avatar = '';
		}

		if ($this->config->showuserstats) {
			//user type determination
			$ugid = $this->userinfo->gid;
			$uIsMod = 0;
			$uIsAdm = 0;
			$uIsMod = in_array ( $this->userinfo->userid, $this->catModerators );

			if ($ugid == 0) {
				$this->msg_html->usertype = JText::_('COM_KUNENA_VIEW_VISITOR');
			} else {
				if (CKunenaTools::isAdmin ( $this->userinfo->id )) {
					$this->msg_html->usertype = JText::_('COM_KUNENA_VIEW_ADMIN');
					$uIsAdm = 1;
				} elseif ($uIsMod) {
					$this->msg_html->usertype = JText::_('COM_KUNENA_VIEW_MODERATOR');
				} else {
					$this->msg_html->usertype = JText::_('COM_KUNENA_VIEW_USER');
				}
			}

			//done usertype determination, phew...
			//# of post for this user and ranking
			if ($this->userinfo->userid) {
				$numPosts = ( int ) $this->userinfo->posts;

				//ranking
				$rank = $this->profile->getRank ($this->catid);
				if ($rank->rank_image) {
					$this->msg_html->userrankimg = '<img src="' . KUNENA_URLRANKSPATH . $rank->rank_image . '" alt="" />';
				}
				if ($rank->rank_title) {
					$this->msg_html->userrank = $rank->rank_title;
				}

				$this->msg_html->posts = "" . JText::_('COM_KUNENA_POSTS') . " $numPosts" . "";
			}
		}
		// Start Integration AlphaUserPoints
		// ****************************
		$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if ($this->config->alphauserpoints && file_exists ( $api_AUP )) {
			$this->db->setQuery ( "SELECT points FROM #__alpha_userpoints WHERE `userid`='" . ( int ) $this->kunena_message->userid . "'" );
			$numPoints = $this->db->loadResult ();
			check_dberror ( "Unable to load AUP points." );

			$this->msg_html->points = '' . JText::_('COM_KUNENA_AUP_POINTS') . ' ' . $numPoints;
		}
		// End Integration AlphaUserPoints


		//karma points and buttons
		if ($this->config->showkarma && $this->userinfo->userid != '0') {
			$karmaPoints = $this->userinfo->karma;
			$karmaPoints = ( int ) $karmaPoints;
			$this->msg_html->karma = "<strong>" . JText::_('COM_KUNENA_KARMA') . ":</strong> $karmaPoints";

			if ($this->my->id != '0' && $this->my->id != $this->userinfo->userid) {
				$this->msg_html->karmaminus = CKunenaLink::GetKarmaLink ( 'decrease', $this->catid, $this->kunena_message->id, $this->userinfo->userid, '<img src="' . (isset ( $kunena_icons ['karmaminus'] ) ? (KUNENA_URLICONSPATH . $kunena_icons ['karmaminus']) : (KUNENA_URLEMOTIONSPATH . "karmaminus.gif")) . '" alt="Karma-" border="0" title="' . JText::_('COM_KUNENA_KARMA_SMITE') . '" />' );
				$this->msg_html->karmaplus = CKunenaLink::GetKarmaLink ( 'increase', $this->catid, $this->kunena_message->id, $this->userinfo->userid, '<img src="' . (isset ( $kunena_icons ['karmaplus'] ) ? (KUNENA_URLICONSPATH . $kunena_icons ['karmaplus']) : (KUNENA_URLEMOTIONSPATH . "karmaplus.gif")) . '" alt="Karma+" border="0" title="' . JText::_('COM_KUNENA_KARMA_APPLAUD') . '" />' );
			}
		}

		/*let's see if we should use uddeIM integration */
		if ($this->config->pm_component == "uddeim" && $this->userinfo->userid && $this->my->id) {
			//we should offer the user a PMS link
			//first get the username of the user to contact
			$PMSName = $this->userinfo->username;
			$img_html = "<img src=\"";

			if ($kunena_icons ['pms']) {
				$img_html .= KUNENA_URLICONSPATH . $kunena_icons ['pms'];
			} else {
				$img_html .= KUNENA_URLEMOTIONSPATH . "pm.png";
			}

			$img_html .= "\" alt=\"" . JText::_('COM_KUNENA_VIEW_PMS') . "\" border=\"0\" title=\"" . JText::_('COM_KUNENA_VIEW_PMS') . "\" />";
			$this->msg_html->pms = CKunenaLink::GetUddeImLink( $this->userinfo->userid, $img_html );
		}
		// online - ofline status
		$this->msg_html->online = 0;
		if ($this->userinfo->userid > 0 && $this->userinfo->showOnline == 1) {
			static $onlinecache = array ();
			if (! isset ( $onlinecache [$this->userinfo->userid] )) {
				$query = 'SELECT MAX(s.time) FROM #__session AS s WHERE s.userid = ' . $this->userinfo->userid . ' AND s.client_id = 0 GROUP BY s.userid';
				$this->db->setQuery ( $query );
				$lastseen = $this->db->loadResult ();
				check_dberror ( "Unable get user online information." );
				$timeout = $this->app->getCfg ( 'lifetime', 15 ) * 60;
				$onlinecache [$this->userinfo->userid] = ($lastseen + $timeout) > time ();
			}
			$this->msg_html->online = $onlinecache [$this->userinfo->userid];
		}

		/* PM integration */
		if ($this->config->pm_component == "jomsocial" && $this->userinfo->userid && $this->my->id) {
			$onclick = CMessaging::getPopup ( $this->userinfo->userid );
			$this->msg_html->pms = '<a href="javascript:void(0)" onclick="' . $onclick . "\">";

			if ($kunena_icons ['pms']) {
				$this->msg_html->pms .= "<img src=\"" . KUNENA_URLICONSPATH . $kunena_icons ['pms'] . "\" alt=\"" . JText::_('COM_KUNENA_VIEW_PMS') . "\" border=\"0\" title=\"" . JText::_('COM_KUNENA_VIEW_PMS') . "\" />";
			} else {
				$this->msg_html->pms .= JText::_('COM_KUNENA_VIEW_PMS');
			}

			$this->msg_html->pms .= "</a>";
		}
		//Check if the Integration settings are on, and set the variables accordingly.
		if ($this->config->fb_profile == "cb") {
			if ($this->config->fb_profile == 'cb' && $this->userinfo->userid > 0) {
				$this->msg_html->prflink = CKunenaCBProfile::getProfileURL ( $this->userinfo->userid );
				$this->msg_html->profile = "<a href=\"" . $this->msg_html->prflink . "\"><img src=\"";

				if ($kunena_icons ['userprofile']) {
					$this->msg_html->profile .= KUNENA_URLICONSPATH . $kunena_icons ['userprofile'];
				} else {
					$this->msg_html->profile .= KUNENA_JLIVEURL . "/components/com_comprofiler/images/profiles.gif";
				}

				$this->msg_html->profile .= "\" alt=\"" . JText::_('COM_KUNENA_VIEW_PROFILE') . "\" border=\"0\" title=\"" . JText::_('COM_KUNENA_VIEW_PROFILE') . "\" /></a>";
			}
		} else if ($this->userinfo->gid > 0) {
			//Kunena Profile link.
			$this->msg_html->profileicon = "<img src=\"";

			if ($kunena_icons ['userprofile']) {
				$this->msg_html->profileicon .= KUNENA_URLICONSPATH . $kunena_icons ['userprofile'];
			} else {
				$this->msg_html->profileicon .= KUNENA_URLICONSPATH . "profile.gif";
			}

			$this->msg_html->profileicon .= "\" alt=\"" . JText::_('COM_KUNENA_VIEW_PROFILE') . "\" border=\"0\" title=\"" . JText::_('COM_KUNENA_VIEW_PROFILE') . "\" />";
			$this->msg_html->profile = CKunenaLink::GetProfileLink ( $this->config, $this->userinfo->userid, $this->msg_html->profileicon );
		}

		if ($this->userinfo->personalText != '') {
			$this->msg_html->personal = kunena_htmlspecialchars ( CKunenaTools::parseText ( $this->userinfo->personalText ) );
		}

		//Show admins the IP address of the user:
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->msg_html->ip = $this->kunena_message->ip;
		}

		$this->msg_html->subject = CKunenaTools::parseText ( $this->kunena_message->subject );
		$this->msg_html->text = CKunenaTools::parseBBCode ( $this->kunena_message->message );

		// Add attachments
		if (isset($this->kunena_message->attachments)) {
			$this->msg_html->attachments = array();

			foreach($this->kunena_message->attachments as $attachment)
			{
				// Check if file has been pre-processed
				if (is_null($attachment->hash)){
					// This attachment has not been processed.
					// It migth be a legacy file, or the settings might have been reset.
					// Force recalculation ...

					// TODO: Perform image re-prosessing
				}

				// shorttype based on MIME type to determine if image for displaying purposes
				$attachment->shorttype = (stripos($attachment->filetype, 'image/') !== false) ? 'image' : $attachment->filetype;

				$this->msg_html->attachments[] = $attachment;
			}
		}

		$this->msg_html->signature = CKunenaTools::parseBBCode ( $this->userinfo->signature );

		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || ($this->topicLocked == 0)) {
			//user is allowed to reply/quote
			if ($this->my->id > 0) {
				$this->msg_html->quickreply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'reply', JText::_('COM_KUNENA_BUTTON_QUICKREPLY') ), 'nofollow', 'buttoncomm btn-left kqreply', JText::_('COM_KUNENA_BUTTON_QUICKREPLY_LONG'), ' id="kreply'.$this->kunena_message->id.'"' );
			}
			$this->msg_html->reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'reply', JText::_('COM_KUNENA_BUTTON_REPLY') ), 'nofollow', 'buttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_REPLY_LONG') );
			$this->msg_html->quote = CKunenaLink::GetTopicPostReplyLink ( 'quote', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'quote', JText::_('COM_KUNENA_BUTTON_QUOTE') ), 'nofollow', 'buttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_QUOTE_LONG') );
		} else {
			//user is not allowed to write a post
			if ($this->topicLocked) {
				$this->msg_html->closed = JText::_('COM_KUNENA_POST_LOCK_SET');
			} else {
				$this->msg_html->closed = JText::_('COM_KUNENA_VIEW_DISABLED');
			}
		}

		$showedEdit = 0; //reset this value
		$this->msg_html->class = 'class="kmsg"';

		//Offer an moderator a few tools
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->msg_html->edit = CKunenaLink::GetTopicPostLink ( 'edit', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'edit', JText::_('COM_KUNENA_BUTTON_EDIT') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_EDIT_LONG') );
			$this->msg_html->delete = CKunenaLink::GetTopicPostLink ( 'delete', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_LONG') );
			$this->msg_html->split = CKunenaLink::GetTopicPostLink ( 'split', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'split', JText::_('COM_KUNENA_BUTTON_SPLIT_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_SPLIT_TOPIC_LONG') );
			$this->msg_html->merge = CKunenaLink::GetTopicPostLink ( 'merge', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'merge', JText::_('COM_KUNENA_BUTTON_MERGE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MERGE_LONG') );
			if ($this->kunena_message->hold == 1) {
				$this->msg_html->publish = CKunenaLink::GetTopicPostReplyLink ( 'approve', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'approve', JText::_('COM_KUNENA_BUTTON_APPROVE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_APPROVE_LONG') );
				$this->msg_html->class = 'class="kmsg kunapproved"';
			}
		}
		else if ($this->config->useredit && $this->my->id && $this->my->id == $this->profile->userid) {
			//Now, if the viewer==author and the viewer is allowed to edit his/her own post then offer an 'edit' link
			if (CKunenaTools::editTimeCheck($this->kunena_message->modified_time, $this->kunena_message->time)) {
				$this->msg_html->edit = CKunenaLink::GetTopicPostLink ( 'edit', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'edit', JText::_('COM_KUNENA_BUTTON_EDIT') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_EDIT_LONG') );
				$this->msg_html->delete = CKunenaLink::GetTopicPostLink ( 'deleteownpost', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_LONG') );
			}
		}
		CKunenaTools::loadTemplate('/view/message.php');
	}

	function getPagination($catid, $threadid, $page, $totalpages, $maxpages) {
		$kunena_config = & CKunenaConfig::getInstance ();

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
			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $catid, $threadid, 1, $kunena_config->messages_per_page, 1, '', $rel = 'follow' ) . '</li>';
			if ($startpage > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $catid, $threadid, $i, $kunena_config->messages_per_page, $i, '', $rel = 'follow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $catid, $threadid, $totalpages, $kunena_config->messages_per_page, $totalpages, '', $rel = 'follow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}

	function display() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		if (! $this->first_message) {
			echo JText::_('COM_KUNENA_MODERATION_INVALID_ID');
			return;
		}
		CKunenaTools::loadTemplate('/view/view.php');
	}
}
