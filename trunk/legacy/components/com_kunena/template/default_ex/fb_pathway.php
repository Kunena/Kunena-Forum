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
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');
global $fbConfig;
?>
<!-- Pathway -->
<?php
$sfunc = mosGetParam($_REQUEST, "func", null);

if ($func != "")
{
        if (file_exists($mosConfig_absolute_path . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png')) {
            $jr_arrow = '<img src="' . KUNENA_JLIVEURL . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png" alt="" />';
        }
        else {
            $jr_arrow = '<img src="' . KUNENA_JLIVEURL . '/images/M_images/arrow.png" alt="" />';
        }

        $catids = intval($catid);
        $parent_ids = 1000;
        $jr_it = 1;
        $jr_path_menu = array ();

        while ($parent_ids)
        {
            $query = "select * from #__fb_categories where id=$catids and published=1";
            $database->setQuery($query);
            $database->loadObject($results);
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

            // write path
            if (empty($spath)) {
                $spath = $sname;
            }
            else {
                $spath = $sname . " " . $jr_arrow . $jr_arrow . " " . $spath;
            }

            // next looping
            $catids = $parent_ids;
            $jr_it++;
        }

        $jr_path_menu[] = $shome;
        //reverse the array
        $jr_path_menu = array_reverse($jr_path_menu);

        //  echo $shome." " . $jr_arrow .$jr_arrow ." ". $spath;
        //attach topic name
        if ($sfunc == "view" and $id)
        {
            $sql = "select subject from #__fb_messages where id = $id";
            $database->setQuery($sql);
            $jr_topic_title = stripslashes(htmlspecialchars($database->loadResult()));
            $jr_path_menu[] = $jr_topic_title;
        //     echo " " . $jr_arrow .$jr_arrow ." ". $jr_topic_title;
        }

        // print the list
        $jr_forum_count = count($jr_path_menu);

	$firepath = '';
        for ($i = 0; $i <= (count($jr_path_menu) - 2); $i++)
        {
            if ($i > 0 && $i != $jr_forum_count) {
                $firepath .= " " . $jr_arrow . $jr_arrow . " ";
            }

            $firepath .= $jr_path_menu[$i] . " ";
        }

	$fireinfo = '';
        if ($forumLocked)
        {
            $fireinfo = $fbIcons['forumlocked'] ? '<img src="' . KUNENA_URLICONSPATH . '' . $fbIcons['forumlocked']
                     . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0"  alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
            $lockedForum = 1;
        }

        if ($forumReviewed)
        {
            $fireinfo = $fbIcons['forummoderated'] ? '<img src="' . KUNENA_URLICONSPATH . '' . $fbIcons['forummoderated']
                     . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '">';
            $moderatedForum = 1;
        }

         //get viewing
        $fb_queryName = $fbConfig->username ? "username" : "name";
		$query= "SELECT w.userid, u.$fb_queryName AS username , k.showOnline FROM #__fb_whoisonline AS w LEFT JOIN #__users AS u ON u.id=w.userid LEFT JOIN #__fb_users AS k ON k.userid=w.userid  WHERE w.link like '%" . addslashes($_SERVER['REQUEST_URI']) . "%'";
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
			$fireonline .= " ($total_viewing " . _KUNENA_PATHWAY_VIEWING . ")&nbsp;";
			$totalguest = 0;
			foreach ($users as $user) {
				if ($user->userid != 0)
				{
					if ( $user->showOnline > 0 ){
					$fireonline .= '<small>' . CKunenaLink::GetProfileLink($fbConfig,  $user->userid, $user->username) . ' ,</small> ';
					}
				}
				else
				{
					$totalguest = $totalguest + 1;
				}
			}
      if ($totalguest > 0) { if ($totalguest==1) { $fireonline .= '<small style="font-weight:normal;" >('.$totalguest.') '._WHO_ONLINE_GUEST.'</small>'; } else { $fireonline .= '<small style="font-weight:normal;" >('.$totalguest.') '._WHO_ONLINE_GUESTS.'</small>'; } }
       }

	$fr_title = $fr_title_name . $jr_topic_title;
        $mainframe->setPageTitle(($fr_title ? $fr_title : _KUNENA_CATEGORIES) . ' - ' . stripslashes($fbConfig->board_title));

	$pathway1 = '<div class="forum-pathway-1">';
	$pathway1 .= CKunenaLink::GetKunenaLink( htmlspecialchars(stripslashes($fbConfig->board_title)) ) . $firepath . $fireinfo;
	$pathway1 .= '</div>';
	$pathway2 = '<div class="forum-pathway-2">';
	$pathway2 .= $fireonline;
	$pathway2 .= '</div>';
        unset($shome, $spath, $parent_ids, $catids, $results, $sname);

      echo '<div class = "'. $boardclass .'forum-pathway">';
      echo $pathway1.$pathway2;
      echo '</div>';
}
?>
<!-- / Pathway -->
