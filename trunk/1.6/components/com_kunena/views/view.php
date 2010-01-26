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

	function __construct($catid, $id) {
		$this->id = $id;
		$this->catid = $catid;

		$this->db = JFactory::getDBO ();
		$this->session = CKunenaSession::getInstance ();
		$allow_forum = ($this->session->allowed != '') ? explode ( ',', $this->session->allowed ) : array ();

		// Is user allowed to see the forum specified in URL?
		if (! in_array ( $this->catid, $allow_forum )) {
			return;
		}
		$this->allow = 1;

		$query = "SELECT a.*, b.*, p.id AS poll_id, modified.name AS modified_name, modified.username AS modified_username
			FROM #__fb_messages AS a
			LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid
			LEFT JOIN #__users AS modified ON a.modified_by = modified.id
			LEFT JOIN #__fb_polls AS p ON a.id=p.threadid
			WHERE a.id='$this->id' AND a.hold='0'";
		$this->db->setQuery ( $query );
		$this->first_message = $this->db->loadObject ();
		check_dberror ( 'Unable to load current message.' );

		// Invalid message id (deleted, on hold?)
		if (! $this->first_message)
			return;

		// Is user allowed to see the forum specified in the message?
		$this->catid = $this->first_message->catid;
		if (! in_array ( $this->catid, $allow_forum )) {
			$this->allow = 0;
			return;
		}

		$kunena_app = & JFactory::getApplication ();
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
			$query = "SELECT COUNT(*) FROM #__fb_messages AS a WHERE a.thread='{$this->thread}' AND hold='0' AND a.id<='{$this->id}'";
			$this->db->setQuery ( $query );
			$replyCount = $this->db->loadResult ();
			check_dberror ( 'Unable to calculate location of current message.' );

			$replyPage = $replyCount > $this->config->messages_per_page ? ceil ( $replyCount / $this->config->messages_per_page ) : 1;

			header ( "HTTP/1.1 301 Moved Permanently" );
			header ( "Location: " . htmlspecialchars_decode ( CKunenaLink::GetThreadPageURL ( $this->config, 'view', $this->catid, $this->thread, $replyPage, $this->config->messages_per_page, $this->first_message->id ) ) );

			$kunena_app->close ();
		}

		// START
		$this->my = JFactory::getUser ();

		$this->emoticons = smile::getEmoticons ( 0 );
		$this->prevCheck = $this->session->lasttime;
		$this->read_topics = explode ( ',', $this->session->readtopics );

		$showedEdit = 0;
		$this->kunena_forum_locked = 0;

		$this->topicLocked = $this->first_message->locked;
		$topicSticky = $this->first_message->ordering;

		CKunenaTools::markTopicRead ( $this->thread, $this->my->id );

		//update the hits counter for this topic & exclude the owner
		if ($this->my->id == 0 || $this->first_message->userid != $this->my->id) {
			$this->db->setQuery ( "UPDATE #__fb_messages SET hits=hits+1 WHERE id='{$this->thread}' AND parent='0'" );
			$this->db->query ();
			check_dberror ( 'Unable to update message hits.' );
		}

		$query = "SELECT COUNT(*) FROM #__fb_messages AS a WHERE a.thread='{$this->thread}' AND hold='0'";
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
					WHERE a.thread='$this->thread' AND a.id!='$this->id' AND a.hold='0'
					AND a.catid='$this->catid' ORDER BY id $ordering";
		$this->db->setQuery ( $query, $replystart, $replylimit );
		$replies = $this->db->loadObjectList ();
		check_dberror ( 'Unable to load replies' );

		$this->flat_messages = array ();
		if ($page == 1 && $ordering == 'asc')
			$this->flat_messages [] = $this->first_message; // ASC: first message is the first one
		foreach ( $replies as $message )
			$this->flat_messages [] = $message;
		if ($page == $totalpages && $ordering == 'desc')
			$this->flat_messages [] = $this->first_message; // DESC: first message is the last one
		unset ( $replies );

		$this->pagination = $this->getPagination ( $this->catid, $this->thread, $page, $totalpages, $maxpages );

		//Get the category name for breadcrumb
		$this->db->setQuery ( "SELECT * FROM #__fb_categories WHERE id='{$this->catid}'" );
		$this->catinfo = $this->db->loadObject ();
		check_dberror ( 'Unable to load category info' );
		//Get Parent's cat.name for breadcrumb
		$this->db->setQuery ( "SELECT id, name FROM #__fb_categories WHERE id='{$this->catinfo->parent}'" );
		$objCatParentInfo = $this->db->loadObject ();
		check_dberror ( 'Unable to load parent category info' );

		$this->kunena_forum_locked = $this->catinfo->locked;

		//meta description and keywords
		$metaKeys = kunena_htmlspecialchars ( stripslashes ( "{$this->first_message->subject}, {$objCatParentInfo->name}, {$this->config->board_title}, " . _GEN_FORUM . ', ' . $kunena_app->getCfg ( 'sitename' ) ) );
		$metaDesc = kunena_htmlspecialchars ( stripslashes ( "{$this->first_message->subject} ({$page}/{$totalpages}) - {$objCatParentInfo->name} - {$this->catinfo->name} - {$this->config->board_title} " . _GEN_FORUM ) );

		$document = & JFactory::getDocument ();
		$cur = $document->get ( 'description' );
		$metaDesc = $cur . '. ' . $metaDesc;
		$document->setMetadata ( 'keywords', $metaKeys );
		$document->setDescription ( $metaDesc );

		//Perform subscriptions check only once
		$fb_cansubscribe = 0;
		if ($this->config->allowsubscriptions && ("" != $this->my->id || 0 != $this->my->id)) {
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
		if ($this->config->allowfavorites && ("" != $this->my->id || 0 != $this->my->id)) {
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
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || ($this->kunena_forum_locked == 0 && $this->topicLocked == 0)) {
			//this user is allowed to reply to this topic
			$this->thread_reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->thread, CKunenaTools::showButton ( 'reply', _KUNENA_BUTTON_REPLY_TOPIC ), 'nofollow', 'buttoncomm btn-left', _KUNENA_BUTTON_REPLY_TOPIC_LONG );
		}

		// Thread Subscription
		if ($fb_cansubscribe == 1) {
			// this user is allowed to subscribe - check performed further up to eliminate duplicate checks
			// for top and bottom navigation
			$this->thread_subscribe = CKunenaLink::GetTopicPostLink ( 'subscribe', $this->catid, $this->id, CKunenaTools::showButton ( 'subscribe', _KUNENA_BUTTON_SUBSCRIBE_TOPIC ), 'nofollow', 'buttonuser btn-left', _KUNENA_BUTTON_SUBSCRIBE_TOPIC_LONG );
		}

		if ($this->my->id != 0 && $this->config->allowsubscriptions && $fb_cansubscribe == 0) {
			// this user is allowed to unsubscribe
			$this->thread_subscribe = CKunenaLink::GetTopicPostLink ( 'unsubscribe', $this->catid, $this->id, CKunenaTools::showButton ( 'subscribe', _KUNENA_BUTTON_UNSUBSCRIBE_TOPIC ), 'nofollow', 'buttonuser btn-left', _KUNENA_BUTTON_UNSUBSCRIBE_TOPIC_LONG );
		}

		//START: FAVORITES
		if ($fb_canfavorite == 1) {
			// this user is allowed to add a favorite - check performed further up to eliminate duplicate checks
			// for top and bottom navigation
			$this->thread_favorite = CKunenaLink::GetTopicPostLink ( 'favorite', $this->catid, $this->id, CKunenaTools::showButton ( 'favorite', _KUNENA_BUTTON_FAVORITE_TOPIC ), 'nofollow', 'buttonuser btn-left', _KUNENA_BUTTON_FAVORITE_TOPIC_LONG );
		}

		if ($this->my->id != 0 && $this->config->allowfavorites && $fb_canfavorite == 0) {
			// this user is allowed to unfavorite
			$this->thread_favorite = CKunenaLink::GetTopicPostLink ( 'unfavorite', $this->catid, $this->id, CKunenaTools::showButton ( 'favorite', _KUNENA_BUTTON_UNFAVORITE_TOPIC ), 'nofollow', 'buttonuser btn-left', _KUNENA_BUTTON_UNFAVORITE_TOPIC_LONG );
		}
		// FINISH: FAVORITES


		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || ($this->kunena_forum_locked == 0)) {
			//this user is allowed to post a new topic
			$this->thread_new = CKunenaLink::GetPostNewTopicLink ( $this->catid, CKunenaTools::showButton ( 'newtopic', _KUNENA_BUTTON_NEW_TOPIC ), 'nofollow', 'buttoncomm btn-left', _KUNENA_BUTTON_NEW_TOPIC_LONG );
		}

		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			// offer the moderator always the move link to relocate a topic to another forum
			// and the (un)sticky bit links
			// and the (un)lock links
			$this->thread_move = CKunenaLink::GetTopicPostLink ( 'move', $this->catid, $this->id, CKunenaTools::showButton ( 'move', _KUNENA_BUTTON_MOVE_TOPIC ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_MOVE_TOPIC_LONG );

			if ($topicSticky == 0) {
				$this->thread_sticky = CKunenaLink::GetTopicPostLink ( 'sticky', $this->catid, $this->id, CKunenaTools::showButton ( 'sticky', _KUNENA_BUTTON_STICKY_TOPIC ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_STICKY_TOPIC_LONG );
			} else {
				$this->thread_sticky = CKunenaLink::GetTopicPostLink ( 'unsticky', $this->catid, $this->id, CKunenaTools::showButton ( 'sticky', _KUNENA_BUTTON_UNSTICKY_TOPIC ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_UNSTICKY_TOPIC_LONG );
			}

			if ($this->topicLocked == 0) {
				$this->thread_lock = CKunenaLink::GetTopicPostLink ( 'lock', $this->catid, $this->id, CKunenaTools::showButton ( 'lock', _KUNENA_BUTTON_LOCK_TOPIC ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_LOCK_TOPIC_LONG );
			} else {
				$this->thread_lock = CKunenaLink::GetTopicPostLink ( 'unlock', $this->catid, $this->id, CKunenaTools::showButton ( 'lock', _KUNENA_BUTTON_UNLOCK_TOPIC ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_UNLOCK_TOPIC_LONG );
			}
			$this->thread_delete = CKunenaLink::GetTopicPostLink ( 'delete', $this->catid, $this->id, CKunenaTools::showButton ( 'delete', _KUNENA_BUTTON_DELETE_TOPIC ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_DELETE_TOPIC_LONG );
			$this->thread_merge = CKunenaLink::GetTopicPostLink ( 'merge', $this->catid, $this->id, CKunenaTools::showButton ( 'merge', _KUNENA_BUTTON_MERGE_TOPIC ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_MERGE_TOPIC_LONG );
		}

		$this->headerdesc = nl2br ( stripslashes ( smile::smileReplace ( $this->catinfo->headerdesc, 0, $this->config->disemoticons, $this->emoticons ) ) );

		$tabclass = array ("sectiontableentry1", "sectiontableentry2" );

		$this->mmm = 0;

		//check if topic is locked
		$this->topicLocked = $this->first_message->locked;
		if (! $this->topicLocked) {
			//topic not locked; check if forum is locked
			$this->topicLocked = $this->catinfo->locked;
		}
	}

	function displayPathway() {
		if (file_exists ( KUNENA_ABSTMPLTPATH . '/pathway.php' )) {
			require_once (KUNENA_ABSTMPLTPATH . '/pathway.php');
		} else {
			require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'pathway.php');
		}
	}

	function displayPoll() {
		if ($this->config->pollenabled == "1" && $this->first_message->poll_id) {
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/poll/pollbox.php' )) {
				require_once (KUNENA_ABSTMPLTPATH . '/plugin/poll/pollbox.php');
			} else {
				require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/poll/pollbox.php');
			}
		}
	}

	function displayThreadActions() {
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'thread.actions.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'thread.actions.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'view' . DS . 'thread.actions.php');
		}
	}

	function displayMessageActions() {
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.actions.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.actions.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'view' . DS . 'message.actions.php');
		}
	}

	function displayMessageContents() {
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.contents.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.contents.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'view' . DS . 'message.contents.php');
		}
	}

	function displayProfileBox() {
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.profilebox.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.profilebox.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'view' . DS . 'message.profilebox.php');
		}
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'forumjump.php' )) {
				include (KUNENA_ABSTMPLTPATH . DS . 'forumjump.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'forumjump.php');
			}
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
				$userinfo->gender = _KUNENA_NOGENDER;
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
			$kunenaProfile = & CkunenaCBProfile::getInstance ();
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
				$this->msg_html->usertype = _VIEW_VISITOR;
			} else {
				if (CKunenaTools::isAdmin ( $this->userinfo->id )) {
					$this->msg_html->usertype = _VIEW_ADMIN;
					$uIsAdm = 1;
				} elseif ($uIsMod) {
					$this->msg_html->usertype = _VIEW_MODERATOR;
				} else {
					$this->msg_html->usertype = _VIEW_USER;
				}
			}

			//done usertype determination, phew...
			//# of post for this user and ranking
			if ($this->userinfo->userid) {
				$numPosts = ( int ) $this->userinfo->posts;

				//ranking
				if ($this->config->showranking) {
					$rank = CKunenaTools::getRank ( $this->profile );

					if ($this->config->rankimages && isset ( $rank->rank_image )) {
						$this->msg_html->userrankimg = '<img src="' . $rank->rank_image . '" alt="" />';
					}
					$this->msg_html->userrank = $rank->rank_title;
				}

				$this->msg_html->posts = '<div class="viewcover">' . "<strong>" . _POSTS . " $numPosts" . "</strong>" . "</div>";
			}
		}
		// Start Integration AlphaUserPoints
		// ****************************
		$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if ($this->config->alphauserpoints && file_exists ( $api_AUP )) {
			$this->db->setQuery ( "SELECT points FROM #__alpha_userpoints WHERE `userid`='" . ( int ) $this->kunena_message->userid . "'" );
			$numPoints = $this->db->loadResult ();
			check_dberror ( "Unable to load AUP points." );

			$this->msg_html->points = '</strong>' . _KUNENA_AUP_POINTS . '</strong> ' . $numPoints;
		}
		// End Integration AlphaUserPoints


		//karma points and buttons
		if ($this->config->showkarma && $this->userinfo->userid != '0') {
			$karmaPoints = $this->userinfo->karma;
			$karmaPoints = ( int ) $karmaPoints;
			$this->msg_html->karma = "<strong>" . _KARMA . ":</strong> $karmaPoints";

			if ($this->my->id != '0' && $this->my->id != $this->userinfo->userid) {
				$this->msg_html->karmaminus = CKunenaLink::GetKarmaLink ( 'decrease', $this->catid, $this->kunena_message->id, $this->userinfo->userid, '<img src="' . (isset ( $kunena_icons ['karmaminus'] ) ? (KUNENA_URLICONSPATH . $kunena_icons ['karmaminus']) : (KUNENA_URLEMOTIONSPATH . "karmaminus.gif")) . '" alt="Karma-" border="0" title="' . _KARMA_SMITE . '" align="absmiddle" />' );
				$this->msg_html->karmaplus = CKunenaLink::GetKarmaLink ( 'increase', $this->catid, $this->kunena_message->id, $this->userinfo->userid, '<img src="' . (isset ( $kunena_icons ['karmaplus'] ) ? (KUNENA_URLICONSPATH . $kunena_icons ['karmaplus']) : (KUNENA_URLEMOTIONSPATH . "karmaplus.gif")) . '" alt="Karma+" border="0" title="' . _KARMA_APPLAUD . '" align="absmiddle" />' );
			}
		}
		/*let's see if we should use Missus integration */
		if ($this->config->pm_component == "missus" && $this->userinfo->userid && $this->my->id) {
			//we should offer the user a Missus link
			//first get the username of the user to contact
			$PMSName = $this->userinfo->username;
			$img_html = "<img src='";

			if ($kunena_icons ['pms']) {
				$img_html .= KUNENA_URLICONSPATH . $kunena_icons ['pms'];
			} else {
				$img_html .= KUNENA_URLICONSPATH . $kunena_icons ['pms'];
			}

			$img_html .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" />";
			$this->msg_html->pms = CKunenaLink::GetMissusLink($this->userinfo->userid, $img_html, _GEN_FORUM . ': ' . urlencode ( utf8_encode ( $this->kunena_message->subject ) ) );
		}

		/*let's see if we should use JIM integration */
		if ($this->config->pm_component == "jim" && $this->userinfo->userid && $this->my->id) {
			//we should offer the user a JIM link
			//first get the username of the user to contact
			$PMSName = $this->userinfo->username;
			$img_html = "<img src='";

			if ($kunena_icons ['pms']) {
				$img_html .= KUNENA_URLICONSPATH . $kunena_icons ['pms'];
			} else {
				$img_html .= KUNENA_URLICONSPATH . $kunena_icons ['pms'];
				;
			}

			$img_html .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" />";

			$this->msg_html->pms = CKunenaLink::GetJimLink($PMSName, urlencode ( utf8_encode ( $this->kunena_message->subject ) ), $img_html);
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
				$img_html .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
			}

			$img_html .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" />";
			$this->msg_html->pms = CKunenaLink::GetUddeImLink( $this->userinfo->userid, $img_html );
		}
		/*let's see if we should use myPMS2 integration */
		if ($this->config->pm_component == "pms" && $this->userinfo->userid && $this->my->id) {
			//we should offer the user a PMS link
			//first get the username of the user to contact
			$PMSName = $this->userinfo->username;
			$img_html = "<img src=\"";

			if ($kunena_icons ['pms']) {
				$img_html .= KUNENA_URLICONSPATH . $kunena_icons ['pms'];
			} else {
				$img_html .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
			}

			$img_html .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" />";
			$this->msg_html->pms = CKunenaLink::GetPmsLink($PMSName, urlencode ( utf8_encode ( $this->kunena_message->subject ) ), $img_html );
		}

		// online - ofline status
		if ($this->userinfo->userid > 0) {
			static $onlinecache = array ();
			if (! isset ( $onlinecache [$this->userinfo->userid] )) {
				$sql = "SELECT COUNT(userid) FROM #__session WHERE userid='{$this->userinfo->userid}'";
				$this->db->setQuery ( $sql );
				$onlinecache [$this->userinfo->userid] = $this->db->loadResult ();
				check_dberror ( 'Unable to load online status' );
			}
			$isonline = $onlinecache [$this->userinfo->userid];

			if ($isonline && $this->userinfo->showOnline == 1) {
				$this->msg_html->online = isset ( $kunena_icons ['onlineicon'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['onlineicon'] . '" border="0" alt="' . _MODLIST_ONLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'onlineicon.gif" border="0"  alt="' . _MODLIST_ONLINE . '" />';
			} else {
				$this->msg_html->online = isset ( $kunena_icons ['offlineicon'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['offlineicon'] . '" border="0" alt="' . _MODLIST_OFFLINE . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'offlineicon.gif" border="0"  alt="' . _MODLIST_OFFLINE . '" />';
			}
		}
		/* PM integration */
		if ($this->config->pm_component == "jomsocial" && $this->userinfo->userid && $this->my->id) {
			$onclick = CMessaging::getPopup ( $this->userinfo->userid );
			$this->msg_html->pms = '<a href="javascript:void(0)" onclick="' . $onclick . "\">";

			if ($kunena_icons ['pms']) {
				$this->msg_html->pms .= "<img src=\"" . KUNENA_URLICONSPATH . $kunena_icons ['pms'] . "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" />";
			} else {
				$this->msg_html->pms .= _VIEW_PMS;
			}

			$this->msg_html->pms .= "</a>";
		}
		//Check if the Integration settings are on, and set the variables accordingly.
		if ($this->config->fb_profile == "cb") {
			if ($this->config->fb_profile == 'cb' && $this->userinfo->userid > 0) {
				$this->msg_html->prflink = CKunenaCBProfile::getProfileURL ( $this->userinfo->userid );
				$this->msg_html->profile = "<a href=\"" . $this->msg_html->prflink . "\">                                              <img src=\"";

				if ($kunena_icons ['userprofile']) {
					$this->msg_html->profile .= KUNENA_URLICONSPATH . $kunena_icons ['userprofile'];
				} else {
					$this->msg_html->profile .= KUNENA_JLIVEURL . "/components/com_comprofiler/images/profiles.gif";
				}

				$this->msg_html->profile .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" /></a>";
			}
		} else if ($this->userinfo->gid > 0) {
			//Kunena Profile link.
			$this->msg_html->profileicon = "<img src=\"";

			if ($kunena_icons ['userprofile']) {
				$this->msg_html->profileicon .= KUNENA_URLICONSPATH . $kunena_icons ['userprofile'];
			} else {
				$this->msg_html->profileicon .= KUNENA_URLICONSPATH . "profile.gif";
			}

			$this->msg_html->profileicon .= "\" alt=\"" . _VIEW_PROFILE . "\" border=\"0\" title=\"" . _VIEW_PROFILE . "\" />";
			$this->msg_html->profile = CKunenaLink::GetProfileLink ( $this->config, $this->userinfo->userid, $this->msg_html->profileicon );
		}

		// Begin: Additional Info //
		if ($this->userinfo->gender != '') {
			$gender = _KUNENA_NOGENDER;
			if ($this->userinfo->gender == 1) {
				$gender = '' . _KUNENA_MYPROFILE_MALE;
				$this->msg_html->gender = isset ( $kunena_icons ['msgmale'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['msgmale'] . '" border="0" alt="' . _KUNENA_MYPROFILE_GENDER . ': ' . $gender . '" title="' . _KUNENA_MYPROFILE_GENDER . ': ' . $gender . '" />' : '' . _KUNENA_MYPROFILE_GENDER . ': ' . $gender . '';
			}

			if ($this->userinfo->gender == 2) {
				$gender = '' . _KUNENA_MYPROFILE_FEMALE;
				$this->msg_html->gender = isset ( $kunena_icons ['msgfemale'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['msgfemale'] . '" border="0" alt="' . _KUNENA_MYPROFILE_GENDER . ': ' . $gender . '" title="' . _KUNENA_MYPROFILE_GENDER . ': ' . $gender . '" />' : '' . _KUNENA_MYPROFILE_GENDER . ': ' . $gender . '';
			}

		}
		if ($this->userinfo->personalText != '') {
			$this->msg_html->personal = kunena_htmlspecialchars ( CKunenaTools::parseText ( $this->userinfo->personalText ) );
		}
		if ($this->userinfo->ICQ != '') {
			$this->msg_html->icq = '<a href="http://www.icq.com/people/cmd.php?uin=' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->ICQ ) ) . '&action=message"><img src="http://status.icq.com/online.gif?icq=' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->ICQ ) ) . '&img=26" title="ICQ#: ' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->ICQ ) ) . '" alt="ICQ#: ' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->ICQ ) ) . '" /></a>';
		}
		if ($this->userinfo->location != '') {
			$this->msg_html->location = isset ( $kunena_icons ['msglocation'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['msglocation'] . '" border="0" alt="' . _KUNENA_MYPROFILE_LOCATION . ': ' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->location ) ) . '" title="' . _KUNENA_MYPROFILE_LOCATION . ': ' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->location ) ) . '" />' : ' ' . _KUNENA_MYPROFILE_LOCATION . ': ' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->location ) ) . '';
		}
		if ($this->userinfo->birthdate != '0001-01-01' and $this->userinfo->birthdate != '0000-00-00' and $this->userinfo->birthdate != '') {
			$birthday = CKunenaTimeformat::showDate ( $this->userinfo->birthdate, 'date' );
			$this->msg_html->birthdate = isset ( $kunena_icons ['msgbirthdate'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['msgbirthdate'] . '" border="0" alt="' . _KUNENA_PROFILE_BIRTHDAY . ': ' . $birthday . '" title="' . _KUNENA_PROFILE_BIRTHDAY . ': ' . $birthday . '" />' : ' ' . _KUNENA_PROFILE_BIRTHDAY . ': ' . $birthday . '';
		}
		if ($this->userinfo->websiteurl != '') {
			$this->msg_html->website = isset ( $kunena_icons ['msgwebsite'] ) ? '<a href="http://' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->websiteurl ) ) . '" target="_blank"><img src="' . KUNENA_URLICONSPATH . $kunena_icons ['msgwebsite'] . '" border="0" alt="' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->websitename ) ) . '" title="' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->websitename ) ) . '" /></a>' : '<a href="http://' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->websiteurl ) ) . '" target="_blank">' . kunena_htmlspecialchars ( stripslashes ( $this->userinfo->websitename ) ) . '</a>';
		}

		// Finish: Additional Info //


		//Show admins the IP address of the user:
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->msg_html->ip = $this->kunena_message->ip;
		}

		$this->msg_html->subject = CKunenaTools::parseText ( $this->kunena_message->subject );
		$this->msg_html->text = CKunenaTools::parseBBCode ( $this->kunena_message->message );
		$this->msg_html->signature = CKunenaTools::parseBBCode ( $this->userinfo->signature );

		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) || ($this->kunena_forum_locked == 0 && $this->topicLocked == 0)) {
			//user is allowed to reply/quote
			if ($this->my->id > 0) {
				$this->msg_html->quickreply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'reply', _KUNENA_BUTTON_QUICKREPLY ), 'nofollow', 'buttoncomm btn-left kqr_fire', _KUNENA_BUTTON_QUICKREPLY_LONG, ' id="kqr_sc__' . $this->msg_html->id . '" onclick="return false;"' );
			}
			$this->msg_html->reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'reply', _KUNENA_BUTTON_REPLY ), 'nofollow', 'buttoncomm btn-left', _KUNENA_BUTTON_REPLY_LONG );
			$this->msg_html->quote = CKunenaLink::GetTopicPostReplyLink ( 'quote', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'quote', _KUNENA_BUTTON_QUOTE ), 'nofollow', 'buttoncomm btn-left', _KUNENA_BUTTON_QUOTE_LONG );
		} else {
			//user is not allowed to write a post
			if ($this->topicLocked == 1 || $this->kunena_forum_locked) {
				$this->msg_html->closed = _POST_LOCK_SET;
			} else {
				$this->msg_html->closed = _VIEW_DISABLED;
			}
		}

		$showedEdit = 0; //reset this value
		//Offer an moderator the delete link
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->msg_html->delete = CKunenaLink::GetTopicPostLink ( 'delete', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'delete', _KUNENA_BUTTON_DELETE ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_DELETE_LONG );
			$this->msg_html->merge = CKunenaLink::GetTopicPostLink ( 'merge', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'merge', _KUNENA_BUTTON_MERGE ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_MERGE_LONG );
		}

		if ($this->config->useredit && $this->my->id != "") {
			//Now, if the viewer==author and the viewer is allowed to edit his/her own post then offer an 'edit' link
			$allowEdit = 0;
			if ($this->my->id == $this->userinfo->userid) {
				if ((( int ) $this->config->useredittime) == 0) {
					$allowEdit = 1;
				} else {
					//Check whether edit is in time
					$modtime = $this->kunena_message->modified_time;
					if (! $modtime) {
						$modtime = $this->kunena_message->time;
					}
					if (($modtime + (( int ) $this->config->useredittime)) >= CKunenaTimeformat::internalTime ()) {
						$allowEdit = 1;
					}
				}
			}
			if ($allowEdit) {
				$this->msg_html->edit = CKunenaLink::GetTopicPostLink ( 'edit', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'edit', _KUNENA_BUTTON_EDIT ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_EDIT_LONG );
				$showedEdit = 1;
			}
		}

		if (CKunenaTools::isModerator ( $this->my->id, $this->catid ) && $showedEdit != 1) {
			//Offer a moderator always the edit link except when it is already showing..
			$this->msg_html->edit = CKunenaLink::GetTopicPostLink ( 'edit', $this->catid, $this->kunena_message->id, CKunenaTools::showButton ( 'edit', _KUNENA_BUTTON_EDIT ), 'nofollow', 'buttonmod btn-left', _KUNENA_BUTTON_EDIT_LONG );
		}

		//(JJ)
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'message.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'view' . DS . 'message.php');
		}
	}

	function getPagination($catid, $threadid, $page, $totalpages, $maxpages) {
		$kunena_config = & CKunenaConfig::getInstance ();

		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<span class="kpagination">' . _PAGE;
		if ($startpage > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $catid, $threadid, 1, $kunena_config->messages_per_page, 1, '', $rel = 'follow' );
			if ($startpage > 2) {
				$output .= "...";
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= "<strong>$i</strong>";
			} else {
				$output .= CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $catid, $threadid, $i, $kunena_config->messages_per_page, $i, '', $rel = 'follow' );
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= "...";
			}

			$output .= CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $catid, $threadid, $totalpages, $kunena_config->messages_per_page, $totalpages, '', $rel = 'follow' );
		}

		$output .= '</span>';
		return $output;
	}

	function display() {
		if (! $this->allow) {
			echo _KUNENA_NO_ACCESS;
			return;
		}
		if (! $this->first_message) {
			echo _MODERATION_INVALID_ID;
			return;
		}
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'view.php' )) {
			include (KUNENA_ABSTMPLTPATH . DS . 'view' . DS . 'view.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'view' . DS . 'view.php');
		}
	}
}
