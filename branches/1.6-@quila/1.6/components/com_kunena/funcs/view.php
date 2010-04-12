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

class CKunenaViewMessage {
	// Message actions
	public $message_quickreply = null;
	public $message_reply = null;
	public $message_quote = null;
	public $message_edit = null;
	public $message_merge = null;
	public $message_split = null;
	public $message_delete = null;
	public $message_move = null;
	public $message_publish = null;
	public $message_closed = null;

	// Message
	public $id = null;
	public $catid = null;
	public $thread = null;
	public $subject = null;
	public $message = null;
	public $class = null;

	public $ipLink = null;
	public $numLink = null;
	public $msgsuffix = null;

	public $userid = null;
	public $username = null;
	public $useravatar = null;
	public $usertype = null;
	public $userposts = null;
	public $userrankimg = null;
	public $userranktitle = null;
	public $userpoints = null;
	public $userkarma = null;
	public $profilelink = null;
	public $personaltext = null;
	public $signature = null;

	public $attachments = array();

	function __construct($parent, $message) {
		kimport('html.parser');
		$this->replynum = $parent->replynum;
		$this->mmm = $parent->mmm;
		$this->topicLocked = $parent->topicLocked;
		$this->allow_anonymous = $parent->allow_anonymous;
		$this->anonymous = $parent->anonymous;
		$this->myname = $parent->myname;
		$this->templatepath = $parent->templatepath;
		$this->msg = $message;

		$this->my = JFactory::getUser ();
		$this->config = CKunenaConfig::getInstance ();
		$this->db = JFactory::getDBO ();
	}

	function displayActions() {
		CKunenaTools::loadTemplate('/view/message.actions.php');
	}

	function displayContents() {
		CKunenaTools::loadTemplate('/view/message.contents.php');
	}

	function displayProfile() {
		CKunenaTools::loadTemplate('/view/message.profilebox.php');
	}

	function displayAttachments() {
		if ( empty ( $this->attachments ) ) return;
		CKunenaTools::loadTemplate('/view/message.attachments.php');
	}

	function display() {
		$message = $this->msg;
		$this->id = $message->id;
		$this->catid = $message->catid;
		$this->thread = $message->thread;

		// Link to individual message
		if ($this->config->ordering_system == 'old_ord') {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink ( $this->id, '#' . $this->id );
		} else {
			$this->numLink = CKunenaLink::GetSamePageAnkerLink( $this->id, '#' . $this->replynum );
		}
		// New post suffix for class
		if ($message->new) {
			$this->msgsuffix = '_new';
		}

		$subject = stripslashes ($message->subject);
		$this->resubject = JString::strtolower ( JString::substr ( $subject, 0, JString::strlen ( JText::_('COM_KUNENA_POST_RE') ) ) ) == JString::strtolower ( JText::_('COM_KUNENA_POST_RE') ) ? $subject : JText::_('COM_KUNENA_POST_RE') . ' ' . $subject;
		$this->subject = KunenaParser::parseText ( $subject );
		$this->message = KunenaParser::parseBBCode ( stripslashes($message->message) );

		//Show admins the IP address of the user:
		if ($message->ip && (CKunenaTools::isAdmin () || (CKunenaTools::isModerator ( $this->my->id, $this->catid ) && !$this->config->hide_ip))) {
			$this->ipLink = CKunenaLink::GetMessageIPLink ( $message->ip );
		}

		$this->profile = KunenaFactory::getUser ( $message->userid );

		// Modify profile values by integration
		$triggerParams = array ('userid' => $message->userid, 'userinfo' => &$this->profile );
		$integration = KunenaFactory::getProfile();
		$integration->trigger ( 'profileIntegration', $triggerParams );

		// Choose username
		$this->userid = $this->profile->userid;
		$this->username = $this->config->username ? $this->profile->username : $this->profile->name;
		if ((!$this->username || !$message->userid || $this->config->changename) && $message->name) {
			$this->username = stripslashes ($message->name);
		}

		$avatar = $this->profile->getAvatarLink ();
		if ($avatar) {
			$this->avatar = '<span class="kavatar">' . $avatar . '</span>';
		}

		if ($this->config->showuserstats) {
			$this->userposts = $this->profile->posts;
			if ($this->config->userlist_usertype) $this->usertype = $this->profile->getType($this->catid);
			$this->userrankimage = $this->profile->getRank ($this->catid, 'image');
			$this->userranktitle = $this->profile->getRank ($this->catid, 'title');
		}

		// Start Integration AlphaUserPoints
		// ****************************
		$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if ($this->config->alphauserpoints && file_exists ( $api_AUP )) {
			$this->db->setQuery ( "SELECT points FROM #__alpha_userpoints WHERE `userid`='" . ( int ) $message->userid . "'" );
			$this->userpoints = $this->db->loadResult ();
			check_dberror ( "Unable to load AUP points." );
		}
		// End Integration AlphaUserPoints

		//karma points and buttons
		if ($this->config->showkarma && $this->profile->userid) {
			$this->userkarma = JText::_('COM_KUNENA_KARMA') . ": " . $this->profile->karma;

			if ($this->my->id && $this->my->id != $this->profile->userid) {
				$this->userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'decrease', $this->catid, $this->id, $this->userid, '<span class="karmaminus" alt="Karma-" border="0" title="' . JText::_('COM_KUNENA_KARMA_SMITE') . '"> </span>' );
				$this->userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'increase', $this->catid, $this->id, $this->userid, '<span class="karmaplus" alt="Karma+" border="0" title="' . JText::_('COM_KUNENA_KARMA_APPLAUD') . '"> </span>' );
			}
		}

		$this->profilelink = $this->profile->profileIcon('profile');
		$this->personaltext = KunenaParser::parseText ( stripslashes($this->profile->personalText) );
		$this->signature = KunenaParser::parseBBCode ( stripslashes($this->profile->signature) );

		// Add attachments
		if (isset($message->attachments)) {
			foreach($message->attachments as $attachment)
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

				$this->attachments[] = $attachment;
			}
		}

		if ((!$message->hold && (CKunenaTools::isModerator ( $this->my->id, $this->catid )) || ($this->topicLocked == 0))) {
			//user is allowed to reply/quote
			if ($this->my->id) {
				$this->message_quickreply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->id, CKunenaTools::showButton ( 'reply', JText::_('COM_KUNENA_BUTTON_QUICKREPLY') ), 'nofollow', 'buttoncomm btn-left kqreply', JText::_('COM_KUNENA_BUTTON_QUICKREPLY_LONG'), ' id="kreply'.$this->id.'"' );
			}
			$this->message_reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $this->catid, $this->id, CKunenaTools::showButton ( 'reply', JText::_('COM_KUNENA_BUTTON_REPLY') ), 'nofollow', 'buttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_REPLY_LONG') );
			$this->message_quote = CKunenaLink::GetTopicPostReplyLink ( 'quote', $this->catid, $this->id, CKunenaTools::showButton ( 'quote', JText::_('COM_KUNENA_BUTTON_QUOTE') ), 'nofollow', 'buttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_QUOTE_LONG') );
		} else {
			//user is not allowed to write a post
			if ($this->topicLocked) {
				$this->message_closed = JText::_('COM_KUNENA_POST_LOCK_SET');
			} else {
				$this->message_closed = JText::_('COM_KUNENA_VIEW_DISABLED');
			}
		}

		$this->class = 'class="kmsg"';

		//Offer an moderator a few tools
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			unset($this->message_closed);
			$this->message_edit = CKunenaLink::GetTopicPostLink ( 'edit', $this->catid, $this->id, CKunenaTools::showButton ( 'edit', JText::_('COM_KUNENA_BUTTON_EDIT') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_EDIT_LONG') );
			$this->message_delete = CKunenaLink::GetTopicPostLink ( 'delete', $this->catid, $this->id, CKunenaTools::showButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_LONG') );
			$this->message_split = CKunenaLink::GetTopicPostLink ( 'split', $this->catid, $this->id, CKunenaTools::showButton ( 'split', JText::_('COM_KUNENA_BUTTON_SPLIT_TOPIC') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_SPLIT_TOPIC_LONG') );
			$this->message_merge = CKunenaLink::GetTopicPostLink ( 'merge', $this->catid, $this->id, CKunenaTools::showButton ( 'merge', JText::_('COM_KUNENA_BUTTON_MERGE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MERGE_LONG') );
			$this->message_move = CKunenaLink::GetTopicPostLink ( 'movepost', $this->catid, $this->id, CKunenaTools::showButton ( 'move', JText::_('COM_KUNENA_BUTTON_MOVE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MOVE_LONG') );
			if ($message->hold == 1) {
				$this->message_publish = CKunenaLink::GetTopicPostReplyLink ( 'approve', $this->catid, $this->id, CKunenaTools::showButton ( 'approve', JText::_('COM_KUNENA_BUTTON_APPROVE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_APPROVE_LONG') );
				$this->class = 'class="kmsg kunapproved"';
			}
		}
		else if ($this->config->useredit && $this->my->id && $this->my->id == $this->profile->userid) {
			//Now, if the viewer==author and the viewer is allowed to edit his/her own post then offer an 'edit' link
			if (CKunenaTools::editTimeCheck($message->modified_time, $message->time)) {
				$this->message_edit = CKunenaLink::GetTopicPostLink ( 'edit', $this->catid, $this->id, CKunenaTools::showButton ( 'edit', JText::_('COM_KUNENA_BUTTON_EDIT') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_EDIT_LONG') );
				$this->message_delete = CKunenaLink::GetTopicPostLink ( 'deleteownpost', $this->catid, $this->id, CKunenaTools::showButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE') ), 'nofollow', 'buttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_LONG') );
			}
		}

		CKunenaTools::loadTemplate('/view/message.php', false, $this->templatepath);
	}

	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}
}

class CKunenaView {
	public $allow = 0;
	public $templatepath = null;

	// Thread actions
	public $thread_reply = null;
	public $thread_new = null;
	public $thread_subscribe = null;
	public $thread_favorite = null;
	public $thread_sticky = null;
	public $thread_lock = null;
	public $thread_delete = null;
	public $thread_move = null;
	public $thread_merge = null;
	public $pagination = null;
	public $goto = null;

	function __construct($func, $catid, $id, $limitstart=0, $limit=0) {
		require_once(KUNENA_PATH_LIB . DS . 'kunena.smile.class.php');
		require_once(KUNENA_PATH_LIB . DS . 'kunena.link.class.php');

		$this->db = JFactory::getDBO ();
		$this->config = CKunenaConfig::getInstance ();
		$this->session = KunenaFactory::getSession ();
		$this->myprofile = KunenaFactory::getUser ();

		$this->func = $func;
		$this->catid = $catid;
		$this->id = $id;

		//prepare paging
		$this->limitstart = $limitstart;
		if ($this->limitstart < 0)
			$this->limitstart = 0;
		$this->limit = $limit;
		if ($this->limit < 1)
			$this->limit = $this->config->messages_per_page;
}

	function setTemplate($path) {
		$this->templatepath = $path;
	}

	function getView() {
		// Is user allowed to see the forum specified in URL?
		if ($this->catid && ! $this->session->canRead ( $this->catid )) {
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
		if (! $this->session->canRead ( $this->first_message->catid )) {
			$this->allow = 0;
			return;
		}

			$this->app = & JFactory::getApplication ();
		// Test if this is a valid URL. If not, redirect browser to the right location
		$this->thread = $this->first_message->parent == 0 ? $this->id : $this->first_message->thread;
		if ($this->first_message->moved || $this->thread != $this->id || $this->catid != $this->first_message->catid) {
			$this->catid = $this->first_message->catid;
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
			header ( "Location: " . htmlspecialchars_decode ( CKunenaLink::GetThreadPageURL ( 'view', $this->catid, $this->thread, $replyPage, $this->config->messages_per_page, $this->first_message->id ) ) );

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

		if (!$this->myprofile->ordering) {
			$ordering = ($this->config->default_sort == 'desc' ? 'DESC' : 'ASC'); // Just to make sure only valid options make it
		} else {
			$ordering = 'DESC';
		}
		$maxpages = 9 - 2; // odd number here (show - 2)
		$totalpages = ceil ( $this->total_messages / $this->limit );
		$page = floor ( $this->limitstart / $this->limit ) + 1;
		$firstpage = 1;
		if ($ordering == 'desc')
			$firstpage = $totalpages;

		// Get replies of current thread
		$query = "SELECT a.*, b.*, modified.name AS modified_name, modified.username AS modified_username
					FROM #__fb_messages AS a
					LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid
					LEFT JOIN #__users AS modified ON a.modified_by = modified.id
					WHERE a.thread='{$this->thread}' AND {$where}
					ORDER BY id {$ordering}";
		$this->db->setQuery ( $query, $this->limitstart, $this->limit );
		$posts = $this->db->loadObjectList ();
		check_dberror ( 'Unable to load replies' );

		// Load attachments

		// First collect the message ids of the first message and all replies
		$messageids = array();
		foreach($posts AS $post){
			$messageids[] = $post->id;
		}

		// create a list of ids we can use for our sql
		$idstr = @join ( ",", $messageids );

		// now grab all attchments for these messages
		$attachments = array();
		if ($idstr) {
			$query = "SELECT * FROM #__kunena_attachments
				WHERE mesid IN ($idstr)";
			$this->db->setQuery ( $query );
			$attachments = $this->db->loadObjectList ();
			check_dberror ( 'Unable to load attachments' );
		}

		// arrange attachments by message
		$message_attachments = array();
		foreach ($attachments as $attachment) $message_attachments[$attachment->mesid][] = $attachment;

		$this->messages = array ();
		foreach ( $posts as $message )
			$this->messages [] = $message;
		unset ( $posts );

		// Now that we have all relevant messages in messages, asign any matching attachments
		foreach ( $this->messages as $message ){
			// Mark as new
			if ($this->my->id && $this->prevCheck < $message->time && ! in_array ( $message->thread, $this->read_topics )) {
				$message->new = true;
			} else {
				$message->new = false;
			}
			// Assign attachments
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
		$this->replydir = $this->config->default_sort == 'desc' ? -1 : 1;
		if ($this->replydir<0) $this->replynum = $this->total_messages - $this->limitstart + 1;
		else $this->replynum = $this->limitstart;

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

	function displayThreadActions($location=0) {
		static $locations = array('top', 'bottom');
		$this->goto = '<a name="forum'.$locations[$location].'"></a>';
		$location ^= 1;
		$this->goto .= CKunenaLink::GetSamePageAnkerLink ( 'forum'.$locations[$location],
		CKunenaTools::showButton ( $locations[$location], '&nbsp;' ), 'nofollow', 'buttongoto', JText::_('COM_KUNENA_GEN_GOTO'.$locations[$location]));

		CKunenaTools::loadTemplate('/view/thread.actions.php');
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
	}

	function displayMessage($message) {
		$this->replynum += $this->replydir;
		$this->mmm ++;
		$message = new CKunenaViewMessage($this, $message);
		$message->display();
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
			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, 1, $kunena_config->messages_per_page, 1, '', $rel = 'follow' ) . '</li>';
			if ($startpage > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, $i, $kunena_config->messages_per_page, $i, '', $rel = 'follow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, $totalpages, $kunena_config->messages_per_page, $totalpages, '', $rel = 'follow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}

	function display() {
		$this->getView();

		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		if (! $this->first_message) {
			echo JText::_('COM_KUNENA_MODERATION_INVALID_ID');
			return;
		}
		CKunenaTools::loadTemplate('/view/view.php', false, $this->templatepath);
	}

	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}
}
