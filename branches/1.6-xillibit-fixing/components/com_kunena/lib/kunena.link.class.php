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
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaLink {
	//
	// Basic universal href link
	//
	function GetHrefLink($link, $name, $title = '', $rel = 'nofollow', $class = '', $anker = '', $attr = '') {
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="' . $link . ($anker ? ('#' . $anker) : '') . '" title="' . $title . '"' . ($rel ? ' rel="' . $rel . '"' : '') . ($attr ? ' ' . $attr : '') . '>' . $name . '</a>';
	}

	//
	// Basic universal href link
	//
	function GetSefHrefLink($link, $name, $title = '', $rel = 'nofollow', $class = '', $anker = '', $attr = '') {
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="' . KunenaRoute::_ ( $link ) . ($anker ? ('#' . $anker) : '') . '" title="' . $title . '"' . ($rel ? ' rel="' . $rel . '"' : '') . ($attr ? ' ' . $attr : '') . '>' . $name . '</a>';
	}

	// Simple link is a barebones href link used for e.g. Jscript links
	function GetSimpleLink($id, $name = '', $class = '', $attr = '') {
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . ' href="' . $id . '" ' . ($attr ? ' ' . $attr : '') . '>' . $name . '</a>';
	}

	//
	// Central Consolidation of all internal href links
	//


	function GetCreditsLink() {
		return CKunenaLink::GetSefHrefLink ( 'http://www.kunena.com', 'Kunena', 'Kunena', 'follow', NULL, NULL, 'target="_blank"' );
	}

	function GetTeamCreditsLink($catid, $name = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=credits&catid=' . $catid, $name, '', 'follow' );
	}

	function GetKunenaLink($name, $rel = 'follow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL, $name, '', $rel );
	}

	function GetKunenaURL($redirect = false) {
		return $redirect == false ? KunenaRoute::_ ( KUNENA_LIVEURLREL ) : htmlspecialchars_decode ( KunenaRoute::_ ( KUNENA_LIVEURLREL ) );
	}

	function GetRSSLink($name, $rel = 'follow', $params = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=rss' . $params, $name, '', $rel, '', '', 'target="_blank"' );
	}

	function GetRSSURL($params = '') {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=rss' . $params );
	}

	function GetPDFLink($catid, $id, $name, $rel = 'nofollow', $title = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&id=' . $id . '&catid=' . $catid . '&func=fb_pdf', $name, $title, $rel );
	}

	function GetCategoryLink($func, $catid, $catname, $rel = 'follow', $class = '', $title = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid, $catname, $title, $rel, $class );
	}

	//to get & instead of &amp; for redirect and JS links set redirect to true
	function GetCategoryURL($func, $catid = '', $redirect = false) {
		$strcatid = '';
		if ($catid != '')
			$strcatid = "&catid={$catid}";
		$c_url = KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=' . $func . $strcatid );
		return $redirect == false ? $c_url : htmlspecialchars_decode ( $c_url );
	}

	function GetCategoryListLink($name, $rel = 'follow', $class = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=listcat', $name, '', $rel, $class );
	}

	function GetCategoryPageLink($func, $catid, $page, $pagename, $rel = 'follow', $class = '') {
		if ($page == 1 || ! is_numeric ( $page )) {
			// page 1 is identical to a link to the regular category link
			$pagelink = CKunenaLink::GetCategoryLink ( $func, $catid, $pagename, $rel, $class );
		} else {
			$pagelink = CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&page=' . $page, $pagename, '', $rel, $class );
		}

		return $pagelink;
	}

	function GetReviewURL($redirect = false) {
		return $redirect == false ? KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=review' ) : htmlspecialchars_decode ( KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=review' ) );
	}

	function GetCategoryReviewListLink($catid, $catname, $rel = 'nofollow', $class = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=review&action=list&catid=' . $catid, $catname, '', $rel, $class );
	}

	function GetThreadLink($func, $catid, $threadid, $threadname, $title, $rel = 'follow', $class = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid, $threadname, $title, $rel, $class );
	}

	function GetThreadPageLink($func, $catid, $threadid, $page, $limit, $name, $anker = '', $rel = 'follow', $class = '') {
		$kunena_config = & CKunenaConfig::getInstance ();
		if ($page == 1 || ! is_numeric ( $page ) || ! is_numeric ( $limit )) {
			// page 1 is identical to a link to the top of the thread
			$pagelink = CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid, $name, '', $rel, $class, $anker );
		} else {
			$pagelink = CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid . '&limit=' . $limit . '&limitstart=' . (($page - 1) * $limit), $name, '', $rel, $class, $anker );
		}

		return $pagelink;
	}

	// GetThreadPageURL is basically identically to the prior function except that it returns a clear text
	// non-encoded URL. This functions is used by the email function to notify users about new posts.
	function GetThreadPageURL($func, $catid, $threadid, $page, $limit = '', $anker = '', $redirect = false) {
		if ($page == 1 || ! is_numeric ( $page ) || ! is_numeric ( $limit )) {
			// page 1 is identical to a link to the top of the thread
			$pageURL = htmlspecialchars_decode ( KUNENA_LIVEURLREL ) . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid;
		} else {
			$pageURL = htmlspecialchars_decode ( KUNENA_LIVEURLREL ) . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid . '&limit=' . $limit . '&limitstart=' . (($page - 1) * $limit);
		}

		return $redirect == false ? KunenaRoute::_ ( $pageURL ) . ($anker ? ('#' . $anker) : '') : htmlspecialchars_decode ( KunenaRoute::_ ( $pageURL ) . ($anker ? ('#' . $anker) : '') );
	}

	function GetSamePageAnkerLink($anker, $name, $rel = 'nofollow', $class = '') {
		jimport ( 'joomla.environment.request' );
		return CKunenaLink::GetSefHrefLink ( JRequest::getURI (), $name, '', $rel, $class, $anker );
	}

	function GetReportURL($redirect = false) {
		return $redirect == false ? KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=report' ) : htmlspecialchars_decode ( KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=report' ) );
	}

	function GetReportMessageLink($catid, $id, $name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=report&catid=' . $catid . '&id=' . $id, $name, '', $rel );
	}

	function GetMessageIPLink($msg_ip, $rel = 'nofollow') {
		if (! empty ( $msg_ip )) {
			$iplink = '<a href="http://whois.domaintools.com/' . $msg_ip . '" target="_blank">';
			$iplink .= 'IP: ' . $msg_ip . '</a>';
		} else {
			$iplink = '&nbsp;';
		}

		return $iplink;
	}

	function GetMyProfileLink($userid, $name, $rel = 'nofollow', $task = '') {
		return CKunenaLink::GetHrefLink ( CKunenaLink::GetMyProfileURL ( $userid, $task ), $name, '', $rel );
	}

	function GetMyProfileURL($userid = '', $task = '', $redirect = false) {
		$profile = KunenaFactory::getProfile ();
		$link = $profile->getProfileURL ( $userid, $task );
		return $redirect != true ? $link : htmlspecialchars_decode ( $link );
	}

	function GetProfileLink($userid, $name, $rel = 'nofollow', $class = '') {
		if ($userid > 0) {
			if (CKunenaTools::isAdmin ( $userid )) {
				$class = 'admin';
			} else if (CKunenaTools::isModerator ( $userid )) {
				$class = 'moderator';
			} else {
				$class = '';
			}
			$link = CKunenaLink::GetProfileURL ( $userid );
			if (! empty ( $link ))
				return CKunenaLink::GetHrefLink ( $link, $name, '', $rel, $class );
		}
		return $name;
	}

	function GetProfileURL($userid) {
		$profile = KunenaFactory::getProfile ();
		return $profile->getProfileURL ( $userid );
	}

	function GetUserlistURL($action = '') {
		$profile = KunenaFactory::getProfile ();
		return $profile->getUserListURL ( $action );
	}

	function GetUserlistLink($action, $name, $rel = 'nofollow', $class = '') {
		$link = self::GetUserlistURL ( $action );
		return self::GetHrefLink ( $link, $name, '', $rel, $class );
	}

	function GetViewLink($func, $id, $catid, $view, $name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&id=' . $id . '&view=' . $view . '&catid=' . $catid, $name, '', $rel );
	}

	function GetPendingMessagesLink($catid, $name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=review&action=list&catid=' . $catid, $name, '', $rel );
	}

	function GetShowLatestLink($name, $rel = 'follow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=latest', $name, '', $rel );
	}

	function GetShowLatestURL() {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=latest' );
	}

	function GetShowMyLatestLink($name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=mylatest', $name, '', $rel );
	}

	function GetShowNoRepliesLink($name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=noreplies', $name, '', $rel );
	}

	function GetShowLatestThreadsLink($period, $name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=latest&do=show&sel=' . $period, $name, '', $rel );
	}

	function GetShowLatestThreadsURL($period) {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=latest&do=show&sel=' . $period );
	}

	// Function required to support default template
	function GetLatestPageLink($func, $page, $rel = 'follow', $class = '', $sel = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&page=' . $page . (($sel) ? '&sel=' . $sel : ''), $page, '', $rel, $class );
	}

	function GetPostURL($catid = '', $redirect = false) {
		$cat = '';
		if ($catid != '')
			$cat = "&catid={$catid}";
		return $redirect == false ? KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=post' . $cat ) : htmlspecialchars_decode ( KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=post' . $cat ) );
	}

	function GetPostNewTopicLink($catid, $name, $rel = 'nofollow', $class = '', $title = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=post&do=new&catid=' . $catid, $name, $title, $rel, $class );
	}

	function GetTopicPostLink($do, $catid, $id, $name, $rel = 'nofollow', $class = '', $title = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=post&do=' . $do . '&catid=' . $catid . '&id=' . $id, $name, $title, $rel, $class );
	}

	function GetTopicPostReplyLink($do, $catid, $id, $name, $rel = 'nofollow', $class = '', $title = '', $attr = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=post&do=' . $do . '&catid=' . $catid . '&id=' . $id, $name, $title, $rel, $class, '', $attr );
	}

	function GetEmailLink($email, $name) {
		return CKunenaLink::GetSimpleLink ( 'mailto:' . stripslashes ( $email ), stripslashes ( $name ) );
	}

	function GetKarmaLink($do, $catid, $pid, $userid, $name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=karma&do=' . $do . '&userid=' . $userid . '&pid=' . $pid . '&catid=' . $catid, $name, '', $rel );
	}

	function GetRulesLink($name, $rel = 'nofollow') {
		$kunena_config = & CKunenaConfig::getInstance ();
		$ruleslink = $kunena_config->rules_infb ? KUNENA_LIVEURLREL . '&func=rules' : $kunena_config->rules_link;
		return CKunenaLink::GetSefHrefLink ( $ruleslink, $name, '', $rel );
	}

	function GetHelpLink($name, $rel = 'nofollow') {
		$kunena_config = & CKunenaConfig::getInstance ();
		$helplink = $kunena_config->help_infb ? KUNENA_LIVEURLREL . '&func=help' : $kunena_config->help_link;
		return CKunenaLink::GetSefHrefLink ( $helplink, $name, '', $rel );
	}

	function GetSearchURL($func, $searchword, $limitstart, $limit, $params = '') {
		$kunena_config = & CKunenaConfig::getInstance ();
		$limitstr = "";
		if ($limitstart > 0)
			$limitstr .= "&limitstart=$limitstart";
		if ($limit > 0 && $limit != $kunena_config->messages_per_page_search)
			$limitstr .= "&limit=$limit";
		if ($searchword)
			$searchword = '&q=' . urlencode ( $searchword );
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func={$func}{$searchword}{$params}{$limitstr}" );
	}

	function GetSearchLink($func, $searchword, $limitstart, $limit, $name, $params = '', $rel = 'nofollow') {
		$kunena_config = & CKunenaConfig::getInstance ();
		$limitstr = "";
		if ($limitstart > 0)
			$limitstr .= "&limitstart=$limitstart";
		if ($limit > 0 && $limit != $kunena_config->messages_per_page_search)
			$limitstr .= "&limit=$limit";
		if ($searchword)
			$searchword = '&q=' . urlencode ( $searchword );
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . "&func={$func}{$searchword}{$params}{$limitstr}", $name, '', $rel );
	}

	function GetAnnouncementURL($do, $id = NULL) {
		$idstring = '';
		if ($id !== NULL)
			$idstring .= "&id=$id";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=announcement&do={$do}{$idstring}" );
	}

	function GetAnnouncementLink($do, $id = NULL, $name, $title, $rel = 'nofollow') {
		$idstring = '';
		if ($id !== NULL)
			$idstring .= "&id=$id";
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . "&func=announcement&do={$do}{$idstring}", $name, $title, $rel );
	}

	function GetPollURL($do, $id = NULL, $catid) {
		$idstring = '';
		if ($id !== NULL)
			$idstring .= "&id=$id";
		$catidstr = "&catid=$catid";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=poll&do={$do}{$idstring}{$catidstr}" );
	}

	function GetJsonURL($action, $do = '') {

		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=json;&action=$action&do=$do" );
	}

	function GetMarkThisReadLink($catid, $name, $rel = 'nofollow', $title = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=markThisRead&catid=' . $catid, $name, $title, $rel );
	}

	function GetWhoIsOnlineLink($name, $class, $rel = 'follow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=who', $name, '', $rel, $class );
	}

	function GetStatsLink($name, $class = '', $rel = 'follow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=stats', $name, '', $rel, $class );
	}

	//
	//Some URL functions for the discuss bot
	//
	function GetContentView($rowid, $rowItemid) {
		return KunenaRoute::_ ( 'index.php?option=com_content&task=view&Itemid=' . $rowItemid . '&id=' . $rowid );
	}

	//
	// Macro functions that build more complex html output with embedded links
	//


	//
	// This function builds the auto redirect block to go back to the latest post of a particular thread
	// It is used for various operations. Input parameter is any post id. It will determine the thread,
	// latest post of that thread and number of pages based on the supplied page limit.
	//
	function GetLatestPostAutoRedirectHTML($pid, $limit, $catid = 0) {
		$kunena_config = & CKunenaConfig::getInstance ();
		$kunena_db = &JFactory::getDBO ();
		// First determine the thread, latest post and number of posts for the post supplied
		$where = '';
		if ($catid > 0)
			$where .= " AND a.catid = {$catid} ";
		$kunena_db->setQuery ( "SELECT a.thread AS thread, MAX(a.id) AS latest_id, MAX(a.catid) AS catid, COUNT(*) AS totalmessages
                             FROM #__fb_messages AS a,
                                (SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$pid}') AS b
                             WHERE a.thread = b.thread AND a.hold='0' {$where}
                             GROUP BY a.thread" );
		$result = $kunena_db->loadObject ();
		check_dberror ( "Unable to retrieve latest post." );

		// Now Calculate the number of pages for this particular thread
		if (is_object ( $result )) {
			$catid = $result->catid;
			$threadPages = ceil ( $result->totalmessages / $limit );
		}

		// Finally build output block


		$Output = '<div align="center">';
		if (is_object ( $result ))
			$Output .= CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $catid, $result->thread, $threadPages, $limit, JText::_ ( 'COM_KUNENA_POST_SUCCESS_VIEW' ), $result->latest_id ) . '<br />';
		$Output .= CKunenaLink::GetCategoryLink ( 'showcat', $catid, JText::_ ( 'COM_KUNENA_POST_SUCCESS_FORUM' ) ) . '<br />';
		$Output .= '</div>';
		if (is_object ( $result ))
			$Output .= CKunenaLink::GetAutoRedirectHTML ( CKunenaLink::GetThreadPageURL ( 'view', $catid, $result->thread, $threadPages, $limit, $result->latest_id ), 3500 );
		else
			$Output .= CKunenaLink::GetAutoRedirectHTML ( KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=showcat&catid=' . $catid ), 3500 );

		return $Output;
	}

	function GetLatestPageAutoRedirectURL($pid, $limit, $catid = 0) {
		$kunena_config = & CKunenaConfig::getInstance ();
		$kunena_db = &JFactory::getDBO ();
		// First determine the thread, latest post and number of posts for the post supplied
		$where = '';
		if ($catid > 0)
			$where .= " AND a.catid = {$catid} ";
		$kunena_db->setQuery ( "SELECT a.thread AS thread, MAX(a.id) AS latest_id, MAX(a.catid) AS catid, COUNT(*) AS totalmessages
                             FROM #__fb_messages AS a,
                                (SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$pid}') AS b
                             WHERE a.thread = b.thread AND a.hold='0' {$where}
                             GROUP BY a.thread" );
		$result = $kunena_db->loadObject ();
		check_dberror ( "Unable to retrieve latest post." );
		if (! is_object ( $result ))
			return htmlspecialchars_decode ( KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=showcat&catid=' . $catid ) );

		// Now Calculate the number of pages for this particular thread
		$catid = $result->catid;
		$threadPages = ceil ( $result->totalmessages / $limit );

		// Finally build output block
		return htmlspecialchars_decode ( CKunenaLink::GetThreadPageURL ( 'view', $catid, $result->thread, $threadPages, $limit ) );
	}

	function GetLatestCategoryAutoRedirectHTML($catid) {
		$Output = '<div id="Kunena_post_result" align="center">';
		$Output .= CKunenaLink::GetCategoryLink ( 'showcat', $catid, JText::_ ( 'COM_KUNENA_POST_SUCCESS_FORUM' ) ) . '<br />';
		$Output .= '</div>';
		$Output .= CKunenaLink::GetAutoRedirectHTML ( KUNENA_LIVEURLREL . '&func=showcat&catid=' . $catid, 3500 );

		return $Output;
	}

	function GetAutoRedirectHTML($url, $timeout) {
		$url = htmlspecialchars_decode ( $url );
		$Output = "\n<script type=\"text/javascript\">\n// <![CDATA[\n";
		$Output .= "kunenaRedirectTimeout('$url', $timeout);";
		$Output .= "\n// ]]>\n</script>\n";

		return $Output;
	}

	function GetAutoRedirectThreadPageHTML($func, $catid, $threadid, $page, $limit = '', $anker = '', $timeout) {
		$p_url = CKunenaLink::GetThreadPageURL ( $func, $catid, $threadid, $page, $limit, $anker );
		return CKunenaLink::GetAutoRedirectHTML ( $p_url, $timeout );
	}
}