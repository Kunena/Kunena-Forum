<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
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
		return CKunenaLink::GetHrefLink ( 'http://www.kunena.org', 'Kunena', 'Kunena', 'follow', NULL, NULL, 'target="_blank"' );
	}

	function GetTeamCreditsLink($catid, $name = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=credits&catid=' . $catid, $name, '', 'follow' );
	}

	function GetKunenaLink($name, $rel = 'follow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL, $name, '', $rel );
	}

	function GetAttachmentLink($folder,$filename,$name,$title = '', $rel = 'nofollow') {
		return CKunenaLink::GetHrefLink ( JURI::ROOT().$folder.'/'.$filename, $name, $title, $rel );
	}

	function GetKunenaURL($xhtml = true) {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL, $xhtml );
	}

	function GetRSSLink($name, $rel = 'follow', $params = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=rss&format=feed' . $params, $name, '', $rel, '', '', 'target="_blank"' );
	}

	function GetRSSURL($params = '', $xhtml = true) {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=rss&format=feed' . $params, $xhtml );
	}

	function GetPDFLink($catid, $limit, $limitstart, $id, $name, $rel = 'nofollow', $class = '', $title = '') {
		$strlimit = '';
		$strlimitstart = '';
		if ( !empty($limit) ) $strlimit = "&limit={$limit}";
		if ( !empty($limitstart) ) $strlimitstart = "&limitstart={$limitstart}";
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&id=' . $id . '&catid=' . $catid . '&func=pdf' .$strlimit.$strlimitstart, $name, $title, $rel, $class );
	}

	function GetCategoryLink($func, $catid, $catname, $rel = 'follow', $class = '', $title = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid, $catname, $title, $rel, $class );
	}

	function GetCategoryActionLink($func, $catid, $catname, $rel = 'follow', $class = '', $title = '', $extra = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . $extra . '&'.JUtility::getToken().'=1', $catname, $title, $rel, $class );
	}

	function GetCategoryURL($func, $catid = '', $xhtml = true) {
		$strcatid = '';
		if ($catid != '')
			$strcatid = "&catid={$catid}";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=' . $func . $strcatid, $xhtml );
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

	function GetReviewLink($name, $rel = 'nofollow', $class = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=review', $name, '', $rel, $class );
	}

	function GetReviewURL($xhtml = true) {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=review', $xhtml );
	}

	function GetCategoryReviewListLink($catid, $catname, $rel = 'nofollow', $class = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=review&action=list&catid=' . $catid, $catname, '', $rel, $class );
	}

	function GetThreadLink($func, $catid, $threadid, $threadname, $title, $rel = 'follow', $class = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid, $threadname, $title, $rel, $class );
	}

	function GetThreadLayoutLink($layout, $catid, $threadid, $mesid, $threadname, $limitstart=0, $limit=0, $title='', $rel = 'nofollow', $class = '') {
		$anker = '';
		$query = array();
		if ($mesid) {
			if (!$layout) $l = JFactory::getApplication()->getUserState( "com_kunena.view_layout", 'view' );
			else $l = $layout;
			if ($l == 'threaded') $query[] = "&mesid={$mesid}";
			else $anker = $mesid;
		}
		if ($layout) $query[] = "&layout={$layout}";
		if ($limitstart) {
			$query[] = "&limitstart={$limitstart}";
			$query[] = "&limit={$limit}";
		}
		$query = implode('', $query);
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=view&catid=' . $catid . '&id=' . $threadid . $query, $threadname, $title, $rel, $class, $anker );
	}
	function GetThreadPageLink($func, $catid, $threadid, $page, $limit, $name, $anker = '', $rel = 'follow', $class = '') {
		$kunena_config = KunenaFactory::getConfig ();
		$pagelink = CKunenaLink::GetHrefLink ( self::GetThreadPageURL($func, $catid, $threadid, $page, $limit, $anker), $name, '', $rel, $class );
		return $pagelink;
	}

	function GetThreadPageSpecialLink($func, $catid, $threadid, $page, $limit, $name, $anker = '', $rel = 'follow', $class = '', $title='') {
		$kunena_config = KunenaFactory::getConfig ();
		if ($page == 1 || ! is_numeric ( $page ) || ! is_numeric ( $limit )) {
			// page 1 is identical to a link to the top of the thread
			$pagelink = CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid, $name, $title, $rel, $class, $anker );
		} else {
			$pagelink = CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid . '&limit=' . $limit . '&limitstart=' . (($page - 1) * $limit), $name, $title, $rel, $class, $anker );
		}

		return $pagelink;
	}

	function GetThreadPageURL($func, $catid, $threadid, $page, $limit = '', $anker = '', $xhtml = true) {
		$layout = JFactory::getApplication()->getUserState( "com_kunena.view_layout", 'view' );
		$query = '';
		if ($layout == 'threaded' && $anker>0) {
			$query = "&mesid={$anker}";
			$anker = '';
		}
		if ($page == 1 || ! is_numeric ( $page ) || ! is_numeric ( $limit )) {
			// page 1 is identical to a link to the top of the thread
			$pageURL = KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid . $query;
		} else {
			$pageURL = KUNENA_LIVEURLREL . '&func=' . $func . '&catid=' . $catid . '&id=' . $threadid . $query . '&limit=' . $limit . '&limitstart=' . (($page - 1) * $limit);
		}

		return KunenaRoute::_ ( $pageURL, $xhtml ) . ($anker ? ('#' . $anker) : '');
	}

	function GetSamePageAnkerLink($anker, $name, $rel = 'nofollow', $class = '') {
		jimport ( 'joomla.environment.request' );
		return CKunenaLink::GetHrefLink ( htmlspecialchars(JRequest::getURI (), ENT_COMPAT, 'UTF-8'), $name, '', $rel, $class, $anker );
	}

	function GetReportURL($xhtml = true) {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=report', $xhtml );
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

	// Returns always link to Kunena profile
	function GetMyProfileLink($userid, $name = null, $rel = 'nofollow', $task = '') {
		if (!$name) {
			$profile = KunenaFactory::getUser($userid);
			$name = htmlspecialchars($profile->getName(), ENT_COMPAT, 'UTF-8');
		}
		return CKunenaLink::GetHrefLink ( CKunenaLink::GetMyProfileURL ( $userid, $task ), $name, '', $rel );
	}

	// Returns always url to Kunena profile
	function GetMyProfileURL($userid = 0, $task = '', $xhtml = true, $extra = '') {
		if (!$task) {
			// Workaround for menu redirect: be more verbose
			kimport('integration.integration');
			$profileIntegration = KunenaIntegration::detectIntegration('profile', true);
			if ($profileIntegration != 'kunena') $task='summary';
		}
		$my = JFactory::getUser();
		if ($userid && $userid!=$my->id) $userid = "&userid=$userid";
		else $userid = '';
		if ($task) $task = "&do=$task";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=profile{$userid}{$task}{$extra}", $xhtml );
	}

	function GetProfileLink($userid, $name = null, $title ='', $rel = 'nofollow', $class = '') {
		if (!$name) {
			$profile = KunenaFactory::getUser($userid);
			$name = htmlspecialchars($profile->getName(), ENT_COMPAT, 'UTF-8');
		}
		if ($userid == 0) {
			$uclass = 'kwho-guest';
		} else if (CKunenaTools::isAdmin ( $userid )) {
			$uclass = 'kwho-admin';
		} else if (CKunenaTools::isModerator ( $userid, false )) {
			$uclass = 'kwho-globalmoderator';
		} else if (CKunenaTools::isModerator ( $userid )) {
			$uclass = 'kwho-moderator';
		} else {
			$uclass = 'kwho-user';
		}
		if ($userid > 0) {
			$link = CKunenaLink::GetProfileURL ( $userid );
			if (! empty ( $link ))
				return CKunenaLink::GetHrefLink ( $link, $name, $title, $rel, $uclass );
		}
		return "<span class=\"{$uclass}\">{$name}</span>";
	}

	function GetProfileURL($userid, $xhtml = true) {
		$profile = KunenaFactory::getProfile ();
		return $profile->getProfileURL ( $userid, '', $xhtml );
	}

	function GetUserlistURL($action = '', $xhtml = true) {
		$profile = KunenaFactory::getProfile ();
		return $profile->getUserListURL ( $action, $xhtml );
	}

	function GetUserlistPostURL($xhtml = true) {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=userlist', $xhtml );
	}

	function GetModerateUserLink($userid, $name = null, $title ='', $rel = 'nofollow', $class = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=moderateuser&userid=' . $userid, $name, $title, $rel, $class );
	}

	function GetUserlistLink($action, $name, $rel = 'nofollow', $class = '') {
		$link = self::GetUserlistURL ( $action );
		if ($link) {
			return self::GetHrefLink ( $link, $name, '', $rel, $class );
		}
		return $name;
	}

	function GetViewLink($func, $id, $catid, $view, $name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=' . $func . '&id=' . $id . '&view=' . $view . '&catid=' . $catid, $name, '', $rel );
	}

	function GetPendingMessagesLink($catid, $name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=review&action=list&catid=' . $catid, $name, '', $rel );
	}

	function GetShowLatestLink($name, $do = '', $rel = 'follow') {
		if ($do) $do = "&do=$do";
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . "&func=latest{$do}", $name, '', $rel );
	}

	function GetShowLatestURL($xhtml = true) {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=latest', $xhtml );
	}

	function GetShowMyLatestLink($name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=latest&do=mylatest', $name, '', $rel );
	}

	function GetShowMyLatestURL() {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=latest&do=mylatest');
	}

	function GetShowNoRepliesLink($name, $rel = 'nofollow') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=latest&do=noreplies', $name, '', $rel );
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

	function GetPostURL($catid = '', $xhtml = true) {
		$cat = '';
		if ($catid != '')
			$cat = "&catid={$catid}";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=post' . $cat, $xhtml );
	}

	function GetPostNewTopicLink($catid, $name, $rel = 'nofollow', $class = '', $title = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=post&do=new&catid=' . $catid, $name, $title, $rel, $class );
	}

	// Get actions: favorite, subscribe, delete, approve etc
	function GetTopicPostLink($do, $catid, $id, $name, $rel = 'nofollow', $class = '', $title = '', $attr = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=post&do=' . $do . '&catid=' . $catid . '&id=' . $id . '&'.JUtility::getToken().'=1', $name, $title, $rel, $class, '', $attr );
	}

	// Post actions: post, edit, moderate etc
	function GetTopicPostReplyLink($do, $catid, $id, $name, $rel = 'nofollow', $class = '', $title = '', $attr = '') {
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=post&do=' . $do . '&catid=' . $catid . '&id=' . $id, $name, $title, $rel, $class, '', $attr );
	}

	function GetEmailLink($email, $name) {
		return CKunenaLink::GetSimpleLink ( 'mailto:' . $email, $name );
	}

	function GetKarmaLink($do, $catid, $pid, $userid, $name, $rel = 'nofollow') {
		$token = '&'.JUtility::getToken().'=1';
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . '&func=karma&do=' . $do . '&userid=' . $userid . '&pid=' . $pid . '&catid=' . $catid.$token, $name, '', $rel );
	}

	function GetThankYouLink( $catid, $pid, $targetuserid, $name, $title, $class) {
		$token = '&'.JUtility::getToken().'=1';
		return CKunenaLink::GetSefHrefLink (KUNENA_LIVEURLREL.'&func=thankyou&pid='.$pid.'&catid='.$catid.$token, $name, $title, 'nofollow', $class);
	}

	function GetRulesLink($name, $rel = 'nofollow') {
		$kunena_config = KunenaFactory::getConfig ();
		$ruleslink = $kunena_config->rules_infb ? KUNENA_LIVEURLREL . '&func=rules' : $kunena_config->rules_link;
		return CKunenaLink::GetSefHrefLink ( $ruleslink, $name, '', $rel );
	}

	function GetHelpLink($name, $rel = 'nofollow') {
		$kunena_config = KunenaFactory::getConfig ();
		$helplink = $kunena_config->help_infb ? KUNENA_LIVEURLREL . '&func=help' : $kunena_config->help_link;
		return CKunenaLink::GetSefHrefLink ( $helplink, $name, '', $rel );
	}

	function GetSearchURL($func, $searchword='', $limitstart=0, $limit=0, $params = '') {
		$kunena_config = KunenaFactory::getConfig ();
		$limitstr = "";
		if ($limitstart > 0)
			$limitstr .= "&limitstart=$limitstart";
		if ($limit > 0 && $limit != $kunena_config->messages_per_page_search)
			$limitstr .= "&limit=$limit";
		if ($searchword)
			$searchword = '&q=' . urlencode ( $searchword );
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func={$func}{$searchword}{$params}{$limitstr}" );
	}

	function GetWhoURL() {
		return KunenaRoute::_( KUNENA_LIVEURLREL.'&amp;func=who');
	}

	function GetSearchLink($func, $searchword, $limitstart, $limit, $name, $params = '', $rel = 'nofollow') {
		$kunena_config = KunenaFactory::getConfig ();
		$limitstr = "";
		if ($limitstart > 0)
			$limitstr .= "&limitstart=$limitstart";
		if ($limit > 0 && $limit != $kunena_config->messages_per_page_search)
			$limitstr .= "&limit=$limit";
		if ($searchword)
			$searchword = '&q=' . urlencode ( $searchword );
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . "&func={$func}{$searchword}{$params}{$limitstr}", $name, '', $rel );
	}

	function GetAnnouncementURL($do, $id = NULL, $xhtml = true) {
		$idstring = '';
		if ($id)
			$idstring .= "&id=$id";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=announcement&do={$do}{$idstring}", $xhtml );
	}

	function GetAnnouncementLink($do, $id = NULL, $name, $title, $rel = 'nofollow') {
		$idstring = '';
		if ($id)
			$idstring .= "&id=$id";
		return CKunenaLink::GetSefHrefLink ( KUNENA_LIVEURLREL . "&func=announcement&do={$do}{$idstring}", $name, $title, $rel );
	}

	function GetPollURL($do, $id = NULL, $catid) {
		$idstring = '';
		if ($id)
			$idstring .= "&id=$id";
		$catidstr = "&catid=$catid";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=poll&do={$do}{$idstring}{$catidstr}" );
	}

	function GetPollsURL($do, $catid) {
		$catidstr = "&catid=$catid";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=polls&do={$do}{$catidstr}" );
	}

	function GetJsonURL($action='', $do = '', $xhtml = false) {
		if ($action) $action = "&action=$action";
		if ($do) $do = "&do=$do";
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . "&func=json$action$do", $xhtml );
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

	function GetStatsURL() {
		return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=stats' );
	}

	//
	//Some URL functions for the discuss bot
	//
	function GetContentView($rowid, $rowItemid) {
		return KunenaRoute::_ ( 'index.php?option=com_content&task=view&Itemid=' . $rowItemid . '&id=' . $rowid );
	}

	function GetLatestPageAutoRedirectURL($pid, $limit = 0, $catid = 0, $xhtml = false) {
		$kunena_config = KunenaFactory::getConfig ();
		$myprofile = KunenaFactory::getUser ();
		if ($myprofile->ordering != '0') {
			$topic_ordering = $myprofile->ordering == '1' ? true : false;
		} else {
			$topic_ordering = $kunena_config->default_sort == 'asc' ? false : true;
		}
		if (!$limit) $limit = $kunena_config->messages_per_page;
		$kunena_db = &JFactory::getDBO ();
		// First determine the thread, latest post and number of posts for the post supplied
		$where = '';
		if ($catid > 0)
			$where .= " AND a.catid = {$catid} ";
		$kunena_db->setQuery ( "SELECT a.thread AS thread, MAX(a.id) AS latest_id, MAX(a.catid) AS catid, COUNT(*) AS totalmessages
                             FROM #__kunena_messages AS a,
                                (SELECT MAX(thread) AS thread FROM #__kunena_messages WHERE id={$kunena_db->Quote($pid)}) AS b
                             WHERE a.thread = b.thread AND a.hold='0' {$where}
                             GROUP BY a.thread" );
		$result = $kunena_db->loadObject ();
		if (KunenaError::checkDatabaseError()) return;
		if (! is_object ( $result ))
			return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=showcat&catid=' . $catid , $xhtml );

		// Now Calculate the number of pages for this particular thread
		if($topic_ordering) $threadPages = 1;
		else $threadPages = ceil ( $result->totalmessages / $limit );

		// Finally build output block
		return CKunenaLink::GetThreadPageURL ( 'view', $result->catid, $result->thread, $threadPages, $limit, $result->latest_id, $xhtml );
	}

	function GetMessageURL($pid, $catid=0, $limit = 0, $xhtml = true) {
		$kunena_config = KunenaFactory::getConfig ();
		$myprofile = KunenaFactory::getUser ();
		if ($myprofile->ordering != '0') {
			$topic_ordering = $myprofile->ordering == '1' ? '>=' : '<=';
		} else {
			$topic_ordering = $kunena_config->default_sort == 'asc' ? '<=' : '>=';
		}
		$maxmin = $topic_ordering == '<=' ? 'MAX' : 'MIN';
		if ($limit < 1) $limit = $kunena_config->messages_per_page;
		$access = KunenaFactory::getAccessControl();
		$hold = $access->getAllowedHold($myprofile, $catid);
		$kunena_db = JFactory::getDBO ();
		// First determine the thread, latest post and number of posts for the post supplied
		$kunena_db->setQuery ( "SELECT a.thread AS thread, {$maxmin}(a.id) AS latest_id, MAX(a.catid) AS catid, COUNT(*) AS totalmessages
                             FROM #__kunena_messages AS a, (SELECT thread FROM #__kunena_messages WHERE id={$kunena_db->Quote($pid)}) AS b
                             WHERE a.thread = b.thread AND a.hold IN ({$hold}) AND a.id {$topic_ordering} {$kunena_db->Quote($pid)}
                             GROUP BY a.thread" );
		$result = $kunena_db->loadObject ();
		if (KunenaError::checkDatabaseError()) return;
		if (! is_object ( $result ))
			return KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=showcat&catid=' . $result->catid, $xhtml );
		return CKunenaLink::GetThreadPageURL ( 'view', $result->catid, $result->thread, ceil ( $result->totalmessages / $limit ), $limit, $result->latest_id, $xhtml );
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