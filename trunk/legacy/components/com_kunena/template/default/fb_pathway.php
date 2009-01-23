<?php
/**
* @version $Id: fb_pathway.php 1064 2008-10-05 23:29:35Z fxstein $
* Fireboard Component
* @package Fireboard
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
?>

    <div class = "<?php echo $boardclass ?>forum-pathway">
        <?php
        if (file_exists($mosConfig_absolute_path . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png')) {
            $jr_arrow = '<img src="' . JB_JLIVEURL . '/templates/' . $mainframe->getTemplate() . '/images/arrow.png" alt="" />';
        }
        else {
            $jr_arrow = '<img src="' . JB_JLIVEURL . '/images/M_images/arrow.png" alt="" />';
        }

        $catids = intval($catid);
        $parent_ids = 1000;
        $jr_it = 1;
        $jr_path_menu = array ();
        $shome = '<div class="forum-pathway-1">' . fb_link::GetFireBoardLink( $fbConfig->board_title );

        while ($parent_ids)
        {
            $query = "select * from #__fb_categories where id=$catids and published=1";
            $database->setQuery($query);
            $database->loadObject($results);
			$parent_ids = $results->parent;
			$fr_name = trim($results->name);
            //$cids=@mysql_result( $results, 0, 'id' );
            $sname = fb_link::GetCategoryLink( 'showcat', $catids, $fr_name);

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
            $jr_topic_title = stripslashes($database->loadResult());
            $jr_path_menu[] = $jr_topic_title;
        //     echo " " . $jr_arrow .$jr_arrow ." ". $jr_topic_title;
        }

        // print the list
        $jr_forum_count = count($jr_path_menu);

        for ($i = 0; $i <= (count($jr_path_menu) - 1); $i++)
        {
            if ($i > 0 && $i == $jr_forum_count - 1) {
                echo '</div><div class="forum-pathway-2">';
            }
            else if ($i > 0) {
                echo " " . $jr_arrow . $jr_arrow . " ";
            }

            echo $jr_path_menu[$i] . " ";
        }

        if ($forumLocked)
        {
            echo $fbIcons['forumlocked'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['forumlocked']
                     . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '  <img src="' . JB_URLEMOTIONSPATH . 'lock.gif"  border="0"  alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
            $lockedForum = 1;
        }
        else {
            echo "";
        }

        if ($forumReviewed)
        {
            echo $fbIcons['forummoderated'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['forummoderated']
                     . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '  <img src="' . JB_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '">';
            $moderatedForum = 1;
        }
        else {
            echo "";
        }

         //get viewing
        $fb_queryName = $fbConfig->username ? "username" : "name";
		$query= "SELECT w.userid, u.$fb_queryName AS username , k.showOnline FROM #__fb_whoisonline AS w LEFT JOIN #__users AS u ON u.id=w.userid LEFT JOIN #__fb_users AS k ON k.userid=w.userid  WHERE w.link like '%" . addslashes($_SERVER['REQUEST_URI']) . "%'";
		$database->setQuery($query);
		$users = $database->loadObjectList();
			check_dberror("Unable to load who is online.");
		$total_viewing = count($users);


        if ($sfunc == "userprofile")
        {
            echo _USER_PROFILE;
            echo $username;
        }
        else {
			echo " ($total_viewing " . _FB_PATHWAY_VIEWING . ")&nbsp;";
			$totalguest = 0;
			foreach ($users as $user) {
				if ($user->userid != 0)
				{
					if ( $user->showOnline > 0 ){
					echo '<small>' . fb_link::GetProfileLink( $user->userid, $user->username) . ' ,</small> ';
					}
				}
				else
				{
					$totalguest = $totalguest + 1;
				}
			}
      if ($totalguest > 0) { if ($totalguest==1) { echo '<small style="font-weight:normal;" >('.$totalguest.') '._WHO_ONLINE_GUEST.'</small>'; } else { echo '<small style="font-weight:normal;" >('.$totalguest.') '._WHO_ONLINE_GUESTS.'</small>'; } }
       }

        unset($shome, $spath, $parent_ids, $catids, $results, $sname);
        $mainframe->setPageTitle($fr_title_name . (($jr_topic_title) ? $jr_topic_title : "") . ' - ' . $fbConfig->board_title);
        ?>
    </div>

    </div>

<?php
}
?>
<!-- / Pathway -->
