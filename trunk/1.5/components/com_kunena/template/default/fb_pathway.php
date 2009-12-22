<?php
/**
* @version $Id$
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

global $func, $boardclass, $id, $catid, $kunena_db;
global $kunena_topic_tile;

$kunena_config =& CKunenaConfig::getInstance();
?>
<!-- Pathway -->
<?php
$sfunc = JRequest::getVar("func", null);

if ($func != "")
{
?>

    <div class = "<?php echo $boardclass ?>forum-pathway">
        <?php
        $catids = intval($catid);
        $kunena_path_menu = array ();
        echo '<div class="path-element-first">' . CKunenaLink::GetKunenaLink( kunena_htmlspecialchars(stripslashes($kunena_config->board_title)) ) . '</div>';

        $spath = '';

        while ($catids > 0)
        {
            $query = "SELECT * FROM #__fb_categories WHERE id='{$catids}' AND published='1'";
            $kunena_db->setQuery($query);
            $results = $kunena_db->loadObject();
            if (!$results) break;
			$parent_ids = $results->parent;
			$fr_name = kunena_htmlspecialchars(trim(stripslashes($results->name)));
            //$cids=@mysql_result( $results, 0, 'id' );
            $sname = CKunenaLink::GetCategoryLink( 'showcat', $catids, $fr_name);

            if ($catid == $catids && $sfunc != "view")
            {
                $fr_title_name = $fr_name;
                $kunena_path_menu[] = $fr_name;
            }
            else {
                $kunena_path_menu[] = $sname;
            }

            // write path
            if (empty($spath)) {
                $spath = $sname;
            }
            else {
                $spath = $sname . "<div class=\"path-element\">" . $spath . "</div>";
            }

            // next looping
            $catids = $parent_ids;
        }

        //reverse the array
        $kunena_path_menu = array_reverse($kunena_path_menu);

        //  echo $shome." " . $jr_arrow .$jr_arrow ." ". $spath;
        //attach topic name
        if ($sfunc == "view" and $id)
        {
            $sql = "SELECT subject, id FROM #__fb_messages WHERE id='{$id}'";
            $kunena_db->setQuery($sql);
            $kunena_topic_tile = stripslashes(kunena_htmlspecialchars($kunena_db->loadResult()));
            $kunena_path_menu[] = $kunena_topic_tile;
        //     echo " " . $jr_arrow .$jr_arrow ." ". $kunena_topic_tile;
        }

        // print the list
        $jr_forum_count = count($kunena_path_menu);

		$fireinfo = '';
        if (!empty($forumLocked))
        {
            $fireinfo = isset($fbIcons['forumlocked']) ? ' <img src="' . KUNENA_URLICONSPATH . $fbIcons['forumlocked']
                     . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : ' <img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0"  alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
            $lockedForum = 1;
        }

        if (!empty($forumReviewed))
        {
            $fireinfo = isset($fbIcons['forummoderated']) ? ' <img src="' . KUNENA_URLICONSPATH . $fbIcons['forummoderated']
                     . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : ' <img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '">';
            $moderatedForum = 1;
        }

        for ($i = 0; $i < $jr_forum_count; $i++)
        {
            if ($i == $jr_forum_count-1) {
                echo '<div class="path-element-last">' . $kunena_path_menu[$i] . $fireinfo . '</div>';
            }
            else {
                echo '<div class="path-element">' . $kunena_path_menu[$i] . '</div>';
            }
        }

         //get viewing
        $fb_queryName = $kunena_config->username ? "username" : "name";
		$query= "SELECT w.userid, u.id, u.{$fb_queryName} AS username, k.showOnline FROM #__fb_whoisonline AS w LEFT JOIN #__users AS u ON u.id=w.userid LEFT JOIN #__fb_users AS k ON k.userid=w.userid WHERE w.link like '%" . addslashes($_SERVER['REQUEST_URI']) . "%' GROUP BY w.userid ORDER BY u.{$fb_queryName} ASC";
		$kunena_db->setQuery($query);
		$users = $kunena_db->loadObjectList();
			check_dberror("Unable to load who is online.");
		$total_viewing = count($users);

        if ($sfunc == "userprofile")
        {
            echo _USER_PROFILE;
            echo $username;
        }
        else {
			echo "<div class=\"path-element-users\">($total_viewing " . _KUNENA_PATHWAY_VIEWING . ")&nbsp;";
			$totalguest = 0;
			$lastone = end($users);
			$divider = ', ';
			foreach ($users as $user) {
				if ($user->userid != 0)
				{
					if($user==$lastone && !$totalguest){
					$divider = '';
					}
					if ( $user->showOnline > 0 ){
					echo CKunenaLink::GetProfileLink($kunena_config,  $user->userid, $user->username) . $divider.' ';
					}
				}
				else
				{
					$totalguest = $totalguest + 1;
				}
			}
      if ($totalguest > 0) { if ($totalguest==1) { echo $totalguest.'&nbsp;'._WHO_ONLINE_GUEST; } else { echo '('.$totalguest.') '._WHO_ONLINE_GUESTS; } }
       }

        unset($shome, $spath, $parent_ids, $catids, $results, $sname);
        $fr_title = '';
		if (!empty($fr_title_name)) $fr_title .= $fr_title_name;
		if (!empty($kunena_topic_tile)) $fr_title .= $kunena_topic_tile;

		$document=& JFactory::getDocument();

		$document->setTitle(($fr_title ? $fr_title : _KUNENA_CATEGORIES) . ' - ' . stripslashes($kunena_config->board_title));
        ?>
		</div>
    </div>

<?php
}
?>
<!-- / Pathway -->
