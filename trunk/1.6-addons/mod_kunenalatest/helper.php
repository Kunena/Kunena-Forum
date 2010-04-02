<?php
/**
* @version		$Id
* @package		klatestpost
* @copyright	(c) 2010 Kunena Team, All rights reserved
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modklatestpostHelper
{
	function getKunenaLatestList(&$params,$k_config,$db,$page = 0)
	{	  
		$my = JFactory::getUser ();
		$nbpoststoshow = $params->get( 'nbpost' );		
						
		$page = $page < 1 ? 1 : $page;
		
		//Time translation
		$back_time = 720 * 3600; //hours*(mins*secs)
		$querytime = time () - $back_time;	 
	
		$lookcats = explode ( ',', $k_config->latestcategory );
		$catlist = array ();
		$latestcats = '';		
		foreach ( $lookcats as $catnum ) {
			if (( int ) $catnum > 0)
				$catlist [] = ( int ) $catnum;
		}
		if (count ( $catlist ))
			$latestcats = " AND m.catid IN (" . implode ( ',', $catlist ) . ") ";
			
	  $query = "Select allowed FROM #__fb_sessions";
		$db->setQuery ( $query );
		$cat_total = $db->loadResult ();		

		$query = "Select COUNT(DISTINCT t.thread) FROM #__fb_messages AS t
			INNER JOIN #__fb_messages AS m ON m.id=t.thread
		WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$cat_total})
		AND t.time >'{$querytime}' AND t.hold=0 AND t.moved=0 AND t.catid IN ({$cat_total})" . $latestcats; // if categories are limited apply filter


		$db->setQuery ( $query );
		$total = ( int ) $db->loadResult ();		
		$order = "lastid DESC";    	
		
		$query = "SELECT m.id, MAX(t.id) AS lastid FROM #__fb_messages AS t
			INNER JOIN #__fb_messages AS m ON m.id=t.thread
			WHERE m.moved='0' AND m.hold='0' AND m.catid IN ({$cat_total})
			AND t.time>'{$querytime}' AND t.hold='0' AND t.moved='0' AND t.catid IN ({$cat_total}) {$latestcats}
			GROUP BY t.thread
			ORDER BY {$order}
		";

		$db->setQuery ( $query, '', $params->get( 'nbpost' ) );
		$threadids = $db->loadResultArray ();
    
    $idstr = @join ( ",", $threadids );
    
		
		$query = "SELECT a.*, j.id AS userid, u.posts, t.message AS messagetext, l.myfavorite, l.favcount, l.attachments,
			l.msgcount, l.mycount, l.lastid, l.mylastid, l.lastid AS lastread, 0 AS unread, u.avatar, c.id AS catid, c.name AS catname, c.class_sfx
		FROM (
			SELECT m.thread, MAX(f.userid IS NOT null AND f.userid='{$my->id}') AS myfavorite, COUNT(DISTINCT f.userid) AS favcount, COUNT(a.mesid) AS attachments,
				COUNT(DISTINCT m.id) AS msgcount, COUNT(DISTINCT IF(m.userid={$my->id}, m.id, NULL)) AS mycount, MAX(m.id) AS lastid, MAX(IF(m.userid={$my->id}, m.id, 0)) AS mylastid, MAX(m.time) AS lasttime
			FROM #__fb_messages AS m";
			if ($k_config->allowfavorites) $query .= " LEFT JOIN #__fb_favorites AS f ON f.thread = m.thread";
			else $query .= " LEFT JOIN (SELECT 0 AS userid, 0 AS myfavorite) AS f ON 1";
			$query .= "
			LEFT JOIN #__fb_attachments AS a ON a.mesid = m.thread
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
    
    $messages = '';	
	  foreach ( $messagelist as $message ) {				
				if ($message->parent == 0) {
					$messages [$message->id] = $message;					
				} 
			}
		return $messages;
	}

  function getKunenaConfigClass()
	{    
		$path = JPATH_SITE.DS.'components'.DS.'com_kunena'.DS.'lib'.DS.'kunena.config.class.php';
		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists( $path )) {
			require_once( $path );
      $return = CKunenaConfig::getInstance ();			
		} else {
			JError::raiseWarning( 0, 'File Kunena Config Class not found.' );
			return $false;
		} 
    		
		return $return;
	}

  function getKunenaLinkClass()
	{    
		$path = JPATH_SITE.DS.'components'.DS.'com_kunena'.DS.'lib'.DS.'kunena.link.class.php';;
		$false = false;

		// If the file exists include it and try to instantiate the object
		if (file_exists( $path )) {
			require_once( $path );
      $return = new CKunenaLink();			
		} else {
			JError::raiseWarning( 0, 'File Kunena Link Class not found.' );
			return $false;
		} 
    		
		return $return;
	}
	
}
