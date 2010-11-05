<?php
/**
* @version $Id: fb_pathway.php 362 2009-02-11 00:30:24Z mahagr $
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
defined( '_JEXEC' ) or die('Restricted access');
global $fbConfig;
?>
<!-- Pathway -->
<?php
$sfunc = JRequest::getVar("func", null);

if ($func != "")
{
        $catids = intval($catid);
        $parent_ids = 1000;
        $jr_it = 1;
        $jr_path_menu = array ();

        while ($parent_ids)
        {
            $query = "select * from #__fb_categories where id=$catids and published=1";
            $database->setQuery($query);
            $results = $database->loadObject();
			$parent_ids = $results->parent;
			$fr_name = htmlspecialchars(trim(stripslashes($results->name)));
            //$cids=@mysql_result( $results, 0, 'id' );
            $sname = CKunenaLink::GetCategoryLink( 'showcat', $catids, $fr_name);

            if ($jr_it == 1 && $sfunc != "view")
            {
                $fr_title_name = $fr_name;
                $jr_path_menu[] = $fr_name;
            }
            else {
                $jr_path_menu[] = $sname;
            }

            // next looping
            $catids = $parent_ids;
            $jr_it++;
        }

        $jr_path_menu[] = $shome;
        //reverse the array
        $jr_path_menu = array_reverse($jr_path_menu);

        //attach topic name
        if ($sfunc == "view" and $id)
        {
            $sql = "select subject from #__fb_messages where id = $id";
            $database->setQuery($sql);
            $jr_topic_title = stripslashes(html_entity_decode_utf8($database->loadResult()));
            $jr_path_menu[] = $jr_topic_title;
        //     echo " " . $jr_arrow .$jr_arrow ." ". $jr_topic_title;
        }

        // print the list
        $jr_forum_count = count($jr_path_menu);

	$firepath = '<div class="path-element-first">'. CKunenaLink::GetKunenaLink( htmlspecialchars(stripslashes($fbConfig->board_title)) ) . '</div>';
	$filelast = '';
        for ($i = 0; $i <= (count($jr_path_menu) - 1); $i++)
        {
            if ($i > 0 && $i == $jr_forum_count - 1) {
                $firelast .= '<br /><div class="path-element-last">' . $jr_path_menu[$i] . '</div>';
            }
            else if ($i > 0) {
                $firepath .= '<div class="path-element">' . $jr_path_menu[$i] . '</div>';
            }
        }

	$fireinfo = '';
        if ($forumLocked)
        {
            $fireinfo = $fbIcons['forumlocked'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['forumlocked']
                     . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0"  alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
            $lockedForum = 1;
        }

        if ($forumReviewed)
        {
            $fireinfo = $fbIcons['forummoderated'] ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['forummoderated']
                     . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '">';
            $moderatedForum = 1;
        }

         //get viewing
        $fb_queryName = $fbConfig->username ? "username" : "name";
		$query= "SELECT w.userid, u.$fb_queryName AS username , k.showOnline FROM #__fb_whoisonline AS w LEFT JOIN #__users AS u ON u.id=w.userid LEFT JOIN #__fb_users AS k ON k.userid=w.userid  WHERE w.link like '%" . addslashes($_SERVER['REQUEST_URI']) . "%' GROUP BY w.userid ORDER BY u.$fb_queryName ASC";
		$database->setQuery($query);
		$users = $database->loadObjectList();
			check_dberror("Unable to load who is online.");
		$total_viewing = count($users);

	$fireonline = '';
        if ($sfunc == "userprofile")
        {
            $fireonline .= _USER_PROFILE;
            $fireonline .= $username;
        }
        else {
			$fireonline .= "<div class=\"path-element-users\">($total_viewing " . _KUNENA_PATHWAY_VIEWING . ")&nbsp;";
			$totalguest = 0;
                        $divider = ', ';
			$lastone = end($users);
			foreach ($users as $user) {
				if ($user->userid != 0)
				{
                                        if($user==$lastone && !$totalguest){
                                            $divider = '';
                                        }
					if ( $user->showOnline > 0 ){
					$fireonline .= CKunenaLink::GetProfileLink($fbConfig,  $user->userid, $user->username) . $divider;
					}
				}
				else
				{
					$totalguest = $totalguest + 1;
				}
			}
			if ($totalguest > 0) { 
				if ($totalguest==1) { 
					$fireonline .= '('.$totalguest.') '._WHO_ONLINE_GUEST; 
				} else { 
					$fireonline .= '('.$totalguest.') '._WHO_ONLINE_GUESTS; 
				}
			}
			$fireonline .= '</div>';
       }

	$fr_title = $fr_title_name . $jr_topic_title;
        $mainframe->setPageTitle(($fr_title ? $fr_title : _KUNENA_CATEGORIES) . ' - ' . stripslashes($fbConfig->board_title));

	$pathway1 = $firepath . $fireinfo;
	$pathway2 .= $firelast . $fireonline;
        unset($shome, $spath, $parent_ids, $catids, $results, $sname);

      echo '<div class = "'. $boardclass .'forum-pathway">';
      echo $pathway1.$pathway2;
      echo '</div>';
}
?>
<!-- / Pathway -->
