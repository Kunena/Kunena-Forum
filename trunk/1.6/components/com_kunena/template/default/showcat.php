<?php
/**
 * @version $Id: showcat.php 1210 2009-11-23 06:51:41Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined( '_JEXEC' ) or die();


$kunena_db = &JFactory::getDBO ();
$kunena_app = & JFactory::getApplication ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_session = & CKunenaSession::getInstance ();
global $kunena_icons;

function KunenaShowcatPagination($catid, $page, $totalpages, $maxpages) {
	$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
	$endpage = $startpage + $maxpages;
	if ($endpage > $totalpages) {
		$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
		$endpage = $totalpages;
	}

	$output = '<span class="fb_pagination">' . _PAGE;

	if (($startpage) > 1) {
		if ($endpage < $totalpages)
			$endpage --;
		$output .= CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, 1, 1, $rel = 'follow' );
		if (($startpage) > 2) {
			$output .= "...";
		}
	}

	for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
		if ($page == $i) {
			$output .= "<strong>$i</strong>";
		} else {
			$output .= CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, $i, $i, $rel = 'follow' );
		}
	}

	if ($endpage < $totalpages) {
		if ($endpage < $totalpages - 1) {
			$output .= "...";
		}

		$output .= CKunenaLink::GetCategoryPageLink ( 'showcat', $catid, $totalpages, $totalpages, $rel = 'follow' );
	}

	$output .= '</span>';
	return $output;
}

//Security basics begin
//Securing passed form elements:
$catid = ( int ) $catid;

//resetting some things:
$moderatedForum = 0;
$this->kunena_forum_locked = 0;
$topicLocked = 0;
$topicSticky = 0;

//get the allowed forums and turn it into an array
$allow_forum = ($kunena_session->allowed != '') ? explode ( ',', $kunena_session->allowed ) : array ();

if (in_array ( $catid, $allow_forum )) {
	$threads_per_page = $kunena_config->threads_per_page;

	if ($catid <= 0) {
		//make sure we got a valid category id
		$catid = 1;
	}

	/*//////////////// Start selecting messages, prepare them for threading, etc... /////////////////*/
	$page = ( int ) $page;
	$page = $page < 1 ? 1 : $page;
	$offset = ($page - 1) * $threads_per_page;
	$row_count = $page * $threads_per_page;
	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__fb_messages WHERE parent='0' AND catid='{$catid}' AND hold='0'" );
	$total = ( int ) $kunena_db->loadResult ();
	check_dberror ( 'Unable to get message count.' );
	$totalpages = ceil ( $total / $threads_per_page );

	$query = "SELECT t.id, MAX(m.id) AS lastid FROM #__fb_messages AS t
	INNER JOIN #__fb_messages AS m ON t.id = m.thread
	WHERE t.parent='0' AND t.hold='0' AND t.catid='{$catid}' AND m.hold='0' AND m.catid='{$catid}'
	GROUP BY m.thread ORDER BY t.ordering DESC, lastid DESC";
	$kunena_db->setQuery ( $query, $offset, $threads_per_page );
	$threadids = $kunena_db->loadResultArray ();
	check_dberror ( "Unable to load thread list." );
	$idstr = @join ( ",", $threadids );

	$this->favthread = array ();
	$this->thread_counts = array ();
	$this->messages = array ();
	$this->messages [0] = array ();
	$routerlist = array();
	if (count ( $threadids ) > 0) {
		$query = "SELECT a.*, j.id AS userid, t.message AS messagetext, l.myfavorite, l.favcount, l.attachmesid,
							l.msgcount, l.lastid, u.avatar, c.id AS catid, c.name AS catname, c.class_sfx
	FROM (
		SELECT m.thread, MAX(f.userid='{$kunena_my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount, COUNT(a.mesid) AS attachmesid,
			COUNT(DISTINCT m.id) AS msgcount, MAX(m.id) AS lastid, MAX(m.time) AS lasttime
		FROM #__fb_messages AS m
		LEFT JOIN #__fb_favorites AS f ON f.thread = m.thread
		LEFT JOIN #__fb_attachments AS a ON a.mesid = m.thread
		WHERE m.hold='0' AND m.thread IN ({$idstr})
		GROUP BY thread
	) AS l
	INNER JOIN #__fb_messages AS a ON a.thread = l.thread
	INNER JOIN #__fb_messages_text AS t ON a.thread = t.mesid
	LEFT JOIN #__users AS j ON j.id = a.userid
	LEFT JOIN #__fb_users AS u ON u.userid = j.id
	LEFT JOIN #__fb_categories AS c ON c.id = a.catid
	WHERE (a.parent='0' OR a.id=l.lastid)
	ORDER BY ordering DESC, lastid DESC";

		$kunena_db->setQuery ( $query );
		$messagelist = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load messages." );

		foreach ( $messagelist as $message ) {
			$this->messages [$message->parent] [] = $message;
			$this->messagetext [$message->id] = JString::substr ( smile::purify ( $message->messagetext ), 0, 500 );
			if ($message->parent == 0) {
				$this->hits [$message->id] = $message->hits;
				$this->thread_counts [$message->id] = $message->msgcount - 1;
				$last_read [$message->id]->unread = 0;
				if ($message->favcount)
					$this->favthread [$message->id] = $message->favcount;
				if ($message->id == $message->lastid)
					$last_read [$message->id]->lastread = $last_reply [$message->id] = $message;
				$routerlist[$message->id] = $message->subject;
			} else {
				$last_read [$message->thread]->lastread = $last_reply [$message->thread] = $message;
			}
		}
		include_once(KUNENA_PATH . DS . 'router.php');
		KunenaRouter::loadMessages($routerlist);

		$kunena_db->setQuery ( "SELECT thread, MIN(id) AS lastread, SUM(1) AS unread FROM #__fb_messages " . "WHERE hold='0' AND moved='0' AND thread IN ({$idstr}) AND time>'{$this->prevCheck}' GROUP BY thread" );
		$msgidlist = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to get unread messages count and first id." );

		foreach ( $msgidlist as $msgid ) {
			if (! in_array ( $msgid->thread, $this->read_topics ))
				$last_read [$msgid->thread] = $msgid;
		}
	}

	//get number of pending messages
	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__fb_messages WHERE catid='{$catid}' AND hold='1'" );
	$numPending = $kunena_db->loadResult ();
	check_dberror ( 'Unable to get number of pending messages.' );

	//Get the category information
	$query = "SELECT c.*, s.catid AS subscribeid
				FROM #__fb_categories AS c
				LEFT JOIN #__fb_subscriptions_categories AS s ON c.id = s.catid
				AND s.userid = '{$kunena_my->id}'
				WHERE c.id='$catid'";

	$kunena_db->setQuery ( $query );
	$objCatInfo = $kunena_db->loadObject ();
	check_dberror ( 'Unable to get categories.' );
	//Get the Category's parent category name for breadcrumb
	$kunena_db->setQuery ( "SELECT name, id FROM #__fb_categories WHERE id='{$objCatInfo->parent}'" );
	$objCatParentInfo = $kunena_db->loadObject ();
	check_dberror ( 'Unable to get parent category.' );

	//check if this forum is locked
	$this->kunena_forum_locked = $objCatInfo->locked;
	//check if this forum is subject to review
	$this->kunena_forum_reviewed = $objCatInfo->review;

	//Perform subscriptions check
	$kunena_cansubscribecat = 0;
	if ($kunena_config->allowsubscriptions && ("" != $kunena_my->id || 0 != $kunena_my->id)) {
		if ($objCatInfo->subscribeid == '') {
			$kunena_cansubscribecat = 1;
		}
	}

	//meta description and keywords
	$metaKeys = kunena_htmlspecialchars ( stripslashes ( _KUNENA_CATEGORIES . ", {$objCatParentInfo->name}, {$objCatInfo->name}, {$kunena_config->board_title}, " . $kunena_app->getCfg ( 'sitename' ) ) );
	$metaDesc = kunena_htmlspecialchars ( stripslashes ( "{$objCatParentInfo->name} ({$page}/{$totalpages}) - {$objCatInfo->name} - {$kunena_config->board_title}" ) );

	$document = & JFactory::getDocument ();
	$cur = $document->get ( 'description' );
	$metaDesc = $cur . '. ' . $metaDesc;
	$document = & JFactory::getDocument ();
	$document->setMetadata ( 'keywords', $metaKeys );
	$document->setDescription ( $metaDesc );
	?>
<!-- Pathway -->
<?php
	if (file_exists ( KUNENA_ABSTMPLTPATH . '/pathway.php' )) {
		require_once (KUNENA_ABSTMPLTPATH . '/pathway.php');
	} else {
		require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'pathway.php');
	}
	?>
<!-- / Pathway -->
<?php
	if ($objCatInfo->headerdesc) {
		?>
<table class="fb_forum-headerdesc<?php echo isset($objCatInfo->class_sfx) ? ' fb_forum-headerdesc' . $objCatInfo->class_sfx : '';?>" border="0" cellpadding="0"
	cellspacing="0" width="100%">
	<tr>
		<td><?php
		$kunena_emoticons = smile::getEmoticons ( 0 );
		$headerdesc = stripslashes ( smile::smileReplace ( $objCatInfo->headerdesc, 0, $kunena_config->disemoticons, $kunena_emoticons ) );
		$headerdesc = nl2br ( $headerdesc );
		echo $headerdesc;
		?>
		</td>
	</tr>
</table>
<?php
	}
	?>

<!-- B: List Actions -->

<table class="fb_list_actions" border="0" cellpadding="0"
	cellspacing="0" width="100%">
	<tr>
		<td class="fb_list_actions_goto"><?php
	//go to bottom
	echo '<a name="forumtop" /> ';
	echo CKunenaLink::GetSamePageAnkerLink ( 'forumbottom', isset ( $kunena_icons ['bottomarrow'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM );
	?>

		</td>
		<td class="fb_list_actions_forum" width="100%"><?php
	if (CKunenaTools::isModerator($kunena_my->id, $catid) || ($this->kunena_forum_locked == 0 && ($kunena_my->id > 0 || $kunena_config->pubwrite))) {
		//this user is allowed to post a new topic:
		$forum_new = CKunenaLink::GetPostNewTopicLink ( $catid, CKunenaTools::showButton('newtopic', _KUNENA_BUTTON_NEW_TOPIC), 'nofollow', 'buttoncomm btn-left', _KUNENA_BUTTON_NEW_TOPIC_LONG );
	}
	if ($kunena_my->id != 0) {
		$forum_markread = CKunenaLink::GetCategoryLink ( 'markThisRead', $catid, CKunenaTools::showButton('markread', _KUNENA_BUTTON_MARKFORUMREAD), 'nofollow', 'buttonuser btn-left', _KUNENA_BUTTON_MARKFORUMREAD_LONG );
	}

		// Thread Subscription
	if ($kunena_cansubscribecat == 1) {
		// this user is allowed to subscribe - check performed further up to eliminate duplicate checks
		// for top and bottom navigation
		$thread_subscribecat = CKunenaLink::GetCategoryLink ( 'subscribecat', $catid, CKunenaTools::showButton('subscribe', _KUNENA_BUTTON_SUBSCRIBE_CATEGORY), 'nofollow', 'buttonuser btn-left', _KUNENA_BUTTON_SUBSCRIBE_CATEGORY_LONG );
	}

	if ($kunena_my->id != 0 && $kunena_config->allowsubscriptions && $kunena_cansubscribecat == 0) {
		// this user is allowed to unsubscribe
		$thread_subscribecat = CKunenaLink::GetCategoryLink ( 'unsubscribecat', $catid, CKunenaTools::showButton('subscribe', _KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY), 'nofollow', 'buttonuser btn-left', _KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG );
	}

	if (isset ( $forum_new ) || isset ( $forum_markread ) || isset ( $thread_subscribecat ) ) {
		echo '<div class="fb_message_buttons_row">';
		if (isset ( $forum_new ))
			echo $forum_new;
		if (isset ( $forum_markread ))
			echo ' ' . $forum_markread;
		if (isset ( $thread_subscribecat ))
			echo ' ' . $thread_subscribecat;
		echo '</div>';
	}
	?>

		</td>
		<td class="fb_list_pages_all" nowrap="nowrap"><?php
	//pagination 1
	if (count ( $this->messages [0] ) > 0) {
		$maxpages = 9 - 2; // odd number here (show - 2)
		$totalpages = ceil ( $total / $threads_per_page );
		echo $pagination = KunenaShowcatPagination ( $catid, $page, $totalpages, $maxpages );
	}
	?>
		</td>
	</tr>
</table>

<!-- F: List Actions -->

<?php
	//(JJ)
	if (file_exists ( KUNENA_ABSTMPLTPATH . '/sub_category_list.php' )) {
		include (KUNENA_ABSTMPLTPATH . '/sub_category_list.php');
	} else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'sub_category_list.php');
	}
	?>

<?php
	//get all readTopics in an array
	$readTopics = "";
	$kunena_db->setQuery ( "SELECT readtopics FROM #__fb_sessions WHERE userid='{$kunena_my->id}'" );
	$readTopics = $kunena_db->loadResult ();
	check_dberror ( 'Unable to get read topics.' );

	if (count ( $readTopics ) == 0) {
		$readTopics = "0";
	} //make sure at least something is in there..
	//make it into an array
	$this->read_topics = explode ( ',', $readTopics );

	if (count ( $this->messages ) > 0) {
		if (file_exists ( KUNENA_ABSTMPLTPATH . '/flat.php' )) {
			include (KUNENA_ABSTMPLTPATH . '/flat.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'flat.php');
		}
	} else {
		echo "<p align=\"center\">";
		echo '<br /><br />' . _SHOWCAT_NO_TOPICS;
		echo "</p>";
	}
	?>

<!-- B: List Actions Bottom -->

<table class="fb_list_actions_bottom" border="0" cellpadding="0"
	cellspacing="0" width="100%">
	<tr>
		<td class="fb_list_actions_goto"><?php
	//go to top
	echo '<a name="forumbottom" />';
	echo CKunenaLink::GetSamePageAnkerLink ( 'forumtop', isset ( $kunena_icons ['toparrow'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['toparrow'] . '" border="0" alt="' . _GEN_GOTOTOP . '" title="' . _GEN_GOTOTOP . '"/>' : _GEN_GOTOTOP );
	?>

		</td>
		<td class="fb_list_actions_forum" width="100%"><?php
	if (isset ( $forum_new ) || isset ( $forum_markread )) {
		echo '<div class="fb_message_buttons_row">';
		if (isset ( $forum_new ))
			echo $forum_new;
		if (isset ( $forum_markread ))
			echo ' ' . $forum_markread;
		if (isset ( $thread_subscribecat ))
			echo ' ' . $thread_subscribecat;
		echo '</div>';
	}
	?>

		</td>
		<td class="fb_list_pages_all" nowrap="nowrap"><?php
	//pagination 2
	if (count ( $this->messages [0] ) > 0) {
		echo $pagination;
	}
	?>
		</td>
	</tr>
</table>
<?php
	echo '<div class = "fb_forum-pathway-bottom">';
	echo $this->kunena_pathway1;
	echo '</div>';
	?>

<!-- F: List Actions Bottom -->

<!-- B: Category List Bottom -->

<table class="fb_list_bottom" border="0" cellspacing="0" cellpadding="0"
	width="100%">
	<tr>
		<td class="fb_list_moderators"><!-- Mod List --> <?php
	//get the Moderator list for display
	$kunena_db->setQuery ( "SELECT * FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='{$catid}'" );
	$modslist = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load moderators." );

	if (count ( $modslist ) > 0) :
		?>

		<div class="fbbox-bottomarea-modlist"><?php
		echo '' . _GEN_MODERATORS . ": ";
		foreach ( $modslist as $mod ) {
			echo CKunenaLink::GetProfileLink ( $kunena_config, $mod->userid, $mod->username ) . '&nbsp; ';
		}
		?>
		</div>


	<?php endif;
	?> <!-- /Mod List --></td>
		<td class="fb_list_categories"><?php

	//(JJ) FINISH: CAT LIST BOTTOM


	if ($kunena_config->enableforumjump)
		require_once (KUNENA_PATH_LIB . DS . 'kunena.forumjump.php');

	?>
		</td>
	</tr>
</table>

<!-- F: Category List Bottom -->



<?php
} else {
	echo _KUNENA_NO_ACCESS;
}
