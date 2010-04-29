<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( '' );

class modKunenaLatestHelper {
	function getKunenaLatestList($params, $page = 0) {
		$db = JFactory::getDBO ();
		$k_config = KunenaFactory::getConfig();
		$my = JFactory::getUser ();
		$nbpoststoshow = $params->get ( 'nbpost' );

		$page = $page < 1 ? 1 : $page;

		$lookcats = explode ( ',', $params->get( 'category_id' ) );
		$catlist = array ();
		foreach ( $lookcats as $catnum ) {
			$catlist [] = ( int ) $catnum;
		}

		$latestcats = '';
		if ( !empty($catlist) && !in_array(0, $catlist)) {
			$catlist = implode ( ',', $catlist );
			$latestcats = ' AND m.catid IN ('.$catlist.') ';
		}

		$userAPI = Kunena::getUserAPI();
		$cat_total = $userAPI->getAllowedCategories($my->id);

		$query = "Select COUNT(DISTINCT t.thread) FROM #__fb_messages AS t
			INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$cat_total})
		AND t.hold=0 AND t.moved=0 AND t.catid IN ({$cat_total})" . $latestcats; // if categories are limited apply filter

		$db->setQuery ( $query );
		$total = ( int ) $db->loadResult ();
		CKunenaTools::checkDatabaseError();

		$order = "lastid DESC";
		$query = "SELECT m.id, MAX(t.id) AS lastid FROM #__fb_messages AS t
			INNER JOIN #__fb_messages AS m ON m.id=t.thread
			WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$cat_total})
			AND t.hold='0' AND t.moved='0' AND t.catid IN ({$cat_total}) {$latestcats}
			GROUP BY t.thread
			ORDER BY {$order}
		";

		$db->setQuery ( $query, '', $params->get ( 'nbpost' ) );
		$threadids = $db->loadResultArray ();
		CKunenaTools::checkDatabaseError();

		$idstr = @join ( ",", $threadids );

		if (empty($threadids)) {
			return array();
		}

		$query = "SELECT a.*, j.id AS userid, u.posts, t.message AS messagetext, l.myfavorite, l.favcount, l.attachments,
			l.msgcount, l.mycount, l.lastid, l.mylastid, l.lastid AS lastread, 0 AS unread, l.lasttime, u.avatar, c.id AS catid, c.name AS catname, c.class_sfx
		FROM (
			SELECT m.thread, MAX(f.userid IS NOT null AND f.userid='{$my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount, COUNT(a.mesid) AS attachments,
				COUNT(DISTINCT m.id) AS msgcount, COUNT(DISTINCT IF(m.userid={$my->id}, m.id, NULL)) AS mycount, MAX(m.id) AS lastid, MAX(IF(m.userid={$my->id}, m.id, 0)) AS mylastid, MAX(m.time) AS lasttime
			FROM #__fb_messages AS m";
		if ($k_config->allowfavorites)
			$query .= " LEFT JOIN #__fb_favorites AS f ON f.thread = m.thread";
		else
			$query .= " LEFT JOIN #__fb_favorites AS f ON f.thread = 0";
		$query .= "
			LEFT JOIN #__kunena_attachments AS a ON a.mesid = m.thread
			WHERE m.hold='0' AND m.moved='0' AND m.thread IN ({$idstr})
			GROUP BY thread
		) AS l
		INNER JOIN #__fb_messages AS a ON a.thread = l.thread
		INNER JOIN #__fb_messages_text AS t ON a.thread = t.mesid
		LEFT JOIN #__users AS j ON j.id = a.userid
		LEFT JOIN #__fb_users AS u ON u.userid = j.id
		LEFT JOIN #__fb_categories AS c ON c.id = a.catid
		WHERE (a.parent='0' OR a.id=l.lastid)
		ORDER BY {$order} ";

		$db->setQuery ( $query );
		$messagelist = $db->loadObjectList ();
		CKunenaTools::checkDatabaseError();

		$messages = array();
		foreach ( $messagelist as $message ) {
			if ($message->parent == 0) {
				$messages [$message->id] = $message;
			}
		}
		return $messages;
	}

	function userAvatar( $userid,  $params ) {
    $kunena_user = KunenaFactory::getUser((int)$userid);
	  $avatarlink = $kunena_user->getAvatarLink('', $params->get ( 'avatarwidth') ,$params->get ( 'avatarheight' ));
    return CKunenaLink::GetProfileLink ($userid,$avatarlink, $kunena_user->name  );

  }
}
